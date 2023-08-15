<?php

namespace App\Http\Livewire;

use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Mail\RegistrationUnpaid;
use App\Models\VisitorTransaction as VisitorTransactions;
use App\Models\MainVisitor as MainVisitors;
use App\Models\AdditionalVisitor as AdditionalVisitors;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;

class VisitorRegistrationForm extends Component
{
    public $countries, $salutations;
    public $event;
    public $finalEbEndDate, $finalStdStartDate;
    public $currentStep = 1;
    public $showAddVisitorModal = false;
    public $showEditVisitorModal = false;
    public $additionalVisitors = [];

    // VISITOR PASS TYPE
    public $visitorPassType, $rateType, $rateTypeString;

    // MAIN VISITOR
    public $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $nationality, $country, $city, $companyName, $jobTitle, $heardWhere;

    // SUB VISITOR
    public $subSalutation, $subFirstName, $subMiddleName, $subLastName, $subEmailAddress, $subMobileNumber, $subNationality, $subCountry, $subCity, $subCompanyName, $subJobTitle;

    // SUB VISITOR EDIT
    public $subIdEdit, $subSalutationEdit, $subFirstNameEdit, $subMiddleNameEdit, $subLastNameEdit, $subEmailAddressEdit, $subMobileNumberEdit, $subNationalityEdit, $subCountryEdit, $subCityEdit, $subCompanyNameEdit, $subJobTitleEdit;

    // 3RD PAGE
    public $paymentMethod, $finalEventStartDate, $finalEventEndDate, $finalQuantity, $finalUnitPrice, $finalNetAmount, $finalDiscount, $finalVat, $finalTotal;

    public $visitorInvoiceDetails = array();
    public $currentMainVisitorId;

    // 4TH PAGE
    public $sessionId, $cardDetails, $orderId, $transactionId, $htmlCodeOTP;

    // ERROR CHECKER
    public $emailMainExistingError, $emailSubExistingError, $emailMainAlreadyUsedError, $emailSubAlreadyUsedError;
    public $paymentMethodError;

    // BANK DETAILS
    public $bankDetails;

    public $eventFormattedDate;

    protected $listeners = ['registrationConfirmed' => 'addtoDatabase', 'emitInitiateAuth' => 'initiateAuthenticationCC', 'emitSubmit' => 'submitBankTransfer', 'emitSubmitStep3' => 'submitStep3'];

    public function mount($data)
    {
        $this->countries = config('app.countries');
        $this->salutations = config('app.salutations');

        if ($data->category == "AF" || $data->category == "AFS" || $data->category == "AFV") {
            $this->bankDetails = config('app.bankDetails.AF');
        } else {
            $this->bankDetails = config('app.bankDetails.DEFAULT');
        }

        $this->event = $data;
        $this->currentStep = 1;

        $this->cardDetails = false;

        $today = Carbon::today();

        if ($this->event->eb_end_date != null && $this->event->eb_member_rate != null && $this->event->eb_nmember_rate != null) {
            if ($today->lte(Carbon::parse($this->event->eb_end_date))) {
                $this->finalEbEndDate = Carbon::parse($this->event->eb_end_date)->format('d M Y');
            } else {
                $this->finalEbEndDate = null;
            }
        } else {
            $this->finalEbEndDate = null;
        }

        $this->finalStdStartDate = Carbon::parse($this->event->std_start_date)->format('d M Y');
        $this->finalEventStartDate = Carbon::parse($this->event->event_start_date)->format('d M Y');
        $this->finalEventEndDate = Carbon::parse($this->event->event_end_date)->format('d M Y');
        $this->visitorPassType = "nonMember";

        $this->eventFormattedDate = Carbon::parse($this->event->event_start_date)->format('d') . '-' . Carbon::parse($this->event->event_end_date)->format('d M Y');
    }

    public function render()
    {
        return view('livewire.registration.visitor.visitor-registration-form');
    }

    

