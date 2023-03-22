<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\MainDelegate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function registrationView($eventYear, $eventCategory, $eventId){
        if(Event::where('year', $eventYear)->where('category', $eventCategory)->where('id', $eventId)->exists()){

            $event = Event::where('year', $eventYear)->where('category', $eventCategory)->where('id', $eventId)->first();

            return view('registration.registration',[
                'pageTitle' => $event->name,
                'event' => $event,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    
    public function eventRegistrantsView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $finalListOfRegistrants = array();

            $registrants = MainDelegate::where('event_id', $eventId)->get();

            if(!$registrants->isEmpty()){
                foreach($registrants as $registrant){
                    $date_time_value = $registrant->registered_date_time;
                    $date = Carbon::parse($date_time_value);
                    $formatted_date = $date->format('M j, Y g:i A');

                    array_push($finalListOfRegistrants, [
                        'registrantId' => $registrant->id,
                        'registeredDateTime' => $formatted_date,
                        'registrantCompany' => $registrant->company_name,
                        'registrantCountry' => $registrant->company_country,
                        'registrantCity' => $registrant->company_city,
                        'registrantPassType' => $registrant->pass_type,
                        'registrantQuantity' => $registrant->quantity,
                        'registrantStatus' => $registrant->status,
                        'registrantTotalAmount' => $registrant->total_amount,
                    ]);
                }
            }

            return view('admin.event.detail.registrants.registrants', [
                "pageTitle" => "Event Registrants",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
                "finalListOfRegistrants" => $finalListOfRegistrants,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrantDetailView($eventCategory, $eventId, $registrantId){
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            if(MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->exists()){
                
                return view('admin.event.detail.registrants.registrants_detail', [
                    "pageTitle" => "Event Registrant Details",
                    "eventCategory" => $eventCategory,
                    "eventId" => $eventId,
                    "registrantId" => $registrantId,
                ]);
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }
}
