<?php

namespace Tests\Feature;

use Tests\TestCase;

class KbliInternalApiKeyTest extends TestCase
{
    public function test_refresh_requires_internal_api_key(): void
    {
        $this->postJson('/api/kbli-recommendations/refresh', [
            'kbli_code' => '00000',
        ])->assertStatus(401);
    }

    public function test_refresh_rejects_invalid_internal_api_key(): void
    {
        $this->postJson('/api/kbli-recommendations/refresh', [
            'kbli_code' => '00000',
        ], [
            'X-Internal-Api-Key' => 'wrong',
        ])->assertStatus(403);
    }

    public function test_refresh_allows_request_with_valid_internal_api_key_to_reach_validation(): void
    {
        $this->postJson('/api/kbli-recommendations/refresh', [
            'kbli_code' => '00000',
        ], [
            'X-Internal-Api-Key' => 'testing-internal-key',
        ])->assertStatus(422);
    }
}
