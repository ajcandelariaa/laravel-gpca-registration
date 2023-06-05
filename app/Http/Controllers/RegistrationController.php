<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationCardDeclined;
use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Models\AdditionalDelegate;
use App\Models\PromoCode;
use App\Models\Event;
use App\Models\MainDelegate;
use App\Models\Transaction;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session as Session;
use NumberFormatter;

class RegistrationController extends Controller
{

    // =========================================================
    //                       RENDER VIEWS
    // =========================================================

    public function homepageView()
    {
        $events = Event::where('active', true)->orderBy('event_start_date', 'asc')->get();
        $finalUpcomingEvents = array();
        $finalPastEvents = array();

        if (!$events->isEmpty()) {
            foreach ($events as $event) {
                $eventLink = env('APP_URL') . '/register/' . $event->year . '/' . $event->category . '/' . $event->id;
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d M Y') . ' - ' . Carbon::parse($event->event_end_date)->format('d M Y');

                $eventEndDate = Carbon::parse($event->event_end_date);

                if (Carbon::now()->lt($eventEndDate->addDay())) {
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
            $mainDelegate = MainDelegate::where('id', $mainDelegateId)->first();

            if ($mainDelegate->confirmation_status == "failed" || $mainDelegate->confirmation_date_time == null) {
                $event = Event::where('id', $eventId)->first();
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');
                $invoiceLink = env('APP_URL') . '/' . $eventCategory . '/' . $eventId . '/view-invoice/' . $mainDelegateId;

                if ($eventCategory == "AF") {
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

                return view('registration.success-messages.registration_failed_message', [
                    'pageTitle' => "Registration Failed",
                    'event' => $event,
                    'mainDelegate' => $mainDelegate,
                    'eventFormattedDate' =>  $eventFormattedDate,
                    'invoiceLink' => $invoiceLink,
                    'bankDetails' => $bankDetails,
                ]);
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function registrationSuccessView($eventYear, $eventCategory, $eventId, $mainDelegateId)
    {
        if (Event::where('year', $eventYear)->where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $mainDelegate = MainDelegate::where('id', $mainDelegateId)->first();

            if ($mainDelegate->confirmation_status == "success" || $mainDelegate->confirmation_date_time == null) {
                $event = Event::where('id', $eventId)->first();
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');
                $invoiceLink = env('APP_URL') . '/' . $eventCategory . '/' . $eventId . '/view-invoice/' . $mainDelegateId;

                if ($eventCategory == "AF") {
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

                return view('registration.success-messages.registration_success_message', [
                    'pageTitle' => "Registration Success",
                    'event' => $event,
                    'mainDelegate' => $mainDelegate,
                    'eventFormattedDate' =>  $eventFormattedDate,
                    'invoiceLink' => $invoiceLink,
                    'bankDetails' => $bankDetails,
                ]);
            } else {
                abort(404, 'The URL is incorrect');
            }
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

            if (Carbon::now()->lt($eventEndDate->addDay())) {
                return view('registration.registration', [
                    'pageTitle' => $event->name . " - Registration",
                    'event' => $event,
                ]);
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

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
            if (MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->exists()) {
                $finalData = array();

                $subDelegatesArray = array();
                $subDelegatesReplacementArray = array();
                $allDelegatesArray = array();
                $allDelegatesArrayTemp = array();

                $countFinalQuantity = 0;

                $eventYear = Event::where('id', $eventId)->value('year');
                $mainDelegate = MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->first();
                $mainDiscount = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $mainDelegate->pcode_used)->where('badge_type', $mainDelegate->badge_type)->value('discount');

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

                        $subDiscount = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $subDelegate->pcode_used)->where('badge_type', $subDelegate->badge_type)->value('discount');

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

                    'invoiceNumber' => $invoiceNumber,
                    'allDelegates' => $allDelegatesArray,

                    'invoiceData' => $this->getInvoice($eventCategory, $eventId, $registrantId),
                ];

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

    public function registrantViewInvoice($eventCategory, $eventId, $registrantId)
    {
        $finalData = $this->getInvoice($eventCategory, $eventId, $registrantId);

        if ($finalData['finalQuantity'] > 0) {
            if ($finalData['paymentStatus'] == "unpaid") {
                $pdf = Pdf::loadView('admin.events.transactions.invoices.unpaid', $finalData);
            } else {
                $pdf = Pdf::loadView('admin.events.transactions.invoices.paid', $finalData);
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
            if ($finalData['paymentStatus'] == "unpaid") {
                $pdf = Pdf::loadView('admin.events.transactions.invoices.unpaid', $finalData);
            } else {
                $pdf = Pdf::loadView('admin.events.transactions.invoices.paid', $finalData);
            }
            return $pdf->stream('invoice.pdf');
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function getInvoice($eventCategory, $eventId, $registrantId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            if (MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->exists()) {
                $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();
                $invoiceDetails = array();
                $countFinalQuantity = 0;

                $mainDelegate = MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->first();

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
                    $mainDiscount = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $mainDelegate->pcode_used)->where('badge_type', $mainDelegate->badge_type)->value('discount');

                    if ($mainDiscount != null) {
                        if ($mainDiscount == 100) {
                            $delegateDescription = "Delegate Registration Fee - Complimentary";
                        } else if ($mainDiscount > 0 && $mainDiscount < 100) {
                            $delegateDescription = "Delegate Registration Fee - " . $mainDelegate->rate_type_string . " (" . $mainDiscount . "% discount)";
                        } else {
                            $delegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                        }
                    } else {
                        $delegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                    }


                    array_push($invoiceDetails, [
                        'delegateDescription' => $delegateDescription,
                        'delegateNames' => [
                            $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                        ],
                        'badgeType' => $mainDelegate->badge_type,
                        'quantity' => 1,
                        'totalDiscount' => $mainDelegate->unit_price * ($mainDiscount / 100),
                        'totalNetAmount' =>  $mainDelegate->unit_price - ($mainDelegate->unit_price * ($mainDiscount / 100)),
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
                            $subDiscount = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $subDelegate->pcode_used)->where('badge_type', $subDelegate->badge_type)->value('discount');

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
                                $totalDiscountTemp = ($mainDelegate->unit_price * ($invoiceDetails[$existingIndex]['promoCodeDiscount'] / 100)) * $quantityTemp;
                                $totalNetAmountTemp = ($mainDelegate->unit_price * $quantityTemp) - $totalDiscountTemp;

                                $invoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                                $invoiceDetails[$existingIndex]['totalDiscount'] = $totalDiscountTemp;
                                $invoiceDetails[$existingIndex]['totalNetAmount'] = $totalNetAmountTemp;
                            } else {

                                if ($subDiscount != null) {
                                    if ($subDiscount == 100) {
                                        $subDelegateDescription = "Delegate Registration Fee - Complimentary";
                                    } else if ($subDiscount > 0 && $subDiscount < 100) {
                                        $subDelegateDescription = "Delegate Registration Fee - " . $mainDelegate->rate_type_string . " (" . $subDiscount . "% discount)";
                                    } else {
                                        $subDelegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                                    }
                                } else {
                                    $subDelegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                                }

                                array_push($invoiceDetails, [
                                    'delegateDescription' => $subDelegateDescription,
                                    'delegateNames' => [
                                        $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name,
                                    ],
                                    'badgeType' => $subDelegate->badge_type,
                                    'quantity' => 1,
                                    'totalDiscount' => $mainDelegate->unit_price * ($subDiscount / 100),
                                    'totalNetAmount' =>  $mainDelegate->unit_price - ($mainDelegate->unit_price * ($subDiscount / 100)),
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

                $eventFormattedData = Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');

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
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function eventRegistrantsExportData($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
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

                    $promoCodeDiscount = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $mainDelegate->pcode_used)->value('discount');

                    if ($promoCodeDiscount  != null) {
                        $discountPrice = $mainDelegate->unit_price * ($promoCodeDiscount / 100);
                        $netAMount = $mainDelegate->unit_price - $discountPrice;
                    } else {
                        $discountPrice = 0.0;
                        $netAMount = $mainDelegate->unit_price;
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
                        'printed_badge_date' => null,

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

                            $promoCodeDiscount = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $subDelegate->pcode_used)->value('discount');

                            if ($promoCodeDiscount  != null) {
                                $discountPrice = $mainDelegate->unit_price * ($promoCodeDiscount / 100);
                                $netAMount = $mainDelegate->unit_price - $discountPrice;
                            } else {
                                $discountPrice = 0.0;
                                $netAMount = $mainDelegate->unit_price;
                            }

                            $lastDigit = 1000 + intval($subTransactionId);
                            $tempBookReferenceSub = "$event->year" . "$getEventcode" . "$lastDigit";

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
                                'printed_badge_date' => null,

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

            $fileName = 'transactions.csv';
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
                'Printed badge',

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
                            $data['printed_badge_date'],

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
            return response()->stream($callback, 200, $headers);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function capturePayment()
    {
        $mainDelegateId = request()->query('mainDelegateId');
        $sessionId = request()->query('sessionId');

        if (
            request()->input('response_gatewayRecommendation') == "PROCEED" &&
            request()->input('result') == "SUCCESS" &&
            request()->input('order_id') &&
            request()->input('transaction_id') &&
            request()->query('sessionId') &&
            request()->query('mainDelegateId')
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

                $details1 = [
                    'name' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                    'eventLink' => $event->link,
                    'eventName' => $event->name,
                    'eventDates' => $eventFormattedData,
                    'eventLocation' => $event->location,
                    'eventCategory' => $event->category,

                    'jobTitle' => $mainDelegate->job_title,
                    'companyName' => $mainDelegate->company_name,
                    'amountPaid' => $mainDelegate->total_amount,
                    'transactionId' => $tempTransactionId,
                    'invoiceLink' => $invoiceLink,
                ];

                $details2 = [
                    'name' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                    'eventLink' => $event->link,
                    'eventName' => $event->name,

                    'invoiceAmount' => $mainDelegate->total_amount,
                    'amountPaid' => $mainDelegate->total_amount,
                    'balance' => 0,
                    'invoiceLink' => $invoiceLink,
                ];

                Mail::to($mainDelegate->email_address)->cc(config('app.ccEmailNotif'))->queue(new RegistrationPaid($details1));
                Mail::to($mainDelegate->email_address)->cc(config('app.ccEmailNotif'))->queue(new RegistrationPaymentConfirmation($details2));

                if ($mainDelegate->assistant_email_address != null) {
                    Mail::to($mainDelegate->assistant_email_address)->queue(new RegistrationPaid($details1));
                    Mail::to($mainDelegate->assistant_email_address)->queue(new RegistrationPaymentConfirmation($details2));
                }

                $additionalDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegateId)->get();

                if (!$additionalDelegates->isEmpty()) {
                    foreach ($additionalDelegates as $additionalDelegate) {
                        $transactionId = Transaction::where('delegate_id', $additionalDelegate->id)->where('delegate_type', "sub")->value('id');
                        $lastDigit = 1000 + intval($transactionId);
                        $tempTransactionId = "$event->year" . "$getEventcode" . "$lastDigit";

                        $details1 = [
                            'name' => $additionalDelegate->salutation . " " . $additionalDelegate->first_name . " " . $additionalDelegate->middle_name . " " . $additionalDelegate->last_name,
                            'eventLink' => $event->link,
                            'eventName' => $event->name,
                            'eventDates' => $eventFormattedData,
                            'eventLocation' => $event->location,
                            'eventCategory' => $event->category,

                            'jobTitle' => $additionalDelegate->job_title,
                            'companyName' => $mainDelegate->company_name,
                            'amountPaid' => $mainDelegate->total_amount,
                            'transactionId' => $tempTransactionId,
                            'invoiceLink' => $invoiceLink,
                        ];

                        $details2 = [
                            'name' => $additionalDelegate->salutation . " " . $additionalDelegate->first_name . " " . $additionalDelegate->middle_name . " " . $additionalDelegate->last_name,
                            'eventLink' => $event->link,
                            'eventName' => $event->name,

                            'invoiceAmount' => $mainDelegate->total_amount,
                            'amountPaid' => $mainDelegate->total_amount,
                            'balance' => 0,
                            'invoiceLink' => $invoiceLink,
                        ];
                        Mail::to($additionalDelegate->email_address)->queue(new RegistrationPaid($details1));
                        Mail::to($additionalDelegate->email_address)->queue(new RegistrationPaymentConfirmation($details2));
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
                    'eventDates' => $eventFormattedData,
                    'eventLocation' => $event->location,
                    'bankDetails' => $bankDetails,
                    'invoiceLink' => $invoiceLink,
                ];

                Mail::to($mainDelegate->email_address)->cc(config('app.ccEmailNotif'))->queue(new RegistrationCardDeclined($details));

                if ($mainDelegate->assistant_email_address != null) {
                    Mail::to($mainDelegate->assistant_email_address)->queue(new RegistrationCardDeclined($details));
                }

                $additionalDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegateId)->get();

                if (!$additionalDelegates->isEmpty()) {
                    foreach ($additionalDelegates as $additionalDelegate) {
                        $details = [
                            'name' => $additionalDelegate->salutation . " " . $additionalDelegate->first_name . " " . $additionalDelegate->middle_name . " " . $additionalDelegate->last_name,
                            'eventLink' => $event->link,
                            'eventName' => $event->name,
                            'eventDates' => $eventFormattedData,
                            'eventLocation' => $event->location,
                            'bankDetails' => $bankDetails,
                            'invoiceLink' => $invoiceLink,
                        ];
                        Mail::to($additionalDelegate->email_address)->queue(new RegistrationCardDeclined($details));
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
                'eventDates' => $eventFormattedData,
                'eventLocation' => $event->location,
                'bankDetails' => $bankDetails,
                'invoiceLink' => $invoiceLink,
            ];

            Mail::to($mainDelegate->email_address)->cc(config('app.ccEmailNotif'))->queue(new RegistrationCardDeclined($details));

            if ($mainDelegate->assistant_email_address != null) {
                Mail::to($mainDelegate->assistant_email_address)->queue(new RegistrationCardDeclined($details));
            }

            $additionalDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegateId)->get();

            if (!$additionalDelegates->isEmpty()) {
                foreach ($additionalDelegates as $additionalDelegate) {
                    $details = [
                        'name' => $additionalDelegate->salutation . " " . $additionalDelegate->first_name . " " . $additionalDelegate->middle_name . " " . $additionalDelegate->last_name,
                        'eventLink' => $event->link,
                        'eventName' => $event->name,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $event->location,
                        'bankDetails' => $bankDetails,
                        'invoiceLink' => $invoiceLink,
                    ];
                    Mail::to($additionalDelegate->email_address)->queue(new RegistrationCardDeclined($details));
                }
            }
            return redirect()->route('register.loading.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventYear' => $event->year, 'mainDelegateId' => $mainDelegateId, 'status' => "failed"]);
        }
    }
}
