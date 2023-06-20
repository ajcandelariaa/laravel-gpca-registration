<?php

namespace App\Http\Livewire;

use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Mail\RegistrationUnpaid;
use App\Models\SpouseTransaction as SpouseTransactions;
use App\Models\MainSpouse as MainSpouses;
use App\Models\AdditionalSpouse as AdditionalSpouses;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;

class SpouseRegistrationForm extends Component
{
    public $countries, $salutations;
    public $event;
    public $finalEbEndDate, $finalStdStartDate;
    public $currentStep = 1;
    public $showAddSpouseModal = false;
    public $showEditSpouseModal = false;
    public $additionalSpouses = [];

    // DELEGATE PASS TYPE
    public $spousePassType, $rateType, $rateTypeString;

    // MAIN DELEGATE
    public $salutation, $firstName, $middleName, $lastName, $nationality, $country, $city, $emailAddress, $mobileNumber, $heardWhere, $referenceDelegateName;

    // SUB DELEGATE
    public $subSalutation, $subFirstName, $subMiddleName, $subLastName, $subNationality, $subCountry, $subCity, $subEmailAddress, $subMobileNumber;

    // SUB DELEGATE EDIT
    public $subIdEdit, $subSalutationEdit, $subFirstNameEdit, $subMiddleNameEdit, $subLastNameEdit, $subNationalityEdit, $subCountryEdit, $subCityEdit, $subEmailAddressEdit, $subMobileNumberEdit;

    // 3RD PAGE
    public $paymentMethod, $finalEventStartDate, $finalEventEndDate, $finalQuantity, $finalUnitPrice, $finalNetAmount, $finalDiscount, $finalVat, $finalTotal;

    public $spouseInvoiceDetails = array();
    public $currentMainSpouseId;

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

        if ($data->category == "AF") {
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
        $this->spousePassType = "nonMember";

        $this->eventFormattedDate = Carbon::parse($this->event->event_start_date)->format('d') . '-' . Carbon::parse($this->event->event_end_date)->format('d M Y');
    }

    public function render()
    {
        return view('livewire.registration.spouse.spouse-registration-form');
    }

