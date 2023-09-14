<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationCardDeclined;
use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Models\AdditionalDelegate;
use App\Models\AdditionalSpouse;
use App\Models\AdditionalVisitor;
use App\Models\PromoCode;
use App\Models\Event;
use App\Models\MainDelegate;
use App\Models\MainSpouse;
use App\Models\MainVisitor;
use App\Models\PrintedBadge;
use App\Models\RccAwardsAdditionalParticipant;
use App\Models\RccAwardsDocument;
use App\Models\RccAwardsMainParticipant;
use App\Models\RccAwardsParticipantTransaction;
use App\Models\SpouseTransaction;
use App\Models\Transaction;
use App\Models\VisitorTransaction;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session as Session;
use Illuminate\Support\Facades\Storage;
use NumberFormatter;
use Illuminate\Support\Str;


class RegistrationController extends Controller
{

    // =========================================================
    //                       RENDER VIEWS
    // =========================================================

    public function homepageView()
    {
        $events = Event::orderBy('event_start_date', 'asc')->get();
        $finalUpcomingEvents = array();
        $finalPastEvents = array();

        if (!$events->isEmpty()) {
            foreach ($events as $event) {
                $eventLink = env('APP_URL') . '/register/' . $event->year . '/' . $event->category . '/' . $event->id;
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d M Y') . ' - ' . Carbon::parse($event->event_end_date)->format('d M Y');

                $eventEndDate = Carbon::parse($event->event_end_date);

                if (Carbon::now()->lt($eventEndDate->addDay()) && $event->active) {
                    array_push($finalUpcomingEvents, [
                        'eventLogo' => $event->logo,
                        'eventName' => $event->name,
                        'eventCategory' => $event->category,
                        'eventDate' => $eventFormattedDate,
                        'eventLocation' => $event->location,
                        'eventDescription' => $event->description,
                        'eventLink' => $eventLink,
                    ]);
                } else {
                    array_push($finalPastEvents, [
                        'eventLogo' => $event->logo,
                        'eventName' => $event->name,
                        'eventCategory' => $event->category,
                        'eventDate' => $eventFormattedDate,
                        'eventLocation' => $event->location,
                        'eventDescription' => $event->description,
                    ]);
                }
            }
        }
        return view('home.homepage', [
            'upcomingEvents' => $finalUpcomingEvents,
            'pastEvents' => $finalPastEvents,
        ]);
    }