    public function checkUnitPrice()
    {
        $today = Carbon::today();

        // CHECK UNIT PRICE
        if ($this->event->eb_end_date != null && $this->event->eb_member_rate != null && $this->event->eb_nmember_rate != null) {
            if ($today->lte(Carbon::parse($this->event->eb_end_date))) {
                if ($this->visitorPassType == "fullMember") {
                    $this->rateTypeString = "Full member early bird rate";
                    $this->finalUnitPrice = $this->event->eb_full_member_rate;
                } else if ($this->visitorPassType == "member") {
                    $this->rateTypeString = "Member early bird rate";
                    $this->finalUnitPrice = $this->event->eb_member_rate;
                } else {
                    $this->rateTypeString = "Non-Member early bird rate";
                    $this->finalUnitPrice = $this->event->eb_nmember_rate;
                }
                $this->rateType = "Early Bird";
            } else {
                if ($this->visitorPassType == "fullMember") {
                    $this->rateTypeString = "Full member standard rate";
                    $this->finalUnitPrice = $this->event->std_full_member_rate;
                } else if ($this->visitorPassType == "member") {
                    $this->rateTypeString = "Member standard rate";
                    $this->finalUnitPrice = $this->event->std_full_member_rate;
                } else {
                    $this->rateTypeString = "Non-Member standard rate";
                    $this->finalUnitPrice = $this->event->std_nmember_rate;
                }
                $this->rateType = "Standard";
            }
        } else {
            if ($this->visitorPassType == "fullMember") {
                $this->rateTypeString = "Full member standard rate";
                $this->finalUnitPrice = $this->event->std_full_member_rate;
            } else if ($this->visitorPassType == "member") {
                $this->rateTypeString = "Member standard rate";
                $this->finalUnitPrice = $this->event->std_member_rate;
            } else {
                $this->rateTypeString = "Non-Member standard rate";
                $this->finalUnitPrice = $this->event->std_nmember_rate;
            }
            $this->rateType = "Standard";
        }
    }

    
    public function calculateAmount()
    {
        array_push($this->visitorInvoiceDetails, [
            'visitorDescription' => "Visitor Registration Fee",
            'visitorNames' => [
                $this->firstName . " " . $this->middleName . " " . $this->lastName,
            ],
            'quantity' => 1,
            'totalDiscount' => 0,
            'totalNetAmount' =>  $this->finalUnitPrice,
        ]);


        if (count($this->additionalVisitors) > 0) {
            for ($i = 0; $i < count($this->additionalVisitors); $i++) {
                $existingIndex = 0;

                array_push(
                    $this->visitorInvoiceDetails[$existingIndex]['visitorNames'],
                    $this->additionalVisitors[$i]['subFirstName'] . " " . $this->additionalVisitors[$i]['subMiddleName'] . " " . $this->additionalVisitors[$i]['subLastName']
                );

                $quantityTemp = $this->visitorInvoiceDetails[$existingIndex]['quantity'] + 1;
                $totalDiscountTemp = 0;
                $totalNetAmountTemp = ($this->finalUnitPrice * $quantityTemp) - $totalDiscountTemp;

                $this->visitorInvoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                $this->visitorInvoiceDetails[$existingIndex]['totalDiscount'] = $totalDiscountTemp;
                $this->visitorInvoiceDetails[$existingIndex]['totalNetAmount'] = $totalNetAmountTemp;
            }
        }

        foreach ($this->visitorInvoiceDetails as $visitorInvoiceDetail) {
            $this->finalQuantity += $visitorInvoiceDetail['quantity'];
            $this->finalDiscount += $visitorInvoiceDetail['totalDiscount'];
            $this->finalNetAmount += $visitorInvoiceDetail['totalNetAmount'];
        }
        $this->finalVat = $this->finalNetAmount * ($this->event->event_vat / 100);
        $this->finalTotal = $this->finalNetAmount + $this->finalVat;
    }
    

