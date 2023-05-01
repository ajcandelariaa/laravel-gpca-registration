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
    public function homepageView()
    {
        $events = Event::where('active', true)->orderBy('event_start_date', 'asc')->get();
        $finalUpcomingEvents = array();
        $finalPastEvents = array();

        if (!$events->isEmpty()) {
            foreach ($events as $event) {
                $eventLink = env('APP_URL') . '/register/' . $event->year . '/' . $event->category . '/' . $event->id;
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d M Y') . ' - ' . Carbon::parse($event->event_end_date)->format('d M Y');

                if (Carbon::parse($event->event_end_date)->isValid()) {
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
        return view('homepage', [
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

                if($mainDelegate->confirmation_date_time == null){
                    MainDelegate::find($mainDelegateId)->fill([
                        'confirmation_date_time' => Carbon::now(),
                        'confirmation_status' => "failed",
                    ])->save();
                }

                return view('registration.registration_failed_message', [
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

                if($mainDelegate->confirmation_date_time == null){
                    MainDelegate::find($mainDelegateId)->fill([
                        'confirmation_date_time' => Carbon::now(),
                        'confirmation_status' => "success",
                    ])->save();
                }

                return view('registration.registration_success_message', [
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

    public function registrationLoading($eventYear, $eventCategory, $eventId, $mainDelegateId, $status)
    {
        $redirectLink = env('APP_URL') . '/register/' . $eventYear . '/' . $eventCategory . '/' . $eventId . '/' . $mainDelegateId . '/' . $status;
        return view('registration.registration_loading', [
            'redirectLink' => $redirectLink,
        ]);
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

                Mail::to($mainDelegate->email_address)->queue(new RegistrationPaid($details1));
                Mail::to($mainDelegate->email_address)->queue(new RegistrationPaymentConfirmation($details2));

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

                Mail::to($mainDelegate->email_address)->queue(new RegistrationCardDeclined($details));

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

            Mail::to($mainDelegate->email_address)->queue(new RegistrationCardDeclined($details));

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
            return view('registration.registration', [
                'pageTitle' => $event->name . " - Registration",
                'event' => $event,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }


    public function eventRegistrantsView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            return view('admin.event.detail.registrants.registrants', [
                "pageTitle" => "Event Registrants",
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
                $invoiceDetails = array();

                $mainDelegate = MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->first();
                $mainDiscount = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $mainDelegate->pcode_used)->where('badge_type', $mainDelegate->badge_type)->value('discount');

                if ($mainDiscount == 100) {
                    $promoCodeMainDiscountString = "($mainDelegate->badge_type Complimentary)";
                } else {
                    $promoCodeMainDiscountString = ($mainDelegate->pcode_used == null) ? '' : "(" . $mainDiscount . "% discount)";
                }

                array_push($invoiceDetails, [
                    'delegateDescription' => "Delegate Registration Fee - {$mainDelegate->rate_type_string} {$promoCodeMainDiscountString}",
                    'delegateNames' => [
                        $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                    ],
                    'badgeType' => $mainDelegate->badge_type,
                    'quantity' => 1,
                    'totalDiscount' => $mainDelegate->unit_price * ($mainDiscount / 100),
                    'totalNetAmount' =>  $mainDelegate->unit_price - ($mainDelegate->unit_price * ($mainDiscount / 100)),
                    'promoCodeDiscount' => $mainDiscount,
                ]);


                $subDelegates = AdditionalDelegate::where('main_delegate_id', $registrantId)->get();
                if (!$subDelegates->isEmpty()) {
                    foreach ($subDelegates as $subDelegate) {

                        $subDiscount = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $subDelegate->pcode_used)->where('badge_type', $subDelegate->badge_type)->value('discount');

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
                        ]);

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
                                $subDelegate->salutation . " " . $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name
                            );

                            $quantityTemp = $invoiceDetails[$existingIndex]['quantity'] + 1;
                            $totalDiscountTemp = ($mainDelegate->unit_price * ($invoiceDetails[$existingIndex]['promoCodeDiscount'] / 100)) * $quantityTemp;
                            $totalNetAmountTemp = ($mainDelegate->unit_price * $quantityTemp) - $totalDiscountTemp;

                            $invoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                            $invoiceDetails[$existingIndex]['totalDiscount'] = $totalDiscountTemp;
                            $invoiceDetails[$existingIndex]['totalNetAmount'] = $totalNetAmountTemp;
                        } else {

                            if ($subDiscount == 100) {
                                $promoCodeSubDiscountString = "($subDelegate->badge_type Complimentary)";
                            } else {
                                $promoCodeSubDiscountString = ($subDelegate->pcode_used == null) ? '' : "(" . $subDiscount . "% discount)";
                            }

                            array_push($invoiceDetails, [
                                'delegateDescription' => "Delegate Registration Fee - {$mainDelegate->rate_type_string} {$promoCodeSubDiscountString}",
                                'delegateNames' => [
                                    $subDelegate->salutation . " " . $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name,
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

                    'mode_of_payment' => $mainDelegate->mode_of_payment,
                    'registration_status' => "$mainDelegate->registration_status",
                    'payment_status' => $mainDelegate->payment_status,
                    'registered_date_time' => Carbon::parse($mainDelegate->registered_date_time)->format('M j, Y g:i A'),
                    'paid_date_time' => ($mainDelegate->paid_date_time == null) ? "N/A" : Carbon::parse($mainDelegate->paid_date_time)->format('M j, Y g:i A'),

                    'subDelegates' => $subDelegatesArray,

                    'invoiceData' => $this->getInvoice($eventCategory, $eventId, $registrantId),
                ];

                return view('admin.event.detail.registrants.registrants_detail', [
                    "pageTitle" => "Event Registrant Details",
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

    public function getInvoice($eventCategory, $eventId, $registrantId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            if (MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->exists()) {
                $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();
                $invoiceDetails = array();

                $mainDelegate = MainDelegate::where('id', $registrantId)->where('event_id', $eventId)->first();
                $mainDiscount = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('promo_code', $mainDelegate->pcode_used)->where('badge_type', $mainDelegate->badge_type)->value('discount');

                if ($mainDiscount == 100) {
                    $promoCodeMainDiscountString = "($mainDelegate->badge_type Complimentary)";
                } else {
                    $promoCodeMainDiscountString = ($mainDelegate->pcode_used == null) ? '' : "(" . $mainDiscount . "% discount)";
                }

                array_push($invoiceDetails, [
                    'delegateDescription' => "Delegate Registration Fee - {$mainDelegate->rate_type_string} {$promoCodeMainDiscountString}",
                    'delegateNames' => [
                        $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                    ],
                    'badgeType' => $mainDelegate->badge_type,
                    'quantity' => 1,
                    'totalDiscount' => $mainDelegate->unit_price * ($mainDiscount / 100),
                    'totalNetAmount' =>  $mainDelegate->unit_price - ($mainDelegate->unit_price * ($mainDiscount / 100)),
                    'promoCodeDiscount' => $mainDiscount,
                ]);


                $subDelegates = AdditionalDelegate::where('main_delegate_id', $registrantId)->get();
                if (!$subDelegates->isEmpty()) {
                    foreach ($subDelegates as $subDelegate) {
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
                                $subDelegate->salutation . " " . $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name
                            );

                            $quantityTemp = $invoiceDetails[$existingIndex]['quantity'] + 1;
                            $totalDiscountTemp = ($mainDelegate->unit_price * ($invoiceDetails[$existingIndex]['promoCodeDiscount'] / 100)) * $quantityTemp;
                            $totalNetAmountTemp = ($mainDelegate->unit_price * $quantityTemp) - $totalDiscountTemp;

                            $invoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                            $invoiceDetails[$existingIndex]['totalDiscount'] = $totalDiscountTemp;
                            $invoiceDetails[$existingIndex]['totalNetAmount'] = $totalNetAmountTemp;
                        } else {
                            if ($subDiscount == 100) {
                                $promoCodeSubDiscountString = "($subDelegate->badge_type Complimentary)";
                            } else {
                                $promoCodeSubDiscountString = ($subDelegate->pcode_used == null) ? '' : "(" . $subDiscount . "% discount)";
                            }

                            array_push($invoiceDetails, [
                                'delegateDescription' => "Delegate Registration Fee - {$mainDelegate->rate_type_string} {$promoCodeSubDiscountString}",
                                'delegateNames' => [
                                    $subDelegate->salutation . " " . $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name,
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

                $invoiceData = [
                    "finalEventStartDate" => Carbon::parse($event->event_start_date)->format('d M Y'),
                    "finalEventEndDate" => Carbon::parse($event->event_end_date)->format('d M Y'),
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

    public function generatePublicInvoice($eventCategory, $eventId, $registrantId)
    {
        $finalData = $this->getInvoice($eventCategory, $eventId, $registrantId);

        if ($finalData['paymentStatus'] == "unpaid") {
            $pdf = Pdf::loadView('admin.event.detail.registrants.invoices.unpaid', $finalData);
        } else {
            $pdf = Pdf::loadView('admin.event.detail.registrants.invoices.paid', $finalData);
        }
        return $pdf->stream('invoice.pdf');
    }

    public function registrantViewInvoice($eventCategory, $eventId, $registrantId)
    {
        $finalData = $this->getInvoice($eventCategory, $eventId, $registrantId);

        if ($finalData['paymentStatus'] == "unpaid") {
            $pdf = Pdf::loadView('admin.event.detail.registrants.invoices.unpaid', $finalData);
        } else {
            $pdf = Pdf::loadView('admin.event.detail.registrants.invoices.paid', $finalData);
        }
        return $pdf->stream('invoice.pdf');
    }

    public function registrantDownloadInvoice($eventCategory, $eventId, $registrantId)
    {
        $finalData = $this->getInvoice($eventCategory, $eventId, $registrantId);

        if ($finalData['paymentStatus'] == "unpaid") {
            $pdf = Pdf::loadView('admin.event.detail.registrants.invoices.unpaid', $finalData);
        } else {
            $pdf = Pdf::loadView('admin.event.detail.registrants.invoices.paid', $finalData);
        }
        return $pdf->download('invoice.pdf');
    }

    public function numberToWords($number)
    {
        $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        return $formatter->format($number);
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
                        'transaction_id' => $mainTransactionId,
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

                            array_push($finalExcelData, [
                                'transaction_id' => $subTransactionId,
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
            );

            $callback = function () use ($finalExcelData, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($finalExcelData as $data) {
                    fputcsv(
                        $file,
                        array(
                            $data['transaction_id'],
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
}
