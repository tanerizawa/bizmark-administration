<?php

return [
    'enabled' => (bool) env('TWO_FACTOR_ENABLED', true),
    'grace_days' => (int) env('TWO_FACTOR_GRACE_DAYS', 7),
    'trust_days' => (int) env('TWO_FACTOR_TRUST_DAYS', 30),
    'cookie_name' => env('TWO_FACTOR_TRUST_COOKIE', 'two_factor_trust'),
    'session_key' => env('TWO_FACTOR_SESSION_KEY', 'two_factor_verified_at'),
];