    public function resetCalculations()
    {
        $this->visitorInvoiceDetails = array();
        $this->finalQuantity = 0;
        $this->finalDiscount = 0;
        $this->finalNetAmount = 0;
        $this->finalVat = 0;
        $this->finalTotal = 0;
    }

    
    public function increaseStep()
    {
        if ($this->currentStep == 1) {
            $this->resetCalculations();
            $this->paymentMethod = null;
            $this->emailMainAlreadyUsedError = null;
            $this->emailMainExistingError = null;

            $this->validate(
                [
                    'companyName' => 'required',
                    'jobTitle' => 'required',
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'nationality' => 'required',
                    'country' => 'required',
                    'city' => 'required',
                    'emailAddress' => 'required|email',
                    'mobileNumber' => 'required',
                ],
                [
                    'companyName.required' => "Company name is required",
                    'jobTitle.required' => "Job title is required",
                    'firstName.required' => "First name is required",
                    'lastName.required' => "Last name is required",
                    'nationality.required' => "Nationality is required",
                    'country.required' => "Country is required",
                    'city.required' => "City is required",
                    'emailAddress.required' => "Email address is required",
                    'emailAddress.email' => "Email address must be a valid email",
                    'mobileNumber.required' => "Mobile number is required",
                ]
            );

            $this->dispatchBrowserEvent('swal:add-step3-registration-loading-screen');
        } else if ($this->currentStep == 2) {
            if ($this->paymentMethod == null) {
                $this->paymentMethodError = "Please choose your payment method first";
            } else {
                if ($this->paymentMethod == "creditCard") {
                    $this->dispatchBrowserEvent('swal:registration-confirmation', [
                        'type' => 'warning',
                        'message' => 'Are you sure you want to pay via Credit card?',
                        'text' => "",
                    ]);
                } else {
                    $this->dispatchBrowserEvent('swal:registration-confirmation', [
                        'type' => 'warning',
                        'message' => 'Are you sure you want to pay via Bank transfer?',
                        'text' => "",
                    ]);
                }
            }
        }
    }

    
    public function submitStep3()
    {
        if ($this->checkEmailIfUsedAlreadyMain($this->emailAddress)) {
            $this->emailMainAlreadyUsedError = "You already used this email!";
        } else {
            if ($this->checkEmailIfExistsInDatabase($this->emailAddress)) {
                $this->emailMainAlreadyUsedError = null;
                $this->emailMainExistingError = "Email is already registered, please use another email!";
            } else {
                $this->emailMainAlreadyUsedError = null;
                $this->emailMainExistingError = null;
                $this->checkUnitPrice();
                $this->calculateAmount();
                $this->currentStep += 1;
            }
        }
        $this->dispatchBrowserEvent('swal:remove-registration-loading-screen');
    }

    
    public function addtoDatabase()
    {
        if ($this->finalTotal == 0) {
            $paymentStatus = "free";
        } else {
            $paymentStatus = "unpaid";
        }

        $newRegistrant = MainVisitors::create([
            'event_id' => $this->event->id,
            'pass_type' => "nonMember",
            'rate_type' => $this->rateType,
            'rate_type_string' => $this->rateTypeString,

            'salutation' => $this->salutation,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'email_address' => $this->emailAddress,
            'mobile_number' => $this->mobileNumber,
            'nationality' => $this->nationality,
            'country' => $this->country,
            'city' => $this->city,
            'company_name' => $this->companyName,
            'job_title' => $this->jobTitle,

            'heard_where' => $this->heardWhere,

            'quantity' => $this->finalQuantity,
            'unit_price' => $this->finalUnitPrice,
            'net_amount' => $this->finalNetAmount,
            'vat_price' => $this->finalVat,
            'discount_price' => $this->finalDiscount,
            'total_amount' => $this->finalTotal,

            'mode_of_payment' => $this->paymentMethod,
            'registration_status' => "droppedOut",
            'payment_status' => $paymentStatus,
            'registered_date_time' => Carbon::now(),
            'paid_date_time' => null,
        ]);

        $transaction = VisitorTransactions::create([
            'event_id' => $this->event->id,
            'event_category' => $this->event->category,
            'visitor_id' => $newRegistrant->id,
            'visitor_type' => "main",
        ]);

        $tempYear = substr(Carbon::now()->year, -2);
        $lastDigit = 1000 + intval($transaction->id);
        $tempOrderId = $this->event->category . "$tempYear" . "$lastDigit";

        $this->currentMainVisitorId = $newRegistrant->id;

        if (!empty($this->additionalVisitors)) {
            foreach ($this->additionalVisitors as $additionalVisitor) {
                $newAdditionVisitor = AdditionalVisitors::create([
                    'main_visitor_id' => $newRegistrant->id,
                    'salutation' => $additionalVisitor['subSalutation'],
                    'first_name' => $additionalVisitor['subFirstName'],
                    'middle_name' => $additionalVisitor['subMiddleName'],
                    'last_name' => $additionalVisitor['subLastName'],
                    'email_address' => $additionalVisitor['subEmailAddress'],
                    'mobile_number' => $additionalVisitor['subMobileNumber'],
                    'nationality' => $additionalVisitor['subNationality'],
                    'country' => $additionalVisitor['subCountry'],
                    'city' => $additionalVisitor['subCity'],
                    'company_name' => $additionalVisitor['subCompanyName'],
                    'job_title' => $additionalVisitor['subJobTitle'],
                ]);

                VisitorTransactions::create([
                    'event_id' => $this->event->id,
                    'event_category' => $this->event->category,
                    'visitor_id' => $newAdditionVisitor->id,
                    'visitor_type' => "sub",
                ]);
            }
        }

        if ($this->paymentMethod == "creditCard") {
            $this->setSessionCC();
            $this->orderId = $tempOrderId;
        } else {
            $this->dispatchBrowserEvent('swal:remove-registration-loading-screen');
        }

        $this->currentStep += 1;
    }
    
    public function decreaseStep()
    {
        $this->resetCalculations();
        $this->currentStep -= 1;
    }

    public function submit()
    {
        if ($this->currentStep == 3) {
            $this->dispatchBrowserEvent('swal:add-registration-loading-screen');
        }
    }
    