    public function registrationFailedView($eventYear, $eventCategory, $eventId, $mainDelegateId)
    {
        if (Event::where('year', $eventYear)->where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $event = Event::where('id', $eventId)->first();

            if ($eventCategory == "AFS") {
                $finalData = $this->registrationFailedViewSpouse($eventCategory, $eventId, $mainDelegateId);
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');
            } else if ($eventCategory == "AFV") {
                $finalData = $this->registrationFailedViewVisitor($eventCategory, $eventId, $mainDelegateId);
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');
            } else if ($eventCategory == "RCCA") {
                $finalData = $this->registrationFailedViewRccAwards($eventCategory, $eventId, $mainDelegateId);
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('j F Y');
            } else {
                $finalData = $this->registrationFailedViewEvents($eventCategory, $eventId, $mainDelegateId);
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');
            }

            return view('registration.success-messages.registration_failed_message', [
                'pageTitle' => "Registration Failed",
                'event' => $event,
                'eventFormattedDate' =>  $eventFormattedDate,
                'invoiceLink' => $finalData['invoiceLink'],
                'bankDetails' => $finalData['bankDetails'],
                'paymentStatus' => $finalData['paymentStatus'],
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrationSuccessView($eventYear, $eventCategory, $eventId, $mainDelegateId)
    {
        if (Event::where('year', $eventYear)->where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $event = Event::where('id', $eventId)->first();

            if ($eventCategory == "AFS") {
                $finalData = $this->registrationSuccessViewSpouse($eventCategory, $eventId, $mainDelegateId);
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');
            } else if ($eventCategory == "AFV") {
                $finalData = $this->registrationSuccessViewVisitor($eventCategory, $eventId, $mainDelegateId);
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');
            } else if ($eventCategory == "RCCA") {
                $finalData = $this->registrationSuccessViewRccAwards($eventCategory, $eventId, $mainDelegateId);
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('j F Y');
            } else {
                $finalData = $this->registrationSuccessViewEvents($eventCategory, $eventId, $mainDelegateId);
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');
            }

            return view('registration.success-messages.registration_success_message', [
                'pageTitle' => "Registration Success",
                'event' => $event,
                'eventFormattedDate' =>  $eventFormattedDate,
                'invoiceLink' => $finalData['invoiceLink'],
                'bankDetails' => $finalData['bankDetails'],
                'paymentStatus' => $finalData['paymentStatus'],
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrationLoadingView($eventYear, $eventCategory, $eventId, $mainDelegateId, $status)
    {
        $redirectLink = env('APP_URL') . '/register/' . $eventYear . '/' . $eventCategory . '/' . $eventId . '/' . $mainDelegateId . '/' . $status;
        return view('registration.registration_loading', [
            'redirectLink' => $redirectLink,
        ]);
    }

    public function registrationOTPView($eventYear, $eventCategory, $eventId)
    {
        if (Event::where('year', $eventYear)->where('category', $eventCategory)->where('id', $eventId)->exists()) {
            if (Session::has('sessionId') && Session::has('paymentStatus') && Session::has('htmlOTP') && Session::has('orderId')) {
                return view('registration.registration_otp', [
                    'htmlCode' => Session::get('htmlOTP'),
                ]);
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrationView($eventYear, $eventCategory, $eventId)
    {
        if (Event::where('year', $eventYear)->where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $event = Event::where('year', $eventYear)->where('category', $eventCategory)->where('id', $eventId)->first();

            $eventEndDate = Carbon::parse($event->event_end_date);

            if ($event->active) {
                if (Carbon::now()->lt($eventEndDate->addDay())) {
                    if ($event->category == "DAW") {
                        $mainDelegates = MainDelegate::where('event_id', $eventId)->get();
                        $totalConfirmedDelegates = 0;

                        if ($mainDelegates->isNotEmpty()) {
                            foreach ($mainDelegates as $mainDelegate) {
                                if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                                    if ($mainDelegate->registration_status == "confirmed") {
                                        $totalConfirmedDelegates++;
                                    }
                                }

                                $additionalDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegate->id)->get();
                                if ($additionalDelegates->isNotEmpty()) {
                                    foreach ($additionalDelegates as $additionalDelegate) {
                                        if ($additionalDelegate->delegate_replaced_by_id == null && (!$additionalDelegate->delegate_refunded)) {
                                            if ($mainDelegate->registration_status == "confirmed") {
                                                $totalConfirmedDelegates++;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if ($totalConfirmedDelegates >= 50) {
                            abort(404, 'The URL is incorrect');
                        } else {
                            return view('registration.registration', [
                                'pageTitle' => $event->name . " - Registration",
                                'event' => $event,
                            ]);
                        }
                    } else {
                        return view('registration.registration', [
                            'pageTitle' => $event->name . " - Registration",
                            'event' => $event,
                        ]);
                    }
                } else {
                    abort(404, 'The URL is incorrect');
                }
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    // public function eventOnsiteRegistrationView($eventCategory, $eventId){
    //     if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
    //         $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();

    //         $eventEndDate = Carbon::parse($event->event_end_date);

    //         if (Carbon::now()->lt($eventEndDate->addDay())) {
    //             return view('registration.registration', [
    //                 'pageTitle' => $event->name . " - Onsite Registration",
    //                 'event' => $event,
    //             ]);
    //         } else {
    //             abort(404, 'The URL is incorrect');
    //         }
    //     } else {
    //         abort(404, 'The URL is incorrect');
    //     }
    // }

    public function eventRegistrantsView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            return view('admin.events.transactions.registrants', [
                "pageTitle" => "Transactions",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrantDetailView($eventCategory, $eventId, $registrantId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            if ($eventCategory == "AFS") {
                $finalData = $this->registrantDetailSpousesView($eventCategory, $eventId, $registrantId);
            } else if ($eventCategory == "AFV") {
                $finalData = $this->registrantDetailVisitorsView($eventCategory, $eventId, $registrantId);
            } else if ($eventCategory == "RCCA") {
                $finalData = $this->registrantDetailRCCAwardsView($eventCategory, $eventId, $registrantId);
            } else {
                $finalData = $this->registrantDetailEventsView($eventCategory, $eventId, $registrantId);
            }

            // dd($finalData);
            return view('admin.events.transactions.registrants_detail', [
                "pageTitle" => "Transaction Details",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
                "registrantId" => $registrantId,
                "finalData" => $finalData,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrantDetailEventsView($eventCategory, $eventId, $registrantId)
    {
        if (MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->exists()) {
            $finalData = array();

            $subDelegatesArray = array();
            $subDelegatesReplacementArray = array();
            $allDelegatesArray = array();
            $allDelegatesArrayTemp = array();

            $countFinalQuantity = 0;

            $eventYear = Event::where('id', $eventId)->value('year');
            $mainDelegate = MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->first();

            $promoCode = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $mainDelegate->pcode_used)->where('badge_type', $mainDelegate->badge_type)->first();

            if ($promoCode != null) {
                $mainDiscount = $promoCode->discount;
                $mainDiscountType = $promoCode->discount_type;
            } else {
                $mainDiscount = 0;
                $mainDiscountType = null;
            }

            foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                if ($eventCategory == $eventCategoryC) {
                    $eventCode = $code;
                }
            }

            $tempYear = Carbon::parse($mainDelegate->registered_date_time)->format('y');
            $transactionId = Transaction::where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->value('id');
            $lastDigit = 1000 + intval($transactionId);
            $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;


            if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                $countFinalQuantity++;
            }

            $subDelegates = AdditionalDelegate::where('main_delegate_id', $registrantId)->get();
            if (!$subDelegates->isEmpty()) {
                foreach ($subDelegates as $subDelegate) {
                    if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {
                        $countFinalQuantity++;
                    }

                    $subPromoCode = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $subDelegate->pcode_used)->where('badge_type', $subDelegate->badge_type)->first();

                    if ($subPromoCode != null) {
                        $subDiscount = $subPromoCode->discount;
                        $subDiscountType = $subPromoCode->discount_type;
                    } else {
                        $subDiscount = 0;
                        $subDiscountType = null;
                    }


                    foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                        if ($eventCategory == $eventCategoryC) {
                            $eventCode = $code;
                        }
                    }

                    if ($subDelegate->delegate_replaced_from_id != null) {
                        array_push($subDelegatesReplacementArray, [
                            'subDelegateId' => $subDelegate->id,
                            'name' => $subDelegate->salutation . " " . $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name,
                            'salutation' => $subDelegate->salutation,
                            'first_name' => $subDelegate->first_name,
                            'middle_name' => $subDelegate->middle_name,
                            'last_name' => $subDelegate->last_name,
                            'email_address' => $subDelegate->email_address,
                            'mobile_number' => $subDelegate->mobile_number,
                            'nationality' => $subDelegate->nationality,
                            'job_title' => $subDelegate->job_title,
                            'badge_type' => $subDelegate->badge_type,
                            'pcode_used' => $subDelegate->pcode_used,
                            'discount' => $subDiscount,
                            'discount_type' => $subDiscountType,

                            'delegate_cancelled' => $subDelegate->delegate_cancelled,
                            'delegate_replaced' => $subDelegate->delegate_replaced,
                            'delegate_refunded' => $subDelegate->delegate_refunded,

                            'delegate_replaced_type' => $subDelegate->delegate_replaced_type,
                            'delegate_original_from_id' => $subDelegate->delegate_original_from_id,
                            'delegate_replaced_from_id' => $subDelegate->delegate_replaced_from_id,
                            'delegate_replaced_by_id' => $subDelegate->delegate_replaced_by_id,

                            'delegate_cancelled_datetime' => $subDelegate->delegate_cancelled_datetime,
                            'delegate_refunded_datetime' => $subDelegate->delegate_refunded_datetime,
                            'delegate_replaced_datetime' => $subDelegate->delegate_replaced_datetime,
                        ]);
                    } else {
                        array_push($subDelegatesArray, [
                            'subDelegateId' => $subDelegate->id,
                            'name' => $subDelegate->salutation . " " . $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name,
                            'salutation' => $subDelegate->salutation,
                            'first_name' => $subDelegate->first_name,
                            'middle_name' => $subDelegate->middle_name,
                            'last_name' => $subDelegate->last_name,
                            'email_address' => $subDelegate->email_address,
                            'mobile_number' => $subDelegate->mobile_number,
                            'nationality' => $subDelegate->nationality,
                            'job_title' => $subDelegate->job_title,
                            'badge_type' => $subDelegate->badge_type,
                            'pcode_used' => $subDelegate->pcode_used,
                            'discount' => $subDiscount,
                            'discount_type' => $subDiscountType,

                            'delegate_cancelled' => $subDelegate->delegate_cancelled,
                            'delegate_replaced' => $subDelegate->delegate_replaced,
                            'delegate_refunded' => $subDelegate->delegate_refunded,

                            'delegate_replaced_type' => $subDelegate->delegate_replaced_type,
                            'delegate_original_from_id' => $subDelegate->delegate_original_from_id,
                            'delegate_replaced_from_id' => $subDelegate->delegate_replaced_from_id,
                            'delegate_replaced_by_id' => $subDelegate->delegate_replaced_by_id,

                            'delegate_cancelled_datetime' => $subDelegate->delegate_cancelled_datetime,
                            'delegate_refunded_datetime' => $subDelegate->delegate_refunded_datetime,
                            'delegate_replaced_datetime' => $subDelegate->delegate_replaced_datetime,
                        ]);
                    }
                }
            }


            $finalTransactionId = $eventYear . $eventCode . $lastDigit;

            array_push($allDelegatesArrayTemp, [
                'transactionId' => $finalTransactionId,
                'mainDelegateId' => $mainDelegate->id,
                'delegateId' => $mainDelegate->id,
                'delegateType' => "main",

                'name' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                'salutation' => $mainDelegate->salutation,
                'first_name' => $mainDelegate->first_name,
                'middle_name' => $mainDelegate->middle_name,
                'last_name' => $mainDelegate->last_name,
                'email_address' => $mainDelegate->email_address,
                'mobile_number' => $mainDelegate->mobile_number,
                'nationality' => $mainDelegate->nationality,
                'job_title' => $mainDelegate->job_title,
                'badge_type' => $mainDelegate->badge_type,
                'pcode_used' => $mainDelegate->pcode_used,
                'discount' => $mainDiscount,
                'discount_type' => $mainDiscountType,

                'is_replacement' => false,
                'delegate_cancelled' => $mainDelegate->delegate_cancelled,
                'delegate_replaced' => $mainDelegate->delegate_replaced,
                'delegate_refunded' => $mainDelegate->delegate_refunded,

                'delegate_replaced_type' => "main",
                'delegate_original_from_id' => $mainDelegate->id,
                'delegate_replaced_from_id' => null,
                'delegate_replaced_by_id' => $mainDelegate->delegate_replaced_by_id,

                'delegate_cancelled_datetime' => ($mainDelegate->delegate_cancelled_datetime == null) ? "N/A" : Carbon::parse($mainDelegate->delegate_cancelled_datetime)->format('M j, Y g:i A'),
                'delegate_refunded_datetime' => ($mainDelegate->delegate_refunded_datetime == null) ? "N/A" : Carbon::parse($mainDelegate->delegate_refunded_datetime)->format('M j, Y g:i A'),
                'delegate_replaced_datetime' => ($mainDelegate->delegate_replaced_datetime == null) ? "N/A" : Carbon::parse($mainDelegate->delegate_replaced_datetime)->format('M j, Y g:i A'),
            ]);

            if ($mainDelegate->delegate_replaced_by_id != null) {
                foreach ($subDelegatesReplacementArray as $subDelegateReplacement) {
                    if ($mainDelegate->id == $subDelegateReplacement['delegate_original_from_id'] && $subDelegateReplacement['delegate_replaced_type'] == "main") {

                        $transactionId = Transaction::where('delegate_id', $subDelegateReplacement['subDelegateId'])->where('delegate_type', "sub")->value('id');
                        $lastDigit = 1000 + intval($transactionId);
                        $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                        array_push($allDelegatesArrayTemp, [
                            'transactionId' => $finalTransactionId,
                            'mainDelegateId' => $mainDelegate->id,
                            'delegateId' => $subDelegateReplacement['subDelegateId'],
                            'delegateType' => "sub",

                            'name' => $subDelegateReplacement['salutation'] . " " . $subDelegateReplacement['first_name'] . " " . $subDelegateReplacement['middle_name'] . " " . $subDelegateReplacement['last_name'],
                            'salutation' => $subDelegateReplacement['salutation'],
                            'first_name' => $subDelegateReplacement['first_name'],
                            'middle_name' => $subDelegateReplacement['middle_name'],
                            'last_name' => $subDelegateReplacement['last_name'],
                            'email_address' => $subDelegateReplacement['email_address'],
                            'mobile_number' => $subDelegateReplacement['mobile_number'],
                            'nationality' => $subDelegateReplacement['nationality'],
                            'job_title' => $subDelegateReplacement['job_title'],
                            'badge_type' => $subDelegateReplacement['badge_type'],
                            'pcode_used' => $subDelegateReplacement['pcode_used'],
                            'discount' => $subDelegateReplacement['discount'],
                            'discount_type' => $subDelegateReplacement['discount_type'],

                            'is_replacement' => true,
                            'delegate_cancelled' => $subDelegateReplacement['delegate_cancelled'],
                            'delegate_replaced' => $subDelegateReplacement['delegate_replaced'],
                            'delegate_refunded' => $subDelegateReplacement['delegate_refunded'],

                            'delegate_replaced_type' => $subDelegateReplacement['delegate_replaced_type'],
                            'delegate_original_from_id' => $subDelegateReplacement['delegate_original_from_id'],
                            'delegate_replaced_from_id' => $subDelegateReplacement['delegate_replaced_from_id'],
                            'delegate_replaced_by_id' => $subDelegateReplacement['delegate_replaced_by_id'],

                            'delegate_cancelled_datetime' => ($subDelegateReplacement['delegate_cancelled_datetime'] == null) ? "N/A" : Carbon::parse($subDelegateReplacement['delegate_cancelled_datetime'])->format('M j, Y g:i A'),
                            'delegate_refunded_datetime' => ($subDelegateReplacement['delegate_refunded_datetime'] == null) ? "N/A" : Carbon::parse($subDelegateReplacement['delegate_refunded_datetime'])->format('M j, Y g:i A'),
                            'delegate_replaced_datetime' => ($subDelegateReplacement['delegate_replaced_datetime'] == null) ? "N/A" : Carbon::parse($subDelegateReplacement['delegate_replaced_datetime'])->format('M j, Y g:i A'),
                        ]);
                    }
                }
            }

            array_push($allDelegatesArray, $allDelegatesArrayTemp);

            $allDelegatesArrayTemp = array();

            foreach ($subDelegatesArray as $subDelegate) {
                $allDelegatesArrayTemp = array();

                $transactionId = Transaction::where('delegate_id', $subDelegate['subDelegateId'])->where('delegate_type', "sub")->value('id');
                $lastDigit = 1000 + intval($transactionId);
                $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                array_push($allDelegatesArrayTemp, [
                    'transactionId' => $finalTransactionId,
                    'mainDelegateId' => $mainDelegate->id,
                    'delegateId' => $subDelegate['subDelegateId'],
                    'delegateType' => "sub",

                    'name' => $subDelegate['salutation'] . " " . $subDelegate['first_name'] . " " . $subDelegate['middle_name'] . " " . $subDelegate['last_name'],
                    'salutation' => $subDelegate['salutation'],
                    'first_name' => $subDelegate['first_name'],
                    'middle_name' => $subDelegate['middle_name'],
                    'last_name' => $subDelegate['last_name'],
                    'email_address' => $subDelegate['email_address'],
                    'mobile_number' => $subDelegate['mobile_number'],
                    'nationality' => $subDelegate['nationality'],
                    'job_title' => $subDelegate['job_title'],
                    'badge_type' => $subDelegate['badge_type'],
                    'pcode_used' => $subDelegate['pcode_used'],
                    'discount' => $subDelegate['discount'],
                    'discount_type' => $subDelegate['discount_type'],

                    'is_replacement' => false,
                    'delegate_cancelled' => $subDelegate['delegate_cancelled'],
                    'delegate_replaced' => $subDelegate['delegate_replaced'],
                    'delegate_refunded' => $subDelegate['delegate_refunded'],

                    'delegate_replaced_type' => "sub",
                    'delegate_original_from_id' => $subDelegate['subDelegateId'],
                    'delegate_replaced_from_id' => null,
                    'delegate_replaced_by_id' => $subDelegate['delegate_replaced_by_id'],

                    'delegate_cancelled_datetime' => ($subDelegate['delegate_cancelled_datetime'] == null) ? "N/A" : Carbon::parse($subDelegate['delegate_cancelled_datetime'])->format('M j, Y g:i A'),
                    'delegate_refunded_datetime' => ($subDelegate['delegate_refunded_datetime'] == null) ? "N/A" : Carbon::parse($subDelegate['delegate_refunded_datetime'])->format('M j, Y g:i A'),
                    'delegate_replaced_datetime' => ($subDelegate['delegate_replaced_datetime'] == null) ? "N/A" : Carbon::parse($subDelegate['delegate_replaced_datetime'])->format('M j, Y g:i A'),
                ]);

                if ($subDelegate['delegate_replaced_by_id'] != null) {
                    foreach ($subDelegatesReplacementArray as $subDelegateReplacement) {
                        if ($subDelegate['subDelegateId']  == $subDelegateReplacement['delegate_original_from_id'] && $subDelegateReplacement['delegate_replaced_type'] == "sub") {

                            $transactionId = Transaction::where('delegate_id', $subDelegateReplacement['subDelegateId'])->where('delegate_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                            array_push($allDelegatesArrayTemp, [
                                'transactionId' => $finalTransactionId,
                                'mainDelegateId' => $mainDelegate->id,
                                'delegateId' => $subDelegateReplacement['subDelegateId'],
                                'delegateType' => "sub",

                                'name' => $subDelegateReplacement['salutation'] . " " . $subDelegateReplacement['first_name'] . " " . $subDelegateReplacement['middle_name'] . " " . $subDelegateReplacement['last_name'],
                                'salutation' => $subDelegateReplacement['salutation'],
                                'first_name' => $subDelegateReplacement['first_name'],
                                'middle_name' => $subDelegateReplacement['middle_name'],
                                'last_name' => $subDelegateReplacement['last_name'],
                                'email_address' => $subDelegateReplacement['email_address'],
                                'mobile_number' => $subDelegateReplacement['mobile_number'],
                                'nationality' => $subDelegateReplacement['nationality'],
                                'job_title' => $subDelegateReplacement['job_title'],
                                'badge_type' => $subDelegateReplacement['badge_type'],
                                'pcode_used' => $subDelegateReplacement['pcode_used'],
                                'discount' => $subDelegateReplacement['discount'],
                                'discount_type' => $subDelegateReplacement['discount_type'],

                                'is_replacement' => true,
                                'delegate_cancelled' => $subDelegateReplacement['delegate_cancelled'],
                                'delegate_replaced' => $subDelegateReplacement['delegate_replaced'],
                                'delegate_refunded' => $subDelegateReplacement['delegate_refunded'],

                                'delegate_replaced_type' => "sub",
                                'delegate_original_from_id' => $subDelegate['subDelegateId'],
                                'delegate_replaced_from_id' => $subDelegateReplacement['delegate_replaced_from_id'],
                                'delegate_replaced_by_id' => $subDelegateReplacement['delegate_replaced_by_id'],

                                'delegate_cancelled_datetime' => ($subDelegateReplacement['delegate_cancelled_datetime'] == null) ? "N/A" : Carbon::parse($subDelegateReplacement['delegate_cancelled_datetime'])->format('M j, Y g:i A'),
                                'delegate_refunded_datetime' => ($subDelegateReplacement['delegate_refunded_datetime'] == null) ? "N/A" : Carbon::parse($subDelegateReplacement['delegate_refunded_datetime'])->format('M j, Y g:i A'),
                                'delegate_replaced_datetime' => ($subDelegateReplacement['delegate_replaced_datetime'] == null) ? "N/A" : Carbon::parse($subDelegateReplacement['delegate_replaced_datetime'])->format('M j, Y g:i A'),
                            ]);
                        }
                    }
                }
                array_push($allDelegatesArray, $allDelegatesArrayTemp);
            }

            // dd($allDelegatesArray);

            $finalData = [
                'mainDelegateId' => $mainDelegate->id,
                'pass_type' => $mainDelegate->pass_type,
                'rate_type' => $mainDelegate->rate_type,
                'rate_type_string' => $mainDelegate->rate_type_string,
                'company_name' => $mainDelegate->company_name,
                'company_sector' => $mainDelegate->company_sector,
                'company_address' => $mainDelegate->company_address,
                'company_country' => $mainDelegate->company_country,
                'company_city' => $mainDelegate->company_city,
                'company_telephone_number' => $mainDelegate->company_telephone_number,
                'company_mobile_number' => $mainDelegate->company_mobile_number,
                'assistant_email_address' => $mainDelegate->assistant_email_address,
                'heard_where' => $mainDelegate->heard_where,
                'quantity' => $mainDelegate->quantity,
                'finalQuantity' => $countFinalQuantity,
                'pc_attending_nd' => $mainDelegate->pc_attending_nd,
                'scc_attending_nd' => $mainDelegate->scc_attending_nd,

                'mode_of_payment' => $mainDelegate->mode_of_payment,
                'registration_status' => "$mainDelegate->registration_status",
                'payment_status' => $mainDelegate->payment_status,
                'registered_date_time' => Carbon::parse($mainDelegate->registered_date_time)->format('M j, Y g:i A'),
                'paid_date_time' => ($mainDelegate->paid_date_time == null) ? "N/A" : Carbon::parse($mainDelegate->paid_date_time)->format('M j, Y g:i A'),

                'registration_method' => $mainDelegate->registration_method,
                'transaction_remarks' => $mainDelegate->transaction_remarks,

                'registration_confirmation_sent_count' => $mainDelegate->registration_confirmation_sent_count,
                'registration_confirmation_sent_datetime' => ($mainDelegate->registration_confirmation_sent_datetime == null) ? "N/A" : Carbon::parse($mainDelegate->registration_confirmation_sent_datetime)->format('M j, Y g:i A'),

                'invoiceNumber' => $invoiceNumber,
                'allDelegates' => $allDelegatesArray,

                'invoiceData' => $this->getInvoice($eventCategory, $eventId, $registrantId),
            ];

            return $finalData;
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrantDetailSpousesView($eventCategory, $eventId, $registrantId)
    {
        if (MainSpouse::where('id', $registrantId)->where('event_id', $eventId)->exists()) {
            $finalData = array();

            $subSpousesArray = array();
            $subSpousesReplacementArray = array();
            $allSpousesArray = array();
            $allSpousesArrayTemp = array();

            $countFinalQuantity = 0;

            $eventYear = Event::where('id', $eventId)->value('year');
            $mainSpouse = MainSpouse::where('id', $registrantId)->where('event_id', $eventId)->first();

            foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                if ($eventCategory == $eventCategoryC) {
                    $eventCode = $code;
                }
            }

            $tempYear = Carbon::parse($mainSpouse->registered_date_time)->format('y');
            $transactionId = SpouseTransaction::where('spouse_id', $mainSpouse->id)->where('spouse_type', "main")->value('id');
            $lastDigit = 1000 + intval($transactionId);
            $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;


            if ($mainSpouse->spouse_replaced_by_id == null && (!$mainSpouse->spouse_refunded)) {
                $countFinalQuantity++;
            }

            $subSpouses = AdditionalSpouse::where('main_spouse_id', $registrantId)->get();
            if (!$subSpouses->isEmpty()) {
                foreach ($subSpouses as $subSpouse) {
                    if ($subSpouse->spouse_replaced_by_id == null && (!$subSpouse->spouse_refunded)) {
                        $countFinalQuantity++;
                    }

                    if ($subSpouse->spouse_replaced_from_id != null) {
                        array_push($subSpousesReplacementArray, [
                            'subSpouseId' => $subSpouse->id,
                            'name' => $subSpouse->salutation . " " . $subSpouse->first_name . " " . $subSpouse->middle_name . " " . $subSpouse->last_name,
                            'salutation' => $subSpouse->salutation,
                            'first_name' => $subSpouse->first_name,
                            'middle_name' => $subSpouse->middle_name,
                            'last_name' => $subSpouse->last_name,
                            'email_address' => $subSpouse->email_address,
                            'mobile_number' => $subSpouse->mobile_number,
                            'nationality' => $subSpouse->nationality,
                            'country' => $subSpouse->country,
                            'city' => $subSpouse->city,

                            'spouse_cancelled' => $subSpouse->spouse_cancelled,
                            'spouse_replaced' => $subSpouse->spouse_replaced,
                            'spouse_refunded' => $subSpouse->spouse_refunded,

                            'spouse_replaced_type' => $subSpouse->spouse_replaced_type,
                            'spouse_original_from_id' => $subSpouse->spouse_original_from_id,
                            'spouse_replaced_from_id' => $subSpouse->spouse_replaced_from_id,
                            'spouse_replaced_by_id' => $subSpouse->spouse_replaced_by_id,

                            'spouse_cancelled_datetime' => $subSpouse->spouse_cancelled_datetime,
                            'spouse_refunded_datetime' => $subSpouse->spouse_refunded_datetime,
                            'spouse_replaced_datetime' => $subSpouse->spouse_replaced_datetime,
                        ]);
                    } else {
                        array_push($subSpousesArray, [
                            'subSpouseId' => $subSpouse->id,
                            'name' => $subSpouse->salutation . " " . $subSpouse->first_name . " " . $subSpouse->middle_name . " " . $subSpouse->last_name,
                            'salutation' => $subSpouse->salutation,
                            'first_name' => $subSpouse->first_name,
                            'middle_name' => $subSpouse->middle_name,
                            'last_name' => $subSpouse->last_name,
                            'email_address' => $subSpouse->email_address,
                            'mobile_number' => $subSpouse->mobile_number,
                            'nationality' => $subSpouse->nationality,
                            'country' => $subSpouse->country,
                            'city' => $subSpouse->city,

                            'spouse_cancelled' => $subSpouse->spouse_cancelled,
                            'spouse_replaced' => $subSpouse->spouse_replaced,
                            'spouse_refunded' => $subSpouse->spouse_refunded,

                            'spouse_replaced_type' => $subSpouse->spouse_replaced_type,
                            'spouse_original_from_id' => $subSpouse->spouse_original_from_id,
                            'spouse_replaced_from_id' => $subSpouse->spouse_replaced_from_id,
                            'spouse_replaced_by_id' => $subSpouse->spouse_replaced_by_id,

                            'spouse_cancelled_datetime' => $subSpouse->spouse_cancelled_datetime,
                            'spouse_refunded_datetime' => $subSpouse->spouse_refunded_datetime,
                            'spouse_replaced_datetime' => $subSpouse->spouse_replaced_datetime,
                        ]);
                    }
                }
            }


            $finalTransactionId = $eventYear . $eventCode . $lastDigit;

            array_push($allSpousesArrayTemp, [
                'transactionId' => $finalTransactionId,
                'mainSpouseId' => $mainSpouse->id,
                'spouseId' => $mainSpouse->id,
                'spouseType' => "main",

                'name' => $mainSpouse->salutation . " " . $mainSpouse->first_name . " " . $mainSpouse->middle_name . " " . $mainSpouse->last_name,
                'salutation' => $mainSpouse->salutation,
                'first_name' => $mainSpouse->first_name,
                'middle_name' => $mainSpouse->middle_name,
                'last_name' => $mainSpouse->last_name,
                'email_address' => $mainSpouse->email_address,
                'mobile_number' => $mainSpouse->mobile_number,
                'nationality' => $mainSpouse->nationality,
                'country' => $mainSpouse->country,
                'city' => $mainSpouse->city,

                'is_replacement' => false,
                'spouse_cancelled' => $mainSpouse->spouse_cancelled,
                'spouse_replaced' => $mainSpouse->spouse_replaced,
                'spouse_refunded' => $mainSpouse->spouse_refunded,

                'spouse_replaced_type' => "main",
                'spouse_original_from_id' => $mainSpouse->id,
                'spouse_replaced_from_id' => null,
                'spouse_replaced_by_id' => $mainSpouse->spouse_replaced_by_id,

                'spouse_cancelled_datetime' => ($mainSpouse->spouse_cancelled_datetime == null) ? "N/A" : Carbon::parse($mainSpouse->spouse_cancelled_datetime)->format('M j, Y g:i A'),
                'spouse_refunded_datetime' => ($mainSpouse->spouse_refunded_datetime == null) ? "N/A" : Carbon::parse($mainSpouse->spouse_refunded_datetime)->format('M j, Y g:i A'),
                'spouse_replaced_datetime' => ($mainSpouse->spouse_replaced_datetime == null) ? "N/A" : Carbon::parse($mainSpouse->spouse_replaced_datetime)->format('M j, Y g:i A'),
            ]);

            if ($mainSpouse->spouse_replaced_by_id != null) {
                foreach ($subSpousesReplacementArray as $subSpouseReplacement) {
                    if ($mainSpouse->id == $subSpouseReplacement['spouse_original_from_id'] && $subSpouseReplacement['spouse_replaced_type'] == "main") {

                        $transactionId = SpouseTransaction::where('spouse_id', $subSpouseReplacement['subSpouseId'])->where('spouse_type', "sub")->value('id');
                        $lastDigit = 1000 + intval($transactionId);
                        $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                        array_push($allSpousesArrayTemp, [
                            'transactionId' => $finalTransactionId,
                            'mainSpouseId' => $mainSpouse->id,
                            'spouseId' => $subSpouseReplacement['subSpouseId'],
                            'spouseType' => "sub",

                            'name' => $subSpouseReplacement['salutation'] . " " . $subSpouseReplacement['first_name'] . " " . $subSpouseReplacement['middle_name'] . " " . $subSpouseReplacement['last_name'],
                            'salutation' => $subSpouseReplacement['salutation'],
                            'first_name' => $subSpouseReplacement['first_name'],
                            'middle_name' => $subSpouseReplacement['middle_name'],
                            'last_name' => $subSpouseReplacement['last_name'],
                            'email_address' => $subSpouseReplacement['email_address'],
                            'mobile_number' => $subSpouseReplacement['mobile_number'],
                            'nationality' => $subSpouseReplacement['nationality'],
                            'country' => $subSpouseReplacement['country'],
                            'city' => $subSpouseReplacement['city'],

                            'is_replacement' => true,
                            'spouse_cancelled' => $subSpouseReplacement['spouse_cancelled'],
                            'spouse_replaced' => $subSpouseReplacement['spouse_replaced'],
                            'spouse_refunded' => $subSpouseReplacement['spouse_refunded'],

                            'spouse_replaced_type' => $subSpouseReplacement['spouse_replaced_type'],
                            'spouse_original_from_id' => $subSpouseReplacement['spouse_original_from_id'],
                            'spouse_replaced_from_id' => $subSpouseReplacement['spouse_replaced_from_id'],
                            'spouse_replaced_by_id' => $subSpouseReplacement['spouse_replaced_by_id'],

                            'spouse_cancelled_datetime' => ($subSpouseReplacement['spouse_cancelled_datetime'] == null) ? "N/A" : Carbon::parse($subSpouseReplacement['spouse_cancelled_datetime'])->format('M j, Y g:i A'),
                            'spouse_refunded_datetime' => ($subSpouseReplacement['spouse_refunded_datetime'] == null) ? "N/A" : Carbon::parse($subSpouseReplacement['spouse_refunded_datetime'])->format('M j, Y g:i A'),
                            'spouse_replaced_datetime' => ($subSpouseReplacement['spouse_replaced_datetime'] == null) ? "N/A" : Carbon::parse($subSpouseReplacement['spouse_replaced_datetime'])->format('M j, Y g:i A'),
                        ]);
                    }
                }
            }

            array_push($allSpousesArray, $allSpousesArrayTemp);

            $allSpousesArrayTemp = array();

            foreach ($subSpousesArray as $subSpouse) {
                $allSpousesArrayTemp = array();

                $transactionId = SpouseTransaction::where('spouse_id', $subSpouse['subSpouseId'])->where('spouse_type', "sub")->value('id');
                $lastDigit = 1000 + intval($transactionId);
                $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                array_push($allSpousesArrayTemp, [
                    'transactionId' => $finalTransactionId,
                    'mainSpouseId' => $mainSpouse->id,
                    'spouseId' => $subSpouse['subSpouseId'],
                    'spouseType' => "sub",

                    'name' => $subSpouse['salutation'] . " " . $subSpouse['first_name'] . " " . $subSpouse['middle_name'] . " " . $subSpouse['last_name'],
                    'salutation' => $subSpouse['salutation'],
                    'first_name' => $subSpouse['first_name'],
                    'middle_name' => $subSpouse['middle_name'],
                    'last_name' => $subSpouse['last_name'],
                    'email_address' => $subSpouse['email_address'],
                    'mobile_number' => $subSpouse['mobile_number'],
                    'nationality' => $subSpouse['nationality'],
                    'country' => $subSpouse['country'],
                    'city' => $subSpouse['city'],

                    'is_replacement' => false,
                    'spouse_cancelled' => $subSpouse['spouse_cancelled'],
                    'spouse_replaced' => $subSpouse['spouse_replaced'],
                    'spouse_refunded' => $subSpouse['spouse_refunded'],

                    'spouse_replaced_type' => "sub",
                    'spouse_original_from_id' => $subSpouse['subSpouseId'],
                    'spouse_replaced_from_id' => null,
                    'spouse_replaced_by_id' => $subSpouse['spouse_replaced_by_id'],

                    'spouse_cancelled_datetime' => ($subSpouse['spouse_cancelled_datetime'] == null) ? "N/A" : Carbon::parse($subSpouse['spouse_cancelled_datetime'])->format('M j, Y g:i A'),
                    'spouse_refunded_datetime' => ($subSpouse['spouse_refunded_datetime'] == null) ? "N/A" : Carbon::parse($subSpouse['spouse_refunded_datetime'])->format('M j, Y g:i A'),
                    'spouse_replaced_datetime' => ($subSpouse['spouse_replaced_datetime'] == null) ? "N/A" : Carbon::parse($subSpouse['spouse_replaced_datetime'])->format('M j, Y g:i A'),
                ]);

                if ($subSpouse['spouse_replaced_by_id'] != null) {
                    foreach ($subSpousesReplacementArray as $subSpouseReplacement) {
                        if ($subSpouse['subSpouseId'] == $subSpouseReplacement['spouse_original_from_id'] && $subSpouseReplacement['spouse_replaced_type'] == "sub") {

                            $transactionId = SpouseTransaction::where('spouse_id', $subSpouseReplacement['subSpouseId'])->where('spouse_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                            array_push($allSpousesArrayTemp, [
                                'transactionId' => $finalTransactionId,
                                'mainSpouseId' => $mainSpouse->id,
                                'spouseId' => $subSpouseReplacement['subSpouseId'],
                                'spouseType' => "sub",

                                'name' => $subSpouseReplacement['salutation'] . " " . $subSpouseReplacement['first_name'] . " " . $subSpouseReplacement['middle_name'] . " " . $subSpouseReplacement['last_name'],
                                'salutation' => $subSpouseReplacement['salutation'],
                                'first_name' => $subSpouseReplacement['first_name'],
                                'middle_name' => $subSpouseReplacement['middle_name'],
                                'last_name' => $subSpouseReplacement['last_name'],
                                'email_address' => $subSpouseReplacement['email_address'],
                                'mobile_number' => $subSpouseReplacement['mobile_number'],
                                'nationality' => $subSpouseReplacement['nationality'],
                                'country' => $subSpouseReplacement['country'],
                                'city' => $subSpouseReplacement['city'],

                                'is_replacement' => true,
                                'spouse_cancelled' => $subSpouseReplacement['spouse_cancelled'],
                                'spouse_replaced' => $subSpouseReplacement['spouse_replaced'],
                                'spouse_refunded' => $subSpouseReplacement['spouse_refunded'],

                                'spouse_replaced_type' => "sub",
                                'spouse_original_from_id' => $subSpouse['subSpouseId'],
                                'spouse_replaced_from_id' => $subSpouseReplacement['spouse_replaced_from_id'],
                                'spouse_replaced_by_id' => $subSpouseReplacement['spouse_replaced_by_id'],

                                'spouse_cancelled_datetime' => ($subSpouseReplacement['spouse_cancelled_datetime'] == null) ? "N/A" : Carbon::parse($subSpouseReplacement['spouse_cancelled_datetime'])->format('M j, Y g:i A'),
                                'spouse_refunded_datetime' => ($subSpouseReplacement['spouse_refunded_datetime'] == null) ? "N/A" : Carbon::parse($subSpouseReplacement['spouse_refunded_datetime'])->format('M j, Y g:i A'),
                                'spouse_replaced_datetime' => ($subSpouseReplacement['spouse_replaced_datetime'] == null) ? "N/A" : Carbon::parse($subSpouseReplacement['spouse_replaced_datetime'])->format('M j, Y g:i A'),
                            ]);
                        }
                    }
                }
                array_push($allSpousesArray, $allSpousesArrayTemp);
            }

            $finalData = [
                'mainSpouseId' => $mainSpouse->id,
                'reference_delegate_name' => $mainSpouse->reference_delegate_name,
                'heard_where' => $mainSpouse->heard_where,
                'quantity' => $mainSpouse->quantity,
                'finalQuantity' => $countFinalQuantity,

                'mode_of_payment' => $mainSpouse->mode_of_payment,
                'registration_status' => $mainSpouse->registration_status,
                'payment_status' => $mainSpouse->payment_status,
                'registered_date_time' => Carbon::parse($mainSpouse->registered_date_time)->format('M j, Y g:i A'),
                'paid_date_time' => ($mainSpouse->paid_date_time == null) ? "N/A" : Carbon::parse($mainSpouse->paid_date_time)->format('M j, Y g:i A'),

                'registration_method' => $mainSpouse->registration_method,
                'transaction_remarks' => $mainSpouse->transaction_remarks,

                'registration_confirmation_sent_count' => $mainSpouse->registration_confirmation_sent_count,
                'registration_confirmation_sent_datetime' => ($mainSpouse->registration_confirmation_sent_datetime == null) ? "N/A" : Carbon::parse($mainSpouse->registration_confirmation_sent_datetime)->format('M j, Y g:i A'),

                'invoiceNumber' => $invoiceNumber,
                'allSpouses' => $allSpousesArray,

                'invoiceData' => $this->getInvoice($eventCategory, $eventId, $registrantId),
            ];
            // dd($finalData);
            return $finalData;
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrantDetailVisitorsView($eventCategory, $eventId, $registrantId)
    {
        if (MainVisitor::where('id', $registrantId)->where('event_id', $eventId)->exists()) {
            $finalData = array();

            $subVisitorsArray = array();
            $subVisitorsReplacementArray = array();
            $allVisitorsArray = array();
            $allVisitorsArrayTemp = array();

            $countFinalQuantity = 0;

            $eventYear = Event::where('id', $eventId)->value('year');
            $mainVisitor = MainVisitor::where('id', $registrantId)->where('event_id', $eventId)->first();

            foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                if ($eventCategory == $eventCategoryC) {
                    $eventCode = $code;
                }
            }

            $tempYear = Carbon::parse($mainVisitor->registered_date_time)->format('y');
            $transactionId = VisitorTransaction::where('visitor_id', $mainVisitor->id)->where('visitor_type', "main")->value('id');
            $lastDigit = 1000 + intval($transactionId);
            $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;


            if ($mainVisitor->visitor_replaced_by_id == null && (!$mainVisitor->visitor_refunded)) {
                $countFinalQuantity++;
            }

            $subVisitors = AdditionalVisitor::where('main_visitor_id', $registrantId)->get();
            if (!$subVisitors->isEmpty()) {
                foreach ($subVisitors as $subVisitor) {
                    if ($subVisitor->visitor_replaced_by_id == null && (!$subVisitor->visitor_refunded)) {
                        $countFinalQuantity++;
                    }

                    if ($subVisitor->visitor_replaced_from_id != null) {
                        array_push($subVisitorsReplacementArray, [
                            'subVisitorId' => $subVisitor->id,
                            'name' => $subVisitor->salutation . " " . $subVisitor->first_name . " " . $subVisitor->middle_name . " " . $subVisitor->last_name,
                            'salutation' => $subVisitor->salutation,
                            'first_name' => $subVisitor->first_name,
                            'middle_name' => $subVisitor->middle_name,
                            'last_name' => $subVisitor->last_name,
                            'email_address' => $subVisitor->email_address,
                            'mobile_number' => $subVisitor->mobile_number,
                            'nationality' => $subVisitor->nationality,
                            'country' => $subVisitor->country,
                            'city' => $subVisitor->city,
                            'company_name' => $subVisitor->company_name,
                            'job_title' => $subVisitor->job_title,

                            'visitor_cancelled' => $subVisitor->visitor_cancelled,
                            'visitor_replaced' => $subVisitor->visitor_replaced,
                            'visitor_refunded' => $subVisitor->visitor_refunded,

                            'visitor_replaced_type' => $subVisitor->visitor_replaced_type,
                            'visitor_original_from_id' => $subVisitor->visitor_original_from_id,
                            'visitor_replaced_from_id' => $subVisitor->visitor_replaced_from_id,
                            'visitor_replaced_by_id' => $subVisitor->visitor_replaced_by_id,

                            'visitor_cancelled_datetime' => $subVisitor->visitor_cancelled_datetime,
                            'visitor_refunded_datetime' => $subVisitor->visitor_refunded_datetime,
                            'visitor_replaced_datetime' => $subVisitor->visitor_replaced_datetime,
                        ]);
                    } else {
                        array_push($subVisitorsArray, [
                            'subVisitorId' => $subVisitor->id,
                            'name' => $subVisitor->salutation . " " . $subVisitor->first_name . " " . $subVisitor->middle_name . " " . $subVisitor->last_name,
                            'salutation' => $subVisitor->salutation,
                            'first_name' => $subVisitor->first_name,
                            'middle_name' => $subVisitor->middle_name,
                            'last_name' => $subVisitor->last_name,
                            'email_address' => $subVisitor->email_address,
                            'mobile_number' => $subVisitor->mobile_number,
                            'nationality' => $subVisitor->nationality,
                            'country' => $subVisitor->country,
                            'city' => $subVisitor->city,
                            'company_name' => $subVisitor->company_name,
                            'job_title' => $subVisitor->job_title,

                            'visitor_cancelled' => $subVisitor->visitor_cancelled,
                            'visitor_replaced' => $subVisitor->visitor_replaced,
                            'visitor_refunded' => $subVisitor->visitor_refunded,

                            'visitor_replaced_type' => $subVisitor->visitor_replaced_type,
                            'visitor_original_from_id' => $subVisitor->visitor_original_from_id,
                            'visitor_replaced_from_id' => $subVisitor->visitor_replaced_from_id,
                            'visitor_replaced_by_id' => $subVisitor->visitor_replaced_by_id,

                            'visitor_cancelled_datetime' => $subVisitor->visitor_cancelled_datetime,
                            'visitor_refunded_datetime' => $subVisitor->visitor_refunded_datetime,
                            'visitor_replaced_datetime' => $subVisitor->visitor_replaced_datetime,
                        ]);
                    }
                }
            }


            $finalTransactionId = $eventYear . $eventCode . $lastDigit;

            array_push($allVisitorsArrayTemp, [
                'transactionId' => $finalTransactionId,
                'mainVisitorId' => $mainVisitor->id,
                'visitorId' => $mainVisitor->id,
                'visitorType' => "main",

                'name' => $mainVisitor->salutation . " " . $mainVisitor->first_name . " " . $mainVisitor->middle_name . " " . $mainVisitor->last_name,
                'salutation' => $mainVisitor->salutation,
                'first_name' => $mainVisitor->first_name,
                'middle_name' => $mainVisitor->middle_name,
                'last_name' => $mainVisitor->last_name,
                'email_address' => $mainVisitor->email_address,
                'mobile_number' => $mainVisitor->mobile_number,
                'nationality' => $mainVisitor->nationality,
                'country' => $mainVisitor->country,
                'city' => $mainVisitor->city,
                'company_name' => $mainVisitor->company_name,
                'job_title' => $mainVisitor->job_title,

                'is_replacement' => false,
                'visitor_cancelled' => $mainVisitor->visitor_cancelled,
                'visitor_replaced' => $mainVisitor->visitor_replaced,
                'visitor_refunded' => $mainVisitor->visitor_refunded,

                'visitor_replaced_type' => "main",
                'visitor_original_from_id' => $mainVisitor->id,
                'visitor_replaced_from_id' => null,
                'visitor_replaced_by_id' => $mainVisitor->visitor_replaced_by_id,

                'visitor_cancelled_datetime' => ($mainVisitor->visitor_cancelled_datetime == null) ? "N/A" : Carbon::parse($mainVisitor->visitor_cancelled_datetime)->format('M j, Y g:i A'),
                'visitor_refunded_datetime' => ($mainVisitor->visitor_refunded_datetime == null) ? "N/A" : Carbon::parse($mainVisitor->visitor_refunded_datetime)->format('M j, Y g:i A'),
                'visitor_replaced_datetime' => ($mainVisitor->visitor_replaced_datetime == null) ? "N/A" : Carbon::parse($mainVisitor->visitor_replaced_datetime)->format('M j, Y g:i A'),
            ]);

            if ($mainVisitor->visitor_replaced_by_id != null) {
                foreach ($subVisitorsReplacementArray as $subVisitorReplacement) {
                    if ($mainVisitor->id == $subVisitorReplacement['visitor_original_from_id'] && $subVisitorReplacement['visitor_replaced_type'] == "main") {

                        $transactionId = VisitorTransaction::where('visitor_id', $subVisitorReplacement['subVisitorId'])->where('visitor_type', "sub")->value('id');
                        $lastDigit = 1000 + intval($transactionId);
                        $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                        array_push($allVisitorsArrayTemp, [
                            'transactionId' => $finalTransactionId,
                            'mainVisitorId' => $mainVisitor->id,
                            'visitorId' => $subVisitorReplacement['subVisitorId'],
                            'visitorType' => "sub",

                            'name' => $subVisitorReplacement['salutation'] . " " . $subVisitorReplacement['first_name'] . " " . $subVisitorReplacement['middle_name'] . " " . $subVisitorReplacement['last_name'],
                            'salutation' => $subVisitorReplacement['salutation'],
                            'first_name' => $subVisitorReplacement['first_name'],
                            'middle_name' => $subVisitorReplacement['middle_name'],
                            'last_name' => $subVisitorReplacement['last_name'],
                            'email_address' => $subVisitorReplacement['email_address'],
                            'mobile_number' => $subVisitorReplacement['mobile_number'],
                            'nationality' => $subVisitorReplacement['nationality'],
                            'country' => $subVisitorReplacement['country'],
                            'city' => $subVisitorReplacement['city'],
                            'company_name' => $subVisitorReplacement['company_name'],
                            'job_title' => $subVisitorReplacement['job_title'],

                            'is_replacement' => true,
                            'visitor_cancelled' => $subVisitorReplacement['visitor_cancelled'],
                            'visitor_replaced' => $subVisitorReplacement['visitor_replaced'],
                            'visitor_refunded' => $subVisitorReplacement['visitor_refunded'],

                            'visitor_replaced_type' => $subVisitorReplacement['visitor_replaced_type'],
                            'visitor_original_from_id' => $subVisitorReplacement['visitor_original_from_id'],
                            'visitor_replaced_from_id' => $subVisitorReplacement['visitor_replaced_from_id'],
                            'visitor_replaced_by_id' => $subVisitorReplacement['visitor_replaced_by_id'],

                            'visitor_cancelled_datetime' => ($subVisitorReplacement['visitor_cancelled_datetime'] == null) ? "N/A" : Carbon::parse($subVisitorReplacement['visitor_cancelled_datetime'])->format('M j, Y g:i A'),
                            'visitor_refunded_datetime' => ($subVisitorReplacement['visitor_refunded_datetime'] == null) ? "N/A" : Carbon::parse($subVisitorReplacement['visitor_refunded_datetime'])->format('M j, Y g:i A'),
                            'visitor_replaced_datetime' => ($subVisitorReplacement['visitor_replaced_datetime'] == null) ? "N/A" : Carbon::parse($subVisitorReplacement['visitor_replaced_datetime'])->format('M j, Y g:i A'),
                        ]);
                    }
                }
            }

            array_push($allVisitorsArray, $allVisitorsArrayTemp);

            $allVisitorsArrayTemp = array();

            foreach ($subVisitorsArray as $subVisitor) {
                $allVisitorsArrayTemp = array();

                $transactionId = VisitorTransaction::where('visitor_id', $subVisitor['subVisitorId'])->where('visitor_type', "sub")->value('id');
                $lastDigit = 1000 + intval($transactionId);
                $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                array_push($allVisitorsArrayTemp, [
                    'transactionId' => $finalTransactionId,
                    'mainVisitorId' => $mainVisitor->id,
                    'visitorId' => $subVisitor['subVisitorId'],
                    'visitorType' => "sub",

                    'name' => $subVisitor['salutation'] . " " . $subVisitor['first_name'] . " " . $subVisitor['middle_name'] . " " . $subVisitor['last_name'],
                    'salutation' => $subVisitor['salutation'],
                    'first_name' => $subVisitor['first_name'],
                    'middle_name' => $subVisitor['middle_name'],
                    'last_name' => $subVisitor['last_name'],
                    'email_address' => $subVisitor['email_address'],
                    'mobile_number' => $subVisitor['mobile_number'],
                    'nationality' => $subVisitor['nationality'],
                    'country' => $subVisitor['country'],
                    'city' => $subVisitor['city'],
                    'company_name' => $subVisitor['company_name'],
                    'job_title' => $subVisitor['job_title'],

                    'is_replacement' => false,
                    'visitor_cancelled' => $subVisitor['visitor_cancelled'],
                    'visitor_replaced' => $subVisitor['visitor_replaced'],
                    'visitor_refunded' => $subVisitor['visitor_refunded'],

                    'visitor_replaced_type' => "sub",
                    'visitor_original_from_id' => $subVisitor['subVisitorId'],
                    'visitor_replaced_from_id' => null,
                    'visitor_replaced_by_id' => $subVisitor['visitor_replaced_by_id'],

                    'visitor_cancelled_datetime' => ($subVisitor['visitor_cancelled_datetime'] == null) ? "N/A" : Carbon::parse($subVisitor['visitor_cancelled_datetime'])->format('M j, Y g:i A'),
                    'visitor_refunded_datetime' => ($subVisitor['visitor_refunded_datetime'] == null) ? "N/A" : Carbon::parse($subVisitor['visitor_refunded_datetime'])->format('M j, Y g:i A'),
                    'visitor_replaced_datetime' => ($subVisitor['visitor_replaced_datetime'] == null) ? "N/A" : Carbon::parse($subVisitor['visitor_replaced_datetime'])->format('M j, Y g:i A'),
                ]);

                if ($subVisitor['visitor_replaced_by_id'] != null) {
                    foreach ($subVisitorsReplacementArray as $subVisitorReplacement) {
                        if ($subVisitor['subVisitorId'] == $subVisitorReplacement['visitor_original_from_id'] && $subVisitorReplacement['visitor_replaced_type'] == "sub") {

                            $transactionId = VisitorTransaction::where('visitor_id', $subVisitorReplacement['subVisitorId'])->where('visitor_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                            array_push($allVisitorsArrayTemp, [
                                'transactionId' => $finalTransactionId,
                                'mainVisitorId' => $mainVisitor->id,
                                'visitorId' => $subVisitorReplacement['subVisitorId'],
                                'visitorType' => "sub",

                                'name' => $subVisitorReplacement['salutation'] . " " . $subVisitorReplacement['first_name'] . " " . $subVisitorReplacement['middle_name'] . " " . $subVisitorReplacement['last_name'],
                                'salutation' => $subVisitorReplacement['salutation'],
                                'first_name' => $subVisitorReplacement['first_name'],
                                'middle_name' => $subVisitorReplacement['middle_name'],
                                'last_name' => $subVisitorReplacement['last_name'],
                                'email_address' => $subVisitorReplacement['email_address'],
                                'mobile_number' => $subVisitorReplacement['mobile_number'],
                                'nationality' => $subVisitorReplacement['nationality'],
                                'country' => $subVisitorReplacement['country'],
                                'city' => $subVisitorReplacement['city'],
                                'company_name' => $subVisitorReplacement['company_name'],
                                'job_title' => $subVisitorReplacement['job_title'],

                                'is_replacement' => true,
                                'visitor_cancelled' => $subVisitorReplacement['visitor_cancelled'],
                                'visitor_replaced' => $subVisitorReplacement['visitor_replaced'],
                                'visitor_refunded' => $subVisitorReplacement['visitor_refunded'],

                                'visitor_replaced_type' => "sub",
                                'visitor_original_from_id' => $subVisitor['subVisitorId'],
                                'visitor_replaced_from_id' => $subVisitorReplacement['visitor_replaced_from_id'],
                                'visitor_replaced_by_id' => $subVisitorReplacement['visitor_replaced_by_id'],

                                'visitor_cancelled_datetime' => ($subVisitorReplacement['visitor_cancelled_datetime'] == null) ? "N/A" : Carbon::parse($subVisitorReplacement['visitor_cancelled_datetime'])->format('M j, Y g:i A'),
                                'visitor_refunded_datetime' => ($subVisitorReplacement['visitor_refunded_datetime'] == null) ? "N/A" : Carbon::parse($subVisitorReplacement['visitor_refunded_datetime'])->format('M j, Y g:i A'),
                                'visitor_replaced_datetime' => ($subVisitorReplacement['visitor_replaced_datetime'] == null) ? "N/A" : Carbon::parse($subVisitorReplacement['visitor_replaced_datetime'])->format('M j, Y g:i A'),
                            ]);
                        }
                    }
                }
                array_push($allVisitorsArray, $allVisitorsArrayTemp);
            }

            $finalData = [
                'mainVisitorId' => $mainVisitor->id,
                'heard_where' => $mainVisitor->heard_where,
                'quantity' => $mainVisitor->quantity,
                'finalQuantity' => $countFinalQuantity,

                'mode_of_payment' => $mainVisitor->mode_of_payment,
                'registration_status' => $mainVisitor->registration_status,
                'payment_status' => $mainVisitor->payment_status,
                'registered_date_time' => Carbon::parse($mainVisitor->registered_date_time)->format('M j, Y g:i A'),
                'paid_date_time' => ($mainVisitor->paid_date_time == null) ? "N/A" : Carbon::parse($mainVisitor->paid_date_time)->format('M j, Y g:i A'),

                'registration_method' => $mainVisitor->registration_method,
                'transaction_remarks' => $mainVisitor->transaction_remarks,

                'registration_confirmation_sent_count' => $mainVisitor->registration_confirmation_sent_count,
                'registration_confirmation_sent_datetime' => ($mainVisitor->registration_confirmation_sent_datetime == null) ? "N/A" : Carbon::parse($mainVisitor->registration_confirmation_sent_datetime)->format('M j, Y g:i A'),

                'invoiceNumber' => $invoiceNumber,
                'allVisitors' => $allVisitorsArray,

                'invoiceData' => $this->getInvoice($eventCategory, $eventId, $registrantId),
            ];
            return $finalData;
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrantDetailRCCAwardsView($eventCategory, $eventId, $registrantId)
    {
        if (RccAwardsMainParticipant::where('id', $registrantId)->where('event_id', $eventId)->exists()) {
            $finalData = array();

            $subParticipantReplacementArray = array();
            $allParticipantsArray = array();
            $allParticipantsArrayTemp = array();

            $countFinalQuantity = 0;

            $eventYear = Event::where('id', $eventId)->value('year');
            $mainParticipant = RccAwardsMainParticipant::where('id', $registrantId)->where('event_id', $eventId)->first();

            foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                if ($eventCategory == $eventCategoryC) {
                    $eventCode = $code;
                }
            }

            $tempYear = Carbon::parse($mainParticipant->registered_date_time)->format('y');
            $transactionId = RccAwardsParticipantTransaction::where('participant_id', $mainParticipant->id)->where('participant_type', "main")->value('id');
            $lastDigit = 1000 + intval($transactionId);
            $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;

            if ($mainParticipant->participant_replaced_by_id == null && (!$mainParticipant->participant_refunded)) {
                $countFinalQuantity++;
            }

            $subParticipants = RccAwardsAdditionalParticipant::where('main_participant_id', $registrantId)->get();
            if (!$subParticipants->isEmpty()) {
                foreach ($subParticipants as $subParticipant) {
                    if ($subParticipant->participant_replaced_by_id == null && (!$subParticipant->participant_refunded)) {
                        $countFinalQuantity++;
                    }

                    if ($subParticipant->participant_replaced_from_id != null) {
                        array_push($subParticipantReplacementArray, [
                            'subParticipantId' => $subParticipant->id,
                            'name' => $subParticipant->salutation . " " . $subParticipant->first_name . " " . $subParticipant->middle_name . " " . $subParticipant->last_name,
                            'salutation' => $subParticipant->salutation,
                            'first_name' => $subParticipant->first_name,
                            'middle_name' => $subParticipant->middle_name,
                            'last_name' => $subParticipant->last_name,
                            'email_address' => $subParticipant->email_address,
                            'mobile_number' => $subParticipant->mobile_number,
                            'address' => $subParticipant->address,
                            'country' => $subParticipant->country,
                            'city' => $subParticipant->city,
                            'job_title' => $subParticipant->job_title,

                            'participant_cancelled' => $subParticipant->participant_cancelled,
                            'participant_replaced' => $subParticipant->participant_replaced,
                            'participant_refunded' => $subParticipant->participant_refunded,

                            'participant_replaced_type' => $subParticipant->participant_replaced_type,
                            'participant_original_from_id' => $subParticipant->participant_original_from_id,
                            'participant_replaced_from_id' => $subParticipant->participant_replaced_from_id,
                            'participant_replaced_by_id' => $subParticipant->participant_replaced_by_id,

                            'participant_cancelled_datetime' => $subParticipant->participant_cancelled_datetime,
                            'participant_refunded_datetime' => $subParticipant->participant_refunded_datetime,
                            'participant_replaced_datetime' => $subParticipant->participant_replaced_datetime,
                        ]);
                    }
                }
            }


            $finalTransactionId = $eventYear . $eventCode . $lastDigit;

            array_push($allParticipantsArrayTemp, [
                'transactionId' => $finalTransactionId,
                'mainParticipantId' => $mainParticipant->id,
                'participantId' => $mainParticipant->id,
                'participantType' => "main",

                'name' => $mainParticipant->salutation . " " . $mainParticipant->first_name . " " . $mainParticipant->middle_name . " " . $mainParticipant->last_name,
                'salutation' => $mainParticipant->salutation,
                'first_name' => $mainParticipant->first_name,
                'middle_name' => $mainParticipant->middle_name,
                'last_name' => $mainParticipant->last_name,
                'email_address' => $mainParticipant->email_address,
                'mobile_number' => $mainParticipant->mobile_number,
                'address' => $mainParticipant->address,
                'country' => $mainParticipant->country,
                'city' => $mainParticipant->city,
                'job_title' => $mainParticipant->job_title,

                'is_replacement' => false,
                'participant_cancelled' => $mainParticipant->participant_cancelled,
                'participant_replaced' => $mainParticipant->participant_replaced,
                'participant_refunded' => $mainParticipant->participant_refunded,

                'participant_replaced_type' => "main",
                'participant_original_from_id' => $mainParticipant->id,
                'participant_replaced_from_id' => null,
                'participant_replaced_by_id' => $mainParticipant->participant_replaced_by_id,

                'participant_cancelled_datetime' => ($mainParticipant->participant_cancelled_datetime == null) ? "N/A" : Carbon::parse($mainParticipant->participant_cancelled_datetime)->format('M j, Y g:i A'),
                'participant_refunded_datetime' => ($mainParticipant->participant_refunded_datetime == null) ? "N/A" : Carbon::parse($mainParticipant->participant_refunded_datetime)->format('M j, Y g:i A'),
                'participant_replaced_datetime' => ($mainParticipant->participant_replaced_datetime == null) ? "N/A" : Carbon::parse($mainParticipant->participant_replaced_datetime)->format('M j, Y g:i A'),
            ]);

            if ($mainParticipant->participant_replaced_by_id != null) {
                foreach ($subParticipantReplacementArray as $subParticipantReplacement) {
                    if ($mainParticipant->id == $subParticipantReplacement['participant_original_from_id'] && $subParticipantReplacement['participant_replaced_type'] == "main") {

                        $transactionId = RccAwardsParticipantTransaction::where('participant_id', $subParticipantReplacement['subParticipantId'])->where('participant_type', "sub")->value('id');
                        $lastDigit = 1000 + intval($transactionId);
                        $finalTransactionId = $eventYear . $eventCode . $lastDigit;

                        array_push($allParticipantsArrayTemp, [
                            'transactionId' => $finalTransactionId,
                            'mainParticipantId' => $mainParticipant->id,
                            'participantId' => $subParticipantReplacement['subParticipantId'],
                            'participantType' => "sub",

                            'name' => $subParticipantReplacement['salutation'] . " " . $subParticipantReplacement['first_name'] . " " . $subParticipantReplacement['middle_name'] . " " . $subParticipantReplacement['last_name'],
                            'salutation' => $subParticipantReplacement['salutation'],
                            'first_name' => $subParticipantReplacement['first_name'],
                            'middle_name' => $subParticipantReplacement['middle_name'],
                            'last_name' => $subParticipantReplacement['last_name'],
                            'email_address' => $subParticipantReplacement['email_address'],
                            'mobile_number' => $subParticipantReplacement['mobile_number'],
                            'address' => $subParticipantReplacement['address'],
                            'country' => $subParticipantReplacement['country'],
                            'city' => $subParticipantReplacement['city'],
                            'job_title' => $subParticipantReplacement['job_title'],

                            'is_replacement' => true,
                            'participant_cancelled' => $subParticipantReplacement['participant_cancelled'],
                            'participant_replaced' => $subParticipantReplacement['participant_replaced'],
                            'participant_refunded' => $subParticipantReplacement['participant_refunded'],

                            'participant_replaced_type' => $subParticipantReplacement['participant_replaced_type'],
                            'participant_original_from_id' => $subParticipantReplacement['participant_original_from_id'],
                            'participant_replaced_from_id' => $subParticipantReplacement['participant_replaced_from_id'],
                            'participant_replaced_by_id' => $subParticipantReplacement['participant_replaced_by_id'],

                            'participant_cancelled_datetime' => ($subParticipantReplacement['participant_cancelled_datetime'] == null) ? "N/A" : Carbon::parse($subParticipantReplacement['participant_cancelled_datetime'])->format('M j, Y g:i A'),
                            'participant_refunded_datetime' => ($subParticipantReplacement['participant_refunded_datetime'] == null) ? "N/A" : Carbon::parse($subParticipantReplacement['participant_refunded_datetime'])->format('M j, Y g:i A'),
                            'participant_replaced_datetime' => ($subParticipantReplacement['participant_replaced_datetime'] == null) ? "N/A" : Carbon::parse($subParticipantReplacement['participant_replaced_datetime'])->format('M j, Y g:i A'),
                        ]);
                    }
                }
            }

            array_push($allParticipantsArray, $allParticipantsArrayTemp);

            $entryFormId = RccAwardsDocument::where('event_id', $eventId)->where('participant_id', $mainParticipant->id)->where('document_type', 'entryForm')->value('id');

            $getSupportingDocumentFiles = RccAwardsDocument::where('event_id', $eventId)->where('participant_id', $mainParticipant->id)->where('document_type', 'supportingDocument')->get();

            $supportingDocumentsDownloadId = [];

            if ($getSupportingDocumentFiles->isNotEmpty()) {
                foreach ($getSupportingDocumentFiles as $supportingDocument) {
                    $supportingDocumentsDownloadId[] = $supportingDocument->id;
                }
            }

            $finalData = [
                'mainParticipantId' => $mainParticipant->id,

                'pass_type' => $mainParticipant->pass_type,
                'rate_type' => $mainParticipant->rate_type,
                'rate_type_string' => $mainParticipant->rate_type_string,

                'category' => $mainParticipant->category,
                'sub_category' => $mainParticipant->sub_category,
                'company_name' => $mainParticipant->company_name,

                'entryFormId' => $entryFormId,
                'supportingDocumentsDownloadId' => $supportingDocumentsDownloadId,

                'heard_where' => $mainParticipant->heard_where,

                'quantity' => $mainParticipant->quantity,
                'finalQuantity' => $countFinalQuantity,

                'mode_of_payment' => $mainParticipant->mode_of_payment,
                'registration_status' => $mainParticipant->registration_status,
                'payment_status' => $mainParticipant->payment_status,
                'registered_date_time' => Carbon::parse($mainParticipant->registered_date_time)->format('M j, Y g:i A'),
                'paid_date_time' => ($mainParticipant->paid_date_time == null) ? "N/A" : Carbon::parse($mainParticipant->paid_date_time)->format('M j, Y g:i A'),

                'registration_method' => $mainParticipant->registration_method,
                'transaction_remarks' => $mainParticipant->transaction_remarks,

                'registration_confirmation_sent_count' => $mainParticipant->registration_confirmation_sent_count,
                'registration_confirmation_sent_datetime' => ($mainParticipant->registration_confirmation_sent_datetime == null) ? "N/A" : Carbon::parse($mainParticipant->registration_confirmation_sent_datetime)->format('M j, Y g:i A'),

                'invoiceNumber' => $invoiceNumber,
                'allParticipants' => $allParticipantsArray,

                'invoiceData' => $this->getInvoice($eventCategory, $eventId, $registrantId),
            ];
            // dd($finalData);
            return $finalData;
        } else {
            abort(404, 'The URL is incorrect');
        }
    }







    // =========================================================
    //                       RENDER LOGICS
    // =========================================================

    public function numberToWords($number)
    {
        $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        return $formatter->format($number);
    }

    public function generateAdminInvoice($eventCategory, $eventId, $registrantId)
    {
        $finalData = $this->getInvoice($eventCategory, $eventId, $registrantId);
        if ($finalData['finalQuantity'] > 0) {
            if ($eventCategory == "RCCA") {
                if ($finalData['paymentStatus'] == "unpaid") {
                    $pdf = Pdf::loadView('admin.events.transactions.invoices.rcca.unpaid', $finalData);
                } else {
                    $pdf = Pdf::loadView('admin.events.transactions.invoices.rcca.paid', $finalData);
                }
            } else {
                if ($finalData['paymentStatus'] == "unpaid") {
                    $pdf = Pdf::loadView('admin.events.transactions.invoices.unpaid', $finalData);
                } else {
                    $pdf = Pdf::loadView('admin.events.transactions.invoices.paid', $finalData);
                }
            }
            return $pdf->stream('invoice.pdf');
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function generatePublicInvoice($eventCategory, $eventId, $registrantId)
    {
        $finalData = $this->getInvoice($eventCategory, $eventId, $registrantId);
        if ($finalData['finalQuantity'] > 0) {
            if ($finalData['registrationMethod'] != "imported") {
                if ($eventCategory == "RCCA") {
                    if ($finalData['paymentStatus'] == "unpaid") {
                        $pdf = Pdf::loadView('admin.events.transactions.invoices.rcca.unpaid', $finalData);
                    } else {
                        $pdf = Pdf::loadView('admin.events.transactions.invoices.rcca.paid', $finalData);
                    }
                } else {
                    if ($finalData['paymentStatus'] == "unpaid") {
                        $pdf = Pdf::loadView('admin.events.transactions.invoices.unpaid', $finalData);
                    } else {
                        $pdf = Pdf::loadView('admin.events.transactions.invoices.paid', $finalData);
                    }
                }
                return $pdf->stream('invoice.pdf');
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function getInvoice($eventCategory, $eventId, $registrantId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            if ($eventCategory == "AFS") {
                $invoiceData = $this->getInvoiceSpouse($eventCategory, $eventId, $registrantId);
            } else if ($eventCategory == "AFV") {
                $invoiceData = $this->getInvoiceVisitor($eventCategory, $eventId, $registrantId);
            } else if ($eventCategory == "RCCA") {
                $invoiceData = $this->getInvoiceRccAwards($eventCategory, $eventId, $registrantId);
            } else {
                $invoiceData = $this->getInvoiceEvents($eventCategory, $eventId, $registrantId);
            }
            return $invoiceData;
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrantsExportData($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            if ($eventCategory == "AFS") {
                $finalData = $this->spouseRegistrantsExportData($eventCategory, $eventId);
            } else if ($eventCategory == "AFV") {
                $finalData = $this->visitorRegistrantsExportData($eventCategory, $eventId);
            } else if ($eventCategory == "RCCA") {
                $finalData = $this->rccAwardsRegistrantsExportData($eventCategory, $eventId);
            } else {
                $finalData = $this->eventRegistrantsExportData($eventCategory, $eventId);
            }

            return response()->stream($finalData['callback'], 200, $finalData['headers']);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function capturePayment()
    {
        $registrationFormType = request()->query('registrationFormType');

        if ($registrationFormType == "events") {
            $mainDelegateId = request()->query('mainDelegateId');
            $sessionId = request()->query('sessionId');
            if (
                request()->input('response_gatewayRecommendation') == "PROCEED" &&
                request()->input('result') == "SUCCESS" &&
                request()->input('order_id') &&
                request()->input('transaction_id') &&
                request()->query('sessionId') &&
                request()->query('mainDelegateId') &&
                request()->query('registrationFormType')
            ) {
                $orderId = request()->input('order_id');
                $oldTransactionId = request()->input('transaction_id');
                $newTransactionId = substr(uniqid(), -8);

                $apiEndpoint = env('MERCHANT_API_URL');
                $merchantId = env('MERCHANT_ID');
                $authPass = env('MERCHANT_AUTH_PASSWORD');

                $client = new Client();
                $response = $client->request('PUT', $apiEndpoint . '/order/' . $orderId . '/transaction/' . $newTransactionId, [
                    'auth' => [
                        'merchant.' . $merchantId,
                        $authPass,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'apiOperation' => "PAY",
                        "authentication" => [
                            "transactionId" => $oldTransactionId,
                        ],
                        "order" => [
                            "reference" => $orderId,
                        ],
                        "session" => [
                            'id' => $sessionId,
                        ],
                        "transaction" => [
                            "reference" => $orderId,
                        ],
                    ]
                ]);
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                if (
                    $data['response']['gatewayCode'] == "APPROVED" &&
                    $data['response']['gatewayRecommendation'] == "NO_ACTION" &&
                    $data['transaction']['authenticationStatus'] == "AUTHENTICATION_SUCCESSFUL" &&
                    $data['transaction']['type'] == "PAYMENT"
                ) {
                    MainDelegate::find($mainDelegateId)->fill([
                        'registration_status' => "confirmed",
                        'payment_status' => "paid",
                        'paid_date_time' => Carbon::now(),

                        'registration_confirmation_sent_count' => 1,
                        'registration_confirmation_sent_datetime' => Carbon::now(),
                    ])->save();

                    $mainDelegate = MainDelegate::where('id', $mainDelegateId)->first();
                    $event = Event::where('id', $mainDelegate->event_id)->first();
                    $eventFormattedData = Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');

                    $transactionId = Transaction::where('delegate_id', $mainDelegateId)->where('delegate_type', "main")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                        if ($event->category == $eventCategoryC) {
                            $getEventcode = $code;
                        }
                    }

                    $tempTransactionId = "$event->year" . "$getEventcode" . "$lastDigit";
                    $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                    $amountPaid = $mainDelegate->unit_price;

                    if ($mainDelegate->pcode_used != null) {
                        $promoCode = PromoCode::where('event_id', $mainDelegate->event_id)->where('promo_code', $mainDelegate->pcode_used)->first();

                        if ($promoCode != null) {
                            if ($promoCode->discount_type == "percentage") {
                                $amountPaid = $mainDelegate->unit_price - ($mainDelegate->unit_price * ($promoCode->discount / 100));
                            } else {
                                $amountPaid = $mainDelegate->unit_price - $promoCode->discount;
                            }
                        }
                    }

                    $details1 = [
                        'name' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $event->location,
                        'eventCategory' => $event->category,
                        'eventYear' => $event->year,

                        'jobTitle' => $mainDelegate->job_title,
                        'companyName' => $mainDelegate->company_name,
                        'amountPaid' => $amountPaid,
                        'transactionId' => $tempTransactionId,
                        'invoiceLink' => $invoiceLink,
                        'badgeLink' => env('APP_URL') . "/" . $event->category . "/" . $event->id . "/view-badge" . "/" . "main" . "/" . $mainDelegateId,
                    ];

                    $details2 = [
                        'name' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventCategory' => $event->category,
                        'eventYear' => $event->year,

                        'invoiceAmount' => $mainDelegate->total_amount,
                        'amountPaid' => $mainDelegate->total_amount,
                        'balance' => 0,
                        'invoiceLink' => $invoiceLink,
                    ];

                    if ($event->category == "DAW") {
                        $ccEmailNotif = config('app.ccEmailNotif.daw');
                    } else {
                        $ccEmailNotif = config('app.ccEmailNotif.default');
                    }

                    Mail::to($mainDelegate->email_address)->cc($ccEmailNotif)->send(new RegistrationPaid($details1));
                    Mail::to($mainDelegate->email_address)->cc($ccEmailNotif)->send(new RegistrationPaymentConfirmation($details2));

                    if ($mainDelegate->assistant_email_address != null) {
                        $details1['amountPaid'] = $mainDelegate->total_amount;

                        Mail::to($mainDelegate->assistant_email_address)->send(new RegistrationPaid($details1));
                        Mail::to($mainDelegate->assistant_email_address)->send(new RegistrationPaymentConfirmation($details2));
                    }

                    $additionalDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegateId)->get();

                    if (!$additionalDelegates->isEmpty()) {
                        foreach ($additionalDelegates as $additionalDelegate) {
                            $transactionId = Transaction::where('delegate_id', $additionalDelegate->id)->where('delegate_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $tempTransactionId = "$event->year" . "$getEventcode" . "$lastDigit";

                            $amountPaidSub = $mainDelegate->unit_price;

                            if ($additionalDelegate->pcode_used != null) {
                                $promoCode = PromoCode::where('event_id', $mainDelegate->event_id)->where('promo_code', $additionalDelegate->pcode_used)->first();

                                if ($promoCode != null) {
                                    if ($promoCode->discount_type == "percentage") {
                                        $amountPaidSub = $mainDelegate->unit_price - ($mainDelegate->unit_price * ($promoCode->discount / 100));
                                    } else {
                                        $amountPaidSub = $mainDelegate->unit_price - $promoCode->discount;
                                    }
                                }
                            }

                            $details1 = [
                                'name' => $additionalDelegate->salutation . " " . $additionalDelegate->first_name . " " . $additionalDelegate->middle_name . " " . $additionalDelegate->last_name,
                                'eventLink' => $event->link,
                                'eventName' => $event->name,
                                'eventDates' => $eventFormattedData,
                                'eventLocation' => $event->location,
                                'eventCategory' => $event->category,
                                'eventYear' => $event->year,

                                'jobTitle' => $additionalDelegate->job_title,
                                'companyName' => $mainDelegate->company_name,
                                'amountPaid' => $amountPaidSub,
                                'transactionId' => $tempTransactionId,
                                'invoiceLink' => $invoiceLink,
                                'badgeLink' => env('APP_URL') . "/" . $event->category . "/" . $event->id . "/view-badge" . "/" . "sub" . "/" . $additionalDelegate->id,
                            ];

                            Mail::to($additionalDelegate->email_address)->cc($ccEmailNotif)->send(new RegistrationPaid($details1));
                        }
                    }
                    return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "success"]);
                } else {
                    // (This is the part where we will send them email notification failed and redirect them)
                    MainDelegate::find($mainDelegateId)->fill([
                        'registration_status' => "pending",
                        'payment_status' => "unpaid",
                    ])->save();

                    $mainDelegate = MainDelegate::where('id', $mainDelegateId)->first();
                    $event = Event::where('id', $mainDelegate->event_id)->first();
                    $eventFormattedData = Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');

                    if ($event->category == "AF") {
                        $bankDetails = config('app.bankDetails.AF');
                    } else {
                        $bankDetails = config('app.bankDetails.DEFAULT');
                    }

                    $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                    $details = [
                        'name' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventCategory' => $event->category,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $event->location,
                        'bankDetails' => $bankDetails,
                        'invoiceLink' => $invoiceLink,
                        'eventYear' => $event->year,
                    ];


                    if ($event->category == "DAW") {
                        $ccEmailNotif = config('app.ccEmailNotif.daw');
                    } else {
                        $ccEmailNotif = config('app.ccEmailNotif.default');
                    }

                    Mail::to($mainDelegate->email_address)->cc($ccEmailNotif)->send(new RegistrationCardDeclined($details));

                    if ($mainDelegate->assistant_email_address != null) {
                        Mail::to($mainDelegate->assistant_email_address)->send(new RegistrationCardDeclined($details));
                    }

                    $additionalDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegateId)->get();

                    if (!$additionalDelegates->isEmpty()) {
                        foreach ($additionalDelegates as $additionalDelegate) {
                            $details = [
                                'name' => $additionalDelegate->salutation . " " . $additionalDelegate->first_name . " " . $additionalDelegate->middle_name . " " . $additionalDelegate->last_name,
                                'eventLink' => $event->link,
                                'eventName' => $event->name,
                                'eventCategory' => $event->category,
                                'eventDates' => $eventFormattedData,
                                'eventLocation' => $event->location,
                                'bankDetails' => $bankDetails,
                                'invoiceLink' => $invoiceLink,
                                'eventYear' => $event->year,
                            ];
                            Mail::to($additionalDelegate->email_address)->cc($ccEmailNotif)->send(new RegistrationCardDeclined($details));
                        }
                    }
                    return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "failed"]);
                }
            } else {
                // (This is the part where we will send them email notification failed and redirect them)
                MainDelegate::find($mainDelegateId)->fill([
                    'registration_status' => "pending",
                    'payment_status' => "unpaid",
                ])->save();

                $mainDelegate = MainDelegate::where('id', $mainDelegateId)->first();
                $event = Event::where('id', $mainDelegate->event_id)->first();
                $eventFormattedData = Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');

                if ($event->category == "AF") {
                    $bankDetails = config('app.bankDetails.AF');
                } else {
                    $bankDetails = config('app.bankDetails.DEFAULT');
                }

                $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                $details = [
                    'name' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                    'eventLink' => $event->link,
                    'eventName' => $event->name,
                    'eventCategory' => $event->category,
                    'eventDates' => $eventFormattedData,
                    'eventLocation' => $event->location,
                    'bankDetails' => $bankDetails,
                    'invoiceLink' => $invoiceLink,
                    'eventYear' => $event->year,
                ];

                if ($event->category == "DAW") {
                    $ccEmailNotif = config('app.ccEmailNotif.daw');
                } else {
                    $ccEmailNotif = config('app.ccEmailNotif.default');
                }

                Mail::to($mainDelegate->email_address)->cc($ccEmailNotif)->send(new RegistrationCardDeclined($details));

                if ($mainDelegate->assistant_email_address != null) {
                    Mail::to($mainDelegate->assistant_email_address)->send(new RegistrationCardDeclined($details));
                }

                $additionalDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegateId)->get();

                if (!$additionalDelegates->isEmpty()) {
                    foreach ($additionalDelegates as $additionalDelegate) {
                        $details = [
                            'name' => $additionalDelegate->salutation . " " . $additionalDelegate->first_name . " " . $additionalDelegate->middle_name . " " . $additionalDelegate->last_name,
                            'eventLink' => $event->link,
                            'eventName' => $event->name,
                            'eventCategory' => $event->category,
                            'eventDates' => $eventFormattedData,
                            'eventLocation' => $event->location,
                            'bankDetails' => $bankDetails,
                            'invoiceLink' => $invoiceLink,
                            'eventYear' => $event->year,
                        ];
                        Mail::to($additionalDelegate->email_address)->cc($ccEmailNotif)->send(new RegistrationCardDeclined($details));
                    }
                }
                return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "failed"]);
            }
        } else if ($registrationFormType == "spouse") {
            $mainDelegateId = request()->query('mainDelegateId');
            $sessionId = request()->query('sessionId');
            if (
                request()->input('response_gatewayRecommendation') == "PROCEED" &&
                request()->input('result') == "SUCCESS" &&
                request()->input('order_id') &&
                request()->input('transaction_id') &&
                request()->query('sessionId') &&
                request()->query('mainDelegateId') &&
                request()->query('registrationFormType')
            ) {
                $orderId = request()->input('order_id');
                $oldTransactionId = request()->input('transaction_id');
                $newTransactionId = substr(uniqid(), -8);

                $apiEndpoint = env('MERCHANT_API_URL');
                $merchantId = env('MERCHANT_ID');
                $authPass = env('MERCHANT_AUTH_PASSWORD');

                $client = new Client();
                $response = $client->request('PUT', $apiEndpoint . '/order/' . $orderId . '/transaction/' . $newTransactionId, [
                    'auth' => [
                        'merchant.' . $merchantId,
                        $authPass,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'apiOperation' => "PAY",
                        "authentication" => [
                            "transactionId" => $oldTransactionId,
                        ],
                        "order" => [
                            "reference" => $orderId,
                        ],
                        "session" => [
                            'id' => $sessionId,
                        ],
                        "transaction" => [
                            "reference" => $orderId,
                        ],
                    ]
                ]);
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                if (
                    $data['response']['gatewayCode'] == "APPROVED" &&
                    $data['response']['gatewayRecommendation'] == "NO_ACTION" &&
                    $data['transaction']['authenticationStatus'] == "AUTHENTICATION_SUCCESSFUL" &&
                    $data['transaction']['type'] == "PAYMENT"
                ) {
                    MainSpouse::find($mainDelegateId)->fill([
                        'registration_status' => "confirmed",
                        'payment_status' => "paid",
                        'paid_date_time' => Carbon::now(),

                        'registration_confirmation_sent_count' => 1,
                        'registration_confirmation_sent_datetime' => Carbon::now(),
                    ])->save();

                    $mainSpouse = MainSpouse::where('id', $mainDelegateId)->first();
                    $event = Event::where('id', $mainSpouse->event_id)->first();
                    $eventFormattedData = Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');

                    $transactionId = SpouseTransaction::where('spouse_id', $mainDelegateId)->where('spouse_type', "main")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                        if ($event->category == $eventCategoryC) {
                            $getEventcode = $code;
                        }
                    }

                    $tempTransactionId = "$event->year" . "$getEventcode" . "$lastDigit";
                    $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                    $details1 = [
                        'name' => $mainSpouse->salutation . " " . $mainSpouse->first_name . " " . $mainSpouse->middle_name . " " . $mainSpouse->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $event->location,
                        'eventCategory' => $event->category,
                        'eventYear' => $event->year,

                        'nationality' => $mainSpouse->nationality,
                        'country' => $mainSpouse->country,
                        'city' => $mainSpouse->city,
                        'amountPaid' => $mainSpouse->unit_price,
                        'transactionId' => $tempTransactionId,
                        'invoiceLink' => $invoiceLink,
                        'badgeLink' => env('APP_URL') . "/" . $event->category . "/" . $event->id . "/view-badge" . "/" . "main" . "/" . $mainSpouse->id,
                    ];

                    $details2 = [
                        'name' => $mainSpouse->salutation . " " . $mainSpouse->first_name . " " . $mainSpouse->middle_name . " " . $mainSpouse->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventCategory' => $event->category,
                        'eventYear' => $event->year,

                        'invoiceAmount' => $mainSpouse->total_amount,
                        'amountPaid' => $mainSpouse->total_amount,
                        'balance' => 0,
                        'invoiceLink' => $invoiceLink,
                    ];

                    Mail::to($mainSpouse->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationPaid($details1));
                    Mail::to($mainSpouse->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationPaymentConfirmation($details2));


                    $additionalSpouses = AdditionalSpouse::where('main_spouse_id', $mainDelegateId)->get();

                    if (!$additionalSpouses->isEmpty()) {
                        foreach ($additionalSpouses as $additionalSpouse) {
                            $transactionId = SpouseTransaction::where('spouse_id', $additionalSpouse->id)->where('spouse_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $tempTransactionId = "$event->year" . "$getEventcode" . "$lastDigit";

                            $details1 = [
                                'name' => $additionalSpouse->salutation . " " . $additionalSpouse->first_name . " " . $additionalSpouse->middle_name . " " . $additionalSpouse->last_name,
                                'eventLink' => $event->link,
                                'eventName' => $event->name,
                                'eventDates' => $eventFormattedData,
                                'eventLocation' => $event->location,
                                'eventCategory' => $event->category,
                                'eventYear' => $event->year,

                                'nationality' => $additionalSpouse->nationality,
                                'country' => $additionalSpouse->country,
                                'city' => $additionalSpouse->city,
                                'amountPaid' => $mainSpouse->unit_price,
                                'transactionId' => $tempTransactionId,
                                'invoiceLink' => $invoiceLink,
                                'badgeLink' => env('APP_URL') . "/" . $event->category . "/" . $event->id . "/view-badge" . "/" . "sub" . "/" . $additionalSpouse->id,
                            ];

                            Mail::to($additionalSpouse->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationPaid($details1));
                        }
                    }
                    return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "success"]);
                } else {
                    // (This is the part where we will send them email notification failed and redirect them)
                    MainSpouse::find($mainDelegateId)->fill([
                        'registration_status' => "pending",
                        'payment_status' => "unpaid",
                    ])->save();

                    $mainSpouse = MainSpouse::where('id', $mainDelegateId)->first();
                    $event = Event::where('id', $mainSpouse->event_id)->first();
                    $eventFormattedData = Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');

                    if ($event->category == "AF") {
                        $bankDetails = config('app.bankDetails.AF');
                    } else {
                        $bankDetails = config('app.bankDetails.DEFAULT');
                    }

                    $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                    $details = [
                        'name' => $mainSpouse->salutation . " " . $mainSpouse->first_name . " " . $mainSpouse->middle_name . " " . $mainSpouse->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventCategory' => $event->category,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $event->location,
                        'bankDetails' => $bankDetails,
                        'invoiceLink' => $invoiceLink,
                        'eventYear' => $event->year,
                    ];

                    Mail::to($mainSpouse->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationCardDeclined($details));

                    $additionalSpouses = AdditionalSpouse::where('main_spouse_id', $mainDelegateId)->get();

                    if (!$additionalSpouses->isEmpty()) {
                        foreach ($additionalSpouses as $additionalSpouse) {
                            $details = [
                                'name' => $additionalSpouse->salutation . " " . $additionalSpouse->first_name . " " . $additionalSpouse->middle_name . " " . $additionalSpouse->last_name,
                                'eventLink' => $event->link,
                                'eventName' => $event->name,
                                'eventCategory' => $event->category,
                                'eventDates' => $eventFormattedData,
                                'eventLocation' => $event->location,
                                'bankDetails' => $bankDetails,
                                'invoiceLink' => $invoiceLink,
                                'eventYear' => $event->year,
                            ];
                            Mail::to($additionalSpouse->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationCardDeclined($details));
                        }
                    }
                    return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "failed"]);
                }
            } else {
                // (This is the part where we will send them email notification failed and redirect them)
                MainSpouse::find($mainDelegateId)->fill([
                    'registration_status' => "pending",
                    'payment_status' => "unpaid",
                ])->save();

                $mainSpouse = MainSpouse::where('id', $mainDelegateId)->first();
                $event = Event::where('id', $mainSpouse->event_id)->first();
                $eventFormattedData = Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');

                if ($event->category == "AF") {
                    $bankDetails = config('app.bankDetails.AF');
                } else {
                    $bankDetails = config('app.bankDetails.DEFAULT');
                }

                $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                $details = [
                    'name' => $mainSpouse->salutation . " " . $mainSpouse->first_name . " " . $mainSpouse->middle_name . " " . $mainSpouse->last_name,
                    'eventLink' => $event->link,
                    'eventName' => $event->name,
                    'eventCategory' => $event->category,
                    'eventDates' => $eventFormattedData,
                    'eventLocation' => $event->location,
                    'bankDetails' => $bankDetails,
                    'invoiceLink' => $invoiceLink,
                    'eventYear' => $event->year,
                ];

                Mail::to($mainSpouse->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationCardDeclined($details));

                $additionalSpouses = AdditionalSpouse::where('main_spouse_id', $mainDelegateId)->get();

                if (!$additionalSpouses->isEmpty()) {
                    foreach ($additionalSpouses as $additionalSpouse) {
                        $details = [
                            'name' => $additionalSpouse->salutation . " " . $additionalSpouse->first_name . " " . $additionalSpouse->middle_name . " " . $additionalSpouse->last_name,
                            'eventLink' => $event->link,
                            'eventName' => $event->name,
                            'eventCategory' => $event->category,
                            'eventDates' => $eventFormattedData,
                            'eventLocation' => $event->location,
                            'bankDetails' => $bankDetails,
                            'invoiceLink' => $invoiceLink,
                            'eventYear' => $event->year,
                        ];
                        Mail::to($additionalSpouse->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationCardDeclined($details));
                    }
                }
                return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "failed"]);
            }
        } else if ($registrationFormType == "visitor") {
            $mainDelegateId = request()->query('mainDelegateId');
            $sessionId = request()->query('sessionId');
            if (
                request()->input('response_gatewayRecommendation') == "PROCEED" &&
                request()->input('result') == "SUCCESS" &&
                request()->input('order_id') &&
                request()->input('transaction_id') &&
                request()->query('sessionId') &&
                request()->query('mainDelegateId') &&
                request()->query('registrationFormType')
            ) {
                $orderId = request()->input('order_id');
                $oldTransactionId = request()->input('transaction_id');
                $newTransactionId = substr(uniqid(), -8);

                $apiEndpoint = env('MERCHANT_API_URL');
                $merchantId = env('MERCHANT_ID');
                $authPass = env('MERCHANT_AUTH_PASSWORD');

                $client = new Client();
                $response = $client->request('PUT', $apiEndpoint . '/order/' . $orderId . '/transaction/' . $newTransactionId, [
                    'auth' => [
                        'merchant.' . $merchantId,
                        $authPass,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'apiOperation' => "PAY",
                        "authentication" => [
                            "transactionId" => $oldTransactionId,
                        ],
                        "order" => [
                            "reference" => $orderId,
                        ],
                        "session" => [
                            'id' => $sessionId,
                        ],
                        "transaction" => [
                            "reference" => $orderId,
                        ],
                    ]
                ]);
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                if (
                    $data['response']['gatewayCode'] == "APPROVED" &&
                    $data['response']['gatewayRecommendation'] == "NO_ACTION" &&
                    $data['transaction']['authenticationStatus'] == "AUTHENTICATION_SUCCESSFUL" &&
                    $data['transaction']['type'] == "PAYMENT"
                ) {
                    MainVisitor::find($mainDelegateId)->fill([
                        'registration_status' => "confirmed",
                        'payment_status' => "paid",
                        'paid_date_time' => Carbon::now(),

                        'registration_confirmation_sent_count' => 1,
                        'registration_confirmation_sent_datetime' => Carbon::now(),
                    ])->save();

                    $mainVisitor = MainVisitor::where('id', $mainDelegateId)->first();
                    $event = Event::where('id', $mainVisitor->event_id)->first();
                    $eventFormattedData = Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');

                    $transactionId = VisitorTransaction::where('visitor_id', $mainDelegateId)->where('visitor_type', "main")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                        if ($event->category == $eventCategoryC) {
                            $getEventcode = $code;
                        }
                    }

                    $tempTransactionId = "$event->year" . "$getEventcode" . "$lastDigit";
                    $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                    $details1 = [
                        'name' => $mainVisitor->salutation . " " . $mainVisitor->first_name . " " . $mainVisitor->middle_name . " " . $mainVisitor->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $event->location,
                        'eventCategory' => $event->category,
                        'eventYear' => $event->year,

                        'nationality' => $mainVisitor->nationality,
                        'country' => $mainVisitor->country,
                        'city' => $mainVisitor->city,
                        'amountPaid' => $mainVisitor->unit_price,
                        'transactionId' => $tempTransactionId,
                        'invoiceLink' => $invoiceLink,
                        'badgeLink' => env('APP_URL') . "/" . $event->category . "/" . $event->id . "/view-badge" . "/" . "main" . "/" . $mainVisitor->id,
                    ];

                    $details2 = [
                        'name' => $mainVisitor->salutation . " " . $mainVisitor->first_name . " " . $mainVisitor->middle_name . " " . $mainVisitor->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventCategory' => $event->category,
                        'eventYear' => $event->year,

                        'invoiceAmount' => $mainVisitor->total_amount,
                        'amountPaid' => $mainVisitor->total_amount,
                        'balance' => 0,
                        'invoiceLink' => $invoiceLink,
                    ];

                    Mail::to($mainVisitor->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationPaid($details1));
                    Mail::to($mainVisitor->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationPaymentConfirmation($details2));


                    $additionalVisitors = AdditionalVisitor::where('main_visitor_id', $mainDelegateId)->get();

                    if (!$additionalVisitors->isEmpty()) {
                        foreach ($additionalVisitors as $additionalVisitor) {
                            $transactionId = VisitorTransaction::where('visitor_id', $additionalVisitor->id)->where('visitor_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $tempTransactionId = "$event->year" . "$getEventcode" . "$lastDigit";

                            $details1 = [
                                'name' => $additionalVisitor->salutation . " " . $additionalVisitor->first_name . " " . $additionalVisitor->middle_name . " " . $additionalVisitor->last_name,
                                'eventLink' => $event->link,
                                'eventName' => $event->name,
                                'eventDates' => $eventFormattedData,
                                'eventLocation' => $event->location,
                                'eventCategory' => $event->category,
                                'eventYear' => $event->year,

                                'nationality' => $additionalVisitor->nationality,
                                'country' => $additionalVisitor->country,
                                'city' => $additionalVisitor->city,
                                'amountPaid' => $mainVisitor->unit_price,
                                'transactionId' => $tempTransactionId,
                                'invoiceLink' => $invoiceLink,
                                'badgeLink' => env('APP_URL') . "/" . $event->category . "/" . $event->id . "/view-badge" . "/" . "sub" . "/" . $additionalVisitor->id,
                            ];

                            Mail::to($additionalVisitor->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationPaid($details1));
                        }
                    }
                    return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "success"]);
                } else {
                    // (This is the part where we will send them email notification failed and redirect them)
                    MainVisitor::find($mainDelegateId)->fill([
                        'registration_status' => "pending",
                        'payment_status' => "unpaid",
                    ])->save();

                    $mainVisitor = MainVisitor::where('id', $mainDelegateId)->first();
                    $event = Event::where('id', $mainVisitor->event_id)->first();
                    $eventFormattedData = Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');

                    if ($event->category == "AF") {
                        $bankDetails = config('app.bankDetails.AF');
                    } else {
                        $bankDetails = config('app.bankDetails.DEFAULT');
                    }

                    $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                    $details = [
                        'name' => $mainVisitor->salutation . " " . $mainVisitor->first_name . " " . $mainVisitor->middle_name . " " . $mainVisitor->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventCategory' => $event->category,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $event->location,
                        'bankDetails' => $bankDetails,
                        'invoiceLink' => $invoiceLink,
                        'eventYear' => $event->year,
                    ];

                    Mail::to($mainVisitor->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationCardDeclined($details));

                    $additionalVisitors = AdditionalVisitor::where('main_visitor_id', $mainDelegateId)->get();

                    if (!$additionalVisitors->isEmpty()) {
                        foreach ($additionalVisitors as $additionalVisitor) {
                            $details = [
                                'name' => $additionalVisitor->salutation . " " . $additionalVisitor->first_name . " " . $additionalVisitor->middle_name . " " . $additionalVisitor->last_name,
                                'eventLink' => $event->link,
                                'eventName' => $event->name,
                                'eventCategory' => $event->category,
                                'eventDates' => $eventFormattedData,
                                'eventLocation' => $event->location,
                                'bankDetails' => $bankDetails,
                                'invoiceLink' => $invoiceLink,
                                'eventYear' => $event->year,
                            ];
                            Mail::to($additionalVisitor->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationCardDeclined($details));
                        }
                    }
                    return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "failed"]);
                }
            } else {
                // (This is the part where we will send them email notification failed and redirect them)
                MainVisitor::find($mainDelegateId)->fill([
                    'registration_status' => "pending",
                    'payment_status' => "unpaid",
                ])->save();

                $mainVisitor = MainVisitor::where('id', $mainDelegateId)->first();
                $event = Event::where('id', $mainVisitor->event_id)->first();
                $eventFormattedData = Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');

                if ($event->category == "AF") {
                    $bankDetails = config('app.bankDetails.AF');
                } else {
                    $bankDetails = config('app.bankDetails.DEFAULT');
                }

                $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                $details = [
                    'name' => $mainVisitor->salutation . " " . $mainVisitor->first_name . " " . $mainVisitor->middle_name . " " . $mainVisitor->last_name,
                    'eventLink' => $event->link,
                    'eventName' => $event->name,
                    'eventCategory' => $event->category,
                    'eventDates' => $eventFormattedData,
                    'eventLocation' => $event->location,
                    'bankDetails' => $bankDetails,
                    'invoiceLink' => $invoiceLink,
                    'eventYear' => $event->year,
                ];

                Mail::to($mainVisitor->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationCardDeclined($details));

                $additionalVisitors = AdditionalVisitor::where('main_visitor_id', $mainDelegateId)->get();

                if (!$additionalVisitors->isEmpty()) {
                    foreach ($additionalVisitors as $additionalVisitor) {
                        $details = [
                            'name' => $additionalVisitor->salutation . " " . $additionalVisitor->first_name . " " . $additionalVisitor->middle_name . " " . $additionalVisitor->last_name,
                            'eventLink' => $event->link,
                            'eventName' => $event->name,
                            'eventCategory' => $event->category,
                            'eventDates' => $eventFormattedData,
                            'eventLocation' => $event->location,
                            'bankDetails' => $bankDetails,
                            'invoiceLink' => $invoiceLink,
                            'eventYear' => $event->year,
                        ];
                        Mail::to($additionalVisitor->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationCardDeclined($details));
                    }
                }
                return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "failed"]);
            }
        } else {
            $mainDelegateId = request()->query('mainDelegateId');
            $sessionId = request()->query('sessionId');
            if (
                request()->input('response_gatewayRecommendation') == "PROCEED" &&
                request()->input('result') == "SUCCESS" &&
                request()->input('order_id') &&
                request()->input('transaction_id') &&
                request()->query('sessionId') &&
                request()->query('mainDelegateId') &&
                request()->query('registrationFormType')
            ) {
                $orderId = request()->input('order_id');
                $oldTransactionId = request()->input('transaction_id');
                $newTransactionId = substr(uniqid(), -8);

                $apiEndpoint = env('MERCHANT_API_URL');
                $merchantId = env('MERCHANT_ID');
                $authPass = env('MERCHANT_AUTH_PASSWORD');

                $client = new Client();
                $response = $client->request('PUT', $apiEndpoint . '/order/' . $orderId . '/transaction/' . $newTransactionId, [
                    'auth' => [
                        'merchant.' . $merchantId,
                        $authPass,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'apiOperation' => "PAY",
                        "authentication" => [
                            "transactionId" => $oldTransactionId,
                        ],
                        "order" => [
                            "reference" => $orderId,
                        ],
                        "session" => [
                            'id' => $sessionId,
                        ],
                        "transaction" => [
                            "reference" => $orderId,
                        ],
                    ]
                ]);
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                if (
                    $data['response']['gatewayCode'] == "APPROVED" &&
                    $data['response']['gatewayRecommendation'] == "NO_ACTION" &&
                    $data['transaction']['authenticationStatus'] == "AUTHENTICATION_SUCCESSFUL" &&
                    $data['transaction']['type'] == "PAYMENT"
                ) {
                    RccAwardsMainParticipant::find($mainDelegateId)->fill([
                        'registration_status' => "confirmed",
                        'payment_status' => "paid",
                        'paid_date_time' => Carbon::now(),

                        'registration_confirmation_sent_count' => 1,
                        'registration_confirmation_sent_datetime' => Carbon::now(),
                    ])->save();

                    $mainParticipant = RccAwardsMainParticipant::where('id', $mainDelegateId)->first();
                    $event = Event::where('id', $mainParticipant->event_id)->first();

                    $eventFormattedData = Carbon::parse($event->event_start_date)->format('j F Y');

                    $transactionId = RccAwardsParticipantTransaction::where('participant_id', $mainDelegateId)->where('participant_type', "main")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                        if ($event->category == $eventCategoryC) {
                            $getEventcode = $code;
                        }
                    }

                    $tempTransactionId = "$event->year" . "$getEventcode" . "$lastDigit";
                    $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                    $entryFormId = RccAwardsDocument::where('event_id', $event->id)->where('participant_id', $mainDelegateId)->where('document_type', 'entryForm')->value('id');

                    $getSupportingDocumentFiles = RccAwardsDocument::where('event_id', $event->id)->where('participant_id', $mainDelegateId)->where('document_type', 'supportingDocument')->get();

                    $supportingDocumentsDownloadId = [];

                    if ($getSupportingDocumentFiles->isNotEmpty()) {
                        foreach ($getSupportingDocumentFiles as $supportingDocument) {
                            $supportingDocumentsDownloadId[] = $supportingDocument->id;
                        }
                    }

                    $downloadLink = env('APP_URL') . '/download-file/';

                    $details1 = [
                        'name' => $mainParticipant->salutation . " " . $mainParticipant->first_name . " " . $mainParticipant->middle_name . " " . $mainParticipant->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $event->location,
                        'eventCategory' => $event->category,
                        'eventYear' => $event->year,

                        'jobTitle' => $mainParticipant->job_title,
                        'companyName' => $mainParticipant->company_name,
                        'emailAddress' => $mainParticipant->email_address,
                        'mobileNumber' => $mainParticipant->mobile_number,
                        'city' => $mainParticipant->city,
                        'country' => $mainParticipant->country,
                        'category' => $mainParticipant->category,
                        'subCategory' => ($mainParticipant->sub_category != null) ? $mainParticipant->sub_category : 'N/A',
                        'entryFormId' => $entryFormId,
                        'supportingDocumentsDownloadId' => $supportingDocumentsDownloadId,
                        'downloadLink' => $downloadLink,

                        'amountPaid' => $mainParticipant->unit_price,
                        'transactionId' => $tempTransactionId,
                        'invoiceLink' => $invoiceLink,
                        'badgeLink' => env('APP_URL') . "/" . $event->category . "/" . $event->id . "/view-badge" . "/" . "main" . "/" . $mainParticipant->id,
                    ];

                    $details2 = [
                        'name' => $mainParticipant->salutation . " " . $mainParticipant->first_name . " " . $mainParticipant->middle_name . " " . $mainParticipant->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventCategory' => $event->category,
                        'eventYear' => $event->year,

                        'invoiceAmount' => $mainParticipant->total_amount,
                        'amountPaid' => $mainParticipant->total_amount,
                        'balance' => 0,
                        'invoiceLink' => $invoiceLink,
                    ];

                    Mail::to($mainParticipant->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationPaid($details1));
                    Mail::to($mainParticipant->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationPaymentConfirmation($details2));


                    return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "success"]);
                } else {
                    // (This is the part where we will send them email notification failed and redirect them)
                    RccAwardsMainParticipant::find($mainDelegateId)->fill([
                        'registration_status' => "pending",
                        'payment_status' => "unpaid",
                    ])->save();

                    $mainParticipant = RccAwardsMainParticipant::where('id', $mainDelegateId)->first();
                    $event = Event::where('id', $mainParticipant->event_id)->first();
                    $eventFormattedData = Carbon::parse($event->event_start_date)->format('j F Y');

                    if ($event->category == "AF") {
                        $bankDetails = config('app.bankDetails.AF');
                    } else {
                        $bankDetails = config('app.bankDetails.DEFAULT');
                    }

                    $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                    $details = [
                        'name' => $mainParticipant->salutation . " " . $mainParticipant->first_name . " " . $mainParticipant->middle_name . " " . $mainParticipant->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventCategory' => $event->category,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $event->location,
                        'bankDetails' => $bankDetails,
                        'invoiceLink' => $invoiceLink,
                        'eventYear' => $event->year,
                    ];

                    Mail::to($mainParticipant->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationCardDeclined($details));

                    return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "failed"]);
                }
            } else {
                // (This is the part where we will send them email notification failed and redirect them)
                RccAwardsMainParticipant::find($mainDelegateId)->fill([
                    'registration_status' => "pending",
                    'payment_status' => "unpaid",
                ])->save();

                $mainParticipant = RccAwardsMainParticipant::where('id', $mainDelegateId)->first();
                $event = Event::where('id', $mainParticipant->event_id)->first();
                $eventFormattedData = Carbon::parse($event->event_start_date)->format('j F Y');

                if ($event->category == "AF") {
                    $bankDetails = config('app.bankDetails.AF');
                } else {
                    $bankDetails = config('app.bankDetails.DEFAULT');
                }

                $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $mainDelegateId;

                $details = [
                    'name' => $mainParticipant->salutation . " " . $mainParticipant->first_name . " " . $mainParticipant->middle_name . " " . $mainParticipant->last_name,
                    'eventLink' => $event->link,
                    'eventName' => $event->name,
                    'eventCategory' => $event->category,
                    'eventDates' => $eventFormattedData,
                    'eventLocation' => $event->location,
                    'bankDetails' => $bankDetails,
                    'invoiceLink' => $invoiceLink,
                    'eventYear' => $event->year,
                ];

                Mail::to($mainParticipant->email_address)->cc(config('app.ccEmailNotif.default'))->queue(new RegistrationCardDeclined($details));

                return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "failed"]);
            }
        }
    }

    public function downloadFile($documentId)
    {
        if (RccAwardsDocument::where('id', $documentId)->exists()) {
            $documentFilePathTemp = RccAwardsDocument::where('id', $documentId)->value('document');

            $documentFilePath = Str::replace('public', 'storage', $documentFilePathTemp);

            if (!Storage::url($documentFilePath)) {
                abort(404, 'File not found');
            }

            $mimeType = Storage::mimeType($documentFilePath);

            $path = parse_url($documentFilePath, PHP_URL_PATH);
            $filename = basename($path);

            $headers = [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            return Response::download($documentFilePath, $filename, $headers);
        } else {
            abort(404, 'File not found');
        }
    }







    public function registrationFailedViewEvents($eventCategory, $eventId, $mainDelegateId)
    {
        $mainDelegate = MainDelegate::where('id', $mainDelegateId)->first();

        if ($mainDelegate->confirmation_status == "failed" || $mainDelegate->confirmation_date_time == null) {
            $invoiceLink = env('APP_URL') . '/' . $eventCategory . '/' . $eventId . '/view-invoice/' . $mainDelegateId;

            if ($eventCategory == "AF" || $eventCategory == "AFS" || $eventCategory == "AFV") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            if ($mainDelegate->confirmation_date_time == null) {
                MainDelegate::find($mainDelegateId)->fill([
                    'confirmation_date_time' => Carbon::now(),
                    'confirmation_status' => "failed",
                ])->save();
            }

            return [
                'invoiceLink' => $invoiceLink,
                'bankDetails' => $bankDetails,
                'paymentStatus' => $mainDelegate->payment_status,
            ];
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrationFailedViewSpouse($eventCategory, $eventId, $mainSpouseId)
    {
        $mainSpouse = MainSpouse::where('id', $mainSpouseId)->first();

        if ($mainSpouse->confirmation_status == "failed" || $mainSpouse->confirmation_date_time == null) {
            $invoiceLink = env('APP_URL') . '/' . $eventCategory . '/' . $eventId . '/view-invoice/' . $mainSpouseId;

            if ($eventCategory == "AF" || $eventCategory == "AFS"  || $eventCategory == "AFV") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            if ($mainSpouse->confirmation_date_time == null) {
                MainSpouse::find($mainSpouseId)->fill([
                    'confirmation_date_time' => Carbon::now(),
                    'confirmation_status' => "failed",
                ])->save();
            }

            return [
                'invoiceLink' => $invoiceLink,
                'bankDetails' => $bankDetails,
                'paymentStatus' => $mainSpouse->payment_status,
            ];
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrationFailedViewVisitor($eventCategory, $eventId, $mainVisitorId)
    {
        $mainVisitor = MainVisitor::where('id', $mainVisitorId)->first();

        if ($mainVisitor->confirmation_status == "failed" || $mainVisitor->confirmation_date_time == null) {
            $invoiceLink = env('APP_URL') . '/' . $eventCategory . '/' . $eventId . '/view-invoice/' . $mainVisitorId;

            if ($eventCategory == "AF" || $eventCategory == "AFS"  || $eventCategory == "AFV") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            if ($mainVisitor->confirmation_date_time == null) {
                MainVisitor::find($mainVisitorId)->fill([
                    'confirmation_date_time' => Carbon::now(),
                    'confirmation_status' => "failed",
                ])->save();
            }

            return [
                'invoiceLink' => $invoiceLink,
                'bankDetails' => $bankDetails,
                'paymentStatus' => $mainVisitor->payment_status,
            ];
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrationFailedViewRccAwards($eventCategory, $eventId, $mainParticipantId)
    {
        $mainParticipant = RccAwardsMainParticipant::where('id', $mainParticipantId)->first();

        if ($mainParticipant->confirmation_status == "failed" || $mainParticipant->confirmation_date_time == null) {
            $invoiceLink = env('APP_URL') . '/' . $eventCategory . '/' . $eventId . '/view-invoice/' . $mainParticipantId;

            if ($eventCategory == "AF" || $eventCategory == "AFS"  || $eventCategory == "AFV") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            if ($mainParticipant->confirmation_date_time == null) {
                RccAwardsMainParticipant::find($mainParticipantId)->fill([
                    'confirmation_date_time' => Carbon::now(),
                    'confirmation_status' => "failed",
                ])->save();
            }

            return [
                'invoiceLink' => $invoiceLink,
                'bankDetails' => $bankDetails,
                'paymentStatus' => $mainParticipant->payment_status,
            ];
        } else {
            abort(404, 'The URL is incorrect');
        }
    }



    public function registrationSuccessViewEvents($eventCategory, $eventId, $mainDelegateId)
    {
        $mainDelegate = MainDelegate::where('id', $mainDelegateId)->first();

        if ($mainDelegate->confirmation_status == "success" || $mainDelegate->confirmation_date_time == null) {
            $invoiceLink = env('APP_URL') . '/' . $eventCategory . '/' . $eventId . '/view-invoice/' . $mainDelegateId;

            if ($eventCategory == "AF" || $eventCategory == "AFS" || $eventCategory == "AFV") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            if ($mainDelegate->confirmation_date_time == null) {
                MainDelegate::find($mainDelegateId)->fill([
                    'confirmation_date_time' => Carbon::now(),
                    'confirmation_status' => "success",
                ])->save();
            }

            return [
                'invoiceLink' => $invoiceLink,
                'bankDetails' => $bankDetails,
                'paymentStatus' => $mainDelegate->payment_status,
            ];
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrationSuccessViewSpouse($eventCategory, $eventId, $mainSpouseId)
    {
        $mainSpouse = MainSpouse::where('id', $mainSpouseId)->first();

        if ($mainSpouse->confirmation_status == "success" || $mainSpouse->confirmation_date_time == null) {
            $invoiceLink = env('APP_URL') . '/' . $eventCategory . '/' . $eventId . '/view-invoice/' . $mainSpouseId;

            if ($eventCategory == "AF" || $eventCategory == "AFS" || $eventCategory == "AFV") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            if ($mainSpouse->confirmation_date_time == null) {
                MainSpouse::find($mainSpouseId)->fill([
                    'confirmation_date_time' => Carbon::now(),
                    'confirmation_status' => "success",
                ])->save();
            }

            return [
                'invoiceLink' => $invoiceLink,
                'bankDetails' => $bankDetails,
                'paymentStatus' => $mainSpouse->payment_status,
            ];
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrationSuccessViewVisitor($eventCategory, $eventId, $mainVisitorId)
    {
        $mainVisitor = MainVisitor::where('id', $mainVisitorId)->first();

        if ($mainVisitor->confirmation_status == "success" || $mainVisitor->confirmation_date_time == null) {
            $invoiceLink = env('APP_URL') . '/' . $eventCategory . '/' . $eventId . '/view-invoice/' . $mainVisitorId;

            if ($eventCategory == "AF" || $eventCategory == "AFS" || $eventCategory == "AFV") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            if ($mainVisitor->confirmation_date_time == null) {
                MainVisitor::find($mainVisitorId)->fill([
                    'confirmation_date_time' => Carbon::now(),
                    'confirmation_status' => "success",
                ])->save();
            }

            return [
                'invoiceLink' => $invoiceLink,
                'bankDetails' => $bankDetails,
                'paymentStatus' => $mainVisitor->payment_status,
            ];
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrationSuccessViewRccAwards($eventCategory, $eventId, $mainParticipantId)
    {
        $mainParticipant = RccAwardsMainParticipant::where('id', $mainParticipantId)->first();

        if ($mainParticipant->confirmation_status == "success" || $mainParticipant->confirmation_date_time == null) {
            $invoiceLink = env('APP_URL') . '/' . $eventCategory . '/' . $eventId . '/view-invoice/' . $mainParticipantId;

            if ($eventCategory == "AF" || $eventCategory == "AFS" || $eventCategory == "AFV") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            if ($mainParticipant->confirmation_date_time == null) {
                RccAwardsMainParticipant::find($mainParticipantId)->fill([
                    'confirmation_date_time' => Carbon::now(),
                    'confirmation_status' => "success",
                ])->save();
            }

            return [
                'invoiceLink' => $invoiceLink,
                'bankDetails' => $bankDetails,
                'paymentStatus' => $mainParticipant->payment_status,
            ];
        } else {
            abort(404, 'The URL is incorrect');
        }
    }



    public function getInvoiceEvents($eventCategory, $eventId, $registrantId)
    {

        if (MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->exists()) {
            $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();
            $invoiceDetails = array();
            $countFinalQuantity = 0;

            $mainDelegate = MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->first();

            if ($mainDelegate->pass_type == "fullMember") {
                $passType = "Full member";
            } else if ($mainDelegate->pass_type == "member") {
                $passType = "Member";
            } else {
                $passType = "Non-Member";
            }

            $addMainDelegate = true;
            if ($mainDelegate->delegate_cancelled) {
                if ($mainDelegate->delegate_refunded || $mainDelegate->delegate_replaced) {
                    $addMainDelegate = false;
                }
            }

            if ($mainDelegate->delegate_replaced_by_id == null & (!$mainDelegate->delegate_refunded)) {
                $countFinalQuantity++;
            }

            if ($addMainDelegate) {
                $promoCode = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $mainDelegate->pcode_used)->where('badge_type', $mainDelegate->badge_type)->first();

                if ($promoCode != null) {
                    $mainDiscount = $promoCode->discount;
                    $mainDiscountType = $promoCode->discount_type;
                } else {
                    $mainDiscount = 0;
                    $mainDiscountType = null;
                }


                if ($mainDelegate->badge_type == "Leaders of Tomorrow") {
                    $delegateDescription = "Delegate Registration Fee - Leaders of Tomorrow";
                } else {
                    if ($mainDiscount != null) {
                        if ($mainDiscountType == "percentage") {
                            if ($mainDiscount == 100) {
                                $delegateDescription = "Delegate Registration Fee - Complimentary";
                            } else if ($mainDiscount > 0 && $mainDiscount < 100) {
                                $delegateDescription = "Delegate Registration Fee - " . $passType . " discounted rate";
                            } else {
                                $delegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                            }
                        } else {
                            $delegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                        }
                    } else {
                        $delegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                    }
                }

                if ($mainDiscountType == "percentage") {
                    $tempTotalDiscount = $mainDelegate->unit_price * ($mainDiscount / 100);
                    $tempTotalAmount = $mainDelegate->unit_price - ($mainDelegate->unit_price * ($mainDiscount / 100));
                } else {
                    $tempTotalDiscount = $mainDiscount;
                    $tempTotalAmount = $mainDelegate->unit_price - $mainDiscount;
                }

                array_push($invoiceDetails, [
                    'delegateDescription' => $delegateDescription,
                    'delegateNames' => [
                        $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                    ],
                    'badgeType' => $mainDelegate->badge_type,
                    'quantity' => 1,
                    'totalDiscount' => $tempTotalDiscount,
                    'totalNetAmount' =>  $tempTotalAmount,
                    'promoCodeDiscount' => $mainDiscount,
                ]);
            }


            $subDelegates = AdditionalDelegate::where('main_delegate_id', $registrantId)->get();
            if (!$subDelegates->isEmpty()) {
                foreach ($subDelegates as $subDelegate) {

                    if ($subDelegate->delegate_replaced_by_id == null & (!$subDelegate->delegate_refunded)) {
                        $countFinalQuantity++;
                    }

                    $addSubDelegate = true;
                    if ($subDelegate->delegate_cancelled) {
                        if ($subDelegate->delegate_refunded || $subDelegate->delegate_replaced) {
                            $addSubDelegate = false;
                        }
                    }

                    if ($addSubDelegate) {
                        $subPromoCode = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $subDelegate->pcode_used)->where('badge_type', $subDelegate->badge_type)->first();

                        if ($subPromoCode != null) {
                            $subDiscount = $subPromoCode->discount;
                            $subDiscountType = $subPromoCode->discount_type;
                        } else {
                            $subDiscount = 0;
                            $subDiscountType = null;
                        }


                        $checkIfExisting = false;
                        $existingIndex = 0;

                        for ($j = 0; $j < count($invoiceDetails); $j++) {
                            if ($subDelegate->badge_type == $invoiceDetails[$j]['badgeType'] && $subDiscount == $invoiceDetails[$j]['promoCodeDiscount']) {
                                $existingIndex = $j;
                                $checkIfExisting = true;
                                break;
                            }
                        }

                        if ($checkIfExisting) {
                            array_push(
                                $invoiceDetails[$existingIndex]['delegateNames'],
                                $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name
                            );

                            $quantityTemp = $invoiceDetails[$existingIndex]['quantity'] + 1;

                            if ($subDiscountType == "percentage") {
                                $totalDiscountTemp = ($mainDelegate->unit_price * ($invoiceDetails[$existingIndex]['promoCodeDiscount'] / 100)) * $quantityTemp;
                                $totalNetAmountTemp = ($mainDelegate->unit_price * $quantityTemp) - $totalDiscountTemp;
                            } else {
                                $totalDiscountTemp = $invoiceDetails[$existingIndex]['promoCodeDiscount'] * $quantityTemp;
                                $totalNetAmountTemp = ($mainDelegate->unit_price * $quantityTemp) - $totalDiscountTemp;
                            }

                            $invoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                            $invoiceDetails[$existingIndex]['totalDiscount'] = $totalDiscountTemp;
                            $invoiceDetails[$existingIndex]['totalNetAmount'] = $totalNetAmountTemp;
                        } else {

                            if ($subDelegate->badge_type == "Leaders of Tomorrow") {
                                $subDelegateDescription = "Delegate Registration Fee - Leaders of Tomorrow";
                            } else {
                                if ($subDiscount != null) {
                                    if ($subDiscountType == "percentage") {
                                        if ($subDiscount == 100) {
                                            $subDelegateDescription = "Delegate Registration Fee - Complimentary";
                                        } else if ($subDiscount > 0 && $subDiscount < 100) {
                                            $subDelegateDescription = "Delegate Registration Fee - " . $passType . " discounted rate";
                                        } else {
                                            $subDelegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                                        }
                                    } else {
                                        $subDelegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                                    }
                                } else {
                                    $subDelegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                                }
                            }

                            if ($subDiscountType == "percentage") {
                                $tempSubTotalDiscount = $mainDelegate->unit_price * ($subDiscount / 100);
                                $tempSubTotalAmount = $mainDelegate->unit_price - ($mainDelegate->unit_price * ($subDiscount / 100));
                            } else {
                                $tempSubTotalDiscount = $subDiscount;
                                $tempSubTotalAmount = $mainDelegate->unit_price - $subDiscount;
                            }

                            array_push($invoiceDetails, [
                                'delegateDescription' => $subDelegateDescription,
                                'delegateNames' => [
                                    $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name,
                                ],
                                'badgeType' => $subDelegate->badge_type,
                                'quantity' => 1,
                                'totalDiscount' => $tempSubTotalDiscount,
                                'totalNetAmount' =>  $tempSubTotalAmount,
                                'promoCodeDiscount' => $subDiscount,
                            ]);
                        }
                    }
                }
            }

            $transactionId = Transaction::where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->value('id');

            $tempYear = Carbon::parse($mainDelegate->registered_date_time)->format('y');
            $lastDigit = 1000 + intval($transactionId);

            foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                if ($event->category == $eventCategoryC) {
                    $getEventcode = $code;
                }
            }

            $tempInvoiceNumber = "$event->category" . "$tempYear" . "/" . "$lastDigit";
            $tempBookReference = "$event->year" . "$getEventcode" . "$lastDigit";


            if ($eventCategory == "AF") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            $eventFormattedData = Carbon::parse($event->event_start_date)->format('j') . '-' . Carbon::parse($event->event_end_date)->format('j F Y');

            $invoiceData = [
                "finalEventStartDate" => Carbon::parse($event->event_start_date)->format('d M Y'),
                "finalEventEndDate" => Carbon::parse($event->event_end_date)->format('d M Y'),
                "eventFormattedData" => $eventFormattedData,
                "companyName" => $mainDelegate->company_name,
                "companyAddress" => $mainDelegate->company_address,
                "companyCity" => $mainDelegate->company_city,
                "companyCountry" => $mainDelegate->company_country,
                "invoiceDate" => Carbon::parse($mainDelegate->registered_date_time)->format('d/m/Y'),
                "invoiceNumber" => $tempInvoiceNumber,
                "bookRefNumber" => $tempBookReference,
                "paymentStatus" => $mainDelegate->payment_status,
                "registrationMethod" => $mainDelegate->registration_method,
                "eventName" => $event->name,
                "eventLocation" => $event->location,
                "eventVat" => $event->event_vat,
                'vat_price' => $mainDelegate->vat_price,
                'net_amount' => $mainDelegate->net_amount,
                'total_amount' => $mainDelegate->total_amount,
                'unit_price' => $mainDelegate->unit_price,
                'invoiceDetails' => $invoiceDetails,
                'bankDetails' => $bankDetails,
                'finalQuantity' => $countFinalQuantity,
                'total_amount_string' => ucwords($this->numberToWords($mainDelegate->total_amount)),
            ];

            return $invoiceData;
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function getInvoiceSpouse($eventCategory, $eventId, $registrantId)
    {
        if (MainSpouse::where('id', $registrantId)->where('event_id', $eventId)->exists()) {
            $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();
            $invoiceDetails = array();
            $countFinalQuantity = 0;

            $mainSpouse = MainSpouse::where('id', $registrantId)->where('event_id', $eventId)->first();

            $addMainSpouse = true;
            if ($mainSpouse->spouse_cancelled) {
                if ($mainSpouse->spouse_refunded || $mainSpouse->spouse_replaced) {
                    $addMainSpouse = false;
                }
            }

            if ($mainSpouse->spouse_replaced_by_id == null & (!$mainSpouse->spouse_refunded)) {
                $countFinalQuantity++;
            }

            if ($addMainSpouse) {
                array_push($invoiceDetails, [
                    'delegateDescription' => "Spouse Registration Fee",
                    'delegateNames' => [
                        $mainSpouse->first_name . " " . $mainSpouse->middle_name . " " . $mainSpouse->last_name,
                    ],
                    'badgeType' => null,
                    'quantity' => 1,
                    'totalDiscount' => 0,
                    'totalNetAmount' =>  $mainSpouse->unit_price,
                    'promoCodeDiscount' => 0,
                ]);
            }


            $subSpouses = AdditionalSpouse::where('main_spouse_id', $registrantId)->get();
            if (!$subSpouses->isEmpty()) {
                foreach ($subSpouses as $subSpouse) {
                    if ($subSpouse->spouse_replaced_by_id == null & (!$subSpouse->spouse_refunded)) {
                        $countFinalQuantity++;
                    }

                    $addSubSpouse = true;
                    if ($subSpouse->spouse_cancelled) {
                        if ($subSpouse->spouse_refunded || $subSpouse->spouse_replaced) {
                            $addSubSpouse = false;
                        }
                    }

                    if ($addSubSpouse) {
                        $existingIndex = 0;

                        if (count($invoiceDetails) == 0) {
                            array_push($invoiceDetails, [
                                'delegateDescription' => "Spouse Registration Fee",
                                'delegateNames' => [
                                    $subSpouse->first_name . " " . $subSpouse->middle_name . " " . $subSpouse->last_name,
                                ],
                                'badgeType' => null,
                                'quantity' => 1,
                                'totalDiscount' => 0,
                                'totalNetAmount' =>  $mainSpouse->unit_price,
                                'promoCodeDiscount' => 0,
                            ]);
                        } else {
                            array_push(
                                $invoiceDetails[$existingIndex]['delegateNames'],
                                $subSpouse->first_name . " " . $subSpouse->middle_name . " " . $subSpouse->last_name
                            );

                            $quantityTemp = $invoiceDetails[$existingIndex]['quantity'] + 1;
                            $invoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                            $invoiceDetails[$existingIndex]['totalNetAmount'] = $mainSpouse->unit_price * $quantityTemp;
                        }
                    }
                }
            }

            $transactionId = SpouseTransaction::where('spouse_id', $mainSpouse->id)->where('spouse_type', "main")->value('id');

            $tempYear = Carbon::parse($mainSpouse->registered_date_time)->format('y');
            $lastDigit = 1000 + intval($transactionId);

            foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                if ($event->category == $eventCategoryC) {
                    $getEventcode = $code;
                }
            }

            $tempInvoiceNumber = "$event->category" . "$tempYear" . "/" . "$lastDigit";
            $tempBookReference = "$event->year" . "$getEventcode" . "$lastDigit";

            if ($eventCategory == "AF") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            $eventFormattedData = Carbon::parse($event->event_start_date)->format('j') . '-' . Carbon::parse($event->event_end_date)->format('j F Y');
            $fullname = $mainSpouse->first_name . ' ' . $mainSpouse->middle_name . ' ' . $mainSpouse->last_name;
            $invoiceData = [
                "finalEventStartDate" => Carbon::parse($event->event_start_date)->format('d M Y'),
                "finalEventEndDate" => Carbon::parse($event->event_end_date)->format('d M Y'),
                "eventFormattedData" => $eventFormattedData,
                "companyName" => $fullname,
                "companyAddress" => $mainSpouse->country,
                "companyCity" => $mainSpouse->city,
                "companyCountry" => null,
                "invoiceDate" => Carbon::parse($mainSpouse->registered_date_time)->format('d/m/Y'),
                "invoiceNumber" => $tempInvoiceNumber,
                "bookRefNumber" => $tempBookReference,
                "paymentStatus" => $mainSpouse->payment_status,
                "eventName" => $event->name,
                "eventLocation" => $event->location,
                "eventVat" => $event->event_vat,
                'vat_price' => $mainSpouse->vat_price,
                'net_amount' => $mainSpouse->net_amount,
                'total_amount' => $mainSpouse->total_amount,
                'unit_price' => $mainSpouse->unit_price,
                'invoiceDetails' => $invoiceDetails,
                'bankDetails' => $bankDetails,
                'finalQuantity' => $countFinalQuantity,
                'total_amount_string' => ucwords($this->numberToWords($mainSpouse->total_amount)),
            ];

            return $invoiceData;
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function getInvoiceVisitor($eventCategory, $eventId, $registrantId)
    {
        if (MainVisitor::where('id', $registrantId)->where('event_id', $eventId)->exists()) {
            $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();
            $invoiceDetails = array();
            $countFinalQuantity = 0;

            $mainVisitor = MainVisitor::where('id', $registrantId)->where('event_id', $eventId)->first();

            $addMainVisitor = true;
            if ($mainVisitor->visitor_cancelled) {
                if ($mainVisitor->visitor_refunded || $mainVisitor->visitor_replaced) {
                    $addMainVisitor = false;
                }
            }

            if ($mainVisitor->visitor_replaced_by_id == null & (!$mainVisitor->visitor_refunded)) {
                $countFinalQuantity++;
            }

            if ($addMainVisitor) {
                array_push($invoiceDetails, [
                    'delegateDescription' => "Visitor Registration Fee",
                    'delegateNames' => [
                        $mainVisitor->first_name . " " . $mainVisitor->middle_name . " " . $mainVisitor->last_name,
                    ],
                    'badgeType' => null,
                    'quantity' => 1,
                    'totalDiscount' => 0,
                    'totalNetAmount' =>  $mainVisitor->unit_price,
                    'promoCodeDiscount' => 0,
                ]);
            }


            $subVisitors = AdditionalVisitor::where('main_visitor_id', $registrantId)->get();
            if (!$subVisitors->isEmpty()) {
                foreach ($subVisitors as $subVisitor) {
                    if ($subVisitor->visitor_replaced_by_id == null & (!$subVisitor->visitor_refunded)) {
                        $countFinalQuantity++;
                    }

                    $addSubVisitor = true;
                    if ($subVisitor->visitor_cancelled) {
                        if ($subVisitor->visitor_refunded || $subVisitor->visitor_replaced) {
                            $addSubVisitor = false;
                        }
                    }

                    if ($addSubVisitor) {
                        $existingIndex = 0;

                        if (count($invoiceDetails) == 0) {
                            array_push($invoiceDetails, [
                                'delegateDescription' => "Visitor Registration Fee",
                                'delegateNames' => [
                                    $subVisitor->first_name . " " . $subVisitor->middle_name . " " . $subVisitor->last_name,
                                ],
                                'badgeType' => null,
                                'quantity' => 1,
                                'totalDiscount' => 0,
                                'totalNetAmount' =>  $mainVisitor->unit_price,
                                'promoCodeDiscount' => 0,
                            ]);
                        } else {
                            array_push(
                                $invoiceDetails[$existingIndex]['delegateNames'],
                                $subVisitor->first_name . " " . $subVisitor->middle_name . " " . $subVisitor->last_name
                            );

                            $quantityTemp = $invoiceDetails[$existingIndex]['quantity'] + 1;
                            $invoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                            $invoiceDetails[$existingIndex]['totalNetAmount'] = $mainVisitor->unit_price * $quantityTemp;
                        }
                    }
                }
            }

            $transactionId = VisitorTransaction::where('visitor_id', $mainVisitor->id)->where('visitor_type', "main")->value('id');

            $tempYear = Carbon::parse($mainVisitor->registered_date_time)->format('y');
            $lastDigit = 1000 + intval($transactionId);

            foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                if ($event->category == $eventCategoryC) {
                    $getEventcode = $code;
                }
            }

            $tempInvoiceNumber = "$event->category" . "$tempYear" . "/" . "$lastDigit";
            $tempBookReference = "$event->year" . "$getEventcode" . "$lastDigit";

            if ($eventCategory == "AF") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            $eventFormattedData = Carbon::parse($event->event_start_date)->format('j') . '-' . Carbon::parse($event->event_end_date)->format('j F Y');
            $fullname = $mainVisitor->first_name . ' ' . $mainVisitor->middle_name . ' ' . $mainVisitor->last_name;
            $invoiceData = [
                "finalEventStartDate" => Carbon::parse($event->event_start_date)->format('d M Y'),
                "finalEventEndDate" => Carbon::parse($event->event_end_date)->format('d M Y'),
                "eventFormattedData" => $eventFormattedData,
                "companyName" => $fullname,
                "companyAddress" => $mainVisitor->country,
                "companyCity" => $mainVisitor->city,
                "companyCountry" => null,
                "invoiceDate" => Carbon::parse($mainVisitor->registered_date_time)->format('d/m/Y'),
                "invoiceNumber" => $tempInvoiceNumber,
                "bookRefNumber" => $tempBookReference,
                "paymentStatus" => $mainVisitor->payment_status,
                "eventName" => $event->name,
                "eventLocation" => $event->location,
                "eventVat" => $event->event_vat,
                'vat_price' => $mainVisitor->vat_price,
                'net_amount' => $mainVisitor->net_amount,
                'total_amount' => $mainVisitor->total_amount,
                'unit_price' => $mainVisitor->unit_price,
                'invoiceDetails' => $invoiceDetails,
                'bankDetails' => $bankDetails,
                'finalQuantity' => $countFinalQuantity,
                'total_amount_string' => ucwords($this->numberToWords($mainVisitor->total_amount)),
            ];

            return $invoiceData;
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function getInvoiceRccAwards($eventCategory, $eventId, $registrantId)
    {
        if (RccAwardsMainParticipant::where('id', $registrantId)->where('event_id', $eventId)->exists()) {
            $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();
            $invoiceDetails = array();
            $countFinalQuantity = 0;

            $mainParticipant = RccAwardsMainParticipant::where('id', $registrantId)->where('event_id', $eventId)->first();

            $addMainParticipant = true;
            if ($mainParticipant->participant_cancelled) {
                if ($mainParticipant->participant_refunded || $mainParticipant->participant_replaced) {
                    $addMainParticipant = false;
                }
            }

            if ($mainParticipant->participant_replaced_by_id == null & (!$mainParticipant->participant_refunded)) {
                $countFinalQuantity++;
            }

            if ($addMainParticipant) {
                array_push($invoiceDetails, [
                    'delegateDescription' => "Awards Submission fee",
                    // 'delegateNames' => [
                    //     $mainParticipant->first_name . " " . $mainParticipant->middle_name . " " . $mainParticipant->last_name,
                    // ],
                    'delegateNames' => [
                        "Category: " . $mainParticipant->category,
                    ],
                    'badgeType' => null,
                    'quantity' => 1,
                    'totalDiscount' => 0,
                    'totalNetAmount' =>  $mainParticipant->unit_price,
                    'promoCodeDiscount' => 0,
                ]);
            }


            $subParticipants = RccAwardsAdditionalParticipant::where('main_participant_id', $registrantId)->get();
            if (!$subParticipants->isEmpty()) {
                foreach ($subParticipants as $subParticipant) {
                    if ($subParticipant->participant_replaced_by_id == null & (!$subParticipant->participant_refunded)) {
                        $countFinalQuantity++;
                    }

                    $addSubParticipant = true;
                    if ($subParticipant->participant_cancelled) {
                        if ($subParticipant->participant_refunded || $subParticipant->participant_replaced) {
                            $addSubParticipant = false;
                        }
                    }

                    if ($addSubParticipant) {
                        $existingIndex = 0;

                        if (count($invoiceDetails) == 0) {
                            array_push($invoiceDetails, [
                                'delegateDescription' => "Awards Submission fee",
                                // 'delegateNames' => [
                                //     $subParticipant->first_name . " " . $subParticipant->middle_name . " " . $subParticipant->last_name,
                                // ],
                                'delegateNames' => [
                                    "Category: " . $mainParticipant->category,
                                ],
                                'badgeType' => null,
                                'quantity' => 1,
                                'totalDiscount' => 0,
                                'totalNetAmount' =>  $mainParticipant->unit_price,
                                'promoCodeDiscount' => 0,
                            ]);
                        }
                    }
                }
            }

            $transactionId = RccAwardsParticipantTransaction::where('participant_id', $mainParticipant->id)->where('participant_type', "main")->value('id');

            $tempYear = Carbon::parse($mainParticipant->registered_date_time)->format('y');
            $lastDigit = 1000 + intval($transactionId);

            foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                if ($event->category == $eventCategoryC) {
                    $getEventcode = $code;
                }
            }

            $tempInvoiceNumber = "$event->category" . "$tempYear" . "/" . "$lastDigit";
            $tempBookReference = "$event->year" . "$getEventcode" . "$lastDigit";

            if ($eventCategory == "AF") {
                $bankDetails = config('app.bankDetails.AF');
            } else {
                $bankDetails = config('app.bankDetails.DEFAULT');
            }

            $eventFormattedData = Carbon::parse($event->event_start_date)->format('j F Y');
            $fullname = $mainParticipant->first_name . ' ' . $mainParticipant->middle_name . ' ' . $mainParticipant->last_name;
            $invoiceData = [
                "finalEventStartDate" => Carbon::parse($event->event_start_date)->format('d M Y'),
                "finalEventEndDate" => Carbon::parse($event->event_end_date)->format('d M Y'),
                "eventFormattedData" => $eventFormattedData,
                "companyName" => $mainParticipant->company_name,
                "companyAddress" => $fullname,
                "companyCity" => null,
                "companyCountry" => null,
                "invoiceDate" => Carbon::parse($mainParticipant->registered_date_time)->format('d/m/Y'),
                "invoiceNumber" => $tempInvoiceNumber,
                "bookRefNumber" => $tempBookReference,
                "paymentStatus" => $mainParticipant->payment_status,
                "eventName" => $event->name,
                "eventLocation" => $event->location,
                "eventVat" => $event->event_vat,
                'vat_price' => $mainParticipant->vat_price,
                'net_amount' => $mainParticipant->net_amount,
                'total_amount' => $mainParticipant->total_amount,
                'unit_price' => $mainParticipant->unit_price,
                'invoiceDetails' => $invoiceDetails,
                'bankDetails' => $bankDetails,
                'finalQuantity' => $countFinalQuantity,
                'total_amount_string' => ucwords($this->numberToWords($mainParticipant->total_amount)),
            ];

            return $invoiceData;
        } else {
            abort(404, 'The URL is incorrect');
        }
    }



    public function eventRegistrantsExportData($eventCategory, $eventId)
    {
        $finalExcelData = array();
        $event = Event::where('id', $eventId)->where('category', $eventCategory)->first();

        $mainDelegates = MainDelegate::where('event_id', $eventId)->get();
        if (!$mainDelegates->isEmpty()) {
            foreach ($mainDelegates as $mainDelegate) {
                $mainTransactionId = Transaction::where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->value('id');

                $tempYear = Carbon::parse($mainDelegate->registered_date_time)->format('y');
                $lastDigit = 1000 + intval($mainTransactionId);

                foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                    if ($event->category == $eventCategoryC) {
                        $getEventcode = $code;
                    }
                }

                $tempInvoiceNumber = "$event->category" . "$tempYear" . "/" . "$lastDigit";
                $tempBookReference = "$event->year" . "$getEventcode" . "$lastDigit";

                $promoCodeDiscount = null;
                $discountPrice = 0.0;
                $netAMount = 0.0;

                $promoCode = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $mainDelegate->pcode_used)->first();

                if ($promoCode  != null) {
                    $promoCodeDiscount = $promoCode->discount;
                    $discountType = $promoCode->discount_type;

                    if ($discountType == "percentage") {
                        $discountPrice = $mainDelegate->unit_price * ($promoCodeDiscount / 100);
                        $netAMount = $mainDelegate->unit_price - $discountPrice;
                    } else {
                        $discountPrice = $promoCodeDiscount;
                        $netAMount = $mainDelegate->unit_price - $discountPrice;
                    }
                } else {
                    $discountPrice = 0.0;
                    $netAMount = $mainDelegate->unit_price;
                }

                $printedBadgeCount = 0;
                $printedBadgeDateTime = null;

                $printedBadges = PrintedBadge::where('event_id', $eventId)->where('delegate_id', $mainDelegate->id)->where('delegate_type', 'main')->get();

                if($printedBadges->isNotEmpty()){
                    foreach($printedBadges as $printedBadge){
                        $printedBadgeCount++;
                        $printedBadgeDateTime = $printedBadge->printed_date_time;
                    }
                }

                array_push($finalExcelData, [
                    'transaction_id' => $tempBookReference,
                    'id' => $mainDelegate->id,
                    'delegateType' => 'Main',
                    'event' => $eventCategory,
                    'pass_type' => $mainDelegate->pass_type,
                    'rate_type' => ($netAMount == 0) ? 'Complementary' : $mainDelegate->rate_type,

                    'company_name' => $mainDelegate->company_name,
                    'company_sector' => $mainDelegate->company_sector,
                    'company_address' => $mainDelegate->company_address,
                    'company_city' => $mainDelegate->company_city,
                    'company_country' => $mainDelegate->company_country,
                    'company_telephone_number' => $mainDelegate->company_telephone_number,
                    'company_mobile_number' => $mainDelegate->company_mobile_number,
                    'assistant_email_address' => $mainDelegate->assistant_email_address,

                    'salutation' => $mainDelegate->salutation,
                    'first_name' => $mainDelegate->first_name,
                    'middle_name' => $mainDelegate->middle_name,
                    'last_name' => $mainDelegate->last_name,
                    'email_address' => $mainDelegate->email_address,
                    'mobile_number' => $mainDelegate->mobile_number,
                    'job_title' => $mainDelegate->job_title,
                    'nationality' => $mainDelegate->nationality,
                    'badge_type' => $mainDelegate->badge_type,
                    'pcode_used' => $mainDelegate->pcode_used,

                    'unit_price' => $mainDelegate->unit_price,
                    'discount_price' => $discountPrice,
                    'net_amount' => $netAMount,
                    'printed_badge_count' => $printedBadgeCount,
                    'printed_badge_date_time' => $printedBadgeDateTime,

                    // PLEASE CONTINUE HERE
                    'total_amount' => $mainDelegate->total_amount,
                    'payment_status' => $mainDelegate->payment_status,
                    'registration_status' => $mainDelegate->registration_status,
                    'mode_of_payment' => $mainDelegate->mode_of_payment,
                    'invoice_number' => $tempInvoiceNumber,
                    'reference_number' => $tempBookReference,
                    'registration_date_time' => $mainDelegate->registered_date_time,
                    'paid_date_time' => $mainDelegate->paid_date_time,

                    // NEW june 6 2023
                    'registration_method' => $mainDelegate->registration_method,
                    'transaction_remarks' => $mainDelegate->transaction_remarks,

                    'delegate_cancelled' => $mainDelegate->delegate_cancelled,
                    'delegate_replaced' => $mainDelegate->delegate_replaced,
                    'delegate_refunded' => $mainDelegate->delegate_refunded,

                    'delegate_replaced_type' => null,
                    'delegate_original_from_id' => null,
                    'delegate_replaced_from_id' => null,
                    'delegate_replaced_by_id' => $mainDelegate->delegate_replaced_by_id,

                    'delegate_cancelled_datetime' => $mainDelegate->delegate_cancelled_datetime,
                    'delegate_refunded_datetime' => $mainDelegate->delegate_refunded_datetime,
                    'delegate_replaced_datetime' => $mainDelegate->delegate_replaced_datetime,
                ]);

                $subDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegate->id)->get();

                if (!$subDelegates->isEmpty()) {
                    foreach ($subDelegates as $subDelegate) {
                        $subTransactionId = Transaction::where('delegate_id', $subDelegate->id)->where('delegate_type', "sub")->value('id');

                        $promoCodeDiscount = null;
                        $discountPrice = 0.0;
                        $netAMount = 0.0;

                        $subPromoCode = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $subDelegate->pcode_used)->first();


                        if ($subPromoCode  != null) {
                            $promoCodeDiscount = $subPromoCode->discount;
                            $discountType = $subPromoCode->discount_type;

                            if ($discountType == "percentage") {
                                $discountPrice = $mainDelegate->unit_price * ($promoCodeDiscount / 100);
                                $netAMount = $mainDelegate->unit_price - $discountPrice;
                            } else {
                                $discountPrice = $promoCodeDiscount;
                                $netAMount = $mainDelegate->unit_price - $discountPrice;
                            }
                        } else {
                            $discountPrice = 0.0;
                            $netAMount = $mainDelegate->unit_price;
                        }

                        $lastDigit = 1000 + intval($subTransactionId);
                        $tempBookReferenceSub = "$event->year" . "$getEventcode" . "$lastDigit";
                        
                        $printedBadgeCount = 0;
                        $printedBadgeDateTime = null;

                        $printedBadges = PrintedBadge::where('event_id', $eventId)->where('delegate_id', $subDelegate->id)->where('delegate_type', 'sub')->get();
                        
                        if($printedBadges->isNotEmpty()){
                            foreach($printedBadges as $printedBadge){
                                $printedBadgeCount++;
                                $printedBadgeDateTime = $printedBadge->printed_date_time;
                            }
                        }

                        array_push($finalExcelData, [
                            'transaction_id' => $tempBookReferenceSub,
                            'id' => $subDelegate->id,
                            'delegateType' => 'Sub',
                            'event' => $eventCategory,
                            'pass_type' => $mainDelegate->pass_type,
                            'rate_type' => $mainDelegate->rate_type,

                            'company_name' => $mainDelegate->company_name,
                            'company_sector' => $mainDelegate->company_sector,
                            'company_address' => $mainDelegate->company_address,
                            'company_city' => $mainDelegate->company_city,
                            'company_country' => $mainDelegate->company_country,
                            'company_telephone_number' => $mainDelegate->company_telephone_number,
                            'company_mobile_number' => $mainDelegate->company_mobile_number,
                            'assistant_email_address' => $mainDelegate->assistant_email_address,

                            'salutation' => $subDelegate->salutation,
                            'first_name' => $subDelegate->first_name,
                            'middle_name' => $subDelegate->middle_name,
                            'last_name' => $subDelegate->last_name,
                            'email_address' => $subDelegate->email_address,
                            'mobile_number' => $subDelegate->mobile_number,
                            'job_title' => $subDelegate->job_title,
                            'nationality' => $subDelegate->nationality,
                            'badge_type' => $subDelegate->badge_type,
                            'pcode_used' => $subDelegate->pcode_used,

                            'unit_price' => $mainDelegate->unit_price,
                            'discount_price' => $discountPrice,
                            'net_amount' => $netAMount,
                            'printed_badge_count' => $printedBadgeCount,
                            'printed_badge_date_time' => $printedBadgeDateTime,

                            // PLEASE CONTINUE HERE
                            'total_amount' => $mainDelegate->total_amount,
                            'payment_status' => $mainDelegate->payment_status,
                            'registration_status' => $mainDelegate->registration_status,
                            'mode_of_payment' => $mainDelegate->mode_of_payment,
                            'invoice_number' => $tempInvoiceNumber,
                            'reference_number' => $tempBookReference,
                            'registration_date_time' => $mainDelegate->registered_date_time,
                            'paid_date_time' => $mainDelegate->paid_date_time,

                            // NEW june 6 2023
                            'registration_method' => $mainDelegate->registration_method,
                            'transaction_remarks' => $mainDelegate->transaction_remarks,

                            'delegate_cancelled' => $subDelegate->delegate_cancelled,
                            'delegate_replaced' => $subDelegate->delegate_replaced,
                            'delegate_refunded' => $subDelegate->delegate_refunded,

                            'delegate_replaced_type' => $subDelegate->delegate_replaced_type,
                            'delegate_original_from_id' => $subDelegate->delegate_original_from_id,
                            'delegate_replaced_from_id' => $subDelegate->delegate_replaced_from_id,
                            'delegate_replaced_by_id' => $subDelegate->delegate_replaced_by_id,

                            'delegate_cancelled_datetime' => $subDelegate->delegate_cancelled_datetime,
                            'delegate_refunded_datetime' => $subDelegate->delegate_refunded_datetime,
                            'delegate_replaced_datetime' => $subDelegate->delegate_replaced_datetime,
                        ]);
                    }
                }
            }
        }
        $currentDate = Carbon::now()->format('Y-m-d');
        $fileName = $eventCategory . ' ' . $event->year . ' Transactions ' . '[' . $currentDate . '].csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array(
            'Transaction Id',
            'ID',
            'Delegate Type',
            'Event',
            'Pass Type',
            'Rate Type',

            'Promo Code used',
            'Badge Type',
            'Salutation',
            'First Name',
            'Last Name',
            'Email Address',
            'Mobile Number 1',
            'Job Title',
            'Nationality',

            'Company Name',
            'Company Address',
            'City',
            'Country',
            'Telephone Number',
            'Mobile Number 2',
            'Assistant Email Address',

            'Middle Name',

            'Unit Price',
            'Discount Price',
            'Total Amount',
            'Payment Status',
            'Registration Status',
            'Payment method',
            'Invoice Number',
            'Reference Number',
            'Registered Date & Time',
            'Paid Date & Time',
            'Printed badge count',
            'Printed badge date time',

            'Company Sector',

            'Registration Method',
            'Transaction Remarks',

            'Delegate Cancelled',
            'Delegate Replaced',
            'Delegate Refunded',

            'Delegate Replaced Type',
            'Delegate Original From Id',
            'Delegate Replaced From Id',
            'Delegate Replaced By Id',

            'Delegate Cancelled Date & Time',
            'Delegate Refunded Date & Time',
            'Delegate Replaced Date & Time',

        );

        $callback = function () use ($finalExcelData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($finalExcelData as $data) {
                fputcsv(
                    $file,
                    array(
                        $data['transaction_id'],
                        $data['id'],
                        $data['delegateType'],
                        $data['event'],
                        $data['pass_type'],
                        $data['rate_type'],

                        $data['pcode_used'],
                        $data['badge_type'],

                        $data['salutation'],
                        $data['first_name'],
                        $data['last_name'],
                        $data['email_address'],
                        $data['mobile_number'],
                        $data['job_title'],
                        $data['nationality'],

                        $data['company_name'],
                        $data['company_address'],
                        $data['company_city'],
                        $data['company_country'],
                        $data['company_telephone_number'],
                        $data['company_mobile_number'],
                        $data['assistant_email_address'],

                        $data['middle_name'],

                        $data['unit_price'],
                        $data['discount_price'],
                        $data['net_amount'],
                        $data['payment_status'],
                        $data['registration_status'],
                        $data['mode_of_payment'],
                        $data['invoice_number'],
                        $data['reference_number'],
                        $data['registration_date_time'],
                        $data['paid_date_time'],
                        $data['printed_badge_count'],
                        $data['printed_badge_date_time'],

                        $data['company_sector'],

                        $data['registration_method'],
                        $data['transaction_remarks'],

                        $data['delegate_cancelled'],
                        $data['delegate_replaced'],
                        $data['delegate_refunded'],

                        $data['delegate_replaced_type'],
                        $data['delegate_original_from_id'],
                        $data['delegate_replaced_from_id'],
                        $data['delegate_replaced_by_id'],

                        $data['delegate_cancelled_datetime'],
                        $data['delegate_refunded_datetime'],
                        $data['delegate_replaced_datetime'],

                    )
                );
            }
            fclose($file);
        };
        return [
            'callback' => $callback,
            'headers' => $headers,
        ];
    }

    public function spouseRegistrantsExportData($eventCategory, $eventId)
    {
        $finalExcelData = array();
        $event = Event::where('id', $eventId)->where('category', $eventCategory)->first();

        $mainSpouses = MainSpouse::where('event_id', $eventId)->get();
        if (!$mainSpouses->isEmpty()) {
            foreach ($mainSpouses as $mainSpouse) {
                $mainTransactionId = SpouseTransaction::where('spouse_id', $mainSpouse->id)->where('spouse_type', "main")->value('id');

                $tempYear = Carbon::parse($mainSpouse->registered_date_time)->format('y');
                $lastDigit = 1000 + intval($mainTransactionId);

                foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                    if ($event->category == $eventCategoryC) {
                        $getEventcode = $code;
                    }
                }

                $tempInvoiceNumber = "$event->category" . "$tempYear" . "/" . "$lastDigit";
                $tempBookReference = "$event->year" . "$getEventcode" . "$lastDigit";

                $discountPrice = 0.0;
                $netAMount = $mainSpouse->unit_price;

                array_push($finalExcelData, [
                    'transaction_id' => $tempBookReference,
                    'id' => $mainSpouse->id,
                    'spouseType' => 'Main',
                    'event' => $eventCategory,
                    'pass_type' => $mainSpouse->pass_type,
                    'rate_type' => $mainSpouse->rate_type,

                    'reference_delegate_name' => $mainSpouse->reference_delegate_name,

                    'salutation' => $mainSpouse->salutation,
                    'first_name' => $mainSpouse->first_name,
                    'middle_name' => $mainSpouse->middle_name,
                    'last_name' => $mainSpouse->last_name,
                    'email_address' => $mainSpouse->email_address,
                    'mobile_number' => $mainSpouse->mobile_number,
                    'nationality' => $mainSpouse->nationality,
                    'country' => $mainSpouse->country,
                    'city' => $mainSpouse->city,

                    'unit_price' => $mainSpouse->unit_price,
                    'discount_price' => $discountPrice,
                    'net_amount' => $netAMount,
                    'printed_badge_date' => null,

                    // PLEASE CONTINUE HERE
                    'total_amount' => $mainSpouse->total_amount,
                    'payment_status' => $mainSpouse->payment_status,
                    'registration_status' => $mainSpouse->registration_status,
                    'mode_of_payment' => $mainSpouse->mode_of_payment,
                    'invoice_number' => $tempInvoiceNumber,
                    'reference_number' => $tempBookReference,
                    'registration_date_time' => $mainSpouse->registered_date_time,
                    'paid_date_time' => $mainSpouse->paid_date_time,

                    // NEW june 6 2023
                    'registration_method' => $mainSpouse->registration_method,
                    'transaction_remarks' => $mainSpouse->transaction_remarks,

                    'spouse_cancelled' => $mainSpouse->spouse_cancelled,
                    'spouse_replaced' => $mainSpouse->spouse_replaced,
                    'spouse_refunded' => $mainSpouse->spouse_refunded,

                    'spouse_replaced_type' => null,
                    'spouse_original_from_id' => null,
                    'spouse_replaced_from_id' => null,
                    'spouse_replaced_by_id' => $mainSpouse->spouse_replaced_by_id,

                    'spouse_cancelled_datetime' => $mainSpouse->spouse_cancelled_datetime,
                    'spouse_refunded_datetime' => $mainSpouse->spouse_refunded_datetime,
                    'spouse_replaced_datetime' => $mainSpouse->spouse_replaced_datetime,
                ]);

                $subSpouses = AdditionalSpouse::where('main_spouse_id', $mainSpouse->id)->get();

                if (!$subSpouses->isEmpty()) {
                    foreach ($subSpouses as $subSpouse) {
                        $subTransactionId = SpouseTransaction::where('spouse_id', $subSpouse->id)->where('spouse_type', "sub")->value('id');

                        $discountPrice = 0.0;
                        $netAMount = $mainSpouse->unit_price;

                        $lastDigit = 1000 + intval($subTransactionId);
                        $tempBookReferenceSub = "$event->year" . "$getEventcode" . "$lastDigit";

                        array_push($finalExcelData, [
                            'transaction_id' => $tempBookReferenceSub,
                            'id' => $subSpouse->id,
                            'spouseType' => 'Sub',
                            'event' => $eventCategory,
                            'pass_type' => $mainSpouse->pass_type,
                            'rate_type' => $mainSpouse->rate_type,

                            'reference_delegate_name' => $mainSpouse->reference_delegate_name,

                            'salutation' => $subSpouse->salutation,
                            'first_name' => $subSpouse->first_name,
                            'middle_name' => $subSpouse->middle_name,
                            'last_name' => $subSpouse->last_name,
                            'email_address' => $subSpouse->email_address,
                            'mobile_number' => $subSpouse->mobile_number,
                            'nationality' => $subSpouse->nationality,
                            'country' => $subSpouse->country,
                            'city' => $subSpouse->city,

                            'unit_price' => $mainSpouse->unit_price,
                            'discount_price' => $discountPrice,
                            'net_amount' => $netAMount,
                            'printed_badge_date' => null,

                            // PLEASE CONTINUE HERE
                            'total_amount' => $mainSpouse->total_amount,
                            'payment_status' => $mainSpouse->payment_status,
                            'registration_status' => $mainSpouse->registration_status,
                            'mode_of_payment' => $mainSpouse->mode_of_payment,
                            'invoice_number' => $tempInvoiceNumber,
                            'reference_number' => $tempBookReference,
                            'registration_date_time' => $mainSpouse->registered_date_time,
                            'paid_date_time' => $mainSpouse->paid_date_time,

                            // NEW june 6 2023
                            'registration_method' => $mainSpouse->registration_method,
                            'transaction_remarks' => $mainSpouse->transaction_remarks,

                            'spouse_cancelled' => $subSpouse->spouse_cancelled,
                            'spouse_replaced' => $subSpouse->spouse_replaced,
                            'spouse_refunded' => $subSpouse->spouse_refunded,

                            'spouse_replaced_type' => $subSpouse->spouse_replaced_type,
                            'spouse_original_from_id' => $subSpouse->spouse_original_from_id,
                            'spouse_replaced_from_id' => $subSpouse->spouse_replaced_from_id,
                            'spouse_replaced_by_id' => $subSpouse->spouse_replaced_by_id,

                            'spouse_cancelled_datetime' => $subSpouse->spouse_cancelled_datetime,
                            'spouse_refunded_datetime' => $subSpouse->spouse_refunded_datetime,
                            'spouse_replaced_datetime' => $subSpouse->spouse_replaced_datetime,
                        ]);
                    }
                }
            }
        }

        $currentDate = Carbon::now()->format('Y-m-d');
        $fileName = $eventCategory . ' ' . $event->year . ' Transactions ' . '[' . $currentDate . '].csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array(
            'Transaction Id',
            'ID',
            'Spouse Type',
            'Event',
            'Rate Type',

            'Reference Delegate Full Name',

            'Salutation',
            'First Name',
            'Middle Name',
            'Last Name',
            'Email Address',
            'Mobile Number',
            'Nationality',
            'Country',
            'City',

            'Unit Price',
            'Discount Price',
            'Total Amount',
            'Payment Status',
            'Registration Status',
            'Payment method',
            'Invoice Number',
            'Reference Number',
            'Registered Date & Time',
            'Paid Date & Time',
            'Printed badge',

            'Registration Method',
            'Transaction Remarks',

            'Spouse Cancelled',
            'Spouse Replaced',
            'Spouse Refunded',

            'Spouse Replaced Type',
            'Spouse Original From Id',
            'Spouse Replaced From Id',
            'Spouse Replaced By Id',

            'Spouse Cancelled Date & Time',
            'Spouse Refunded Date & Time',
            'Spouse Replaced Date & Time',

        );

        $callback = function () use ($finalExcelData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($finalExcelData as $data) {
                fputcsv(
                    $file,
                    array(
                        $data['transaction_id'],
                        $data['id'],
                        $data['spouseType'],
                        $data['event'],
                        $data['rate_type'],

                        $data['reference_delegate_name'],

                        $data['salutation'],
                        $data['first_name'],
                        $data['middle_name'],
                        $data['last_name'],
                        $data['email_address'],
                        $data['mobile_number'],
                        $data['nationality'],
                        $data['country'],
                        $data['city'],

                        $data['unit_price'],
                        $data['discount_price'],
                        $data['net_amount'],
                        $data['payment_status'],
                        $data['registration_status'],
                        $data['mode_of_payment'],
                        $data['invoice_number'],
                        $data['reference_number'],
                        $data['registration_date_time'],
                        $data['paid_date_time'],
                        $data['printed_badge_date'],

                        $data['registration_method'],
                        $data['transaction_remarks'],

                        $data['spouse_cancelled'],
                        $data['spouse_replaced'],
                        $data['spouse_refunded'],

                        $data['spouse_replaced_type'],
                        $data['spouse_original_from_id'],
                        $data['spouse_replaced_from_id'],
                        $data['spouse_replaced_by_id'],

                        $data['spouse_cancelled_datetime'],
                        $data['spouse_refunded_datetime'],
                        $data['spouse_replaced_datetime'],

                    )
                );
            }
            fclose($file);
        };
        return [
            'callback' => $callback,
            'headers' => $headers,
        ];
    }

    public function visitorRegistrantsExportData($eventCategory, $eventId)
    {
        $finalExcelData = array();
        $event = Event::where('id', $eventId)->where('category', $eventCategory)->first();

        $mainVisitors = MainVisitor::where('event_id', $eventId)->get();
        if (!$mainVisitors->isEmpty()) {
            foreach ($mainVisitors as $mainVisitor) {
                $mainTransactionId = VisitorTransaction::where('visitor_id', $mainVisitor->id)->where('visitor_type', "main")->value('id');

                $tempYear = Carbon::parse($mainVisitor->registered_date_time)->format('y');
                $lastDigit = 1000 + intval($mainTransactionId);

                foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                    if ($event->category == $eventCategoryC) {
                        $getEventcode = $code;
                    }
                }

                $tempInvoiceNumber = "$event->category" . "$tempYear" . "/" . "$lastDigit";
                $tempBookReference = "$event->year" . "$getEventcode" . "$lastDigit";

                $discountPrice = 0.0;
                $netAMount = $mainVisitor->unit_price;

                array_push($finalExcelData, [
                    'transaction_id' => $tempBookReference,
                    'id' => $mainVisitor->id,
                    'visitorType' => 'Main',
                    'event' => $eventCategory,
                    'pass_type' => $mainVisitor->pass_type,
                    'rate_type' => $mainVisitor->rate_type,

                    'salutation' => $mainVisitor->salutation,
                    'first_name' => $mainVisitor->first_name,
                    'middle_name' => $mainVisitor->middle_name,
                    'last_name' => $mainVisitor->last_name,
                    'email_address' => $mainVisitor->email_address,
                    'mobile_number' => $mainVisitor->mobile_number,
                    'nationality' => $mainVisitor->nationality,
                    'country' => $mainVisitor->country,
                    'city' => $mainVisitor->city,

                    'company_name' => $mainVisitor->company_name,
                    'job_title' => $mainVisitor->job_title,

                    'unit_price' => $mainVisitor->unit_price,
                    'discount_price' => $discountPrice,
                    'net_amount' => $netAMount,
                    'printed_badge_date' => null,

                    // PLEASE CONTINUE HERE
                    'total_amount' => $mainVisitor->total_amount,
                    'payment_status' => $mainVisitor->payment_status,
                    'registration_status' => $mainVisitor->registration_status,
                    'mode_of_payment' => $mainVisitor->mode_of_payment,
                    'invoice_number' => $tempInvoiceNumber,
                    'reference_number' => $tempBookReference,
                    'registration_date_time' => $mainVisitor->registered_date_time,
                    'paid_date_time' => $mainVisitor->paid_date_time,

                    // NEW june 6 2023
                    'registration_method' => $mainVisitor->registration_method,
                    'transaction_remarks' => $mainVisitor->transaction_remarks,

                    'visitor_cancelled' => $mainVisitor->visitor_cancelled,
                    'visitor_replaced' => $mainVisitor->visitor_replaced,
                    'visitor_refunded' => $mainVisitor->visitor_refunded,

                    'visitor_replaced_type' => null,
                    'visitor_original_from_id' => null,
                    'visitor_replaced_from_id' => null,
                    'visitor_replaced_by_id' => $mainVisitor->visitor_replaced_by_id,

                    'visitor_cancelled_datetime' => $mainVisitor->visitor_cancelled_datetime,
                    'visitor_refunded_datetime' => $mainVisitor->visitor_refunded_datetime,
                    'visitor_replaced_datetime' => $mainVisitor->visitor_replaced_datetime,
                ]);

                $subVisitors = AdditionalVisitor::where('main_visitor_id', $mainVisitor->id)->get();

                if (!$subVisitors->isEmpty()) {
                    foreach ($subVisitors as $subVisitor) {
                        $subTransactionId = VisitorTransaction::where('visitor_id', $subVisitor->id)->where('visitor_type', "sub")->value('id');

                        $discountPrice = 0.0;
                        $netAMount = $mainVisitor->unit_price;

                        $lastDigit = 1000 + intval($subTransactionId);
                        $tempBookReferenceSub = "$event->year" . "$getEventcode" . "$lastDigit";

                        array_push($finalExcelData, [
                            'transaction_id' => $tempBookReferenceSub,
                            'id' => $subVisitor->id,
                            'visitorType' => 'Sub',
                            'event' => $eventCategory,
                            'pass_type' => $mainVisitor->pass_type,
                            'rate_type' => $mainVisitor->rate_type,

                            'salutation' => $subVisitor->salutation,
                            'first_name' => $subVisitor->first_name,
                            'middle_name' => $subVisitor->middle_name,
                            'last_name' => $subVisitor->last_name,
                            'email_address' => $subVisitor->email_address,
                            'mobile_number' => $subVisitor->mobile_number,
                            'nationality' => $subVisitor->nationality,
                            'country' => $subVisitor->country,
                            'city' => $subVisitor->city,

                            'company_name' => $subVisitor->company_name,
                            'job_title' => $subVisitor->job_title,

                            'unit_price' => $mainVisitor->unit_price,
                            'discount_price' => $discountPrice,
                            'net_amount' => $netAMount,
                            'printed_badge_date' => null,

                            // PLEASE CONTINUE HERE
                            'total_amount' => $mainVisitor->total_amount,
                            'payment_status' => $mainVisitor->payment_status,
                            'registration_status' => $mainVisitor->registration_status,
                            'mode_of_payment' => $mainVisitor->mode_of_payment,
                            'invoice_number' => $tempInvoiceNumber,
                            'reference_number' => $tempBookReference,
                            'registration_date_time' => $mainVisitor->registered_date_time,
                            'paid_date_time' => $mainVisitor->paid_date_time,

                            // NEW june 6 2023
                            'registration_method' => $mainVisitor->registration_method,
                            'transaction_remarks' => $mainVisitor->transaction_remarks,

                            'visitor_cancelled' => $subVisitor->visitor_cancelled,
                            'visitor_replaced' => $subVisitor->visitor_replaced,
                            'visitor_refunded' => $subVisitor->visitor_refunded,

                            'visitor_replaced_type' => $subVisitor->visitor_replaced_type,
                            'visitor_original_from_id' => $subVisitor->visitor_original_from_id,
                            'visitor_replaced_from_id' => $subVisitor->visitor_replaced_from_id,
                            'visitor_replaced_by_id' => $subVisitor->visitor_replaced_by_id,

                            'visitor_cancelled_datetime' => $subVisitor->visitor_cancelled_datetime,
                            'visitor_refunded_datetime' => $subVisitor->visitor_refunded_datetime,
                            'visitor_replaced_datetime' => $subVisitor->visitor_replaced_datetime,
                        ]);
                    }
                }
            }
        }

        $currentDate = Carbon::now()->format('Y-m-d');
        $fileName = $eventCategory . ' ' . $event->year . ' Transactions ' . '[' . $currentDate . '].csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array(
            'Transaction Id',
            'ID',
            'Visitor Type',
            'Event',
            'Rate Type',

            'Salutation',
            'First Name',
            'Middle Name',
            'Last Name',
            'Email Address',
            'Mobile Number',
            'Nationality',
            'Country',
            'City',

            'Company name',
            'Job title',

            'Unit Price',
            'Discount Price',
            'Total Amount',
            'Payment Status',
            'Registration Status',
            'Payment method',
            'Invoice Number',
            'Reference Number',
            'Registered Date & Time',
            'Paid Date & Time',
            'Printed badge',

            'Registration Method',
            'Transaction Remarks',

            'Visitor Cancelled',
            'Visitor Replaced',
            'Visitor Refunded',

            'Visitor Replaced Type',
            'Visitor Original From Id',
            'Visitor Replaced From Id',
            'Visitor Replaced By Id',

            'Visitor Cancelled Date & Time',
            'Visitor Refunded Date & Time',
            'Visitor Replaced Date & Time',

        );

        $callback = function () use ($finalExcelData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($finalExcelData as $data) {
                fputcsv(
                    $file,
                    array(
                        $data['transaction_id'],
                        $data['id'],
                        $data['visitorType'],
                        $data['event'],
                        $data['rate_type'],

                        $data['salutation'],
                        $data['first_name'],
                        $data['middle_name'],
                        $data['last_name'],
                        $data['email_address'],
                        $data['mobile_number'],
                        $data['nationality'],
                        $data['country'],
                        $data['city'],

                        $data['company_name'],
                        $data['job_title'],

                        $data['unit_price'],
                        $data['discount_price'],
                        $data['net_amount'],
                        $data['payment_status'],
                        $data['registration_status'],
                        $data['mode_of_payment'],
                        $data['invoice_number'],
                        $data['reference_number'],
                        $data['registration_date_time'],
                        $data['paid_date_time'],
                        $data['printed_badge_date'],

                        $data['registration_method'],
                        $data['transaction_remarks'],

                        $data['visitor_cancelled'],
                        $data['visitor_replaced'],
                        $data['visitor_refunded'],

                        $data['visitor_replaced_type'],
                        $data['visitor_original_from_id'],
                        $data['visitor_replaced_from_id'],
                        $data['visitor_replaced_by_id'],

                        $data['visitor_cancelled_datetime'],
                        $data['visitor_refunded_datetime'],
                        $data['visitor_replaced_datetime'],

                    )
                );
            }
            fclose($file);
        };
        return [
            'callback' => $callback,
            'headers' => $headers,
        ];
    }

    public function rccAwardsRegistrantsExportData($eventCategory, $eventId)
    {
        $finalExcelData = array();
        $event = Event::where('id', $eventId)->where('category', $eventCategory)->first();

        $mainParticipants = RccAwardsMainParticipant::where('event_id', $eventId)->get();
        if (!$mainParticipants->isEmpty()) {
            foreach ($mainParticipants as $mainParticipant) {
                $mainTransactionId = RccAwardsParticipantTransaction::where('participant_id', $mainParticipant->id)->where('participant_type', "main")->value('id');

                $tempYear = Carbon::parse($mainParticipant->registered_date_time)->format('y');
                $lastDigit = 1000 + intval($mainTransactionId);

                foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                    if ($event->category == $eventCategoryC) {
                        $getEventcode = $code;
                    }
                }

                $tempInvoiceNumber = "$event->category" . "$tempYear" . "/" . "$lastDigit";
                $tempBookReference = "$event->year" . "$getEventcode" . "$lastDigit";

                $discountPrice = 0.0;
                $netAMount = $mainParticipant->unit_price;

                $entryFormId = RccAwardsDocument::where('event_id', $eventId)->where('participant_id', $mainParticipant->id)->where('document_type', 'entryForm')->value('id');


                $getSupportingDocumentFiles = RccAwardsDocument::where('event_id', $eventId)->where('participant_id', $mainParticipant->id)->where('document_type', 'supportingDocument')->get();

                $supportingDocumentLinks = [];

                if ($getSupportingDocumentFiles->isNotEmpty()) {
                    foreach ($getSupportingDocumentFiles as $supportingDocument) {
                        $supportingDocumentLink = env('APP_URL') . '/download-file/' . $supportingDocument->id;
                        $supportingDocumentLinks[] = $supportingDocumentLink;
                    }
                }

                $entryFormDownloadLink = env('APP_URL') . '/download-file/' . $entryFormId;

                array_push($finalExcelData, [
                    'transaction_id' => $tempBookReference,
                    'id' => $mainParticipant->id,
                    'participantType' => 'Main',
                    'event' => $eventCategory,
                    'pass_type' => $mainParticipant->pass_type,
                    'rate_type' => $mainParticipant->rate_type,

                    'category' => $mainParticipant->category,
                    'sub_category' => ($mainParticipant->sub_category == null) ? 'N/A' : $mainParticipant->sub_category,
                    'company_name' => $mainParticipant->company_name,

                    'salutation' => $mainParticipant->salutation,
                    'first_name' => $mainParticipant->first_name,
                    'middle_name' => $mainParticipant->middle_name,
                    'last_name' => $mainParticipant->last_name,
                    'email_address' => $mainParticipant->email_address,
                    'mobile_number' => $mainParticipant->mobile_number,
                    'address' => $mainParticipant->address,
                    'country' => $mainParticipant->country,
                    'city' => $mainParticipant->city,
                    'job_title' => $mainParticipant->job_title,

                    'entryFormDownloadLink' => $entryFormDownloadLink,
                    'supportingDocumentLinks' => $supportingDocumentLinks,

                    'unit_price' => $mainParticipant->unit_price,
                    'discount_price' => $discountPrice,
                    'net_amount' => $netAMount,
                    'printed_badge_date' => null,

                    // PLEASE CONTINUE HERE
                    'total_amount' => $mainParticipant->total_amount,
                    'payment_status' => $mainParticipant->payment_status,
                    'registration_status' => $mainParticipant->registration_status,
                    'mode_of_payment' => $mainParticipant->mode_of_payment,
                    'invoice_number' => $tempInvoiceNumber,
                    'reference_number' => $tempBookReference,
                    'registration_date_time' => $mainParticipant->registered_date_time,
                    'paid_date_time' => $mainParticipant->paid_date_time,

                    // NEW june 6 2023
                    'registration_method' => $mainParticipant->registration_method,
                    'transaction_remarks' => $mainParticipant->transaction_remarks,

                    'participant_cancelled' => $mainParticipant->participant_cancelled,
                    'participant_replaced' => $mainParticipant->participant_replaced,
                    'participant_refunded' => $mainParticipant->participant_refunded,

                    'participant_replaced_type' => null,
                    'participant_original_from_id' => null,
                    'participant_replaced_from_id' => null,
                    'participant_replaced_by_id' => $mainParticipant->participant_replaced_by_id,

                    'participant_cancelled_datetime' => $mainParticipant->participant_cancelled_datetime,
                    'participant_refunded_datetime' => $mainParticipant->participant_refunded_datetime,
                    'participant_replaced_datetime' => $mainParticipant->participant_replaced_datetime,
                ]);

                $subParticipants = RccAwardsAdditionalParticipant::where('main_participant_id', $mainParticipant->id)->get();

                if (!$subParticipants->isEmpty()) {
                    foreach ($subParticipants as $subParticipant) {
                        $subTransactionId = RccAwardsParticipantTransaction::where('participant_id', $subParticipant->id)->where('participant_type', "sub")->value('id');

                        $discountPrice = 0.0;
                        $netAMount = $mainParticipant->unit_price;

                        $lastDigit = 1000 + intval($subTransactionId);
                        $tempBookReferenceSub = "$event->year" . "$getEventcode" . "$lastDigit";

                        array_push($finalExcelData, [
                            'transaction_id' => $tempBookReferenceSub,
                            'id' => $subParticipant->id,
                            'participantType' => 'Sub',
                            'event' => $eventCategory,
                            'pass_type' => $mainParticipant->pass_type,
                            'rate_type' => $mainParticipant->rate_type,

                            'category' => $mainParticipant->category,
                            'sub_category' => ($mainParticipant->sub_category == null) ? 'N/A' : $mainParticipant->sub_category,
                            'company_name' => $mainParticipant->company_name,

                            'salutation' => $subParticipant->salutation,
                            'first_name' => $subParticipant->first_name,
                            'middle_name' => $subParticipant->middle_name,
                            'last_name' => $subParticipant->last_name,
                            'email_address' => $subParticipant->email_address,
                            'mobile_number' => $subParticipant->mobile_number,
                            'address' => $subParticipant->address,
                            'country' => $subParticipant->country,
                            'city' => $subParticipant->city,
                            'job_title' => $subParticipant->job_title,

                            'entryFormDownloadLink' => $entryFormDownloadLink,
                            'supportingDocumentLinks' => $supportingDocumentLinks,

                            'unit_price' => $mainParticipant->unit_price,
                            'discount_price' => $discountPrice,
                            'net_amount' => $netAMount,
                            'printed_badge_date' => null,

                            // PLEASE CONTINUE HERE
                            'total_amount' => $mainParticipant->total_amount,
                            'payment_status' => $mainParticipant->payment_status,
                            'registration_status' => $mainParticipant->registration_status,
                            'mode_of_payment' => $mainParticipant->mode_of_payment,
                            'invoice_number' => $tempInvoiceNumber,
                            'reference_number' => $tempBookReference,
                            'registration_date_time' => $mainParticipant->registered_date_time,
                            'paid_date_time' => $mainParticipant->paid_date_time,

                            // NEW june 6 2023
                            'registration_method' => $mainParticipant->registration_method,
                            'transaction_remarks' => $mainParticipant->transaction_remarks,

                            'participant_cancelled' => $subParticipant->participant_cancelled,
                            'participant_replaced' => $subParticipant->participant_replaced,
                            'participant_refunded' => $subParticipant->participant_refunded,

                            'participant_replaced_type' => $subParticipant->participant_replaced_type,
                            'participant_original_from_id' => $subParticipant->participant_original_from_id,
                            'participant_replaced_from_id' => $subParticipant->participant_replaced_from_id,
                            'participant_replaced_by_id' => $subParticipant->participant_replaced_by_id,

                            'participant_cancelled_datetime' => $subParticipant->participant_cancelled_datetime,
                            'participant_refunded_datetime' => $subParticipant->participant_refunded_datetime,
                            'participant_replaced_datetime' => $subParticipant->participant_replaced_datetime,
                        ]);
                    }
                }
            }
        }

        $currentDate = Carbon::now()->format('Y-m-d');
        $fileName = $eventCategory . ' ' . $event->year . ' Transactions ' . '[' . $currentDate . '].csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array(
            'Transaction Id',
            'ID',
            'Participant Type',
            'Event',
            'Pass Type',
            'Rate Type',

            'Category',
            'Sub Category',
            'Company Name',

            'Salutation',
            'First Name',
            'Middle Name',
            'Last Name',
            'Email Address',
            'Mobile Number',
            'Address',
            'Country',
            'City',
            'Job Title',

            'Entry Form',
            'Supporting Document 1',
            'Supporting Document 2',
            'Supporting Document 3',
            'Supporting Document 4',

            'Unit Price',
            'Discount Price',
            'Total Amount',
            'Payment Status',
            'Registration Status',
            'Payment method',
            'Invoice Number',
            'Reference Number',
            'Registered Date & Time',
            'Paid Date & Time',
            'Printed badge',

            'Registration Method',
            'Transaction Remarks',

            'Participant Cancelled',
            'Participant Replaced',
            'Participant Refunded',

            'Participant Replaced Type',
            'Participant Original From Id',
            'Participant Replaced From Id',
            'Participant Replaced By Id',

            'Participant Cancelled Date & Time',
            'Participant Refunded Date & Time',
            'Participant Replaced Date & Time',

        );

        $callback = function () use ($finalExcelData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($finalExcelData as $data) {
                fputcsv(
                    $file,
                    array(
                        $data['transaction_id'],
                        $data['id'],
                        $data['participantType'],
                        $data['event'],
                        $data['pass_type'],
                        $data['rate_type'],

                        $data['category'],
                        $data['sub_category'],
                        $data['company_name'],

                        $data['salutation'],
                        $data['first_name'],
                        $data['middle_name'],
                        $data['last_name'],
                        $data['email_address'],
                        $data['mobile_number'],
                        $data['address'],
                        $data['country'],
                        $data['city'],
                        $data['job_title'],

                        $data['entryFormDownloadLink'],
                        $data['supportingDocumentLinks'][0] ?? 'N/A',
                        $data['supportingDocumentLinks'][1] ?? 'N/A',
                        $data['supportingDocumentLinks'][2] ?? 'N/A',
                        $data['supportingDocumentLinks'][3] ?? 'N/A',

                        $data['unit_price'],
                        $data['discount_price'],
                        $data['net_amount'],
                        $data['payment_status'],
                        $data['registration_status'],
                        $data['mode_of_payment'],
                        $data['invoice_number'],
                        $data['reference_number'],
                        $data['registration_date_time'],
                        $data['paid_date_time'],
                        $data['printed_badge_date'],

                        $data['registration_method'],
                        $data['transaction_remarks'],

                        $data['participant_cancelled'],
                        $data['participant_replaced'],
                        $data['participant_refunded'],

                        $data['participant_replaced_type'],
                        $data['participant_original_from_id'],
                        $data['participant_replaced_from_id'],
                        $data['participant_replaced_by_id'],

                        $data['participant_cancelled_datetime'],
                        $data['participant_refunded_datetime'],
                        $data['participant_replaced_datetime'],
                    )
                );
            }
            fclose($file);
        };

        return [
            'callback' => $callback,
            'headers' => $headers,
        ];
    }
}
