<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),

        // Legacy (backward compat) — not used directly by tiered system
        'model' => env('OPENROUTER_MODEL', 'google/gemini-2.5-flash'),

        // ── Free tier (konsultasi-gratis, public landing page) ──
        // Cost-optimized: good quality at minimal cost per request
        'free_primary_model'    => env('OPENROUTER_FREE_PRIMARY_MODEL', 'google/gemini-2.5-flash'),
        'free_fallback_model'   => env('OPENROUTER_FREE_FALLBACK_MODEL', 'deepseek/deepseek-v3.2'),

        // ── Premium tier (client portal, authenticated users) ──
        // Quality-optimized: best reasoning accuracy for paying clients
        'premium_primary_model'  => env('OPENROUTER_PREMIUM_PRIMARY_MODEL', 'anthropic/claude-3.5-sonnet'),
        'premium_fallback_model' => env('OPENROUTER_PREMIUM_FALLBACK_MODEL', 'google/gemini-2.5-flash'),
    ],

    // SearXNG — Open-source self-hosted metasearch (Priority 1: gratis, unlimited)
    // Docker: bizmark_searxng container — https://github.com/searxng/searxng
    'searxng' => [
        'url' => env('SEARXNG_URL', 'http://bizmark_searxng:8080'),
    ],

    // Google Custom Search API (Priority 2: gratis 100/hari)
    // https://developers.google.com/custom-search/v1/overview
    'google_search' => [
        'api_key' => env('GOOGLE_SEARCH_API_KEY'),
        'engine_id' => env('GOOGLE_SEARCH_ENGINE_ID'),
    ],

    // Google Search Console (GSC) — OAuth2 refresh token flow
    // Setup: Google Cloud Console → APIs & Services → Credentials → OAuth 2.0 Client ID
    // Scopes: https://www.googleapis.com/auth/webmasters.readonly
    // One-time auth URL: https://accounts.google.com/o/oauth2/auth?...
    // See docs/GSC_SETUP.md for step-by-step instructions
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'refresh_token' => env('GOOGLE_REFRESH_TOKEN'),
        'gsc_site_url'  => env('GSC_SITE_URL', 'https://bizmark.id/'),  // must match GSC property URL exactly
    ],

    'perizinan_ai' => [
        'url' => env('PERIZINAN_AI_URL', 'https://api.bizmark.id'),
        'username' => env('PERIZINAN_AI_USERNAME'),
        'password' => env('PERIZINAN_AI_PASSWORD'),
        'timeout' => env('PERIZINAN_AI_TIMEOUT', 30),
    ],

    'pexels' => [
        'api_key' => env('PEXELS_API_KEY'),
    ],

    'indexnow' => [
        'key' => env('INDEXNOW_KEY', 'b1zm4rk-1nd3xn0w-k3y-2026'),
    ],

    // Content Syndication Platforms
    'medium' => [
        'token' => env('MEDIUM_INTEGRATION_TOKEN'),
    ],

    'devto' => [
        'api_key' => env('DEVTO_API_KEY'),
    ],

    'linkedin' => [
        'access_token' => env('LINKEDIN_ACCESS_TOKEN'),
        'organization_id' => env('LINKEDIN_ORGANIZATION_ID'),
    ],

    // Telegram Bot for channel posting
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'channel_id' => env('TELEGRAM_CHANNEL_ID'),
    ],

    // Twitter/X API v2
    'twitter' => [
        'api_key' => env('TWITTER_API_KEY'),
        'api_secret' => env('TWITTER_API_SECRET'),
        'access_token' => env('TWITTER_ACCESS_TOKEN'),
        'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
        'bearer_token' => env('TWITTER_BEARER_TOKEN'),
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
    ],

    // Facebook Page
    'facebook' => [
        'page_id' => env('FACEBOOK_PAGE_ID'),
        'page_access_token' => env('FACEBOOK_PAGE_ACCESS_TOKEN'),
    ],

    // Google Business Profile
    'gbp' => [
        'refresh_token' => env('GBP_REFRESH_TOKEN'),
        'location_id' => env('GBP_LOCATION_ID'),
    ],

    'social_posting' => [
        'free_only' => env('SOCIAL_POSTING_FREE_ONLY', true),
    ],

];
