<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthApiService
{

    static function baseUrl(): string
    {
        return config('app.AUTH_API_ENDPOINT');
    }

    static function authorizationValue()
    {
        return sprintf('Bearer %s', config('app.AUTH_API_TOKEN') );
    }
        
    /**
     * get data
     *
     * @param  string $uri
     * @return mix
     * @throws Illuminate\Http\Client\RequestException
     */
    public static function getUserInfo(string $sessionToken){

        // * Send notification
        $response = Http::withHeaders([
            'Authorization' => self::authorizationValue(),
        ])->get( Str::finish( self::baseUrl(), '/api/session/me?t='. $sessionToken ));

        // * Validate response
        if(!$response->successful()) {
            $response->throw();
        }
        return $response->json();
    }

}