<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdditionalDelegate;
use App\Models\Event;
use App\Models\MainDelegate;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DelegateController extends Controller
{
    // RENDER VIEWS
    public function manageDelegateView()
    {
        $finalListsOfDelegates = array();
        $mainDelegates = MainDelegate::where('payment_status', '!=', 'unpaid')->get();

        if (!$mainDelegates->isEmpty()) {
            foreach ($mainDelegates as $mainDelegate) {
                $event = Event::where('id', $mainDelegate->event_id)->first();

                array_push($finalListsOfDelegates, [
                    'eventCategory' => $event->category,
                    'eventId' => $event->id,
                    'delegateId' => $mainDelegate->id,
                    'delegateType' => "main",
                    'delegateEventCategory' => $event->category,
                    'delegateCompany' => $mainDelegate->company_name,
                    'delegateJobTitle' => $mainDelegate->job_title,
                    'delegateName' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                    'delegateEmailAddress' => $mainDelegate->email_address,
                    'delegateBadgeType' => $mainDelegate->badge_type,
                ]);

                $subDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegate->id)->get();

                if (!$subDelegates->isEmpty()) {
                    foreach ($subDelegates as $subDelegate) {
                        array_push($finalListsOfDelegates, [
                            'eventCategory' => $event->category,
                            'eventId' => $event->id,
                            'delegateId' => $subDelegate->id,
                            'delegateType' => "sub",
                            'delegateEventCategory' => $event->category,
                            'delegateCompany' => $mainDelegate->company_name,
                            'delegateJobTitle' => $subDelegate->job_title,
                            'delegateName' => $subDelegate->salutation . " " . $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name,
                            'delegateEmailAddress' => $subDelegate->email_address,
                            'delegateBadgeType' => $subDelegate->badge_type,
                        ]);
                    }
                }
            }
        }

        return view('admin.delegates.delegate', [
            'pageTitle' => 'Manage Delegate',
            "finalListsOfDelegates" => $finalListsOfDelegates,
        ]);
    }


    public function eventDelegateView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $finalListsOfDelegates = array();

            $mainDelegates = MainDelegate::where('event_id', $eventId)->where('payment_status', '!=', 'unpaid')->get();

            if (!$mainDelegates->isEmpty()) {
                foreach ($mainDelegates as $mainDelegate) {

                    array_push($finalListsOfDelegates, [
                        'eventCategory' => $eventCategory,
                        'eventId' => $eventId,
                        'delegateId' => $mainDelegate->id,
                        'delegateType' => "main",
                        'delegateCompany' => $mainDelegate->company_name,
                        'delegateJobTitle' => $mainDelegate->job_title,
                        'delegateName' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                        'delegateEmailAddress' => $mainDelegate->email_address,
                        'delegateBadgeType' => $mainDelegate->badge_type,
                    ]);


                    $subDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegate->id)->get();

                    if (!$subDelegates->isEmpty()) {
                        foreach ($subDelegates as $subDelegate) {
                            array_push($finalListsOfDelegates, [
                                'delegateId' => $subDelegate->id,
                                'delegateType' => "sub",
                                'delegateCompany' => $mainDelegate->company_name,
                                'delegateJobTitle' => $subDelegate->job_title,
                                'delegateName' => $subDelegate->salutation . " " . $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name,
                                'delegateEmailAddress' => $subDelegate->email_address,
                                'delegateBadgeType' => $subDelegate->badge_type,
                            ]);
                        }
                    }
                }
            }

            return view('admin.event.detail.delegates.delegates', [
                "pageTitle" => "Event Delegates",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
                "finalListsOfDelegates" => $finalListsOfDelegates,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function delegateDetailView($eventCategory, $eventId, $delegateType, $delegateId){
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $finalDelegate = array();
            $tempDelegate = array();

            if($delegateType == "main"){
                $tempDelegate = MainDelegate::where('id', $delegateId)->first();
            } else {
                $tempDelegate = AdditionalDelegate::where('id', $delegateId)->first();
            }

            if($tempDelegate != null){
                if($delegateType  == "main"){
                    $finalDelegate = [
                        'eventCategory' => $eventCategory,
                        'eventId' => $eventId,
                        'delegateType' => $delegateType,
                        'delegateId' => $delegateId,

                        'salutation' => $tempDelegate->salutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'email_address' => $tempDelegate->email_address,
                        'mobile_number' => $tempDelegate->mobile_number,
                        'nationality' => $tempDelegate->nationality,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,

                        'pass_type' => $tempDelegate->pass_type,
                        'companyName' => $tempDelegate->company_name,
                        'company_sector' => $tempDelegate->company_sector,
                        'company_address' => $tempDelegate->company_address,
                        'company_country' => $tempDelegate->company_country,
                        'company_city' => $tempDelegate->company_city,
                        'company_telephone_number' => $tempDelegate->company_telephone_number,
                        'company_mobile_number' => $tempDelegate->company_mobile_number,
                    ];
                } else {
                    $mainDelegateInfo = MainDelegate::where('id', $tempDelegate->main_delegate_id)->first();

                    $finalDelegate = [
                        'delegateType' => $delegateType,
                        'delegateId' => $delegateId,

                        'salutation' => $tempDelegate->salutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'email_address' => $tempDelegate->email_address,
                        'mobile_number' => $tempDelegate->mobile_number,
                        'nationality' => $tempDelegate->nationality,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,

                        'pass_type' => $mainDelegateInfo->pass_type,
                        'companyName' => $mainDelegateInfo->company_name,
                        'company_sector' => $mainDelegateInfo->company_sector,
                        'company_address' => $mainDelegateInfo->company_address,
                        'company_country' => $mainDelegateInfo->company_country,
                        'company_city' => $mainDelegateInfo->company_city,
                        'company_telephone_number' => $mainDelegateInfo->company_telephone_number,
                        'company_mobile_number' => $mainDelegateInfo->company_mobile_number,
                    ];
                }
    
                return view('admin.event.detail.delegates.delegates_detail', [
                    "pageTitle" => "Event Delegates",
                    "eventCategory" => $eventCategory,
                    "eventId" => $eventId,
                    "finalDelegate" => $finalDelegate,
                ]);
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function delegateDetailPrintBadge($eventCategory, $eventId, $delegateType, $delegateId){
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $finalDelegate = array();
            $tempDelegate = array();

            if($delegateType == "main"){
                $tempDelegate = MainDelegate::where('id', $delegateId)->first();
            } else {
                $tempDelegate = AdditionalDelegate::where('id', $delegateId)->first();
            }

            if($tempDelegate != null){
                if($delegateType  == "main"){
                    $finalDelegate = [
                        'salutation' => $tempDelegate->salutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,
                        'companyName' => $tempDelegate->company_name,
                    ];
                } else {
                    $mainDelegateInfo = MainDelegate::where('id', $tempDelegate->main_delegate_id)->first();
                    $finalDelegate = [
                        'salutation' => $tempDelegate->salutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,
                        'companyName' => $mainDelegateInfo->company_name,
                    ];
                }
                $pdf = Pdf::loadView('admin.event.detail.delegates.delegate_badge', $finalDelegate);
                return $pdf->stream('badge.pdf');
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }
}
