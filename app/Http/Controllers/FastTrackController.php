<?php

namespace App\Http\Controllers;

use App\Models\AdditionalDelegate;
use App\Models\DelegateDetailsUpdateLog;
use App\Models\Event;
use App\Models\EventRegistrationType;
use App\Models\MainDelegate;
use App\Models\PrintedBadge;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FastTrackController extends Controller
{
    public function getFastTrackDetails($eventCategory, $eventYear)
    {
        $eventCategory = $eventCategory;
        $eventYear = $eventYear;

        $event = Event::where('category', $eventCategory)->where('year', $eventYear)->first();
        if ($event != null) {
            return response()->json([
                'confirmedAttendees' => $this->getConfirmedDelegates($event->id, $eventCategory, $eventYear),
            ]);
        } else {
            return response()->json([
                'message' => "Event not found",
            ]);
        }
    }

    public function getConfirmedDelegates($eventId, $eventCategory, $eventYear)
    {
        $confirmedDelegates = array();
        $mainDelegates = MainDelegate::where('event_id', $eventId)->get();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }


        foreach ($mainDelegates as $mainDelegate) {
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

                    $fullName = $delegateSalutation;

                    if (!empty($mainDelegate->first_name)) {
                        $fullName .= ' ' . $mainDelegate->first_name;
                    }

                    if (!empty($mainDelegate->middle_name)) {
                        $fullName .= ' ' . $mainDelegate->middle_name;
                    }

                    if (!empty($mainDelegate->last_name)) {
                        $fullName .= ' ' . $mainDelegate->last_name;
                    }

                    if($eventCategory == "PC"){
                        $finalFrontTextBGColor = "#ffffff";
                        $finalFontTextColor = "#0A6A56";
                    } else if ($eventCategory == "SCC"){
                        $finalFrontTextBGColor = "#ffffff";
                        $finalFontTextColor = "#00375D";
                    } else {
                        $finalFrontTextBGColor = $registrationType->badge_footer_front_bg_color;
                        $finalFontTextColor = $registrationType->badge_footer_front_text_color;
                    }

                    array_push($confirmedDelegates, [
                        'transactionId' => $finalTransactionId,
                        'id' => $mainDelegate->id,
                        'delegateType' => "main",
                        'fullName' => trim($fullName),
                        'salutation' => (empty($mainDelegate->salutation)) ? null : $mainDelegate->salutation,
                        'fname' => $mainDelegate->first_name,
                        'mname' => (empty($mainDelegate->middle_name)) ? null : $mainDelegate->middle_name,
                        'lname' => $mainDelegate->last_name,
                        'jobTitle' => trim($mainDelegate->job_title),
                        'companyName' => trim($companyName),
                        'badgeType' => $mainDelegate->badge_type,

                        'frontText' => $registrationType->badge_footer_front_name,
                        'frontTextColor' => $finalFontTextColor,
                        'frontTextBGColor' => $finalFrontTextBGColor,
                        'seatNumber' => $mainDelegate->seat_number ? $mainDelegate->seat_number : "N/A",
                    ]);
                }
            }

            if ($mainDelegate->registration_status == "confirmed") {
                $subDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegate->id)->get();
                if (!$subDelegates->isEmpty()) {
                    foreach ($subDelegates as $subDelegate) {

                        if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {

                            $registrationType = EventRegistrationType::where('event_id', $eventId)->where('event_category', $eventCategory)->where('registration_type', $subDelegate->badge_type)->first();

                            $transactionId = Transaction::where('delegate_id', $subDelegate->id)->where('delegate_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                            $mainDelegate = MainDelegate::select('alternative_company_name', 'company_name')->where('id', $subDelegate->main_delegate_id)->first();

                            if ($mainDelegate->alternative_company_name != null) {
                                $companyName = $mainDelegate->alternative_company_name;
                            } else {
                                $companyName = $mainDelegate->company_name;
                            }

                            if ($subDelegate->salutation == "Dr." || $subDelegate->salutation == "Prof.") {
                                $delegateSalutation = $subDelegate->salutation;
                            } else {
                                $delegateSalutation = null;
                            }

                            $fullName = $delegateSalutation;

                            if (!empty($subDelegate->first_name)) {
                                $fullName .= ' ' . $subDelegate->first_name;
                            }

                            if (!empty($subDelegate->middle_name)) {
                                $fullName .= ' ' . $subDelegate->middle_name;
                            }

                            if (!empty($subDelegate->last_name)) {
                                $fullName .= ' ' . $subDelegate->last_name;
                            }


                            if($eventCategory == "PC"){
                                $finalFrontTextBGColor = "#ffffff";
                                $finalFontTextColor = "#0A6A56";
                            } else if ($eventCategory == "SCC"){
                                $finalFrontTextBGColor = "#ffffff";
                                $finalFontTextColor = "#00375D";
                            } else {
                                $finalFrontTextBGColor = $registrationType->badge_footer_front_bg_color;
                                $finalFontTextColor = $registrationType->badge_footer_front_text_color;
                            }

                            array_push($confirmedDelegates, [
                                'transactionId' => $finalTransactionId,
                                'id' => $subDelegate->id,
                                'delegateType' => "sub",
                                'fullName' => trim($fullName),
                                'salutation' => (empty($subDelegate->salutation)) ? null : $subDelegate->salutation,
                                'fname' => $subDelegate->first_name,
                                'mname' => (empty($subDelegate->middle_name)) ? null : $subDelegate->middle_name,
                                'lname' => $subDelegate->last_name,
                                'jobTitle' => trim($subDelegate->job_title),
                                'companyName' => trim($companyName),
                                'badgeType' => $subDelegate->badge_type,

                                'frontText' => $registrationType->badge_footer_front_name,
                                'frontTextColor' => $finalFontTextColor,
                                'frontTextBGColor' => $finalFrontTextBGColor,
                                'seatNumber' => $subDelegate->seat_number ? $subDelegate->seat_number : "N/A",
                            ]);
                        }
                    }
                }
            }
        }
        return $confirmedDelegates;
    }


    public function printBadge(Request $request, $eventCategory, $eventYear)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'delegateId' => 'required|numeric',
            'delegateType' => 'required|string',
            'pcName' => 'required|string',
            'pcNumber' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->code == env("API_CODE")) {
            $eventId = Event::where('category', $eventCategory)->where('year', $eventYear)->value('id');

            if ($eventId != null) {
                $delegate = null;

                if ($request->delegateType == "main") {
                    $delegate = MainDelegate::find($request->delegateId);
                } else {
                    $delegate = AdditionalDelegate::find($request->delegateId);
                }

                if ($delegate != null) {
                    PrintedBadge::create([
                        'event_id' => $eventId,
                        'event_category' => $eventCategory,
                        'delegate_id' => $request->delegateId,
                        'delegate_type' => $request->delegateType,
                        'printed_by_name' => $request->pcName,
                        'printed_by_pc_number' => $request->pcNumber,
                        'printed_date_time' => Carbon::now(),
                    ]);

                    return response()->json([
                        'message' => 'Success',
                    ], 200);
                } else {
                    return response()->json([
                        'message' => "Attendee doesn't exist",
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => "Event doesn't exist",
                ], 404);
            }
        } else {
            return response()->json([
                'message' => "Unauthorized!",
            ], 401);
        }
    }

    public function updateDetails(Request $request, $eventCategory, $eventYear)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'delegateId' => 'required|numeric',
            'delegateType' => 'required|string',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'jobTitle' => 'required|string',
            'pcName' => 'required|string',
            'pcNumber' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->code == env("API_CODE")) {
            $eventId = Event::where('category', $eventCategory)->where('year', $eventYear)->value('id');
            if ($eventId != null) {
                $delegateType = $request->delegateType;
                $delegateId = $request->delegateId;

                $delegate = ($delegateType == "main") ? MainDelegate::find($delegateId) : AdditionalDelegate::find($delegateId);

                if ($delegate != null) {
                    $delegate->update([
                        'salutation' => $request->salutation,
                        'first_name' => $request->firstName,
                        'middle_name' => $request->middleName,
                        'last_name' => $request->lastName,
                        'job_title' => $request->jobTitle,
                    ]);

                    DelegateDetailsUpdateLog::create([
                        'event_id' => $eventId,
                        'event_category' => $eventCategory,
                        'delegate_id' => $delegateId,
                        'delegate_type' => $delegateType,
                        'updated_by_name' => $request->pcName,
                        'updated_by_pc_number' => $request->pcNumber,
                        'description' => $request->description,
                        'updated_date_time' => Carbon::now(),
                    ]);

                    return response()->json([
                        'message' => 'Success',
                    ], 200);
                } else {
                    return response()->json([
                        'message' => "Attendee doesn't exist",
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => "Event doesn't exist",
                ], 404);
            }
        } else {
            return response()->json([
                'message' => "Unauthorized!",
            ], 401);
        }
    }
}
