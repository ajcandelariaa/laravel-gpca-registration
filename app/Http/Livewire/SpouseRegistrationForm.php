<?php

namespace App\Http\Livewire;

use App\Mail\RegistrationFree;
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
    public $currentStep = 1;

    // DELEGATE PASS TYPE
    public $spousePassType, $rateType, $rateTypeString;

    // MAIN DELEGATE
    public $salutation, $firstName, $middleName, $lastName, $nationality, $country, $city, $emailAddress, $mobileNumber, $heardWhere, $referenceDelegateName, $selectedDays = [];

    public $day_one = false;
    public $day_two = false;
    public $day_three = false;
    public $day_four = false;

    // 3RD PAGE
    public $paymentMethod, $finalStdStartDate, $finalEventStartDate, $finalEventEndDate, $finalQuantity, $finalUnitPrice, $finalNetAmount, $finalDiscount, $finalVat, $finalTotal;

    public $currentMainSpouseId;

    // 4TH PAGE
    public $sessionId, $cardDetails, $orderId, $transactionId, $htmlCodeOTP;

    // ERROR CHECKER
    public $emailMainExistingError, $emailSubExistingError, $paymentMethodError;

    // BANK DETAILS
    public $bankDetails;

    public $eventFormattedDate;

    public $ccEmailNotif;

    protected $listeners = ['registrationConfirmed' => 'addtoDatabase', 'emitInitiateAuth' => 'initiateAuthenticationCC', 'emitSubmit' => 'submitBankTransfer', 'emitSubmitStep3' => 'submitStep3'];

    public function mount($data)
    {
        $this->countries = config('app.countries');
        $this->salutations = config('app.salutations');
        $this->bankDetails = config('app.bankDetails.AF');

        $this->event = $data;
        $this->currentStep = 1;

        $this->cardDetails = false;

        $this->finalStdStartDate = Carbon::parse($this->event->std_start_date)->format('d M Y');
        $this->finalEventStartDate = Carbon::parse($this->event->event_start_date)->format('d M Y');
        $this->finalEventEndDate = Carbon::parse($this->event->event_end_date)->format('d M Y');

        $this->eventFormattedDate = Carbon::parse($this->event->event_start_date)->format('j') . '-' . Carbon::parse($this->event->event_end_date)->format('j F Y');

        $this->ccEmailNotif = config('app.ccEmailNotif.default');
    }

    public function render()
    {
        return view('livewire.registration.spouse.spouse-registration-form');
    }

    public function calculateAmount()
    {
        $price = 0;

        foreach ($this->selectedDays as $selectedDay) {
            if ($selectedDay == 1) {
                $price += 200;
                $this->day_one = true;
            } else if ($selectedDay == 2) {
                $price += 220;
                $this->day_two = true;
            } else if ($selectedDay == 3) {
                $price += 200;
                $this->day_three = true;
            } else {
                $price += 200;
                $this->day_four = true;
            }
        }

        $this->finalUnitPrice = $price;
        $this->finalQuantity = 1;
        $this->finalDiscount = 0;
        $this->finalNetAmount = $this->finalUnitPrice;
        $this->finalVat = $this->finalNetAmount * ($this->event->event_vat / 100);
        $this->finalTotal = $this->finalNetAmount + $this->finalVat;
    }

    public function resetCalculations()
    {
        $this->finalQuantity = 0;
        $this->finalDiscount = 0;
        $this->finalNetAmount = 0;
        $this->finalVat = 0;
        $this->finalTotal = 0;
        $this->day_one = false;
        $this->day_two = false;
        $this->day_three = false;
        $this->day_four = false;
    }

    public function increaseStep()
    {
        if ($this->currentStep == 1) {
            $this->resetCalculations();
            $this->paymentMethod = null;
            $this->emailMainExistingError = null;

            $this->validate(
                [
                    'selectedDays' => 'required',
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
                    'selectedDays.required' => "Choose atleast one day",
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
        if ($this->checkEmailIfExistsInDatabase($this->emailAddress)) {
            $this->emailMainExistingError = "Email is already registered, please use another email!";
        } else {
            $this->emailMainExistingError = null;
            $this->calculateAmount();
            $this->currentStep += 1;
        }

        $this->dispatchBrowserEvent('swal:remove-registration-loading-screen');
    }



    public function addtoDatabase()
    {
        $paymentStatus = "unpaid";

        $newRegistrant = MainSpouses::create([
            'event_id' => $this->event->id,
            'pass_type' => "nonMember",
            'rate_type' => "Standard",
            'rate_type_string' => "Standard spouse rate",

            'salutation' => $this->salutation,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'nationality' => $this->nationality,
            'country' => $this->country,
            'city' => $this->city,
            'email_address' => $this->emailAddress,
            'mobile_number' => $this->mobileNumber,

            'day_one' => $this->day_one,
            'day_two' => $this->day_two,
            'day_three' => $this->day_three,
            'day_four' => $this->day_four,

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
        $paymentStatus = "unpaid";
        $registrationStatus = "pending";

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

        $eventDatesDescription = " ";
        
        if($this->day_one){
            $eventDatesDescription = $eventDatesDescription . "1, ";
        }

        if($this->day_two){
            $eventDatesDescription = $eventDatesDescription . "2, ";
        }

        if($this->day_three){
            $eventDatesDescription = $eventDatesDescription . "3, ";
        }

        if($this->day_four){
            $eventDatesDescription = $eventDatesDescription . "4, ";
        }

        $eventDatesDescription = "December$eventDatesDescription 2023";

        $details1 = [
            'eventCategory' => $this->event->category,
            'eventYear' => $this->event->year,

            'eventDatesDescription' => $eventDatesDescription,

            'name' => $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName,
            'referenceDelegateName' => $this->referenceDelegateName,
            'emailAddress' => $this->emailAddress,
            'mobileNumber' => $this->mobileNumber,
            'country' => $this->country,
            'city' => $this->city,
            'amountPaid' => 0,
            'transactionId' => $tempTransactionId,

            'invoiceLink' => $invoiceLink,
        ];

        try {
            Mail::to($this->emailAddress)->cc($this->ccEmailNotif)->send(new RegistrationUnpaid($details1));
        } catch (\Exception $e) {
            Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationUnpaid($details1));
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
}
