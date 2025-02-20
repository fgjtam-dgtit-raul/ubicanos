<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Municipality;

class APIMunicipalityController extends Controller
{
    public function getMumicipalities()
    {
        $municipalities = Municipality::all()->map(function ($municipality)
            {
                return [
                    "id" => $municipality->_id,
                    "name" => $municipality->name,
                    "cvegeo" => $municipality->cvegeo,
                    "locations" => $municipality->locations
                ];
            }
        )->all();
        return response()->json($municipalities, 200);
    }

    public function getMunicipality(string $municipalityId)
    {
        $municipality = Municipality::find($municipalityId);
        return response()->json($municipality, 200);
    }

    public function getLocations(string $municipalityId)
    {
        $locations = Municipality::find($municipalityId)->locations;
        return response()->json($locations, 200);
    }
}
