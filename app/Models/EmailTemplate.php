<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'content',
        'plain_content',
        'thumbnail',
        'category',
        'is_active',
        'variables',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'variables' => 'array',
    ];

    // Relationships
    public function campaigns()
    {
        return $this->hasMany(EmailCampaign::class, 'template_id');
    }

    // Methods
    public function replaceVariables($data)
    {
        $content = $this->content;
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        return $content;
    }

    public function getAvailableVariables()
    {
        return $this->variables ?? ['name', 'email', 'unsubscribe_url'];
    }
}
