<?php

namespace App\Http\Livewire;

use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Mail\RegistrationUnpaid;
use Livewire\Component;
use App\Models\Member as Members;
use App\Models\RccAwardsMainParticipant as RccAwardsMainParticipants;
use App\Models\RccAwardsAdditionalParticipant as RccAwardsAdditionalParticipants;
use App\Models\RccAwardsDocument as RccAwardsDocuments;
use App\Models\RccAwardsParticipantTransaction as RccAwardsParticipantTransactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Livewire\WithFileUploads;

class RccAwardsRegistrationForm extends Component
{
    use WithFileUploads;

    public $countries, $salutations, $awardsCategories;

    public $members, $event;

    public $eventFormattedStartDate, $finalEbEndDate, $finalStdStartDate, $rateType, $rateTypeString, $currentMainPartcipantId;

    public $currentStep = 1;

    public $participantPassType, $category, $subCategory, $companyName;

    public $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $address, $country, $city, $jobTitle, $heardWhere;

    public $entryForm, $supportingDocuments = [];

    public $paymentMethod, $finalEventStartDate, $finalEventEndDate, $finalQuantity, $finalUnitPrice, $finalNetAmount, $finalDiscount, $finalVat, $finalTotal;

    public $sessionId, $cardDetails, $orderId, $transactionId, $htmlCodeOTP;

    // ERROR CHECKER
    public $participantPassTypeError, $paymentMethodError;
    public $emailMainExistingError, $supportingDocumentsError = [];

    // BANK DETAILS
    public $bankDetails;

    public $eventFormattedDate;


    protected $listeners = ['registrationConfirmed' => 'addtoDatabase', 'emitInitiateAuth' => 'initiateAuthenticationCC', 'emitSubmit' => 'submitBankTransfer', 'emitSubmitStep3' => 'submitStep3'];


    public function mount($data)
    {
        $this->countries = config('app.countries');
        $this->salutations = config('app.salutations');
        $this->awardsCategories = config('app.rccAwardsCategories');

        if ($data->category == "AF" || $data->category == "AFS" || $data->category == "AFV") {
            $this->bankDetails = config('app.bankDetails.AF');
        } else {
            $this->bankDetails = config('app.bankDetails.DEFAULT');
        }

        $this->event = $data;
        $this->currentStep = 1;

        $this->members = Members::where('active', true)->orderBy('name', 'ASC')->get();

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


        $this->eventFormattedDate = Carbon::parse($this->event->event_start_date)->format('j') . '-' . Carbon::parse($this->event->event_end_date)->format('j F Y');

        $this->eventFormattedStartDate = Carbon::parse($this->event->event_start_date)->format('j F Y');
    }

    public function render()
    {
        return view('livewire.registration.rcc_awards.rcc-awards-registration-form');
    }

