<?php

/**
 * Client Portal Redesign Feature Flag Configuration.
 *
 * Controls rollout of the high-tech UI redesign documented in
 * plans/CLIENT_PORTAL_HIGHTECH_REDESIGN_PRD_2026.md
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Master switch
    |--------------------------------------------------------------------------
    | Set to true to enable the v2 portal redesign for all client routes.
    | Override per-environment via .env: CLIENT_PORTAL_REDESIGN=true|false
    */
    'enabled' => env('CLIENT_PORTAL_REDESIGN', true),

    /*
    |--------------------------------------------------------------------------
    | Allow legacy override
    |--------------------------------------------------------------------------
    | When true, appending ?legacy=1 to any client route disables v2 for that
    | session. Useful for QA & emergency rollback. Stored in session.
    */
    'allow_legacy_query' => true,

    /*
    |--------------------------------------------------------------------------
    | Per-route opt-in (incremental rollout)
    |--------------------------------------------------------------------------
    | When `enabled` is false, individual route names below still get the v2
    | shell. Useful for piloting a single page before flipping the master.
    */
    'enabled_routes' => [
        // 'client.dashboard',
        // 'client.applications.index',
    ],

    /*
    |--------------------------------------------------------------------------
    | Show command palette mount
    |--------------------------------------------------------------------------
    */
    'command_palette' => env('CLIENT_PORTAL_CMDK', true),

];
