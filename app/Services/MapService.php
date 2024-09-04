<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MapService
{

    
    /**
     * getMunicipalitiesPolygons
     *
     * @return array
     */
    function getMunicipalitiesPolygons(){
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

        foreach ($municipalitiesPolygons as &$value) {
            $value['center'] = $this->calculateCenter($value['geometry']);
        }
        unset($value);


        return $municipalitiesPolygons;
    }

    /**
     * calculate center
     *
     * @param  array $coordinates
     * @return array
     */
    function calculateCenter($coordinates) {
        $latSum = 0;
        $lonSum = 0;
    
        foreach ($coordinates as $coord) {
            $latSum += $coord[0];
            $lonSum += $coord[1];
        }

        $center = [$latSum / count($coordinates), $lonSum / count($coordinates)];

        return $center;
    }


}