    public function checkUnitPrice()
    {
        $today = Carbon::today();

        if ($this->event->eb_end_date != null && $this->event->eb_member_rate != null && $this->event->eb_nmember_rate != null) {
            if ($today->lte(Carbon::parse($this->event->eb_end_date))) {
                if ($this->participantPassType == "fullMember") {
                    $this->rateTypeString = "Full member early bird rate";
                    $this->finalUnitPrice = $this->event->eb_full_member_rate;
                } else if ($this->participantPassType == "member") {
                    $this->rateTypeString = "Member early bird rate";
                    $this->finalUnitPrice = $this->event->eb_member_rate;
                } else {
                    $this->rateTypeString = "Non-Member early bird rate";
                    $this->finalUnitPrice = $this->event->eb_nmember_rate;
                }
                $this->rateType = "Early Bird";
            } else {
                if ($this->participantPassType == "fullMember") {
                    $this->rateTypeString = "Full member standard rate";
                    $this->finalUnitPrice = $this->event->std_full_member_rate;
                } else if ($this->participantPassType == "member") {
                    $this->rateTypeString = "Member standard rate";
                    $this->finalUnitPrice = $this->event->std_member_rate;
                } else {
                    $this->rateTypeString = "Non-Member standard rate";
                    $this->finalUnitPrice = $this->event->std_nmember_rate;
                }
                $this->rateType = "Standard";
            }
        } else {
            if ($this->participantPassType == "fullMember") {
                $this->rateTypeString = "Full member standard rate";
                $this->finalUnitPrice = $this->event->std_full_member_rate;
            } else if ($this->participantPassType == "member") {
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
    }

    public function increaseStep()
    {
        if ($this->currentStep == 1) {
            $this->validate(
                [
                    'category' => 'required',
                ],
                [
                    'category.required' => "Category is required",
                ]
            );

            if ($this->category == "Sustainable Environmental Protection" || $this->category == "Community Awareness") {
                $this->validate(
                    [
                        'subCategory' => 'required',
                    ],
                    [
                        'subCategory.required' => "Sub category is required",
                    ]
                );
            }

            if ($this->participantPassType != null) {
                $this->participantPassTypeError = null;
                $this->validate(
                    [
                        'companyName' => 'required',
                    ],
                    [
                        'companyName.required' => "Company name is required",
                    ]
                );

                $this->currentStep += 1;
            } else {
                $this->participantPassTypeError = "Participant pass type is required";
            }
        } else if ($this->currentStep == 2) {
            $this->supportingDocumentsError = [];

            $this->validate(
                [
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'emailAddress' => 'required|email',
                    'mobileNumber' => 'required',
                    'address' => 'required',
                    'country' => 'required',
                    'city' => 'required',
                    'jobTitle' => 'required',
                    'entryForm' => 'required|mimes:pdf,doc,docx',
                ],
                [
                    'firstName.required' => "First name is required",
                    'lastName.required' => "Last name is required",
                    'emailAddress.required' => "Email address is required",
                    'emailAddress.email' => "Email address must be a valid email",
                    'mobileNumber.required' => "Mobile number is required",
                    'address.required' => "Address is required",
                    'country.required' => "Country is required",
                    'city.required' => "City is required",
                    'jobTitle.required' => "Job title is required",
                    'entryForm.required' => "Entry form is required",
                    'entryForm.mimes' => "Entry form must be pdf, doc, docx format.",
                ]
            );

            if ($this->supportingDocuments != null) {
                if (count($this->supportingDocuments) < 5) {
                    foreach ($this->supportingDocuments as $index => $document) {
                        $fileType = $document->getClientOriginalExtension();
                        if ($fileType != 'pdf' && $fileType != 'doc' && $fileType != 'docx') {
                            $message = "File " . $index + 1 . ' must be pdf, doc, docx format.';
                            array_push($this->supportingDocumentsError, $message);
                        }
                    }
                } else {
                    array_push($this->supportingDocumentsError, "Maximum 4 files only");
                }
            }

            if (count($this->supportingDocumentsError) == 0) {
                $this->dispatchBrowserEvent('swal:add-step3-registration-loading-screen');
            }
        } else if ($this->currentStep == 3) {
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
        // if ($this->checkEmailIfExistsInDatabase($this->emailAddress)) {
        //     $this->emailMainExistingError = "Email is already registered, please use another email!";
        // } else {
        // }

        $this->emailMainExistingError = null;
        $this->checkUnitPrice();
        $this->calculateAmount();
        $this->paymentMethod = null;
        $this->currentStep += 1;

        $this->dispatchBrowserEvent('swal:remove-registration-loading-screen');
    }

    public function addtoDatabase()
    {
        if ($this->finalTotal == 0) {
            $paymentStatus = "free";
        } else {
            $paymentStatus = "unpaid";
        }

        $newRegistrant = RccAwardsMainParticipants::create([
            'event_id' => $this->event->id,
            'pass_type' => $this->participantPassType,
            'rate_type' => $this->rateType,
            'rate_type_string' => $this->rateTypeString,

            'category' => $this->category,
            'sub_category' => $this->subCategory,
            'company_name' => $this->companyName,

            'salutation' => $this->salutation,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'email_address' => $this->emailAddress,
            'mobile_number' => $this->mobileNumber,
            'address' => $this->address,
            'country' => $this->country,
            'city' => $this->city,
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

        $currentYear = strval(Carbon::parse($this->event->event_start_date)->year);

        $uploadedEntryForm = $this->entryForm->store('public/rcca/' . $currentYear . '/entryform');

        RccAwardsDocuments::create([
            'event_id' => $this->event->id,
            'event_category' => $this->event->category,
            'participant_id' => $newRegistrant->id,
            'document' => $uploadedEntryForm,
            'document_type' => 'entryForm',
        ]);

        if (count($this->supportingDocuments) > 0) {
            foreach ($this->supportingDocuments as $document) {
                $uploadedSupportingDocument = $document->store('public/rcca/' . $currentYear . '/supportingdocument');

                RccAwardsDocuments::create([
                    'event_id' => $this->event->id,
                    'event_category' => $this->event->category,
                    'participant_id' => $newRegistrant->id,
                    'document' => $uploadedSupportingDocument,
                    'document_type' => 'supportingDocument',
                ]);
            }
        }

        $transaction = RccAwardsParticipantTransactions::create([
            'event_id' => $this->event->id,
            'event_category' => $this->event->category,
            'participant_id' => $newRegistrant->id,
            'participant_type' => "main",
        ]);

        $tempYear = substr(Carbon::now()->year, -2);
        $lastDigit = 1000 + intval($transaction->id);
        $tempOrderId = $this->event->category . "$tempYear" . "$lastDigit";

        $this->currentMainPartcipantId = $newRegistrant->id;

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
        if ($this->currentStep == 2) {
            $this->members = Members::where('active', true)->orderBy('name', 'ASC')->get();
        }

        if($this->currentStep == 3){
            $this->entryForm = null;
            $this->supportingDocuments = [];
            $this->supportingDocumentsError = [];
        }
        
        $this->resetCalculations();
        $this->currentStep -= 1;
    }

    public function submit()
    {
        if ($this->currentStep == 4) {
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

        RccAwardsMainParticipants::find($this->currentMainPartcipantId)->fill([
            'registration_status' => $registrationStatus,
            'payment_status' => $paymentStatus,
            'paid_date_time' => null,
        ])->save();

        $transaction = RccAwardsParticipantTransactions::where('participant_id', $this->currentMainPartcipantId)->where('participant_type', "main")->first();
        
        $eventFormattedData = Carbon::parse($this->event->event_start_date)->format('j F Y');
        $lastDigit = 1000 + intval($transaction->id);

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->event->category == $eventCategoryC) {
                $getEventcode = $code;
            }
        }

        $tempTransactionId = $this->event->year . "$getEventcode" . "$lastDigit";
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->currentMainPartcipantId;

        $entryFormId = RccAwardsDocuments::where('event_id', $this->event->id)->where('participant_id', $this->currentMainPartcipantId)->where('document_type', 'entryForm')->value('id');

        $getSupportingDocumentFiles = RccAwardsDocuments::where('event_id', $this->event->id)->where('participant_id', $this->currentMainPartcipantId)->where('document_type', 'supportingDocument')->get();

        $supportingDocumentsDownloadId = [];

        if ($getSupportingDocumentFiles->isNotEmpty()) {
            foreach ($getSupportingDocumentFiles as $supportingDocument) {
                $supportingDocumentsDownloadId[] = $supportingDocument->id;
            }
        }

        $downloadLink = env('APP_URL') . '/download-file/';

        $details1 = [
            'name' => $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName,
            'eventLink' => $this->event->link,
            'eventName' => $this->event->name,
            'eventDates' => $eventFormattedData,
            'eventLocation' => $this->event->location,
            'eventCategory' => $this->event->category,

            'jobTitle' => $this->jobTitle,
            'companyName' => $this->companyName,
            'emailAddress' => $this->emailAddress,
            'mobileNumber' => $this->mobileNumber,
            'city' => $this->city,
            'country' => $this->country,
            'category' => $this->category,
            'subCategory' => ($this->subCategory != null) ? $this->subCategory : 'N/A',
            'entryFormId' => $entryFormId,
            'supportingDocumentsDownloadId' => $supportingDocumentsDownloadId,
            'downloadLink' => $downloadLink,

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

        return redirect()->route('register.success.view', ['eventCategory' => $this->event->category, 'eventId' => $this->event->id, 'eventYear' => $this->event->year, 'mainDelegateId' => $this->currentMainPartcipantId]);
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
                        "redirectResponseUrl" => "$appUrl/capturePayment?sessionId=$this->sessionId&mainDelegateId=$this->currentMainPartcipantId&registrationFormType=rccawards",
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

    public function fullMemberClicked()
    {
        $this->companyName = null;
        if ($this->participantPassType == 'member' || $this->participantPassType == 'nonMember') {
            $this->participantPassType = 'fullMember';
        } else if ($this->participantPassType == 'fullMember') {
            $this->participantPassType = null;
        } else {
            $this->participantPassType = 'fullMember';
        }
    }

    public function memberClicked()
    {
        $this->companyName = null;
        if ($this->participantPassType == 'fullMember' || $this->participantPassType == 'nonMember') {
            $this->participantPassType = 'member';
        } else if ($this->participantPassType == 'member') {
            $this->participantPassType = null;
        } else {
            $this->participantPassType = 'member';
        }
    }

    public function nonMemberClicked()
    {
        $this->companyName = null;
        if ($this->participantPassType == 'fullMember' || $this->participantPassType == 'member') {
            $this->participantPassType = 'nonMember';
        } else if ($this->participantPassType == 'nonMember') {
            $this->participantPassType = null;
        } else {
            $this->participantPassType = 'nonMember';
        }
    }

    public function checkEmailIfExistsInDatabase($emailAddress)
    {
        $allParticipants = RccAwardsParticipantTransactions::where('event_id', $this->event->id)->where('event_category', $this->event->category)->get();

        $countMainParticipant = 0;
        $countSubParticipant = 0;

        if (!$allParticipants->isEmpty()) {
            foreach ($allParticipants as $participant) {
                if ($participant->participant_type == "main") {
                    $mainParticipant = RccAwardsMainParticipants::where('id', $participant->participant_id)->where('email_address', $emailAddress)->where('registration_status', '!=', 'droppedOut')->first();
                    if ($mainParticipant != null) {
                        $countMainParticipant++;
                    }
                } else {
                    $subParticipant = RccAwardsAdditionalParticipants::where('id', $participant->participant_id)->where('email_address', $emailAddress)->first();
                    if ($subParticipant != null) {
                        $registrationStatsMain = RccAwardsMainParticipants::where('id', $subParticipant->main_participant_id)->value('registration_status');
                        if ($registrationStatsMain != "droppedOut") {
                            $countSubParticipant++;
                        }
                    }
                }
            }
        }

        if ($countMainParticipant == 0 && $countSubParticipant == 0) {
            return false;
        } else {
            return true;
        }
    }
}
