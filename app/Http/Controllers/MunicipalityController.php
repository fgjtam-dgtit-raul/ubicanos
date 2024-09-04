<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Municipality;

class MunicipalityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $municipalities = Municipality::all();
        
        return Inertia::render('Municipality/Index',[
            "title" => "Hola mundo",
            "municipalities" => $municipalities
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $municipality_id)
    {
        // retrive the municipality
        $municipality = Municipality::find($municipality_id);
        
        // return the view
        return Inertia::render('Municipality/Edit', [
            "title" => "Municipio " . $municipality->name,
            "municipality" => $municipality
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $municipality_id)
    {
        $request->validate([
            'cvegeo' => 'required|string',
            'locations' => 'required|array',
            'locations.*.name' => 'required|string',
            'locations.*.address' => 'required|string',
            'locations.*.geolocation' => 'required|array',
            'locations.*.geolocation.*' => 'numeric'
        ], [], [
            "locations.*.name" => "oficina",
            "locations.*.address" => "direccion",
            "locations.*.geolocation" => "coordenadas",
            "locations.*.geolocation.*" => "coordenada"
        ]);

        // retrive the municipality
        $municipality = Municipality::find($municipality_id);
        $municipality->locations = $request->input('locations');
        $municipality->save();

        return redirect()->route('municipality.index' );

    }

}
