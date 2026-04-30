<?php

namespace App\Modules\Perizinan\Controllers\Public;

use App\Http\Controllers\Controller;

class PolygonToolController extends Controller
{
    public function index()
    {
        $client = auth('client')->user();

        return view('tools.polygon-shp', [
            'maxPoints' => config('shapefile.max_points', 500),
            'authClient' => $client ? [
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone ?? $client->mobile ?? '',
                'company_name' => $client->company_name ?? '',
                'contact_person' => $client->contact_person ?? '',
            ] : null,
        ]);
    }
}
