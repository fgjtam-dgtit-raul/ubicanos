<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class HomeController extends Controller
{

    function index(Request $request) {

        $sessionToken = $request->cookie("__fgjtam.auth.session-token");

        if( $sessionToken == null){
            // * temporally make a cookie for testing
            $sessionToken = '735B7B602D090B12649AE09641F18681AC4BC94EE96FE1BC7A9E74F55F83A76F';
            $cookie = Cookie::make('__fgjtam.auth.session-token', $sessionToken,  minutes: 60 * 24 * 7, raw: true); // 7 days

            Log::notice("Session cookie its not presented, making a new one.");
        }

        // TODO: retrive the user by the cookie
        $userResponse = $this->getPersonData($sessionToken);
        

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

        if(isset($cookie)){
            return redirect()->route('map')->cookie($cookie);
        }else{
            return redirect()->route('map');
        }

    }

    function showProfile() {
        return redirect("https://fiscaliadigital.fgjtam.gob.mx/mi-perfil");
    }

    private function getPersonData($token){
        return [
            "personId" => "af8b4171-7c2c-4788-049f-08dcc721ec01",
            "rfc" => "SIFF95121856A",
            "curp" => "SIFF951218MTSFNR09",
            "name" => "Jose Saldiva",
            "firstName" => "Ramirez",
            "lastName" => "Almendres",
            "email" => "jose.saldiva@email.com",
            "birthdate" => "1993-11-12T00:00:00",
            "genderId" => 1,
            "genderName" => "MASCULINO",
            "maritalStatusId" => 1,
            "maritalStatusName" => "SOLTERO(A)",
            "nationalityId" => 31,
            "nationalityName" => "MEXICANA",
            "occupationId" => 1,
            "occupationName" => "EMPLEADO",
            "appName" => null,
            "fullName" => "Jose Saldiva Ramirez Almendres",
            "birthdateFormated" => "12/NOV/1993",
            "address" => null,
            "contactInformation" => []
        ];
    }

}
