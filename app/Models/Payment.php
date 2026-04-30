<?php

namespace App\Models;

use App\Observers\AdminAuditObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

#[ObservedBy([AdminAuditObserver::class])]
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'payable_type',
        'payable_id',
        'client_id',
        'quotation_id',
        'amount',
        'payment_type',
        'payment_method',
        'gateway_provider',
        'gateway_transaction_id',
        'gateway_response',
        'status',
        'bank_name',
        'account_number',
        'account_holder',
        'transfer_proof_path',
        'verified_by',
        'verified_at',
        'verification_notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'verified_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (! $payment->payment_number) {
                $payment->payment_number = self::generatePaymentNumber();
            }
        });
    }

    public static function generatePaymentNumber(): string
    {
        $year = date('Y');
        $month = date('m');

        $lastPayment = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if ($lastPayment && is_string($lastPayment->payment_number)) {
            if (preg_match('/(\d+)$/', $lastPayment->payment_number, $m)) {
                $nextNumber = (int) $m[1] + 1;
            }
        }

        return sprintf('PAY-%s%s-%04d', $year, $month, $nextNumber);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Resolve transfer proof storage disk/path with backward compatibility.
     * New uploads use private disk, old records may still exist on public.
     *
     * @return array{0:string,1:string}|null
     */
    public function resolveTransferProofLocation(): ?array
    {
        if (blank($this->transfer_proof_path)) {
            return null;
        }

        $path = ltrim($this->transfer_proof_path, '/');

        if (Storage::disk('private')->exists($path)) {
            return ['private', $path];
        }

        if (Storage::disk('public')->exists($path)) {
            return ['public', $path];
        }

        return null;
    }
}
