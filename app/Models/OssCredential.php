<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class OssCredential extends Model
{
    protected $fillable = [
        'client_id',
        'oss_username_encrypted',
        'oss_password_encrypted',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'oss_username_encrypted',
        'oss_password_encrypted',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Client::class);
    }

    /**
     * Store username (auto-encrypts).
     */
    public function setOssUsernameAttribute(string $value): void
    {
        $this->attributes['oss_username_encrypted'] = Crypt::encryptString($value);
    }

    /**
     * Store password (auto-encrypts).
     */
    public function setOssPasswordAttribute(string $value): void
    {
        $this->attributes['oss_password_encrypted'] = Crypt::encryptString($value);
    }

    /**
     * Get decrypted username.
     */
    public function getDecryptedUsername(): string
    {
        return Crypt::decryptString($this->oss_username_encrypted);
    }

    /**
     * Get decrypted password.
     */
    public function getDecryptedPassword(): string
    {
        return Crypt::decryptString($this->oss_password_encrypted);
    }
}
