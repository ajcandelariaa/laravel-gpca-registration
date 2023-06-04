<?php

namespace App\Http\Livewire;

use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Mail\RegistrationUnpaid;
use Livewire\Component;
use App\Models\Member as Members;
use App\Models\PromoCode as PromoCodes;
use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\EventDelegateFee;
use App\Models\Transaction as Transactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;


class RegistrationForm extends Component
{
    public $countries;
    public $companySectors;
    public $salutations;

    public $members, $event, $delegateFees;

    public $finalEbEndDate, $finalStdStartDate;
    public $currentStep = 1;
    public $showAddDelegateModal = false;
    public $showEditDelegateModal = false;
    public $additionalDelegates = [];


    // DELEGATE PASS TYPE
    public $delegatePassType, $rateType;

    // COMPANY INFO
    public $companyName, $companySector, $companyAddress, $companyCountry, $companyCity, $companyLandlineNumber, $companyMobileNumber, $assistantEmailAddress, $heardWhere;

    // MAIN DELEGATE
    public $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $nationality, $jobTitle, $badgeType, $promoCode, $promoCodeDiscount;

    // SUB DELEGATE
    public $subSalutation, $subFirstName, $subMiddleName, $subLastName, $subEmailAddress, $subMobileNumber, $subNationality, $subJobTitle, $subBadgeType, $subPromoCode, $subPromoCodeDiscount;

    // SUB DELEGATE EDIT
    public $subIdEdit, $subSalutationEdit, $subFirstNameEdit, $subMiddleNameEdit, $subLastNameEdit, $subEmailAddressEdit, $subMobileNumberEdit, $subNationalityEdit, $subJobTitleEdit, $subBadgeTypeEdit, $subPromoCodeEdit, $subPromoCodeDiscountEdit;

    // 3RD PAGE
    public $paymentMethod, $finalEventStartDate, $finalEventEndDate, $finalQuantity, $finalUnitPrice, $finalNetAmount, $finalDiscount, $finalVat, $finalTotal;
    public $delegatInvoiceDetails = array();
    public $currentMainDelegateId;

    // 4TH PAGE
    public $sessionId, $cardDetails, $orderId, $transactionId, $htmlCodeOTP;

    // ERROR CHECKER
    public $emailMainExistingError, $emailSubExistingError, $emailMainAlreadyUsedError, $emailSubAlreadyUsedError;
    public $delegatePassTypeError, $paymentMethodError, $rateTypeString;
    public $promoCodeFailMain, $promoCodeSuccessMain, $promoCodeFailSub, $promoCodeSuccessSub;
    public $promoCodeFailSubEdit, $promoCodeSuccessSubEdit;

    // BANK DETAILS
    public $bankDetails;

    // ADDITIONAL FIELDS
    public $pcAttendingND;
    public $sccAttendingND;


    protected $listeners = ['registrationConfirmed' => 'addtoDatabase', 'emitInitiateAuth' => 'initiateAuthenticationCC', 'emitSubmit' => 'submitBankTransfer', 'emitSubmitStep3' => 'submitStep3'];

    public function mount($data)
    {
        $this->countries = config('app.countries');
        $this->companySectors = config('app.companySectors');
        $this->salutations = config('app.salutations');

        if ($data->category == "AF") {
            $this->bankDetails = config('app.bankDetails.AF');
        } else {
            $this->bankDetails = config('app.bankDetails.DEFAULT');
        }

        $this->event = $data;
        $this->currentStep = 1;

        $this->badgeType = "Delegate";
        $this->subBadgeType = "Delegate";
        $this->subBadgeTypeEdit = "Delegate";

        $this->members = Members::where('active', true)->get();
        $this->delegateFees = EventDelegateFee::where('event_id', $data->id)->where('event_category', $data->category)->get();

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
    }

    public function render()
    {
        return view('livewire.registration.registration-form');
    }

