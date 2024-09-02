<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;

class MapController extends Controller
{
    public function index(){

        // * set center of the map
        $centerMap = array(24.853449, -98.827877);
        

        // * get municipalities data
        $municipalities = \App\Models\Municipality::all();

        // * load municipalities polygons
        $municipalitiesGeom = $this->loadMunicipalitiesPolygons();

        // * load officess locations
        $officesLocations = $this->loadOfficesLocations();
        
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
                "municipalitiesGeom" => $municipalitiesGeom,
                "officesLocations" => $officesLocations
            ]);
        }

    }

    
    /**
     * loadMunicipalitiesPolygons
     *
     * @return array
     */
    private function loadMunicipalitiesPolygons(){
        // * load municipalities geometries
        $contents = File::get('tam-geometries.json');
        $jsonData = json_decode(json: $contents, associative: true)["features"];

        // * process the json data
        $municipalitiesPolygons = array();
        foreach ($jsonData as $element) {
            $municipalitiesGeomItem = array();
            $municipalitiesGeomItem["properties"] = $element["properties"];
            $geometryItems = array();
            foreach( $element["geometry"]["coordinates"][0] as $geomItem){
                array_push( $geometryItems, array( $geomItem[1], $geomItem[0] ));
            }
            $municipalitiesGeomItem["geometry"] = $geometryItems;
            array_push( $municipalitiesPolygons, $municipalitiesGeomItem);
        }

        return $municipalitiesPolygons;
    }
    
    /**
     * loadOfficesLocations
     *
     * @return array
     */
    private function loadOfficesLocations(){
        // * load file
        $contents = File::get('offices.json');
        $officessData = json_decode(json: $contents, associative: true)["offices"];
        return $officessData;
    }

}