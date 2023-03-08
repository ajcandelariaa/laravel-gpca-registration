<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    // RENDER VIEWS
    public function manageEventView(){
        return view('admin.event.events', [
            "pageTitle" => "Manage Event"
        ]);
    }
}