    public function checkUnitPrice()
    {
        $today = Carbon::today();

        // CHECK UNIT PRICE
        if ($this->event->eb_end_date != null && $this->event->eb_member_rate != null && $this->event->eb_nmember_rate != null) {
            if ($today->lte(Carbon::parse($this->event->eb_end_date))) {
                if ($this->delegatePassType == "fullMember") {
                    $this->rateTypeString = "Early Bird Full Member Rate";
                    $this->finalUnitPrice = $this->event->eb_full_member_rate;
                } else if ($this->delegatePassType == "member") {
                    $this->rateTypeString = "Early Bird Member Rate";
                    $this->finalUnitPrice = $this->event->eb_member_rate;
                } else {
                    $this->rateTypeString = "Early Bird Non-Member Rate";
                    $this->finalUnitPrice = $this->event->eb_nmember_rate;
                }
                $this->rateType = "Early Bird";
            } else {
                if ($this->delegatePassType == "fullMember") {
                    $this->rateTypeString = "Standard Full Member Rate";
                    $this->finalUnitPrice = $this->event->std_full_member_rate;
                } else if ($this->delegatePassType == "member") {
                    $this->rateTypeString = "Standard Member Rate";
                    $this->finalUnitPrice = $this->event->std_full_member_rate;
                } else {
                    $this->rateTypeString = "Standard Non-Member Rate";
                    $this->finalUnitPrice = $this->event->std_nmember_rate;
                }
                $this->rateType = "Standard";
            }
        } else {
            if ($this->delegatePassType == "fullMember") {
                $this->rateTypeString = "Standard Full Member Rate";
                $this->finalUnitPrice = $this->event->std_full_member_rate;
            } else if ($this->delegatePassType == "member") {
                $this->rateTypeString = "Standard Member Rate";
                $this->finalUnitPrice = $this->event->std_member_rate;
            } else {
                $this->rateTypeString = "Standard Non-Member Rate";
                $this->finalUnitPrice = $this->event->std_nmember_rate;
            }
            $this->rateType = "Standard";
        }
    }

    public function calculateAmount()
    {
        if ($this->promoCodeDiscount == null) {
            $this->promoCode = null;
            $promoCodeMainDiscountString = "";
        } else {
            $promoCodeMainDiscountString = "- " . $this->promoCodeDiscount . "% discount";
        }

        array_push($this->delegatInvoiceDetails, [
            'delegateDescription' => "Delegate Registration Fee - {$this->rateTypeString} - {$this->badgeType} {$promoCodeMainDiscountString}",
            'delegateNames' => [
                $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName,
            ],
            'badgeType' => $this->badgeType,
            'quantity' => 1,
            'totalDiscount' => $this->finalUnitPrice * ($this->promoCodeDiscount / 100),
            'totalNetAmount' =>  $this->finalUnitPrice - ($this->finalUnitPrice * ($this->promoCodeDiscount / 100)),
            'promoCodeDiscount' => $this->promoCodeDiscount,
        ]);


        if (count($this->additionalDelegates) > 0) {
            for ($i = 0; $i < count($this->additionalDelegates); $i++) {
                $checkIfExisting = false;
                $existingIndex = 0;
                for ($j = 0; $j < count($this->delegatInvoiceDetails); $j++) {
                    if ($this->additionalDelegates[$i]['subBadgeType'] == $this->delegatInvoiceDetails[$j]['badgeType'] && $this->additionalDelegates[$i]['subPromoCodeDiscount'] == $this->delegatInvoiceDetails[$j]['promoCodeDiscount']) {
                        $existingIndex = $j;
                        $checkIfExisting = true;
                        break;
                    }
                }

                if ($checkIfExisting) {
                    array_push(
                        $this->delegatInvoiceDetails[$existingIndex]['delegateNames'],
                        $this->additionalDelegates[$i]['subSalutation'] . " " . $this->additionalDelegates[$i]['subFirstName'] . " " . $this->additionalDelegates[$i]['subMiddleName'] . " " . $this->additionalDelegates[$i]['subLastName']
                    );

                    $quantityTemp = $this->delegatInvoiceDetails[$existingIndex]['quantity'] + 1;
                    $totalDiscountTemp = ($this->finalUnitPrice * ($this->delegatInvoiceDetails[$existingIndex]['promoCodeDiscount'] / 100)) * $quantityTemp;
                    $totalNetAmountTemp = ($this->finalUnitPrice * $quantityTemp) - $totalDiscountTemp;

                    $this->delegatInvoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                    $this->delegatInvoiceDetails[$existingIndex]['totalDiscount'] = $totalDiscountTemp;
                    $this->delegatInvoiceDetails[$existingIndex]['totalNetAmount'] = $totalNetAmountTemp;
                } else {
                    $promoCodeSubDiscountString = ($this->additionalDelegates[$i]['subPromoCodeDiscount'] == null) ? '' : "- " . $this->additionalDelegates[$i]['subPromoCodeDiscount'] . "% discount";
                    array_push($this->delegatInvoiceDetails, [
                        'delegateDescription' => "Delegate Registration Fee - {$this->rateTypeString} - {$this->additionalDelegates[$i]['subBadgeType']}{$promoCodeSubDiscountString}",
                        'delegateNames' => [
                            $this->additionalDelegates[$i]['subSalutation'] . " " . $this->additionalDelegates[$i]['subFirstName'] . " " . $this->additionalDelegates[$i]['subMiddleName'] . " " . $this->additionalDelegates[$i]['subLastName'],
                        ],
                        'badgeType' => $this->additionalDelegates[$i]['subBadgeType'],
                        'quantity' => 1,
                        'totalDiscount' => $this->finalUnitPrice * ($this->additionalDelegates[$i]['subPromoCodeDiscount'] / 100),
                        'totalNetAmount' =>  $this->finalUnitPrice - ($this->finalUnitPrice * ($this->additionalDelegates[$i]['subPromoCodeDiscount'] / 100)),
                        'promoCodeDiscount' => $this->additionalDelegates[$i]['subPromoCodeDiscount'],
                    ]);
                }
            }
        }

        foreach ($this->delegatInvoiceDetails as $delegatInvoiceDetail) {
            $this->finalQuantity += $delegatInvoiceDetail['quantity'];
            $this->finalDiscount += $delegatInvoiceDetail['totalDiscount'];
            $this->finalNetAmount += $delegatInvoiceDetail['totalNetAmount'];
        }
        $this->finalVat = $this->finalNetAmount * ($this->event->event_vat / 100);
        $this->finalTotal = $this->finalNetAmount + $this->finalVat;
    }

