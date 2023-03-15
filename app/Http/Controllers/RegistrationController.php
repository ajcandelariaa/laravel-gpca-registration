<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function registrationView($eventYear, $eventCategory, $eventId){
        if(Event::where('year', $eventYear)->where('category', $eventCategory)->where('id', $eventId)->exists()){

            $event = Event::where('year', $eventYear)->where('category', $eventCategory)->where('id', $eventId)->first();

            return view('registration.registration',[
                'pageTitle' => "Register $eventCategory",
                'event' => $event,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }
}
