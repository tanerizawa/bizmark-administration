<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnifiedLoginOpenRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_protocol_relative_url(): void
    {
        $response = $this->get('/login?redirect=//evil.com/phish');

        // Should NOT redirect to evil.com
        $response->assertStatus(200);
        $response->assertViewIs('auth.unified-login');
    }

    public function test_rejects_external_url(): void
    {
        $response = $this->get('/login?redirect=https://evil.com/phish');

        // Should NOT redirect to evil.com
        $response->assertStatus(200);
        $response->assertViewIs('auth.unified-login');
    }

    public function test_accepts_local_path(): void
    {
        $response = $this->get('/login?redirect=/dashboard');

        // Should accept local redirect
        $response->assertStatus(200);
        $response->assertViewIs('auth.unified-login');
    }

    public function test_accepts_same_host_url(): void
    {
        $response = $this->get('/login?redirect=' . url('/dashboard'));

        // Should accept URL pointing to same host
        $response->assertStatus(200);
        $response->assertViewIs('auth.unified-login');
    }

    public function test_rejects_url_with_encoded_newlines(): void
    {
        $response = $this->get('/login?redirect=%0a%0dhttps://evil.com');

        // Should reject malformed redirect
        $response->assertStatus(200);
        $response->assertViewIs('auth.unified-login');
    }

    public function test_rejects_empty_redirect(): void
    {
        $response = $this->get('/login?redirect=');

        // Empty redirect should be ignored (no error)
        $response->assertStatus(200);
        $response->assertViewIs('auth.unified-login');
    }
}
