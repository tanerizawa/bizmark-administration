<?php

/**
 * Landing page static content.
 * Data dikelola di resources/data/landing.json via StaticDataService::landing().
 * File ini adalah compatibility bridge agar config('landing.*') tetap bekerja.
 */
return app(\App\Services\StaticDataService::class)->landing();
