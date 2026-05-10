<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MapController extends Controller
{
    public function index(string $type = 'leaflet'): Response
    {
        $customers = Customer::with(['businessType', 'photos'])->get();

        return Inertia::render('Maps/MapView', [
            'customers' => CustomerResource::collection($customers),
            'mapLibrary' => $type
        ]);
    }
}
