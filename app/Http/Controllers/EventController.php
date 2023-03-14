<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // RENDER VIEWS
    public function manageEventView(){
        $events = Event::all();
        return view('admin.event.events', [
            "pageTitle" => "Manage Event",
            "events" => $events,
        ]);
    }

    public function manageAddEventView(){
        return view('admin.event.add_event', [
            "pageTitle" => "Add Event",
        ]);
    }


    // RENDER LOGICS
    public function addEvent(Request $request){
        dd($request);
    }
}
