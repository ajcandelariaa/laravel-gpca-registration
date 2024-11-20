<?php

namespace App\Http\Controllers;

use App\Enums\AccessTypes;
use App\Models\AdditionalDelegate;
use App\Models\DelegateDetailsUpdateLog;
use App\Models\Event;
use App\Models\MainDelegate;
use App\Models\PrintedBadge;
use App\Models\ScannedDelegate;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $mainDelegates = MainDelegate::with(['additionalDelegates', 'transaction', 'printedBadge', 'printedBadges'])->where('event_id', $eventId)->limit(20)->get();

        $eventCode = config('app.eventCategories')[$eventCategory];

        foreach ($mainDelegates as $mainDelegate) {
            if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                if ($mainDelegate->registration_status == "confirmed") {

                    $transactionId = $mainDelegate->transaction->id;
                    $lastDigit = 1000 + intval($transactionId);
                    $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                    $companyName = $mainDelegate->alternative_company_name ?? $mainDelegate->company_name;

                    $delegateSalutation = null;
                    if ($mainDelegate->salutation == "Dr." || $mainDelegate->salutation == "Prof.") {
                        $delegateSalutation = $mainDelegate->salutation;
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

                    $fullName = $this->formatFullName($fullName);

                    $finalFrontTextBGColor = "#ffffff";
                    $finalFontTextColor = "#000000";

                    $delegatePrinted = $mainDelegate->printedBadge ? "Yes" : "No";

                    $delegateBadgeCollected = "No";
                    if($delegatePrinted == "Yes"){
                        $delegateBadgeCollected = $mainDelegate->printedBadge->collected ? "Yes" : "No";
                    }

                    array_push($confirmedDelegates, [
                        'transactionId' => $finalTransactionId,
                        'id' => $mainDelegate->id,
                        'delegateType' => "main",
                        'fullName' => $fullName,
                        'salutation' => (empty($mainDelegate->salutation)) ? null : $mainDelegate->salutation,
                        'fname' => $mainDelegate->first_name,
                        'mname' => (empty($mainDelegate->middle_name)) ? null : $mainDelegate->middle_name,
                        'lname' => $mainDelegate->last_name,
                        'jobTitle' => trim($mainDelegate->job_title),
                        'companyName' => trim($companyName),
                        'badgeType' => Str::upper($mainDelegate->badge_type),

                        'frontText' => Str::upper($mainDelegate->badge_type),
                        'frontTextColor' => $finalFontTextColor,
                        'frontTextBGColor' => $finalFrontTextBGColor,
                        'seatNumber' => $mainDelegate->seat_number ? $mainDelegate->seat_number : "N/A",

                        'isCollected' => $delegateBadgeCollected,
                        'isPrinted' => $delegatePrinted,
                        'printedCount' => count($mainDelegate->printedBadges),
                        'paidDateTime' => $mainDelegate->paid_date_time, //"Y-m-d H:i:s"
                        'isSelectedForPrint' => true,
                    ]);
                }
            }

            if ($mainDelegate->registration_status == "confirmed") {
                $subDelegates = $mainDelegate->additionalDelegates;
                if (!$subDelegates->isEmpty()) {
                    foreach ($subDelegates as $subDelegate) {

                        if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {

                            $transactionId = $subDelegate->transaction->id;
                            $lastDigit = 1000 + intval($transactionId);
                            $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                            $companyName = $mainDelegate->alternative_company_name ?? $mainDelegate->company_name;

                            $delegateSalutation = null;
                            if ($subDelegate->salutation == "Dr." || $subDelegate->salutation == "Prof.") {
                                $delegateSalutation = $subDelegate->salutation;
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

                            $fullName = $this->formatFullName($fullName);

                            $finalFrontTextBGColor = "#ffffff";
                            $finalFontTextColor = "#000000";

                            $delegatePrinted = $subDelegate->printedBadge ? "Yes" : "No";

                            $delegateBadgeCollected = "No";
                            if($delegatePrinted == "Yes"){
                                $delegateBadgeCollected = $subDelegate->printedBadge->collected ? "Yes" : "No";
                            }

                            array_push($confirmedDelegates, [
                                'transactionId' => $finalTransactionId,
                                'id' => $subDelegate->id,
                                'delegateType' => "sub",
                                'fullName' => $fullName,
                                'salutation' => (empty($subDelegate->salutation)) ? null : $subDelegate->salutation,
                                'fname' => $subDelegate->first_name,
                                'mname' => (empty($subDelegate->middle_name)) ? null : $subDelegate->middle_name,
                                'lname' => $subDelegate->last_name,
                                'jobTitle' => trim($subDelegate->job_title),
                                'companyName' => trim($companyName),
                                'badgeType' => Str::upper($subDelegate->badge_type),

                                'frontText' => Str::upper($mainDelegate->badge_type),
                                'frontTextColor' => $finalFontTextColor,
                                'frontTextBGColor' => $finalFrontTextBGColor,
                                'seatNumber' => $subDelegate->seat_number ? $subDelegate->seat_number : "N/A",

                                'isCollected' => $delegateBadgeCollected,
                                'isPrinted' => $delegatePrinted,
                                'printedCount' => count($subDelegate->printedBadges),
                                'paidDateTime' => $mainDelegate->paid_date_time,
                                'isSelectedForPrint' => true,
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

    public function toggleBadgeCollected(Request $request, $eventCategory, $eventYear)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'delegateId' => 'required|numeric',
            'delegateType' => 'required|string',
            'isBeingCollected' => 'required|string',
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
                $isBeingCollected = $request->isBeingCollected;
                $collectedBy = $request->collectedBy;

                $printedDelegate = PrintedBadge::where('event_id', $eventId)->where('delegate_id', $delegateId)->where('delegate_type', $delegateType)->first();

                if ($printedDelegate != null) {
                    if($isBeingCollected == "Yes"){
                        PrintedBadge::where('event_id', $eventId)->where('delegate_id', $delegateId)->where('delegate_type', $delegateType)
                        ->update([
                            'collected' => true,
                            'collected_by' => $collectedBy,
                            'collected_marked_datetime' => Carbon::now(),
                        ]);
                    } else {
                        PrintedBadge::where('event_id', $eventId)->where('delegate_id', $delegateId)->where('delegate_type', $delegateType)
                        ->update([
                            'collected' => null,
                            'collected_by' => null,
                            'collected_marked_datetime' => null,
                        ]);
                    }
                    
                    return response()->json([
                        'message' => 'Success',
                    ], 200);
                } else {
                    return response()->json([
                        'message' => "Badge is not yet printed",
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
                        'seat_number' => $request->seatNumber,
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

    public function badgeScan(Request $request, $eventCategory, $eventYear)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'delegateId' => 'required|numeric',
            'delegateType' => 'required|string',
            'location' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'scannedAttendee' => null,
                'statusCode' => 422,
                'message' => 'Validation failed',
            ], 422);
        }

        if ($request->code == env("API_CODE")) {
            $eventId = Event::where('category', $eventCategory)->where('year', $eventYear)->value('id');
            if ($eventId != null) {
                $delegateType = $request->delegateType;
                $delegateId = $request->delegateId;
                $location = $request->location;

                $delegate = ($delegateType == "main") ? MainDelegate::find($delegateId) : AdditionalDelegate::find($delegateId);

                if ($delegate != null) {
                    $name = null;
                    $jobTitle = null;
                    $companyName = null;
                    $badgeType = null;
                    $accessType = null;
                    $transactionId = null;

                    foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                        if ($eventCategory == $eventCategoryC) {
                            $eventCode = $code;
                        }
                    }

                    ScannedDelegate::create([
                        'event_id' => $eventId,
                        'event_category' => $eventCategory,
                        'delegate_id' => $delegateId,
                        'delegate_type' => $delegateType,
                        'scanner_location' => $location,
                        'scanned_date_time' => Carbon::now('Asia/Riyadh'),
                    ]);

                    if ($delegateType == "main") {
                        $delegateDetails = MainDelegate::where('event_id', $eventId)->where('id', $delegateId)->first();

                        if ($delegateDetails->alternative_company_name != null) {
                            $companyName = $delegateDetails->alternative_company_name;
                        } else {
                            $companyName = $delegateDetails->company_name;
                        }

                        if ($delegateDetails->salutation == "Dr." || $delegateDetails->salutation == "Prof.") {
                            $delegateSalutation = $delegateDetails->salutation;
                        } else {
                            $delegateSalutation = null;
                        }

                        $name = $delegateSalutation . ' ' . $delegateDetails->first_name . ' ' . $delegateDetails->middle_name . ' ' . $delegateDetails->last_name;
                        $jobTitle = $delegateDetails->job_title;
                        $companyName = $companyName;
                        $badgeType = $delegateDetails->badge_type;
                        $accessType = $delegateDetails->access_type;
                    } else {
                        $delegateDetails = AdditionalDelegate::where('id', $delegateId)->first();
                        $mainDelegate = MainDelegate::where('event_id', $eventId)->where('id', $delegateDetails->main_delegate_id)->first();

                        if ($mainDelegate->alternative_company_name != null) {
                            $companyName = $mainDelegate->alternative_company_name;
                        } else {
                            $companyName = $mainDelegate->company_name;
                        }


                        if ($delegateDetails->salutation == "Dr." || $delegateDetails->salutation == "Prof.") {
                            $delegateSalutation = $delegateDetails->salutation;
                        } else {
                            $delegateSalutation = null;
                        }

                        $name = $delegateSalutation . ' ' . $delegateDetails->first_name . ' ' . $delegateDetails->middle_name . ' ' . $delegateDetails->last_name;
                        $jobTitle = $delegateDetails->job_title;
                        $companyName = $companyName;
                        $badgeType = $delegateDetails->badge_type;
                        $accessType = $delegateDetails->access_type;
                    }

                    if ($accessType == AccessTypes::CONFERENCE_ONLY->value) {
                        $finalAccessType = "CO";
                    } else if ($accessType == AccessTypes::WORKSHOP_ONLY->value) {
                        $finalAccessType = "WO";
                    } else {
                        $finalAccessType = "FE";
                    }

                    $transactionId = Transaction::where('event_id', $eventId)->where('delegate_id', $delegateId)->where('delegate_type', $delegateType)->value('id');
                    $lastDigit = 1000 + intval($transactionId);
                    $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                    $data = [
                        'accessType' => $finalAccessType,
                        'transactionId' => $finalTransactionId,
                        'fullName' => $name,
                        'jobTitle' => $jobTitle,
                        'companyName' => $companyName,
                        'badgeType' => $badgeType,
                    ];

                    return response()->json([
                        'scannedAttendee' => $data,
                        'statusCode' => 200,
                        'message' => "Success!",
                    ], 200);
                } else {
                    return response()->json([
                        'scannedAttendee' => null,
                        'statusCode' => 404,
                        'message' => "Attendee doesn't exist",
                    ], 404);
                }
            } else {
                return response()->json([
                    'scannedAttendee' => null,
                    'statusCode' => 404,
                    'message' => "Event doesn't exist",
                ], 404);
            }
        } else {
            return response()->json([
                'scannedAttendee' => null,
                'statusCode' => 401,
                'message' => 'Unauthorized!',
            ], 401);
        }
    }

    function formatFullName($name)
    {
        return preg_replace('/\s+/', ' ', trim($name));
    }
}
