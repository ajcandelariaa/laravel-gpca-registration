<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function registrationView($year, $eventName, $eventId){
        return view('registration.registration',[
            'pageTitle' => "Register $eventName",
            'year' => $year,
            'eventName' => $eventName,
            'eventId' => $eventId,
        ]);
    }
}
