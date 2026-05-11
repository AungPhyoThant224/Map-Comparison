<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MapController extends Controller
{
    public function index(Request $request, string $type = 'leaflet'): Response
    {
        $query = Customer::with(['businessType', 'photos']);
        $latCenter = 16.7863;
        $lngCenter = 96.1604;

        // Check if map bounds are sent in the request
        if ($request->has(['sw_lat', 'sw_lng', 'ne_lat', 'ne_lng'])) {
            $query->whereBetween('latitude', [$request->sw_lat, $request->ne_lat])
                ->whereBetween('longitude', [$request->sw_lng, $request->ne_lng]);

            $latCenter = ($request->sw_lat + $request->ne_lat) / 2;
            $lngCenter = ($request->sw_lng + $request->ne_lng) / 2;
        }

        // Inertia Lazy Loading / Partial Reload performance
        return Inertia::render('Maps/MapView', [
            'customers' => fn() => CustomerResource::collection($query->get()),
            'mapLibrary' => $type,
            'initialBounds' => [
                'lat' => $latCenter,
                'lng' => $lngCenter,
            ]
        ]);
    }
}