    public function submitBankTransfer()
    {
        // UPDATE DETAILS
        if ($this->finalTotal == 0) {
            $paymentStatus = "free";
            $registrationStatus = "confirmed";
        } else {
            $paymentStatus = "unpaid";
            $registrationStatus = "pending";
        }

        MainVisitors::find($this->currentMainVisitorId)->fill([
            'registration_status' => $registrationStatus,
            'payment_status' => $paymentStatus,
            'paid_date_time' => null,
        ])->save();

        $transaction = VisitorTransactions::where('visitor_id', $this->currentMainVisitorId)->where('visitor_type', "main")->first();

        $eventFormattedData = Carbon::parse($this->event->event_start_date)->format('d') . '-' . Carbon::parse($this->event->event_end_date)->format('d M Y');
        $lastDigit = 1000 + intval($transaction->id);

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->event->category == $eventCategoryC) {
                $getEventcode = $code;
            }
        }

        $tempTransactionId = $this->event->year . "$getEventcode" . "$lastDigit";
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->currentMainVisitorId;

        $details1 = [
            'name' => $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName,
            'eventLink' => $this->event->link,
            'eventName' => $this->event->name,
            'eventDates' => $eventFormattedData,
            'eventLocation' => $this->event->location,
            'eventCategory' => $this->event->category,
            'eventYear' => $this->event->year,

            'nationality' => $this->nationality,
            'country' => $this->country,
            'city' => $this->city,
            'amountPaid' => 0,
            'transactionId' => $tempTransactionId,
            'invoiceLink' => $invoiceLink,
            
            'badgeLink' => env('APP_URL')."/".$this->event->category."/".$this->event->id."/view-badge"."/"."main"."/".$this->currentMainVisitorId,
        ];

        $details2 = [
            'name' => $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName,
            'eventLink' => $this->event->link,
            'eventName' => $this->event->name,
            'eventCategory' => $this->event->category,
            'eventYear' => $this->event->year,

            'invoiceAmount' => $this->finalTotal,
            'amountPaid' => 0,
            'balance' => 0,
            'invoiceLink' => $invoiceLink,
        ];

        if ($paymentStatus == "free") {
            Mail::to($this->emailAddress)->cc(config('app.ccEmailNotif'))->queue(new RegistrationPaid($details1));
            Mail::to($this->emailAddress)->cc(config('app.ccEmailNotif'))->queue(new RegistrationPaymentConfirmation($details2));
        } else {
            Mail::to($this->emailAddress)->cc(config('app.ccEmailNotif'))->queue(new RegistrationUnpaid($details1));
        }

        $additionalVisitors = AdditionalVisitors::where('main_visitor_id', $this->currentMainVisitorId)->get();
        if (!empty($additionalVisitors)) {
            foreach ($additionalVisitors as $additionalVisitor) {

                $transaction = VisitorTransactions::where('visitor_id', $additionalVisitor->id)->where('visitor_type', "sub")->first();
                $lastDigit = 1000 + intval($transaction->id);
                $tempTransactionId = $this->event->year . "$getEventcode" . "$lastDigit";

                $details1 = [
                    'name' => $additionalVisitor->salutation . " " . $additionalVisitor->first_name . " " . $additionalVisitor->middle_name . " " . $additionalVisitor->last_name,
                    'eventLink' => $this->event->link,
                    'eventName' => $this->event->name,
                    'eventDates' => $eventFormattedData,
                    'eventLocation' => $this->event->location,
                    'eventCategory' => $this->event->category,
                    'eventYear' => $this->event->year,

                    'nationality' => $additionalVisitor->nationality,
                    'country' => $additionalVisitor->country,
                    'city' => $additionalVisitor->city,
                    'amountPaid' => 0,
                    'transactionId' => $tempTransactionId,
                    'invoiceLink' => $invoiceLink,
            
                    'badgeLink' => env('APP_URL')."/".$this->event->category."/".$this->event->id."/view-badge"."/"."sub"."/".$additionalVisitor->id,
                ];

                $details2 = [
                    'name' => $additionalVisitor->salutation . " " . $additionalVisitor->first_name . " " . $additionalVisitor->middle_name . " " . $additionalVisitor->last_name,
                    'eventLink' => $this->event->link,
                    'eventName' => $this->event->name,
                    'eventCategory' => $this->event->category,
                    'eventYear' => $this->event->year,

                    'invoiceAmount' => $this->finalTotal,
                    'amountPaid' => 0,
                    'balance' => 0,
                    'invoiceLink' => $invoiceLink,
                ];

                if ($paymentStatus == "free") {
                    Mail::to($additionalVisitor->email_address)->cc(config('app.ccEmailNotif'))->queue(new RegistrationPaid($details1));
                    Mail::to($additionalVisitor->email_address)->cc(config('app.ccEmailNotif'))->queue(new RegistrationPaymentConfirmation($details2));
                } else {
                    Mail::to($additionalVisitor->email_address)->cc(config('app.ccEmailNotif'))->queue(new RegistrationUnpaid($details1));
                }
            }
        }
        return redirect()->route('register.success.view', ['eventCategory' => $this->event->category, 'eventId' => $this->event->id, 'eventYear' => $this->event->year, 'mainDelegateId' => $this->currentMainVisitorId]);
    }

    
    public function btClicked()
    {
        $this->paymentMethodError = null;
        if ($this->paymentMethod == 'creditCard') {
            $this->paymentMethod = 'bankTransfer';
        } else if ($this->paymentMethod == 'bankTransfer') {
            $this->paymentMethod = null;
        } else {
            $this->paymentMethod = 'bankTransfer';
        }
    }

    public function ccClicked()
    {
        $this->paymentMethodError = null;
        if ($this->paymentMethod == 'bankTransfer') {
            $this->paymentMethod = 'creditCard';
        } else if ($this->paymentMethod == 'creditCard') {
            $this->paymentMethod = null;
        } else {
            $this->paymentMethod = 'creditCard';
        }
    }

    
    public function setSessionCC()
    {
        $apiEndpoint = env('MERCHANT_API_URL');
        $merchantId = env('MERCHANT_ID');
        $authPass = env('MERCHANT_AUTH_PASSWORD');

        // GET SESSION ID
        $clientGetSession = new Client();
        $responseGetSession = $clientGetSession->request('POST', $apiEndpoint . '/session', [
            'auth' => [
                'merchant.' . $merchantId,
                $authPass
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
        ]);
        $bodyGetSession = $responseGetSession->getBody()->getContents();
        $dataGetSession = json_decode($bodyGetSession, true);
        if ($dataGetSession['result'] == "SUCCESS") {
            $this->sessionId =  $dataGetSession['session']['id'];
            Session::put('sessionId', $dataGetSession['session']['id']);

            // UPDATE SESSION
            $clientUpdateSession = new Client();
            $responseUpdateSession = $clientUpdateSession->request('PUT', $apiEndpoint . '/session/' . $this->sessionId, [
                'auth' => [
                    'merchant.' . $merchantId,
                    $authPass
                ],
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    "order" => [
                        'amount' => $this->finalTotal,
                        // 'amount' => 0.27,
                        'currency' => 'USD',
                    ],
                ]
            ]);
            $bodyUpdateSession = $responseUpdateSession->getBody()->getContents();
            $dataUpdateSession = json_decode($bodyUpdateSession, true);

            $this->dispatchBrowserEvent('swal:remove-registration-loading-screen');

            if ($dataUpdateSession['session']['updateStatus'] == "SUCCESS") {
                $this->cardDetails = true;
            } else {
                $this->cardDetails = false;
            }
        }
    }

    public function initiateAuthenticationCC()
    {
        $apiEndpoint = env('MERCHANT_API_URL');
        $merchantId = env('MERCHANT_ID');
        $authPass = env('MERCHANT_AUTH_PASSWORD');

        $generateUniqId = substr(uniqid(), -4);

        $this->transactionId = $this->orderId . "$generateUniqId";

        $clientInitiateAuth = new Client();
        $responseInitiateAuth = $clientInitiateAuth->request('PUT', $apiEndpoint . '/order/' . $this->orderId . '/transaction/' . $this->transactionId, [
            'auth' => [
                'merchant.' . $merchantId,
                $authPass
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                "session" => [
                    'id' => $this->sessionId,
                ],
                "authentication" => [
                    "acceptVersions" => "3DS1,3DS2",
                    "channel" => "PAYER_BROWSER",
                    "purpose" => "PAYMENT_TRANSACTION"
                ],
                "correlationId" => "test",
                "apiOperation" => "INITIATE_AUTHENTICATION"
            ],
        ]);
        $bodyInitiateAuth = $responseInitiateAuth->getBody()->getContents();
        $dataInitiateAuth = json_decode($bodyInitiateAuth, true);

        if (
            $dataInitiateAuth['response']['gatewayCode'] == "AUTHENTICATION_IN_PROGRESS" &&
            $dataInitiateAuth['response']['gatewayRecommendation'] == "PROCEED" &&
            $dataInitiateAuth['transaction']['authenticationStatus'] == "AUTHENTICATION_AVAILABLE" &&
            $dataInitiateAuth['transaction']['type'] == "AUTHENTICATION"
        ) {
            $clientAuthPayer = new Client();
            $appUrl = env('APP_URL');
            $responseAuthPayer = $clientAuthPayer->request('PUT', $apiEndpoint . '/order/' . $this->orderId . '/transaction/' . $this->transactionId, [
                'auth' => [
                    'merchant.' . $merchantId,
                    $authPass
                ],
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    "session" => [
                        'id' => $this->sessionId,
                    ],
                    "authentication" => [
                        "redirectResponseUrl" => "$appUrl/capturePayment?sessionId=$this->sessionId&mainDelegateId=$this->currentMainVisitorId&registrationFormType=visitor",
                    ],
                    "correlationId" => "test",
                    "device" =>  [
                        "browser" =>  "MOZILLA",
                        "browserDetails" =>  [
                            "3DSecureChallengeWindowSize" =>  "FULL_SCREEN",
                            "acceptHeaders" =>  "application/json",
                            "colorDepth" =>  24,
                            "javaEnabled" =>  true,
                            "language" =>  "en-US",
                            "screenHeight" =>  1640,
                            "screenWidth" =>  1480,
                            "timeZone" =>  273
                        ],
                        "ipAddress" =>  "127.0.0.1"
                    ],
                    "apiOperation" => "AUTHENTICATE_PAYER",
                ],
            ]);
            $bodyAuthPayer = $responseAuthPayer->getBody()->getContents();
            $dataAuthPayer = json_decode($bodyAuthPayer, true);

            if (
                $dataAuthPayer['response']['gatewayCode'] == "PENDING" &&
                $dataAuthPayer['response']['gatewayRecommendation'] == "PROCEED" &&
                $dataAuthPayer['transaction']['authenticationStatus'] == "AUTHENTICATION_PENDING" &&
                $dataAuthPayer['transaction']['type'] == "AUTHENTICATION"
            ) {
                $this->htmlCodeOTP = $dataAuthPayer['authentication']['redirect']['html'];
                Session::put('paymentStatus', 'pendingOTP');
                Session::put('htmlOTP', $dataAuthPayer['authentication']['redirect']['html']);
                Session::put('orderId', $this->orderId);

                $this->dispatchBrowserEvent('swal:hide-pay-button');
                return redirect()->route('register.otp.view', ['eventCategory' => $this->event->category, 'eventId' => $this->event->id, 'eventYear' => $this->event->year]);
            } else {
                $this->dispatchBrowserEvent('swal:remove-loading-button');
                $this->dispatchBrowserEvent('swal:registration-error-authentication', [
                    'type' => 'error',
                    'message' => 'Authentication Error 2',
                    'text' => 'There was a problem authenticating your card, please try again!'
                ]);
            }
        } else {
            $this->dispatchBrowserEvent('swal:remove-loading-button');
            $this->dispatchBrowserEvent('swal:registration-error-authentication', [
                'type' => 'error',
                'message' => 'Authentication Error 1',
                'text' => 'There was a problem authenticating your card, please try again!'
            ]);
        }
    }

    
    public function openAddModal()
    {
        $this->showAddVisitorModal = true;
    }

    
    public function resetAddModalFields()
    {
        $this->subSalutation = null;
        $this->subFirstName = null;
        $this->subMiddleName = null;
        $this->subLastName = null;
        $this->subEmailAddress = null;
        $this->subMobileNumber = null;
        $this->subNationality = null;
        $this->subCountry = null;
        $this->subCity = null;
        $this->subCompanyName = null;
        $this->subJobTitle = null;

        $this->emailSubExistingError = null;
        $this->emailSubAlreadyUsedError = null;
    }

    public function closeAddModal()
    {
        $this->showAddVisitorModal = false;
        $this->resetAddModalFields();
    }

    
    public function openEditModal($subVisitorId)
    {
        $this->showEditVisitorModal = true;
        foreach ($this->additionalVisitors as $additionalVisitor) {
            if ($additionalVisitor['subVisitorId'] == $subVisitorId) {
                $this->subIdEdit = $additionalVisitor['subVisitorId'];
                $this->subSalutationEdit = $additionalVisitor['subSalutation'];
                $this->subFirstNameEdit = $additionalVisitor['subFirstName'];
                $this->subMiddleNameEdit = $additionalVisitor['subMiddleName'];
                $this->subLastNameEdit = $additionalVisitor['subLastName'];
                $this->subEmailAddressEdit = $additionalVisitor['subEmailAddress'];
                $this->subMobileNumberEdit = $additionalVisitor['subMobileNumber'];
                $this->subNationalityEdit = $additionalVisitor['subNationality'];
                $this->subCountryEdit = $additionalVisitor['subCountry'];
                $this->subCityEdit = $additionalVisitor['subCity'];
                $this->subCompanyNameEdit = $additionalVisitor['subCompanyName'];
                $this->subJobTitleEdit = $additionalVisitor['subJobTitle'];
            }
        }
    }
    
    public function resetEditModalFields()
    {
        $this->subIdEdit = null;
        $this->subSalutationEdit = null;
        $this->subFirstNameEdit = null;
        $this->subMiddleNameEdit = null;
        $this->subLastNameEdit = null;
        $this->subEmailAddressEdit = null;
        $this->subMobileNumberEdit = null;
        $this->subNationalityEdit = null;
        $this->subCountryEdit = null;
        $this->subCityEdit = null;
        $this->subCompanyNameEdit = null;
        $this->subJobTitleEdit = null;

        $this->emailSubExistingError = null;
        $this->emailSubAlreadyUsedError = null;
    }

    public function closeEditModal()
    {
        $this->showEditVisitorModal = false;
        $this->resetEditModalFields();
    }

    public function saveAdditionalVisitor()
    {
        $this->emailSubAlreadyUsedError = null;
        $this->emailSubExistingError = null;

        $this->validate(
            [
                'subCompanyName' => 'required',
                'subJobTitle' => 'required',
                'subFirstName' => 'required',
                'subLastName' => 'required',
                'subNationality' => 'required',
                'subCountry' => 'required',
                'subCity' => 'required',
                'subEmailAddress' => 'required|email',
                'subMobileNumber' => 'required',
            ],
            [
                'subCompanyName.required' => "Company name is required",
                'subJobTitle.required' => "Job title is required",
                'subFirstName.required' => "First name is required",
                'subLastName.required' => "Last name is required",
                'subNationality.required' => "Nationality is required",
                'subCountry.required' => "Country is required",
                'subCity.required' => "City is required",
                'subEmailAddress.required' => "Email address is required",
                'subEmailAddress.email' => "Email address must be a valid email",
                'subMobileNumber.required' => "Mobile number is required",
            ]
        );

        if ($this->checkEmailIfUsedAlreadySub($this->subEmailAddress)) {
            $this->emailSubAlreadyUsedError = "You already used this email!";
        } else {
            if ($this->checkEmailIfExistsInDatabase($this->subEmailAddress)) {
                $this->emailSubAlreadyUsedError = null;
                $this->emailSubExistingError = "Email is already registered, please use another email!";
            } else {
                $this->emailSubAlreadyUsedError = null;
                $this->emailSubExistingError = null;

                $uuid = Str::uuid();
                array_push($this->additionalVisitors, [
                    'subVisitorId' => $uuid->toString(),
                    'subSalutation' => $this->subSalutation,
                    'subFirstName' => $this->subFirstName,
                    'subMiddleName' => $this->subMiddleName,
                    'subLastName' => $this->subLastName,
                    'subNationality' => $this->subNationality,
                    'subEmailAddress' => $this->subEmailAddress,
                    'subMobileNumber' => $this->subMobileNumber,
                    'subCountry' => $this->subCountry,
                    'subCity' => $this->subCity,
                    'subCompanyName' => $this->subCompanyName,
                    'subJobTitle' => $this->subJobTitle,
                ]);

                $this->resetAddModalFields();
                $this->showAddVisitorModal = false;
            }
        }
    }
    
    public function removeAdditionalVisitor($subVisitorId)
    {
        $arrayTemp = array_filter($this->additionalVisitors, function ($item) use ($subVisitorId) {
            return $item['subVisitorId'] != $subVisitorId;
        });

        $this->additionalVisitors = [];

        foreach ($arrayTemp as $visitor) {
            array_push($this->additionalVisitors, $visitor);
        }
    }

    public function editAdditionalVisitor($subVisitorId)
    {
        $this->emailSubAlreadyUsedError = null;
        $this->emailSubExistingError = null;

        $this->validate(
            [
                'subCompanyNameEdit' => 'required',
                'subJobTitleEdit' => 'required',
                'subFirstNameEdit' => 'required',
                'subLastNameEdit' => 'required',
                'subNationalityEdit' => 'required',
                'subCountryEdit' => 'required',
                'subCityEdit' => 'required',
                'subEmailAddressEdit' => 'required|email',
                'subMobileNumberEdit' => 'required',
            ],
            [
                'subCompanyNameEdit.required' => "Company name is required",
                'subJobTitleEdit.required' => "Job title is required",
                'subFirstNameEdit.required' => "First name is required",
                'subLastNameEdit.required' => "Last name is required",
                'subNationalityEdit.required' => "Nationality is required",
                'subCountryEdit.required' => "Country is required",
                'subCityEdit.required' => "City is required",
                'subEmailAddressEdit.required' => "Email address is required",
                'subEmailAddressEdit.email' => "Email address must be a valid email",
                'subMobileNumberEdit.required' => "Mobile number is required",
            ]
        );

        $tempCheckEmail = false;

        if ($this->subEmailAddressEdit == $this->emailAddress) {
            $tempCheckEmail = true;
        } else {
            foreach ($this->additionalVisitors as $additionalVisitor) {
                if ($additionalVisitor['subVisitorId'] != $subVisitorId) {
                    if ($additionalVisitor['subEmailAddress'] == $this->subEmailAddressEdit) {
                        $tempCheckEmail = true;
                    }
                }
            }
        }

        if ($tempCheckEmail) {
            $this->emailSubAlreadyUsedError = "You already used this email!";
        } else {
            if ($this->checkEmailIfExistsInDatabase($this->subEmailAddressEdit)) {
                $this->emailSubAlreadyUsedError = null;
                $this->emailSubExistingError = "Email is already registered, please use another email!";
            } else {
                $this->emailSubAlreadyUsedError = null;
                $this->emailSubExistingError = null;

                for ($i = 0; $i < count($this->additionalVisitors); $i++) {
                    if ($this->additionalVisitors[$i]['subVisitorId'] == $subVisitorId) {
                        $this->additionalVisitors[$i]['subSalutation'] = $this->subSalutationEdit;
                        $this->additionalVisitors[$i]['subFirstName'] = $this->subFirstNameEdit;
                        $this->additionalVisitors[$i]['subMiddleName'] = $this->subMiddleNameEdit;
                        $this->additionalVisitors[$i]['subLastName'] = $this->subLastNameEdit;
                        $this->additionalVisitors[$i]['subNationality'] = $this->subNationalityEdit;
                        $this->additionalVisitors[$i]['subEmailAddress'] = $this->subEmailAddressEdit;
                        $this->additionalVisitors[$i]['subMobileNumber'] = $this->subMobileNumberEdit;
                        $this->additionalVisitors[$i]['subCountry'] = $this->subCountryEdit;
                        $this->additionalVisitors[$i]['subCity'] = $this->subCityEdit;
                        $this->additionalVisitors[$i]['subCompanyName'] = $this->subCompanyNameEdit;
                        $this->additionalVisitors[$i]['subJobTitle'] = $this->subJobTitleEdit;

                        $this->resetEditModalFields();
                        $this->showEditVisitorModal = false;
                    }
                }
            }
        }
    }

    public function checkEmailIfExistsInDatabase($emailAddress)
    {
        $allVisitors = VisitorTransactions::where('event_id', $this->event->id)->where('event_category', $this->event->category)->get();

        $countMainVisitor = 0;
        $countSubVisitor = 0;

        if (!$allVisitors->isEmpty()) {
            foreach ($allVisitors as $visitor) {
                if ($visitor->visitor_type == "main") {
                    $mainVisitor = MainVisitors::where('id', $visitor->visitor_id)->where('email_address', $emailAddress)->where('registration_status', '!=', 'droppedOut')->first();
                    if ($mainVisitor != null) {
                        $countMainVisitor++;
                    }
                } else {
                    $subVisitor = AdditionalVisitors::where('id', $visitor->visitor_id)->where('email_address', $emailAddress)->first();
                    if ($subVisitor != null) {
                        $registrationStatsMain = MainVisitors::where('id', $subVisitor->main_visitor_id)->value('registration_status');
                        if ($registrationStatsMain != "droppedOut") {
                            $countSubVisitor++;
                        }
                    }
                }
            }
        }

        if ($countMainVisitor == 0 && $countSubVisitor == 0) {
            return false;
        } else {
            return true;
        }
    }

    
    public function checkEmailIfUsedAlreadyMain($emailAddress)
    {
        if (count($this->additionalVisitors) == 0) {
            return false;
        } else {
            foreach ($this->additionalVisitors as $additionalVisitor) {
                if ($emailAddress == $additionalVisitor['subEmailAddress']) {
                    return true;
                    break;
                }
            }
        }
    }

    
    public function checkEmailIfUsedAlreadySub($emailAddress)
    {
        if ($this->emailAddress == $emailAddress) {
            return true;
        } else {
            if (count($this->additionalVisitors) == 0) {
                return false;
            } else {
                foreach ($this->additionalVisitors as $additionalVisitor) {
                    if ($emailAddress == $additionalVisitor['subEmailAddress']) {
                        return true;
                        break;
                    }
                }
            }
        }
    }
}
