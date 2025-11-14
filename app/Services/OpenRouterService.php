<?php

namespace App\Services;

use App\Models\AiQueryLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://openrouter.ai/api/v1';
    protected string $primaryModel = 'anthropic/claude-3.5-sonnet';
    protected string $fallbackModel = 'google/gemini-pro-1.5';
    
    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
    }
    
    public function generatePermitRecommendations(
        string $kbliCode,
        string $kbliDescription,
        string $sector,
        ?string $businessScale = null,
        ?string $locationType = null,
        ?int $clientId = null
    ): ?array {
        return null; // Placeholder - akan dilengkapi nanti
    }
}
