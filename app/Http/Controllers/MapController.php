<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class MapController extends Controller
{
    public function index(){

        $centerMap = array(24.853449, -98.827877);
        
        // * get municipalities data
        $municipalities = \App\Models\Municipality::all();

        // * load tam geometries
        $contents = File::get('tam-geometries.json');
        $jsonData = json_decode(json: $contents, associative: true)["features"];

        // * process the json data
        $municipalitiesGeom = array();
        foreach ($jsonData as $element) {
            $municipalitiesGeomItem = array();
            $municipalitiesGeomItem["properties"] = $element["properties"];
            $geometryItems = array();
            foreach( $element["geometry"]["coordinates"][0] as $geomItem){
                array_push( $geometryItems, array( $geomItem[1], $geomItem[0] ));
            }
            $municipalitiesGeomItem["geometry"] = $geometryItems;
            array_push( $municipalitiesGeom, $municipalitiesGeomItem);
        }

        // * return view
        return Inertia::render("Map/Index", [
            "title" => "Hola mundo",
            "centerMap" => $centerMap,
            "municipalities" => $municipalities,
            "municipalitiesGeom" => $municipalitiesGeom
        ]);


    }
}
