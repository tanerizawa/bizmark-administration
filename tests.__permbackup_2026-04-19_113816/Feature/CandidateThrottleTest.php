<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateThrottleTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_routes_are_throttled(): void
    {
        for ($i = 0; $i < 60; $i++) {
            $this->get('/candidate/test/nonexistent-token')->assertStatus(404);
        }

        $this->get('/candidate/test/nonexistent-token')->assertStatus(429);
    }
}
