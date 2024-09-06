<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;

use App\Services\AuthApiService;
use App\Services\MapService;

use App\Models\User;
use App\Models\Municipality;

class HomeController extends Controller
{

    protected $cookieName = "__fgjtam.auth.session-token";
    protected AuthApiService $authApiService;
    protected MapService $mapService;

    function __construct(AuthApiService $authApiService, MapService $mapService) {
        $this->authApiService = $authApiService;
        $this->mapService = $mapService;
    }


    function index(Request $request) {

        $tags = array();
        if( $request->filled('tags')){
            $tags = explode(",", $request->input('tags'));
        }

        // * validate the request
        $response = $this->validateAuthSessionToken($request);
        if( $response instanceof RedirectResponse) {
            return $response;
        }

        // * set center of the map
        $centerMap = array(24.853449, -98.827877);

        // * get municipalities data
        $municipalities = \App\Models\Municipality::get()->all();

        // * filter municipalities-locations by tags
        if(!empty($tags)){
            $municipalities = $this->filterMunicipalitiesLocationsByTags($municipalities, $tags);
        }

        // * load municipalities polygons and append the center propertie
        $municipalitiesGeom = $this->mapService->getMunicipalitiesPolygons();

        // * return view based on movil or desktop
        $agent = new Agent();
        if ($agent->isMobile()) {

            // * return view
            return Inertia::render("Map/MapMovilGuest", [
                "title" => "Hola mundo",
                "person" => $response,
                "centerMap" => $centerMap,
                "municipalities" => array_values ($municipalities),
                "municipalitiesGeom" => $municipalitiesGeom
            ]);

        } else {

            // * return view
            return Inertia::render("Map/MapGuest", [
                "title" => "Hola mundo",
                "person" => $response,
                "centerMap" => $centerMap,
                "municipalities" => array_values( $municipalities ),
                "municipalitiesGeom" => $municipalitiesGeom
            ]);
        }

    }

    function redirectFiscaliaDigital(Request $request){
        return Inertia::render('Redirect');
    }

    #region private methods
    /**
     * validateAuthSessionToken
     *
     * @param  Request $request
     * @return array|RedirectResponse
     */
    private function validateAuthSessionToken(Request $request) {

        // * retrive the cookie
        $sessionToken = null;
        $cookies = $request->server('HTTP_COOKIE');
        $cookiesArray = explode(';', $cookies);
        foreach ($cookiesArray as $cookie) {
            $cookieArray = explode('=', $cookie);
            if( trim($cookieArray[0]) == $this->cookieName ){
                $sessionToken = $cookieArray[1];
            }
        }

        
        if(!$sessionToken){
            Log::notice("Session cookie it's not presented.");
            return redirect()->route('fiscalia-digital');
        }

        // * retrive the user by from the AuthAPI
        $userResponse = array();
        try {
            $userResponse = $this->authApiService->getUserInfo($sessionToken);
        }catch(\Throwable $th){
            Log::error("Session token are not valide: {message}", [
                "message" => $th->getMessage(),
                "sessionToken" => $sessionToken
            ]);
            return redirect()->route('fiscalia-digital');
        }

        return $userResponse;
    }

    private function filterMunicipalitiesLocationsByTags( array $municipalities, array $tags){
        $tagsLower = array_map('strtolower', $tags);

        $newMunic = array_filter( $municipalities, function($municipality) use($tagsLower) {

            $filteredLocations = array_filter( $municipality->locations, function($location) use($tagsLower){
                if(!isset( $location['tags'] )){
                    return false;
                }

                $locationTagsLower = array_map('strtolower', $location['tags']);

                // Check if at least one tag exists in both arrays
                $matchingTags = array_intersect($locationTagsLower, $tagsLower);

                return !empty($matchingTags);

            });

            // apend filtered locations
            $municipality->locations = array_values($filteredLocations);

            return !empty($filteredLocations);
        });

        return $newMunic;
    }

    #endregion

}
