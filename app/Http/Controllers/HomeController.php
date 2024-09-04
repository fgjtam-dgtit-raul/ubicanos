<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use App\Services\AuthApiService;
use App\Models\User;

class HomeController extends Controller
{

    protected $cookieName = "__fgjtam.auth.session-token";
    protected $authApiService;

    function __construct(AuthApiService $authApiService) {
        $this->authApiService = $authApiService;
    }


    function index(Request $request) {

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

        if( !isset($sessionToken)){
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

        // * retrive the user
        $user = User::firstOrCreate(
            ['email' => $userResponse['email'] ],
            [
                'name' => $userResponse['fullName'],
                'curp' => $userResponse['curp']
            ]
        );

        // * login the user
        Auth::login($user);
        return redirect()->route('map');

    }

    function redirectFiscaliaDigital(Request $request){
        return Inertia::render('Redirect', [
        ]);
    }

}
