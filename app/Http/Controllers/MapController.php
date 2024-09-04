<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;
use App\Services\MapService;

class MapController extends Controller
{

    protected MapService $mapService;

    function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    /**
     * return the view of the map
     *
     * @param  Request $request
     * @return mixed
     */
    public function index(Request $request){

        // * set center of the map
        $centerMap = array(24.853449, -98.827877);

        // * get municipalities data
        $municipalities = \App\Models\Municipality::all();

        // * load municipalities polygons and append the center propertie
        $municipalitiesGeom = $this->mapService->getMunicipalitiesPolygons();

        // * return view based on movil or desktop
        $agent = new Agent();
        if ($agent->isMobile()) {
            
            // * return view
            return Inertia::render("Map/IndexMovil", [
                "title" => "Hola mundo",
                "centerMap" => $centerMap,
                "municipalities" => $municipalities,
                "municipalitiesGeom" => $municipalitiesGeom
            ]);

        } else {
            
            // * return view
            return Inertia::render("Map/Index", [
                "title" => "Hola mundo",
                "centerMap" => $centerMap,
                "municipalities" => $municipalities,
                "municipalitiesGeom" => $municipalitiesGeom
            ]);
        }

    }

}