<?php

namespace App\Http\Controllers;

use App\Models\AdditionalDelegate;
use App\Models\Event;
use App\Models\EventRegistrationType;
use App\Models\MainDelegate;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FastTrackController extends Controller
{
    public function getFastTrackDetails($eventCategory, $eventYear){
        $eventCategory = $eventCategory;
        $eventYear = $eventYear;
        
        if(Event::where('category', $eventCategory)->where('year', $eventYear)->exists()){
            $event = Event::where('category', $eventCategory)->where('year', $eventYear)->first();

            return response()->json([
                // 'eventId' => $event->id,
                // 'eventName' => $event->name,
                // 'eventLogo' => asset(Storage::url($event->logo)),
                // 'eventBanner' => asset(Storage::url($event->banner)),
                'confirmedDelegates' => $this->getConfirmedDelegates($event->id, $eventCategory, $eventYear),
            ], 200);
        } else {
            return response()->json([
                'status' => '404',
                'message' => "Event not found",
            ], 404);
        }
    }

    public function getConfirmedDelegates($eventId, $eventCategory, $eventYear){
        $confirmedDelegates = array();
        $mainDelegates = MainDelegate::where('event_id', $eventId)->get();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        
        foreach ($mainDelegates as $mainDelegate) {
            $companyName = "";

            if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                if ($mainDelegate->registration_status == "confirmed") {

                    $registrationType = EventRegistrationType::where('event_id', $eventId)->where('event_category', $eventCategory)->where('registration_type', $mainDelegate->badge_type)->first();

                    $transactionId = Transaction::where('event_id', $eventId)->where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                    if ($mainDelegate->alternative_company_name != null) {
                        $companyName = $mainDelegate->alternative_company_name;
                    } else {
                        $companyName = $mainDelegate->company_name;
                    }


                    if ($mainDelegate->salutation == "Dr." || $mainDelegate->salutation == "Prof.") {
                        $delegateSalutation = $mainDelegate->salutation;
                    } else {
                        $delegateSalutation = null;
                    }

                    $fullName = $delegateSalutation . ' ' . $mainDelegate->first_name . ' ' . $mainDelegate->middle_name . ' ' . $mainDelegate->last_name;

                    // $printUrl = route('public-print-badge', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'delegateId' => $mainDelegate->id, 'delegateType' => 'main']);
                    array_push($confirmedDelegates, [
                        'transactionId' => $finalTransactionId,
                        'id' => $mainDelegate->id,
                        'delegateType' => "main",
                        'fullName' => trim($fullName),
                        'salutation' => $mainDelegate->salutation,
                        'fname' => $mainDelegate->first_name,
                        'mname' => $mainDelegate->middle_name,
                        'lname' => $mainDelegate->last_name,
                        'jobTitle' => trim($mainDelegate->job_title),
                        'companyName' => trim($companyName),
                        'badgeType' => $mainDelegate->badge_type,

                        'frontText' => $registrationType->badge_footer_front_name,
                        'frontTextColor' => $registrationType->badge_footer_front_text_color,
                        'frontTextBGColor' => $registrationType->badge_footer_front_bg_color,
                        'seatNumber' => $mainDelegate->seat_number,
                        // 'printUrl' => $printUrl,
                    ]);
                }
            }


            $subDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegate->id)->get();

            if (!$subDelegates->isEmpty()) {
                foreach ($subDelegates as $subDelegate) {

                    if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {
                        if ($mainDelegate->registration_status == "confirmed") {

                            $registrationType = EventRegistrationType::where('event_id', $eventId)->where('event_category', $eventCategory)->where('registration_type', $subDelegate->badge_type)->first();

                            $transactionId = Transaction::where('delegate_id', $subDelegate->id)->where('delegate_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $finalTransactionId = $eventYear . $eventCode . $lastDigit;


                            if ($subDelegate->salutation == "Dr." || $subDelegate->salutation == "Prof.") {
                                $delegateSalutation = $subDelegate->salutation;
                            } else {
                                $delegateSalutation = null;
                            }

                            $fullName = $delegateSalutation . ' ' . $subDelegate->first_name . ' ' . $subDelegate->middle_name . ' ' . $subDelegate->last_name;


                            // $printUrl = route('public-print-badge', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'delegateId' => $subDelegate->id, 'delegateType' => 'sub']);

                            array_push($confirmedDelegates, [
                                'transactionId' => $finalTransactionId,
                                'id' => $subDelegate->id,
                                'delegateType' => "sub",
                                'fullName' => trim($fullName),
                                'salutation' => $subDelegate->salutation,
                                'fname' => $subDelegate->first_name,
                                'mname' => $subDelegate->middle_name,
                                'lname' => $subDelegate->last_name,
                                'jobTitle' => trim($subDelegate->job_title),
                                'companyName' => trim($companyName),
                                'badgeType' => $subDelegate->badge_type,

                                'frontText' => $registrationType->badge_footer_front_name,
                                'frontTextColor' => $registrationType->badge_footer_front_text_color,
                                'frontTextBGColor' => $registrationType->badge_footer_front_bg_color,
                                'seatNumber' => $subDelegate->seat_number,
                                // 'printUrl' => $printUrl,
                            ]);
                        }
                    }
                }
            }
        }

        return $confirmedDelegates;
    }
}
