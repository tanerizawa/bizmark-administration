<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'conversation_id',
        'direction',
        'content',
        'wa_message_id',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsAppConversation::class, 'conversation_id');
    }
}
