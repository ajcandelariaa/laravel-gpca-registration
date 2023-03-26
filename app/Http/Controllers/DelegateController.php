<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdditionalDelegate;
use App\Models\Event;
use App\Models\MainDelegate;
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
                $eventCategory = Event::where('id', $mainDelegate->event_id)->value('category');

                array_push($finalListsOfDelegates, [
                    'delegateId' => $mainDelegate->id,
                    'delegateType' => "main",
                    'delegateEventCategory' => $eventCategory,
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
                            'delegateEventCategory' => $eventCategory,
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

            return view('admin.event.detail.delegate', [
                "pageTitle" => "Event Delegates",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
                "finalListsOfDelegates" => $finalListsOfDelegates,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }
}
