<?php

namespace App\Http\Livewire;

use App\Mail\RegistrationFree;
use App\Mail\RegistrationUnpaid;
use App\Models\MainVisitor as MainVisitors;
use App\Models\AdditionalVisitor as AdditionalVisitors;
use App\Models\VisitorTransaction as VisitorTransactions;
use App\Models\Member as Members;
use App\Models\PromoCode as PromoCodes;
use App\Models\EventDelegateFee;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class VisitorRegistrationForm extends Component
{
    public $countries;
    public $companySectors;
    public $salutations;

    public $members, $event, $visitorFees;

    public $currentStep = 1;
    public $showAddVisitorModal = false, $showEditVisitorModal = false;
    public $additionalVisitors = [];

    // VISITOR PASS TYPE
    public $visitorPassType, $rateType;

    // COMPANY INFO
    public $companyName, $companySector, $companyAddress, $companyCountry, $companyCity, $companyLandlineNumber, $companyMobileNumber, $assistantEmailAddress, $heardWhere;

    // MAIN VISITOR
    public $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $nationality, $jobTitle, $badgeType, $promoCode, $promoCodeDiscount, $discountType, $isMainFree = false;

    // SUB VISITOR
    public $subSalutation, $subFirstName, $subMiddleName, $subLastName, $subEmailAddress, $subMobileNumber, $subNationality, $subJobTitle, $subBadgeType, $subPromoCode, $subPromoCodeDiscount, $subDiscountType;

    // SUB VISITOR EDIT
    public $subIdEdit, $subSalutationEdit, $subFirstNameEdit, $subMiddleNameEdit, $subLastNameEdit, $subEmailAddressEdit, $subMobileNumberEdit, $subNationalityEdit, $subJobTitleEdit, $subBadgeTypeEdit, $subPromoCodeEdit, $subPromoCodeDiscountEdit, $subDiscountTypeEdit;

    // 3RD PAGE
    public $paymentMethod, $finalEventStartDate, $finalEventEndDate, $finalQuantity, $finalUnitPrice, $finalNetAmount, $finalDiscount, $finalVat, $finalTotal;

    public $visitorInvoiceDetails = array();
    public $currentMainVisitorId;

    // 4TH PAGE
    public $sessionId, $cardDetails, $orderId, $transactionId, $htmlCodeOTP;

    // ERROR CHECKER
    public $emailMainExistingError, $emailSubExistingError, $emailMainAlreadyUsedError, $emailSubAlreadyUsedError;
    public $visitorPassTypeError, $paymentMethodError, $rateTypeString;
    public $promoCodeFailMain, $promoCodeSuccessMain, $promoCodeFailSub, $promoCodeSuccessSub;
    public $promoCodeFailSubEdit, $promoCodeSuccessSubEdit;

    // BANK DETAILS
    public $bankDetails;

    public $eventFormattedDate;

    public $ccEmailNotif;

    protected $listeners = ['registrationConfirmed' => 'addtoDatabase', 'emitInitiateAuth' => 'initiateAuthenticationCC', 'emitSubmit' => 'submitBankTransfer', 'emitSubmitStep3' => 'submitStep3'];

    public function mount($data)
    {
        $this->countries = config('app.countries');
        $this->companySectors = config('app.companySectors');
        $this->salutations = config('app.salutations');
        $this->bankDetails = config('app.bankDetails.AF');

        $this->event = $data;
        $this->currentStep = 1;

        $this->badgeType = "Visitor";
        $this->subBadgeType = "Visitor";
        $this->subBadgeTypeEdit = "Visitor";

        $this->members = Members::where('active', true)->orderBy('name', 'ASC')->get();
        $this->visitorFees = EventDelegateFee::where('event_id', $data->id)->where('event_category', $data->category)->get();

        $this->cardDetails = false;

        $this->finalEventStartDate = Carbon::parse($this->event->event_start_date)->format('d M Y');
        $this->finalEventEndDate = Carbon::parse($this->event->event_end_date)->format('d M Y');

        $this->eventFormattedDate = Carbon::parse($this->event->event_start_date)->format('d') . '-' . Carbon::parse($this->event->event_end_date)->format('d M Y');

        $this->ccEmailNotif = config('app.ccEmailNotif.default');
    }

    public function render()
    {
        return view('livewire.registration.visitor.visitor-registration-form');
    }



    public function checkUnitPrice()
    {
        // CHECK UNIT PRICE
        if ($this->visitorPassType == "fullMember") {
            $this->rateTypeString = "Full member visitor pass rate";
            $this->finalUnitPrice = $this->event->std_full_member_rate;
        } else if ($this->visitorPassType == "member") {
            $this->rateTypeString = "Member visitor pass rate";
            $this->finalUnitPrice = $this->event->std_member_rate;
        } else {
            $this->rateTypeString = "Non-Member visitor pass rate";
            $this->finalUnitPrice = $this->event->std_nmember_rate;
        }
        $this->rateType = "Standard";
    }


    public function calculateAmount()
    {
        if ($this->promoCodeDiscount == null) {
            $this->promoCode = null;
            $visitorDescription = "Visitor registration fee - {$this->rateTypeString} - {$this->badgeType}";

            $tempUnitPrice = $this->finalUnitPrice;
            $tempTotalDiscount = 0;
            $tempTotalNetAmount = $this->finalUnitPrice;
        } else {
            if ($this->discountType == "percentage") {
                $tempUnitPrice = $this->finalUnitPrice;
                $tempTotalDiscount = $this->finalUnitPrice * ($this->promoCodeDiscount / 100);
                $tempTotalNetAmount = $this->finalUnitPrice - ($this->finalUnitPrice * ($this->promoCodeDiscount / 100));
                $visitorDescription = "Visitor Registration Fee - {$this->rateTypeString} - {$this->badgeType} - {$this->promoCodeDiscount} % discount";
            } else if ($this->discountType == "price") {
                $tempUnitPrice = $this->finalUnitPrice;
                $tempTotalDiscount = $this->promoCodeDiscount;
                $tempTotalNetAmount = $this->finalUnitPrice - $this->promoCodeDiscount;
                $visitorDescription = "Visitor Registration Fee - {$this->rateTypeString} - {$this->badgeType} - $ {$this->promoCodeDiscount} discount";
            } else {
                // FIXED RATE
                $promoCode = PromoCodes::where('event_id', $this->event->id)->where('promo_code', $this->promoCode)->first();
                $tempUnitPrice = $promoCode->new_rate;
                $tempTotalDiscount = 0;
                $tempTotalNetAmount = $promoCode->new_rate;
                $visitorDescription = $promoCode->new_rate_description;
            }

            if ($tempTotalNetAmount == 0) {
                $this->isMainFree = true;
            }
        }

        array_push($this->visitorInvoiceDetails, [
            'visitorDescription' => $visitorDescription,
            'visitorNames' => [
                $this->firstName . " " . $this->middleName . " " . $this->lastName,
            ],
            'badgeType' => $this->badgeType,
            'quantity' => 1,
            'totalUnitPrice' => $tempUnitPrice,
            'totalDiscount' => $tempTotalDiscount,
            'totalNetAmount' =>  $tempTotalNetAmount,
            'promoCode' => $this->promoCode,
            'promoCodeDiscount' => $this->promoCodeDiscount,
        ]);

        if (count($this->additionalVisitors) > 0) {
            for ($i = 0; $i < count($this->additionalVisitors); $i++) {
                $checkIfExisting = false;
                $existingIndex = 0;

                for ($j = 0; $j < count($this->visitorInvoiceDetails); $j++) {
                    if ($this->additionalVisitors[$i]['subBadgeType'] == $this->visitorInvoiceDetails[$j]['badgeType'] && $this->additionalVisitors[$i]['subPromoCodeDiscount'] == $this->visitorInvoiceDetails[$j]['promoCodeDiscount']) {
                        $existingIndex = $j;
                        $checkIfExisting = true;
                        break;
                    }
                }

                if ($checkIfExisting) {
                    array_push(
                        $this->visitorInvoiceDetails[$existingIndex]['visitorNames'],
                        $this->additionalVisitors[$i]['subFirstName'] . " " . $this->additionalVisitors[$i]['subMiddleName'] . " " . $this->additionalVisitors[$i]['subLastName']
                    );

                    $quantityTemp = $this->visitorInvoiceDetails[$existingIndex]['quantity'] + 1;

                    if ($this->additionalVisitors[$i]['subDiscountType'] != null) {
                        if ($this->additionalVisitors[$i]['subDiscountType'] == "percentage") {
                            $totalDiscountTemp = ($this->finalUnitPrice * ($this->visitorInvoiceDetails[$existingIndex]['promoCodeDiscount'] / 100)) * $quantityTemp;
                            $totalNetAmountTemp = ($this->finalUnitPrice * $quantityTemp) - $totalDiscountTemp;
                        } else if ($this->additionalVisitors[$i]['subDiscountType'] == "price") {
                            $totalDiscountTemp = $this->visitorInvoiceDetails[$existingIndex]['promoCodeDiscount'] * $quantityTemp;
                            $totalNetAmountTemp = ($this->finalUnitPrice * $quantityTemp) - $totalDiscountTemp;
                        } else {
                            $totalDiscountTemp = 0;
                            $totalNetAmountTemp = $this->visitorInvoiceDetails[$existingIndex]['totalNetAmount'] * $quantityTemp;
                        }
                    } else {
                        $totalDiscountTemp = 0;
                        $totalNetAmountTemp = $this->finalUnitPrice * $quantityTemp;
                    }

                    $this->visitorInvoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                    $this->visitorInvoiceDetails[$existingIndex]['totalDiscount'] = $totalDiscountTemp;
                    $this->visitorInvoiceDetails[$existingIndex]['totalNetAmount'] = $totalNetAmountTemp;
                } else {
                    $tempSubUnitPrice = $this->finalUnitPrice;
                    $tempSubTotalDiscount = 0;
                    $tempSubTotalNetAmount = $this->finalUnitPrice;

                    if ($this->additionalVisitors[$i]['subPromoCodeDiscount'] == null) {
                        $this->additionalVisitors[$i]['subPromoCode'] = null;
                        $visitorSubDescription = "Visitor registration fee - {$this->rateTypeString} - {$this->additionalVisitors[$i]['subBadgeType']}";
                    } else {
                        if ($this->additionalVisitors[$i]['subDiscountType'] == "percentage") {
                            $tempSubTotalDiscount = $this->finalUnitPrice * ($this->additionalVisitors[$i]['subPromoCodeDiscount'] / 100);
                            $tempSubTotalNetAmount = $this->finalUnitPrice - ($this->finalUnitPrice * ($this->additionalVisitors[$i]['subPromoCodeDiscount'] / 100));
                            $visitorSubDescription = "Visitor Registration Fee - {$this->rateTypeString} - {$this->additionalVisitors[$i]['subBadgeType']} - {$this->additionalVisitors[$i]['subPromoCodeDiscount']} % discount";
                        } else if ($this->additionalVisitors[$i]['subDiscountType'] == "price") {
                            $tempSubTotalDiscount = $this->additionalVisitors[$i]['subPromoCodeDiscount'];
                            $tempSubTotalNetAmount = $this->finalUnitPrice - $this->additionalVisitors[$i]['subPromoCodeDiscount'];
                            $visitorSubDescription = "Visitor Registration Fee - {$this->rateTypeString} - {$this->additionalVisitors[$i]['subBadgeType']} - $ {$this->additionalVisitors[$i]['subPromoCodeDiscount']} discount";
                        } else {
                            // FIXED RATE
                            $promoCode = PromoCodes::where('event_id', $this->event->id)->where('promo_code', $this->additionalVisitors[$i]['subPromoCode'])->first();
                            $tempSubTotalDiscount = 0;
                            $tempSubTotalNetAmount = $promoCode->new_rate;
                            $visitorSubDescription = $promoCode->new_rate_description;
                        }
                    }

                    array_push($this->visitorInvoiceDetails, [
                        'visitorDescription' => $visitorSubDescription,
                        'visitorNames' => [
                            $this->additionalVisitors[$i]['subFirstName'] . " " . $this->additionalVisitors[$i]['subMiddleName'] . " " . $this->additionalVisitors[$i]['subLastName'],
                        ],
                        'badgeType' => $this->additionalVisitors[$i]['subBadgeType'],
                        'quantity' => 1,
                        'totalUnitPrice' => $tempSubUnitPrice,
                        'totalDiscount' => $tempSubTotalDiscount,
                        'totalNetAmount' =>  $tempSubTotalNetAmount,
                        'promoCode' => $this->additionalVisitors[$i]['subPromoCode'],
                        'promoCodeDiscount' => $this->additionalVisitors[$i]['subPromoCodeDiscount'],
                    ]);

                }
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
        $this->isMainFree = false;
    }


    public function increaseStep()
    {
        if ($this->currentStep == 1) {
            if ($this->visitorPassType != null) {
                $this->visitorPassTypeError = null;
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
                $this->visitorPassTypeError = "Visitor pass type is required";
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
                    'visitorPassType.required' => "Pass type is required",
                    'companyName.required' => "Company name is required",
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
                    'mobileNumber' => 'required',
                    'nationality' => 'required',
                    'jobTitle' => 'required',
                    'badgeType' => 'required',
                ],
                [
                    'firstName.required' => "First name is required",
                    'lastName.required' => "Last name is required",
                    'emailAddress.required' => "Email address is required",
                    'emailAddress.email' => "Email address must be a valid email",
                    'mobileNumber.required' => "Mobile number is required",
                    'nationality.required' => "Nationality is required",
                    'jobTitle.required' => "Job title is required",
                    'badgeType.required' => "Visitor type is required",
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
        
        $newRegistrant = MainVisitors::create([
            'event_id' => $this->event->id,
            'pass_type' => $this->visitorPassType,
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
                
                if ($additionalVisitor['subPromoCode'] != null) {
                    PromoCodes::where('event_id', $this->event->id)->where('event_category', $this->event->category)->where('active', true)->where('promo_code', $additionalVisitor['subPromoCode'])->where('badge_type', $additionalVisitor['subBadgeType'])->increment('total_usage');
                }

                $newAdditionVisitor = AdditionalVisitors::create([
                    'main_visitor_id' => $newRegistrant->id,
                    'salutation' => $additionalVisitor['subSalutation'],
                    'first_name' => $additionalVisitor['subFirstName'],
                    'middle_name' => $additionalVisitor['subMiddleName'],
                    'last_name' => $additionalVisitor['subLastName'],
                    'job_title' => $additionalVisitor['subJobTitle'],
                    'email_address' => $additionalVisitor['subEmailAddress'],
                    'nationality' => $additionalVisitor['subNationality'],
                    'mobile_number' => $additionalVisitor['subMobileNumber'],
                    'badge_type' => $additionalVisitor['subBadgeType'],
                    'pcode_used' => $additionalVisitor['subPromoCode'],
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
        if ($this->currentStep == 2) {
            $this->members = Members::where('active', true)->orderBy('name', 'ASC')->get();
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
            $registrationStatus = "pending";
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

            'jobTitle' => $this->jobTitle,
            'companyName' => $this->companyName,
            'amountPaid' => 0,
            'transactionId' => $tempTransactionId,
            'invoiceLink' => $invoiceLink,

            'badgeLink' => env('APP_URL') . "/" . $this->event->category . "/" . $this->event->id . "/view-badge" . "/" . "main" . "/" . $this->currentMainVisitorId,
        ];

        if ($this->isMainFree) {
            try {
                Mail::to($this->emailAddress)->cc($this->ccEmailNotif)->send(new RegistrationFree($details1));
            } catch (\Exception $e) {
                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($details1));
            }
        } else {
            try {
                Mail::to($this->emailAddress)->cc($this->ccEmailNotif)->send(new RegistrationUnpaid($details1));
            } catch (\Exception $e) {
                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationUnpaid($details1));
            }
        }

        if ($this->assistantEmailAddress != null) {
            if ($this->isMainFree) {
                try {
                    Mail::to($this->assistantEmailAddress)->send(new RegistrationFree($details1));
                } catch (\Exception $e) {
                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($details1));
                }
            } else {
                try {
                    Mail::to($this->assistantEmailAddress)->send(new RegistrationUnpaid($details1));
                } catch (\Exception $e) {
                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationUnpaid($details1));
                }
            }
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

                    'jobTitle' => $additionalVisitor->job_title,
                    'companyName' => $this->companyName,
                    'amountPaid' => 0,
                    'transactionId' => $tempTransactionId,
                    'invoiceLink' => $invoiceLink,

                    'badgeLink' => env('APP_URL') . "/" . $this->event->category . "/" . $this->event->id . "/view-badge" . "/" . "sub" . "/" . $additionalVisitor->id,
                ];

                $isSubFree = false;

                $promoCode = PromoCodes::where('event_id', $this->event->id)->where('promo_code', $additionalVisitor->pcode_used)->first();

                if ($promoCode != null) {
                    if ($promoCode->discount_type == "percentage") {
                        $tempTotalNetAmount = $this->finalUnitPrice - ($this->finalUnitPrice * ($promoCode->discount / 100));
                    } else if ($promoCode->discount_type == "price") {
                        $tempTotalNetAmount = $this->finalUnitPrice - $promoCode->discount;
                    } else {
                        $tempTotalNetAmount = $promoCode->new_rate;
                    }

                    if ($tempTotalNetAmount == 0) {
                        $isSubFree = true;
                    }
                } else {
                    $isSubFree = false;
                }

                if ($isSubFree) {
                    try {
                        Mail::to($additionalVisitor->email_address)->cc($this->ccEmailNotif)->send(new RegistrationFree($details1));
                    } catch (\Exception $e) {
                        Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($details1));
                    }
                } else {
                    try {
                        Mail::to($additionalVisitor->email_address)->cc($this->ccEmailNotif)->send(new RegistrationUnpaid($details1));
                    } catch (\Exception $e) {
                        Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationUnpaid($details1));
                    }
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

    public function fullMemberClicked()
    {
        $this->companyName = null;
        if ($this->visitorPassType == 'member' || $this->visitorPassType == 'nonMember') {
            $this->visitorPassType = 'fullMember';
        } else if ($this->visitorPassType == 'fullMember') {
            $this->visitorPassType = null;
        } else {
            $this->visitorPassType = 'fullMember';
        }
    }

    public function memberClicked()
    {
        $this->companyName = null;
        if ($this->visitorPassType == 'fullMember' || $this->visitorPassType == 'nonMember') {
            $this->visitorPassType = 'member';
        } else if ($this->visitorPassType == 'member') {
            $this->visitorPassType = null;
        } else {
            $this->visitorPassType = 'member';
        }
    }

    public function nonMemberClicked()
    {
        $this->companyName = null;
        if ($this->visitorPassType == 'fullMember' || $this->visitorPassType == 'member') {
            $this->visitorPassType = 'nonMember';
        } else if ($this->visitorPassType == 'nonMember') {
            $this->visitorPassType = null;
        } else {
            $this->visitorPassType = 'nonMember';
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
        $this->subJobTitle = null;
        $this->subBadgeType = "Visitor";
        $this->promoCodeSuccessSub = null;
        $this->promoCodeFailSub = null;
        $this->subPromoCode = null;
        $this->subPromoCodeDiscount = null;
        $this->subDiscountType = null;

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
                $this->subJobTitleEdit = $additionalVisitor['subJobTitle'];
                $this->subBadgeTypeEdit = $additionalVisitor['subBadgeType'];
                $this->subPromoCodeEdit = $additionalVisitor['subPromoCode'];
                $this->subPromoCodeDiscountEdit = $additionalVisitor['subPromoCodeDiscount'];
                $this->subDiscountTypeEdit = $additionalVisitor['subDiscountType'];
                $this->promoCodeSuccessSubEdit = $additionalVisitor['promoCodeSuccessSub'];
                $this->promoCodeFailSubEdit = $additionalVisitor['promoCodeFailSub'];
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
        $this->subBadgeTypeEdit = "Visitor";
        $this->subPromoCodeEdit = null;
        $this->subPromoCodeDiscountEdit = null;
        $this->subDiscountTypeEdit = null;
        $this->promoCodeSuccessSubEdit = null;
        $this->promoCodeFailSubEdit = null;

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
                array_push($this->additionalVisitors, [
                    'subVisitorId' => $uuid->toString(),
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
                    'subDiscountType' => $this->subDiscountType,
                    'promoCodeSuccessSub' => $this->promoCodeSuccessSub,
                    'promoCodeFailSub' => $this->promoCodeFailSub,
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
                        $this->additionalVisitors[$i]['subEmailAddress'] = $this->subEmailAddressEdit;
                        $this->additionalVisitors[$i]['subMobileNumber'] = $this->subMobileNumberEdit;
                        $this->additionalVisitors[$i]['subNationality'] = $this->subNationalityEdit;
                        $this->additionalVisitors[$i]['subJobTitle'] = $this->subJobTitleEdit;
                        $this->additionalVisitors[$i]['subBadgeType'] = $this->subBadgeTypeEdit;
                        $this->additionalVisitors[$i]['subPromoCode'] = ($this->promoCodeSuccessSubEdit != null) ? $this->subPromoCodeEdit : null;
                        $this->additionalVisitors[$i]['subPromoCodeDiscount'] = $this->subPromoCodeDiscountEdit;
                        $this->additionalVisitors[$i]['subDiscountType'] = $this->subDiscountTypeEdit;
                        $this->additionalVisitors[$i]['promoCodeSuccessSub'] = $this->promoCodeSuccessSubEdit;
                        $this->additionalVisitors[$i]['promoCodeFailSub'] = $this->promoCodeFailSubEdit;

                        $this->resetEditModalFields();
                        $this->showEditVisitorModal = false;
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
                        $this->discountType = $promoCode->discount_type;

                        if ($promoCode->discount_type == "percentage") {
                            $this->promoCodeSuccessMain = "$promoCode->discount% discount will be availed upon submitting the registration";
                            $this->promoCodeDiscount = $promoCode->discount;
                        } else if ($promoCode->discount_type == "price") {
                            $this->promoCodeSuccessMain = "$$promoCode->discount discount will be availed upon submitting the registration";
                            $this->promoCodeDiscount = $promoCode->discount;
                        } else {
                            $this->promoCodeSuccessMain = "promo code applied";
                            $this->promoCodeDiscount = $promoCode->new_rate;
                        }
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
        $this->discountType = null;
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
                        $this->subBadgeType = $promoCode->badge_type;
                        $this->subDiscountType = $promoCode->discount_type;

                        if ($promoCode->discount_type == "percentage") {
                            $this->promoCodeSuccessSub = "$promoCode->discount% discount will be availed upon submitting the registration";
                            $this->subPromoCodeDiscount = $promoCode->discount;
                        } else if ($promoCode->discount_type == "price") {
                            $this->promoCodeSuccessSub = "$$promoCode->discount discount will be availed upon submitting the registration";
                            $this->subPromoCodeDiscount = $promoCode->discount;
                        } else {
                            $this->promoCodeSuccessSub = "promo code applied";
                            $this->subPromoCodeDiscount = $promoCode->new_rate;
                        }
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
        $this->subDiscountType = null;
        $this->subBadgeType = "Visitor";
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
                        $this->subBadgeTypeEdit = $promoCode->badge_type;
                        $this->subDiscountTypeEdit = $promoCode->discount_type;

                        if ($promoCode->discount_type == "percentage") {
                            $this->promoCodeSuccessSubEdit = "$promoCode->discount% discount will be availed upon submitting the registration";
                            $this->subPromoCodeDiscountEdit = $promoCode->discount;
                        } else if ($promoCode->discount_type == "price") {
                            $this->promoCodeSuccessSubEdit = "$$promoCode->discount discount will be availed upon submitting the registration";
                            $this->subPromoCodeDiscountEdit = $promoCode->discount;
                        } else {
                            $this->promoCodeSuccessSubEdit = "promo code applied";
                            $this->subPromoCodeDiscountEdit = $promoCode->new_rate;
                        }
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
        $this->subDiscountTypeEdit = null;
        $this->subBadgeTypeEdit = "Visitor";
        $this->promoCodeFailSubEdit = null;
        $this->promoCodeSuccessSubEdit = null;
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
