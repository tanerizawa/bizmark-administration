<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppConversation extends Model
{
    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'wa_phone',
        'wa_name',
        'status',
        'context',
        'service_inquiry_id',
        'last_message_at',
    ];

    protected $casts = [
        'context' => 'array',
        'last_message_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id');
    }

    public function serviceInquiry(): BelongsTo
    {
        return $this->belongsTo(ServiceInquiry::class);
    }

    public function isHandedOff(): bool
    {
        return $this->status === 'handoff';
    }
}