    public function checkUnitPrice()
    {
        $today = Carbon::today();

        // CHECK UNIT PRICE
        if ($this->event->eb_end_date != null && $this->event->eb_member_rate != null && $this->event->eb_nmember_rate != null) {
            if ($today->lte(Carbon::parse($this->event->eb_end_date))) {
                if ($this->spousePassType == "fullMember") {
                    $this->rateTypeString = "Full member early bird rate";
                    $this->finalUnitPrice = $this->event->eb_full_member_rate;
                } else if ($this->spousePassType == "member") {
                    $this->rateTypeString = "Member early bird rate";
                    $this->finalUnitPrice = $this->event->eb_member_rate;
                } else {
                    $this->rateTypeString = "Non-Member early bird rate";
                    $this->finalUnitPrice = $this->event->eb_nmember_rate;
                }
                $this->rateType = "Early Bird";
            } else {
                if ($this->spousePassType == "fullMember") {
                    $this->rateTypeString = "Full member standard rate";
                    $this->finalUnitPrice = $this->event->std_full_member_rate;
                } else if ($this->spousePassType == "member") {
                    $this->rateTypeString = "Member standard rate";
                    $this->finalUnitPrice = $this->event->std_full_member_rate;
                } else {
                    $this->rateTypeString = "Non-Member standard rate";
                    $this->finalUnitPrice = $this->event->std_nmember_rate;
                }
                $this->rateType = "Standard";
            }
        } else {
            if ($this->spousePassType == "fullMember") {
                $this->rateTypeString = "Full member standard rate";
                $this->finalUnitPrice = $this->event->std_full_member_rate;
            } else if ($this->spousePassType == "member") {
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
        array_push($this->spouseInvoiceDetails, [
            'spouseDescription' => "Spouse Registration Fee",
            'spouseNames' => [
                $this->firstName . " " . $this->middleName . " " . $this->lastName,
            ],
            'quantity' => 1,
            'totalDiscount' => 0,
            'totalNetAmount' =>  $this->finalUnitPrice,
        ]);


        if (count($this->additionalSpouses) > 0) {
            for ($i = 0; $i < count($this->additionalSpouses); $i++) {
                $existingIndex = 0;

                array_push(
                    $this->spouseInvoiceDetails[$existingIndex]['spouseNames'],
                    $this->additionalSpouses[$i]['subFirstName'] . " " . $this->additionalSpouses[$i]['subMiddleName'] . " " . $this->additionalSpouses[$i]['subLastName']
                );

                $quantityTemp = $this->spouseInvoiceDetails[$existingIndex]['quantity'] + 1;
                $totalDiscountTemp = 0;
                $totalNetAmountTemp = ($this->finalUnitPrice * $quantityTemp) - $totalDiscountTemp;

                $this->spouseInvoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                $this->spouseInvoiceDetails[$existingIndex]['totalDiscount'] = $totalDiscountTemp;
                $this->spouseInvoiceDetails[$existingIndex]['totalNetAmount'] = $totalNetAmountTemp;
            }
        }

        foreach ($this->spouseInvoiceDetails as $spouseInvoiceDetail) {
            $this->finalQuantity += $spouseInvoiceDetail['quantity'];
            $this->finalDiscount += $spouseInvoiceDetail['totalDiscount'];
            $this->finalNetAmount += $spouseInvoiceDetail['totalNetAmount'];
        }
        $this->finalVat = $this->finalNetAmount * ($this->event->event_vat / 100);
        $this->finalTotal = $this->finalNetAmount + $this->finalVat;
    }

    public function resetCalculations()
    {
        $this->spouseInvoiceDetails = array();
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
                    'referenceDelegateName' => 'required',
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'nationality' => 'required',
                    'country' => 'required',
                    'city' => 'required',
                    'emailAddress' => 'required|email',
                    'mobileNumber' => 'required',
                ],
                [
                    'referenceDelegateName.required' => "Full name of Annual GPCA Forum registered attendee is required",
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

        $newRegistrant = MainSpouses::create([
            'event_id' => $this->event->id,
            'pass_type' => "nonMember",
            'rate_type' => $this->rateType,
            'rate_type_string' => $this->rateTypeString,

            'salutation' => $this->salutation,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'nationality' => $this->nationality,
            'country' => $this->country,
            'city' => $this->city,
            'email_address' => $this->emailAddress,
            'mobile_number' => $this->mobileNumber,

            'reference_delegate_name' => $this->referenceDelegateName,
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

        $transaction = SpouseTransactions::create([
            'event_id' => $this->event->id,
            'event_category' => $this->event->category,
            'spouse_id' => $newRegistrant->id,
            'spouse_type' => "main",
        ]);

        $tempYear = substr(Carbon::now()->year, -2);
        $lastDigit = 1000 + intval($transaction->id);
        $tempOrderId = $this->event->category . "$tempYear" . "$lastDigit";

        $this->currentMainSpouseId = $newRegistrant->id;

        if (!empty($this->additionalSpouses)) {
            foreach ($this->additionalSpouses as $additionalSpouse) {
                $newAdditionSpouse = AdditionalSpouses::create([
                    'main_spouse_id' => $newRegistrant->id,
                    'salutation' => $additionalSpouse['subSalutation'],
                    'first_name' => $additionalSpouse['subFirstName'],
                    'middle_name' => $additionalSpouse['subMiddleName'],
                    'last_name' => $additionalSpouse['subLastName'],
                    'nationality' => $additionalSpouse['subNationality'],
                    'country' => $additionalSpouse['subCountry'],
                    'city' => $additionalSpouse['subCity'],
                    'email_address' => $additionalSpouse['subEmailAddress'],
                    'mobile_number' => $additionalSpouse['subMobileNumber'],
                ]);

                SpouseTransactions::create([
                    'event_id' => $this->event->id,
                    'event_category' => $this->event->category,
                    'spouse_id' => $newAdditionSpouse->id,
                    'spouse_type' => "sub",
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

        MainSpouses::find($this->currentMainSpouseId)->fill([
            'registration_status' => $registrationStatus,
            'payment_status' => $paymentStatus,
            'paid_date_time' => null,
        ])->save();

        $transaction = SpouseTransactions::where('spouse_id', $this->currentMainSpouseId)->where('spouse_type', "main")->first();

        $eventFormattedData = Carbon::parse($this->event->event_start_date)->format('d') . '-' . Carbon::parse($this->event->event_end_date)->format('d M Y');
        $lastDigit = 1000 + intval($transaction->id);

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->event->category == $eventCategoryC) {
                $getEventcode = $code;
            }
        }

        $tempTransactionId = $this->event->year . "$getEventcode" . "$lastDigit";
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->currentMainSpouseId;

        $details1 = [
            'name' => $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName,
            'eventLink' => $this->event->link,
            'eventName' => $this->event->name,
            'eventDates' => $eventFormattedData,
            'eventLocation' => $this->event->location,
            'eventCategory' => $this->event->category,

            'nationality' => $this->nationality,
            'country' => $this->country,
            'city' => $this->city,
            'amountPaid' => 0,
            'transactionId' => $tempTransactionId,
            'invoiceLink' => $invoiceLink,
        ];

        $details2 = [
            'name' => $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName,
            'eventLink' => $this->event->link,
            'eventName' => $this->event->name,
            'eventCategory' => $this->event->category,

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

        $additionalSpouses = AdditionalSpouses::where('main_spouse_id', $this->currentMainSpouseId)->get();
        if (!empty($additionalSpouses)) {
            foreach ($additionalSpouses as $additionalSpouse) {

                $transaction = SpouseTransactions::where('spouse_id', $additionalSpouse->id)->where('spouse_type', "sub")->first();
                $lastDigit = 1000 + intval($transaction->id);
                $tempTransactionId = $this->event->year . "$getEventcode" . "$lastDigit";

                $details1 = [
                    'name' => $additionalSpouse->salutation . " " . $additionalSpouse->first_name . " " . $additionalSpouse->middle_name . " " . $additionalSpouse->last_name,
                    'eventLink' => $this->event->link,
                    'eventName' => $this->event->name,
                    'eventDates' => $eventFormattedData,
                    'eventLocation' => $this->event->location,
                    'eventCategory' => $this->event->category,

                    'nationality' => $additionalSpouse->nationality,
                    'country' => $additionalSpouse->country,
                    'city' => $additionalSpouse->city,
                    'amountPaid' => 0,
                    'transactionId' => $tempTransactionId,
                    'invoiceLink' => $invoiceLink,
                ];

                $details2 = [
                    'name' => $additionalSpouse->salutation . " " . $additionalSpouse->first_name . " " . $additionalSpouse->middle_name . " " . $additionalSpouse->last_name,
                    'eventLink' => $this->event->link,
                    'eventName' => $this->event->name,
                    'eventCategory' => $this->event->category,

                    'invoiceAmount' => $this->finalTotal,
                    'amountPaid' => 0,
                    'balance' => 0,
                    'invoiceLink' => $invoiceLink,
                ];

                if ($paymentStatus == "free") {
                    Mail::to($additionalSpouse->email_address)->queue(new RegistrationPaid($details1));
                    Mail::to($additionalSpouse->email_address)->queue(new RegistrationPaymentConfirmation($details2));
                } else {
                    Mail::to($additionalSpouse->email_address)->queue(new RegistrationUnpaid($details1));
                }
            }
        }
        return redirect()->route('register.success.view', ['eventCategory' => $this->event->category, 'eventId' => $this->event->id, 'eventYear' => $this->event->year, 'mainDelegateId' => $this->currentMainSpouseId]);
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
                        "redirectResponseUrl" => "$appUrl/capturePayment?sessionId=$this->sessionId&mainDelegateId=$this->currentMainSpouseId&registrationFormType=spouse",
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
        $this->showAddSpouseModal = true;
    }

    public function resetAddModalFields()
    {
        $this->subSalutation = null;
        $this->subFirstName = null;
        $this->subMiddleName = null;
        $this->subLastName = null;
        $this->subNationality = null;
        $this->subCountry = null;
        $this->subCity = null;
        $this->subEmailAddress = null;
        $this->subMobileNumber = null;

        $this->emailSubExistingError = null;
        $this->emailSubAlreadyUsedError = null;
    }


    public function closeAddModal()
    {
        $this->showAddSpouseModal = false;
        $this->resetAddModalFields();
    }



    public function openEditModal($subSpouseId)
    {
        $this->showEditSpouseModal = true;
        foreach ($this->additionalSpouses as $additionalSpouse) {
            if ($additionalSpouse['subSpouseId'] == $subSpouseId) {
                $this->subIdEdit = $additionalSpouse['subSpouseId'];
                $this->subSalutationEdit = $additionalSpouse['subSalutation'];
                $this->subFirstNameEdit = $additionalSpouse['subFirstName'];
                $this->subMiddleNameEdit = $additionalSpouse['subMiddleName'];
                $this->subLastNameEdit = $additionalSpouse['subLastName'];
                $this->subNationalityEdit = $additionalSpouse['subNationality'];
                $this->subCountryEdit = $additionalSpouse['subCountry'];
                $this->subCityEdit = $additionalSpouse['subCity'];
                $this->subEmailAddressEdit = $additionalSpouse['subEmailAddress'];
                $this->subMobileNumberEdit = $additionalSpouse['subMobileNumber'];
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
        $this->subNationalityEdit = null;
        $this->subCountryEdit = null;
        $this->subCityEdit = null;
        $this->subEmailAddressEdit = null;
        $this->subMobileNumberEdit = null;

        $this->emailSubExistingError = null;
        $this->emailSubAlreadyUsedError = null;
    }


    public function closeEditModal()
    {
        $this->showEditSpouseModal = false;
        $this->resetEditModalFields();
    }



    public function saveAdditionalSpouse()
    {
        $this->emailSubAlreadyUsedError = null;
        $this->emailSubExistingError = null;

        $this->validate(
            [
                'subFirstName' => 'required',
                'subLastName' => 'required',
                'subNationality' => 'required',
                'subCountry' => 'required',
                'subCity' => 'required',
                'subEmailAddress' => 'required|email',
                'subMobileNumber' => 'required',
            ],
            [
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
                array_push($this->additionalSpouses, [
                    'subSpouseId' => $uuid->toString(),
                    'subSalutation' => $this->subSalutation,
                    'subFirstName' => $this->subFirstName,
                    'subMiddleName' => $this->subMiddleName,
                    'subLastName' => $this->subLastName,
                    'subNationality' => $this->subNationality,
                    'subCountry' => $this->subCountry,
                    'subCity' => $this->subCity,
                    'subEmailAddress' => $this->subEmailAddress,
                    'subMobileNumber' => $this->subMobileNumber,
                ]);

                $this->resetAddModalFields();
                $this->showAddSpouseModal = false;
            }
        }
    }


    public function removeAdditionalSpouse($subSpouseId)
    {
        $arrayTemp = array_filter($this->additionalSpouses, function ($item) use ($subSpouseId) {
            return $item['subSpouseId'] != $subSpouseId;
        });

        $this->additionalSpouses = [];

        foreach ($arrayTemp as $spouse) {
            array_push($this->additionalSpouses, $spouse);
        }
    }

    public function editAdditionalSpouse($subSpouseId)
    {
        $this->emailSubAlreadyUsedError = null;
        $this->emailSubExistingError = null;

        $this->validate(
            [
                'subFirstNameEdit' => 'required',
                'subLastNameEdit' => 'required',
                'subNationalityEdit' => 'required',
                'subCountryEdit' => 'required',
                'subCityEdit' => 'required',
                'subEmailAddressEdit' => 'required|email',
                'subMobileNumberEdit' => 'required',
            ],
            [
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
            foreach ($this->additionalSpouses as $additionalSpouse) {
                if ($additionalSpouse['subSpouseId'] != $subSpouseId) {
                    if ($additionalSpouse['subEmailAddress'] == $this->subEmailAddressEdit) {
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

                for ($i = 0; $i < count($this->additionalSpouses); $i++) {
                    if ($this->additionalSpouses[$i]['subSpouseId'] == $subSpouseId) {
                        $this->additionalSpouses[$i]['subSalutation'] = $this->subSalutationEdit;
                        $this->additionalSpouses[$i]['subFirstName'] = $this->subFirstNameEdit;
                        $this->additionalSpouses[$i]['subMiddleName'] = $this->subMiddleNameEdit;
                        $this->additionalSpouses[$i]['subLastName'] = $this->subLastNameEdit;
                        $this->additionalSpouses[$i]['subNationality'] = $this->subNationalityEdit;
                        $this->additionalSpouses[$i]['subCountry'] = $this->subCountryEdit;
                        $this->additionalSpouses[$i]['subCity'] = $this->subCityEdit;
                        $this->additionalSpouses[$i]['subEmailAddress'] = $this->subEmailAddressEdit;
                        $this->additionalSpouses[$i]['subMobileNumber'] = $this->subMobileNumberEdit;

                        $this->resetEditModalFields();
                        $this->showEditSpouseModal = false;
                    }
                }
            }
        }
    }



    public function checkEmailIfExistsInDatabase($emailAddress)
    {
        $allSpouses = SpouseTransactions::where('event_id', $this->event->id)->where('event_category', $this->event->category)->get();

        $countMainSpouse = 0;
        $countSubSpouse = 0;

        if (!$allSpouses->isEmpty()) {
            foreach ($allSpouses as $spouse) {
                if ($spouse->spouse_type == "main") {
                    $mainSpouse = MainSpouses::where('id', $spouse->spouse_id)->where('email_address', $emailAddress)->where('registration_status', '!=', 'droppedOut')->first();
                    if ($mainSpouse != null) {
                        $countMainSpouse++;
                    }
                } else {
                    $subSpouse = AdditionalSpouses::where('id', $spouse->spouse_id)->where('email_address', $emailAddress)->first();
                    if ($subSpouse != null) {
                        $registrationStatsMain = MainSpouses::where('id', $subSpouse->main_spouse_id)->value('registration_status');
                        if ($registrationStatsMain != "droppedOut") {
                            $countSubSpouse++;
                        }
                    }
                }
            }
        }

        if ($countMainSpouse == 0 && $countSubSpouse == 0) {
            return false;
        } else {
            return true;
        }
    }


    public function checkEmailIfUsedAlreadyMain($emailAddress)
    {
        if (count($this->additionalSpouses) == 0) {
            return false;
        } else {
            foreach ($this->additionalSpouses as $additionalSpouse) {
                if ($emailAddress == $additionalSpouse['subEmailAddress']) {
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
            if (count($this->additionalSpouses) == 0) {
                return false;
            } else {
                foreach ($this->additionalSpouses as $additionalSpouse) {
                    if ($emailAddress == $additionalSpouse['subEmailAddress']) {
                        return true;
                        break;
                    }
                }
            }
        }
    }
}