    public function resetCalculations()
    {
        $this->delegatInvoiceDetails = array();
        $this->finalQuantity = 0;
        $this->finalDiscount = 0;
        $this->finalNetAmount = 0;
        $this->finalVat = 0;
        $this->finalTotal = 0;
    }

    public function increaseStep()
    {
        if ($this->currentStep == 1) {
            if ($this->delegatePassType != null) {
                $this->delegatePassTypeError = null;
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
                $this->delegatePassTypeError = "Delegate pass type is required";
            }
        } else if ($this->currentStep == 2) {
            $this->validate(
                [
                    'companySector' => 'required',
                    'companyAddress' => 'required',
                    'companyCountry' => 'required',
                    'companyCity' => 'required',
                    'companyMobileNumber' => 'required',
                    'assistantEmailAddress' => 'nullable|email',
                ],
                [
                    'companySector.required' => 'Company sector is required',
                    'companyAddress.required' => 'Company address is required',
                    'companyCountry.required' => 'Country is required',
                    'companyCity.required' => 'City is required',
                    'companyMobileNumber.required' => 'Mobile number is required',
                    'assistantEmailAddress.email' => 'Assistant\'s email address must be a valid email',
                ]
            );
            $this->currentStep += 1;
        } else if ($this->currentStep == 3) {
            $this->resetCalculations();
            $this->paymentMethod = null;
            $this->emailMainAlreadyUsedError = null;
            $this->emailMainExistingError = null;

            $this->validate(
                [
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'emailAddress' => 'required|email',
                    'nationality' => 'required',
                    'mobileNumber' => 'required',
                    'jobTitle' => 'required',
                    'badgeType' => 'required',
                ],
                [
                    'firstName.required' => "First name is required",
                    'lastName.required' => "Last name is required",
                    'emailAddress.required' => "Email address is required",
                    'emailAddress.email' => "Email address must be a valid email",
                    'nationality.required' => "Nationality is required",
                    'mobileNumber.required' => "Mobile number is required",
                    'jobTitle.required' => "Job title is required",
                    'badgeType.required' => "Registration type is required",
                ]
            );

            $this->dispatchBrowserEvent('swal:add-step3-registration-loading-screen');
        } else if ($this->currentStep == 4) {
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
        if ($this->promoCode != null) {
            PromoCodes::where('event_id', $this->event->id)->where('event_category', $this->event->category)->where('active', true)->where('promo_code', $this->promoCode)->where('badge_type', $this->badgeType)->increment('total_usage');
        }

        if ($this->finalTotal == 0) {
            $paymentStatus = "free";
        } else {
            $paymentStatus = "unpaid";
        }

        $newRegistrant = MainDelegates::create([
            'event_id' => $this->event->id,
            'pass_type' => $this->delegatePassType,
            'rate_type' => $this->rateType,
            'rate_type_string' => $this->rateTypeString,

            'company_name' => $this->companyName,
            'company_sector' => $this->companySector,
            'company_address' => $this->companyAddress,
            'company_country' => $this->companyCountry,
            'company_city' => $this->companyCity,
            'company_telephone_number' => $this->companyLandlineNumber,
            'company_mobile_number' => $this->companyMobileNumber,
            'assistant_email_address' => $this->assistantEmailAddress,

            'salutation' => $this->salutation,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'email_address' => $this->emailAddress,
            'mobile_number' => $this->mobileNumber,
            'nationality' => $this->nationality,
            'job_title' => $this->jobTitle,
            'badge_type' => $this->badgeType,
            'pcode_used' => $this->promoCode,

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

            'pc_attending_nd' => $this->pcAttendingND,
            'scc_attending_nd' => $this->sccAttendingND,
        ]);

        $transaction = Transactions::create([
            'event_id' => $this->event->id,
            'event_category' => $this->event->category,
            'delegate_id' => $newRegistrant->id,
            'delegate_type' => "main",
        ]);

        $tempYear = substr(Carbon::now()->year, -2);
        $lastDigit = 1000 + intval($transaction->id);
        $tempOrderId = $this->event->category . "$tempYear" . "$lastDigit";

        $this->currentMainDelegateId = $newRegistrant->id;

        if (!empty($this->additionalDelegates)) {
            foreach ($this->additionalDelegates as $additionalDelegate) {

                if ($additionalDelegate['subPromoCode'] != null) {
                    PromoCodes::where('event_id', $this->event->id)->where('event_category', $this->event->category)->where('active', true)->where('promo_code', $additionalDelegate['subPromoCode'])->where('badge_type', $additionalDelegate['subBadgeType'])->increment('total_usage');
                }

                $newAdditionDelegate = AdditionalDelegates::create([
                    'main_delegate_id' => $newRegistrant->id,
                    'salutation' => $additionalDelegate['subSalutation'],
                    'first_name' => $additionalDelegate['subFirstName'],
                    'middle_name' => $additionalDelegate['subMiddleName'],
                    'last_name' => $additionalDelegate['subLastName'],
                    'job_title' => $additionalDelegate['subJobTitle'],
                    'email_address' => $additionalDelegate['subEmailAddress'],
                    'nationality' => $additionalDelegate['subNationality'],
                    'mobile_number' => $additionalDelegate['subMobileNumber'],
                    'badge_type' => $additionalDelegate['subBadgeType'],
                    'pcode_used' => $additionalDelegate['subPromoCode'],
                ]);

                Transactions::create([
                    'event_id' => $this->event->id,
                    'event_category' => $this->event->category,
                    'delegate_id' => $newAdditionDelegate->id,
                    'delegate_type' => "sub",
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
        if ($this->currentStep == 2) {
            $this->members = Members::where('active', true)->get();
            $this->resetCalculations();
        }

        if ($this->currentStep == 3) {
            $this->resetCalculations();
        }

        if ($this->currentStep == 4) {
            $this->resetCalculations();
        }

        $this->currentStep -= 1;
    }

    public function submit()
    {
        if ($this->currentStep == 5) {
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

        MainDelegates::find($this->currentMainDelegateId)->fill([
            'registration_status' => $registrationStatus,
            'payment_status' => $paymentStatus,
            'paid_date_time' => null,
        ])->save();

        $transaction = Transactions::where('delegate_id', $this->currentMainDelegateId)->where('delegate_type', "main")->first();

        $eventFormattedData = Carbon::parse($this->event->event_start_date)->format('d') . '-' . Carbon::parse($this->event->event_end_date)->format('d M Y');
        $lastDigit = 1000 + intval($transaction->id);

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->event->category == $eventCategoryC) {
                $getEventcode = $code;
            }
        }

        $tempTransactionId = $this->event->year . "$getEventcode" . "$lastDigit";
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->currentMainDelegateId;

        $details1 = [
            'name' => $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName,
            'eventLink' => $this->event->link,
            'eventName' => $this->event->name,
            'eventDates' => $eventFormattedData,
            'eventLocation' => $this->event->location,
            'eventCategory' => $this->event->category,

            'jobTitle' => $this->jobTitle,
            'companyName' => $this->companyName,
            'amountPaid' => 0,
            'transactionId' => $tempTransactionId,
            'invoiceLink' => $invoiceLink,
        ];

        $details2 = [
            'name' => $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName,
            'eventLink' => $this->event->link,
            'eventName' => $this->event->name,

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

        if ($this->assistantEmailAddress != null) {
            if ($paymentStatus == "free") {
                Mail::to($this->assistantEmailAddress)->queue(new RegistrationPaid($details1));
                Mail::to($this->assistantEmailAddress)->queue(new RegistrationPaymentConfirmation($details2));
            } else {
                Mail::to($this->assistantEmailAddress)->queue(new RegistrationUnpaid($details1));
            }
        }

        $addtionalDelegates = AdditionalDelegates::where('main_delegate_id', $this->currentMainDelegateId)->get();
        if (!empty($addtionalDelegates)) {
            foreach ($addtionalDelegates as $additionalDelegate) {

                $transaction = Transactions::where('delegate_id', $additionalDelegate->id)->where('delegate_type', "sub")->first();

                $lastDigit = 1000 + intval($transaction->id);
                $tempTransactionId = $this->event->year . "$getEventcode" . "$lastDigit";

                $details1 = [
                    'name' => $additionalDelegate->salutation . " " . $additionalDelegate->first_name . " " . $additionalDelegate->middle_name . " " . $additionalDelegate->last_name,
                    'eventLink' => $this->event->link,
                    'eventName' => $this->event->name,
                    'eventDates' => $eventFormattedData,
                    'eventLocation' => $this->event->location,
                    'eventCategory' => $this->event->category,

                    'jobTitle' => $additionalDelegate->job_title,
                    'companyName' => $this->companyName,
                    'amountPaid' => 0,
                    'transactionId' => $tempTransactionId,
                    'invoiceLink' => $invoiceLink,
                ];

                $details2 = [
                    'name' => $additionalDelegate->salutation . " " . $additionalDelegate->first_name . " " . $additionalDelegate->middle_name . " " . $additionalDelegate->last_name,
                    'eventLink' => $this->event->link,
                    'eventName' => $this->event->name,

                    'invoiceAmount' => $this->finalTotal,
                    'amountPaid' => 0,
                    'balance' => 0,
                    'invoiceLink' => $invoiceLink,
                ];

                if ($paymentStatus == "free") {
                    Mail::to($additionalDelegate->email_address)->queue(new RegistrationPaid($details1));
                    Mail::to($additionalDelegate->email_address)->queue(new RegistrationPaymentConfirmation($details2));
                } else {
                    Mail::to($additionalDelegate->email_address)->queue(new RegistrationUnpaid($details1));
                }
            }
        }
        return redirect()->route('register.success.view', ['eventCategory' => $this->event->category, 'eventId' => $this->event->id, 'eventYear' => $this->event->year, 'mainDelegateId' => $this->currentMainDelegateId]);
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
                        "redirectResponseUrl" => "$appUrl/capturePayment?sessionId=$this->sessionId&mainDelegateId=$this->currentMainDelegateId",
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
                    'message' => 'Authentication Error',
                    'text' => 'There was a problem authenticating your card, please try again!'
                ]);
            }
        } else {
            $this->dispatchBrowserEvent('swal:remove-loading-button');
            $this->dispatchBrowserEvent('swal:registration-error-authentication', [
                'type' => 'error',
                'message' => 'Authentication Error',
                'text' => 'There was a problem authenticating your card, please try again!'
            ]);
        }
    }

    public function fullMemberClicked()
    {
        $this->companyName = null;
        if ($this->delegatePassType == 'member' || $this->delegatePassType == 'nonMember') {
            $this->delegatePassType = 'fullMember';
        } else if ($this->delegatePassType == 'fullMember'){
            $this->delegatePassType = null;
        } else {
            $this->delegatePassType = 'fullMember';
        }
    }

    public function memberClicked()
    {
        $this->companyName = null;
        if ($this->delegatePassType == 'fullMember' || $this->delegatePassType == 'nonMember') {
            $this->delegatePassType = 'member';
        } else if ($this->delegatePassType == 'member'){
            $this->delegatePassType = null;
        } else {
            $this->delegatePassType = 'member';
        }
    }

    public function nonMemberClicked()
    {
        $this->companyName = null;
        if ($this->delegatePassType == 'fullMember' || $this->delegatePassType == 'member') {
            $this->delegatePassType = 'nonMember';
        } else if ($this->delegatePassType == 'nonMember'){
            $this->delegatePassType = null;
        } else {
            $this->delegatePassType = 'nonMember';
        }
    }

    public function openAddModal()
    {
        $this->showAddDelegateModal = true;
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
        $this->subJobTitle = null;
        $this->promoCodeSuccessSub = null;
        $this->promoCodeFailSub = null;
        $this->subPromoCode = null;
        $this->subPromoCodeDiscount = null;

        $this->emailSubExistingError = null;
        $this->emailSubAlreadyUsedError = null;
    }

    public function closeAddModal()
    {
        $this->showAddDelegateModal = false;
        $this->resetAddModalFields();
    }

    public function openEditModal($subDelegateId)
    {
        $this->showEditDelegateModal = true;
        foreach ($this->additionalDelegates as $additionalDelegate) {
            if ($additionalDelegate['subDelegateId'] == $subDelegateId) {
                $this->subIdEdit = $additionalDelegate['subDelegateId'];
                $this->subSalutationEdit = $additionalDelegate['subSalutation'];
                $this->subFirstNameEdit = $additionalDelegate['subFirstName'];
                $this->subMiddleNameEdit = $additionalDelegate['subMiddleName'];
                $this->subLastNameEdit = $additionalDelegate['subLastName'];
                $this->subEmailAddressEdit = $additionalDelegate['subEmailAddress'];
                $this->subMobileNumberEdit = $additionalDelegate['subMobileNumber'];
                $this->subNationalityEdit = $additionalDelegate['subNationality'];
                $this->subJobTitleEdit = $additionalDelegate['subJobTitle'];
                $this->subBadgeTypeEdit = $additionalDelegate['subBadgeType'];
                $this->subPromoCodeEdit = $additionalDelegate['subPromoCode'];
                $this->subPromoCodeDiscountEdit = $additionalDelegate['subPromoCodeDiscount'];
                $this->promoCodeSuccessSubEdit = $additionalDelegate['promoCodeSuccessSub'];
                $this->promoCodeFailSubEdit = $additionalDelegate['promoCodeFailSub'];
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
        $this->subJobTitleEdit = null;
        $this->subPromoCodeEdit = null;
        $this->subPromoCodeDiscountEdit = null;
        $this->promoCodeSuccessSubEdit = null;
        $this->promoCodeFailSubEdit = null;

        $this->emailSubExistingError = null;
        $this->emailSubAlreadyUsedError = null;
    }

    public function closeEditModal()
    {
        $this->showEditDelegateModal = false;
        $this->resetEditModalFields();
    }

    public function saveAdditionalDelegate()
    {
        $this->emailSubAlreadyUsedError = null;
        $this->emailSubExistingError = null;

        $this->validate(
            [
                'subFirstName' => 'required',
                'subLastName' => 'required',
                'subEmailAddress' => 'required|email',
                'subMobileNumber' => 'required',
                'subNationality' => 'required',
                'subJobTitle' => 'required',
                'subBadgeType' => 'required',
            ],
            [
                'subFirstName.required' => "First name is required",
                'subLastName.required' => "Last name is required",
                'subEmailAddress.required' => "Email address is required",
                'subEmailAddress.email' => "Email address must be a valid email",
                'subMobileNumber.required' => "Mobile number is required",
                'subNationality.required' => "Nationality is required",
                'subJobTitle.required' => "Job title is required",
                'subBadgeType.required' => "Badge type is required",
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
                array_push($this->additionalDelegates, [
                    'subDelegateId' => $uuid->toString(),
                    'subSalutation' => $this->subSalutation,
                    'subFirstName' => $this->subFirstName,
                    'subMiddleName' => $this->subMiddleName,
                    'subLastName' => $this->subLastName,
                    'subEmailAddress' => $this->subEmailAddress,
                    'subMobileNumber' => $this->subMobileNumber,
                    'subNationality' => $this->subNationality,
                    'subJobTitle' => $this->subJobTitle,
                    'subBadgeType' => $this->subBadgeType,
                    'subPromoCode' => ($this->promoCodeSuccessSub != null) ? $this->subPromoCode : null,
                    'subPromoCodeDiscount' => $this->subPromoCodeDiscount,
                    'promoCodeSuccessSub' => $this->promoCodeSuccessSub,
                    'promoCodeFailSub' => $this->promoCodeFailSub,
                ]);

                $this->resetAddModalFields();
                $this->showAddDelegateModal = false;
            }
        }
    }

    public function removeAdditionalDelegate($subDelegateId)
    {
        $arrayTemp = array_filter($this->additionalDelegates, function ($item) use ($subDelegateId) {
            return $item['subDelegateId'] != $subDelegateId;
        });

        $this->additionalDelegates = [];

        foreach ($arrayTemp as $delegate) {
            array_push($this->additionalDelegates, $delegate);
        }
    }

    public function editAdditionalDelegate($subDelegateId)
    {
        $this->emailSubAlreadyUsedError = null;
        $this->emailSubExistingError = null;

        $this->validate(
            [
                'subFirstNameEdit' => 'required',
                'subLastNameEdit' => 'required',
                'subEmailAddressEdit' => 'required|email',
                'subMobileNumberEdit' => 'required',
                'subNationalityEdit' => 'required',
                'subJobTitleEdit' => 'required',
                'subBadgeTypeEdit' => 'required',
            ],
            [
                'subFirstNameEdit.required' => "First name is required",
                'subLastNameEdit.required' => "Last name is required",
                'subEmailAddressEdit.required' => "Email address is required",
                'subEmailAddressEdit.email' => "Email address must be a valid email",
                'subMobileNumberEdit.required' => "Mobile number is required",
                'subNationalityEdit.required' => "Nationality is required",
                'subJobTitleEdit.required' => "Job title is required",
                'subBadgeTypeEdit.required' => "Badge type is required",
            ]
        );

        $tempCheckEmail = false;

        if ($this->subEmailAddressEdit == $this->emailAddress) {
            $tempCheckEmail = true;
        } else {
            foreach ($this->additionalDelegates as $additionalDelegate) {
                if ($additionalDelegate['subDelegateId'] != $subDelegateId) {
                    if ($additionalDelegate['subEmailAddress'] == $this->subEmailAddressEdit) {
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

                for ($i = 0; $i < count($this->additionalDelegates); $i++) {
                    if ($this->additionalDelegates[$i]['subDelegateId'] == $subDelegateId) {
                        $this->additionalDelegates[$i]['subSalutation'] = $this->subSalutationEdit;
                        $this->additionalDelegates[$i]['subFirstName'] = $this->subFirstNameEdit;
                        $this->additionalDelegates[$i]['subMiddleName'] = $this->subMiddleNameEdit;
                        $this->additionalDelegates[$i]['subLastName'] = $this->subLastNameEdit;
                        $this->additionalDelegates[$i]['subEmailAddress'] = $this->subEmailAddressEdit;
                        $this->additionalDelegates[$i]['subMobileNumber'] = $this->subMobileNumberEdit;
                        $this->additionalDelegates[$i]['subNationality'] = $this->subNationalityEdit;
                        $this->additionalDelegates[$i]['subJobTitle'] = $this->subJobTitleEdit;
                        $this->additionalDelegates[$i]['subBadgeType'] = $this->subBadgeTypeEdit;
                        $this->additionalDelegates[$i]['subPromoCode'] = ($this->promoCodeSuccessSubEdit != null) ? $this->subPromoCodeEdit : null;
                        $this->additionalDelegates[$i]['subPromoCodeDiscount'] = $this->subPromoCodeDiscountEdit;
                        $this->additionalDelegates[$i]['promoCodeSuccessSub'] = $this->promoCodeSuccessSubEdit;
                        $this->additionalDelegates[$i]['promoCodeFailSub'] = $this->promoCodeFailSubEdit;

                        $this->resetEditModalFields();
                        $this->showEditDelegateModal = false;
                    }
                }
            }
        }
    }

    public function applyPromoCodeMain()
    {
        if ($this->promoCode == null) {
            $this->promoCodeFailMain = "Promo code is required.";
        } else {
            $promoCode = PromoCodes::where('event_id', $this->event->id)->where('event_category', $this->event->category)->where('active', true)->where('promo_code', $this->promoCode)->first();
            if ($promoCode == null) {
                $this->promoCodeFailMain = "Invalid code";
            } else {
                if ($promoCode->total_usage < $promoCode->number_of_codes) {
                    $validityDateTime = Carbon::parse($promoCode->validity);
                    if (Carbon::now()->lt($validityDateTime)) {
                        $this->promoCodeFailMain = null;
                        $this->badgeType = $promoCode->badge_type;
                        $this->promoCodeDiscount = $promoCode->discount;
                        $this->promoCodeSuccessMain = "$promoCode->discount% discount will be availed upon submitting the registration";
                    } else {
                        $this->promoCodeFailMain = "Code is expired already";
                    }
                } else {
                    $this->promoCodeFailMain = "Code has reached its capacity";
                }
            }
        }
    }

    public function removePromoCodeMain()
    {
        $this->promoCode = null;
        $this->promoCodeDiscount = null;
        $this->promoCodeFailMain = null;
        $this->promoCodeSuccessMain = null;
    }

    public function applyPromoCodeSub()
    {
        if ($this->subPromoCode == null) {
            $this->promoCodeFailSub = "Promo code is required.";
        } else {
            $promoCode = PromoCodes::where('event_id', $this->event->id)->where('event_category', $this->event->category)->where('active', true)->where('promo_code', $this->subPromoCode)->first();
            if ($promoCode == null) {
                $this->promoCodeFailSub = "Invalid code";
            } else {
                if ($promoCode->total_usage < $promoCode->number_of_codes) {
                    $validityDateTime = Carbon::parse($promoCode->validity);
                    if (Carbon::now()->lt($validityDateTime)) {
                        $this->subPromoCodeDiscount = $promoCode->discount;
                        $this->subBadgeType = $promoCode->badge_type;
                        $this->promoCodeSuccessSub = "$promoCode->discount% discount will be availed upon submitting the registration";
                        $this->promoCodeFailSub = null;
                    } else {
                        $this->promoCodeFailSub = "Code is expired already";
                    }
                } else {
                    $this->promoCodeFailSub = "Code has reached its capacity";
                }
            }
        }
    }

    public function removePromoCodeSub()
    {
        $this->subPromoCode = null;
        $this->subPromoCodeDiscount = null;
        $this->promoCodeFailSub = null;
        $this->promoCodeSuccessSub = null;
    }

    public function applyPromoCodeSubEdit()
    {
        if ($this->subPromoCodeEdit == null) {
            $this->promoCodeFailSubEdit = "Promo code is required.";
        } else {
            $promoCode = PromoCodes::where('event_id', $this->event->id)->where('event_category', $this->event->category)->where('active', true)->where('promo_code', $this->subPromoCodeEdit)->first();
            if ($promoCode == null) {
                $this->promoCodeFailSubEdit = "Invalid code";
            } else {
                if ($promoCode->total_usage < $promoCode->number_of_codes) {
                    $validityDateTime = Carbon::parse($promoCode->validity);
                    if (Carbon::now()->lt($validityDateTime)) {
                        $this->subPromoCodeDiscountEdit = $promoCode->discount;
                        $this->subBadgeTypeEdit = $promoCode->badge_type;
                        $this->promoCodeSuccessSubEdit = "$promoCode->discount% discount will be availed upon submitting the registration";
                        $this->promoCodeFailSubEdit = null;
                    } else {
                        $this->promoCodeFailSubEdit = "Code is expired already";
                    }
                } else {
                    $this->promoCodeFailSubEdit = "Code has reached its capacity";
                }
            }
        }
    }

    public function removePromoCodeSubEdit()
    {
        $this->subPromoCodeEdit = null;
        $this->subPromoCodeDiscountEdit = null;
        $this->promoCodeFailSubEdit = null;
        $this->promoCodeSuccessSubEdit = null;
    }

    public function checkEmailIfExistsInDatabase($emailAddress)
    {
        $allDelegates = Transactions::where('event_id', $this->event->id)->where('event_category', $this->event->category)->get();

        $countMainDelegate = 0;
        $countSubDelegate = 0;

        if (!$allDelegates->isEmpty()) {
            foreach ($allDelegates as $delegate) {
                if ($delegate->delegate_type == "main") {
                    $mainDelegate = MainDelegates::where('id', $delegate->delegate_id)->where('email_address', $emailAddress)->where('registration_status', '!=', 'droppedOut')->first();
                    if ($mainDelegate != null) {
                        $countMainDelegate++;
                    }
                } else {
                    $subDelegate = AdditionalDelegates::where('id', $delegate->delegate_id)->where('email_address', $emailAddress)->first();
                    if ($subDelegate != null) {
                        $registrationStatsMain = MainDelegates::where('id', $subDelegate->main_delegate_id)->value('registration_status');
                        if ($registrationStatsMain != "droppedOut") {
                            $countSubDelegate++;
                        }
                    }
                }
            }
        }

        if ($countMainDelegate == 0 && $countSubDelegate == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function checkEmailIfUsedAlreadyMain($emailAddress)
    {
        if (count($this->additionalDelegates) == 0) {
            return false;
        } else {
            foreach ($this->additionalDelegates as $additionalDelegate) {
                if ($emailAddress == $additionalDelegate['subEmailAddress']) {
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
            if (count($this->additionalDelegates) == 0) {
                return false;
            } else {
                foreach ($this->additionalDelegates as $additionalDelegate) {
                    if ($emailAddress == $additionalDelegate['subEmailAddress']) {
                        return true;
                        break;
                    }
                }
            }
        }
    }
}
