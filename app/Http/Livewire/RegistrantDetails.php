<?php

namespace App\Http\Livewire;

use App\Mail\RegistrationMarkPaid;
use App\Mail\RegistrationReminder;
use Livewire\Component;
use App\Models\Member as Members;
use App\Models\PromoCode as PromoCodes;
use App\Models\MainDelegate as MainDelegates;
use App\Models\Event as Events;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use NumberFormatter;

class RegistrantDetails extends Component
{
    public $countries;
    public $companySectors;
    public $salutations;

    public $badgeTypes = [
        'VVIP',
        'VIP',
        'Speaker',
        'Commitee',
        'Sponsor',
        'Exhibitor',
        'Delegate',
        'Media partner',
        'Organizer',
    ];


    public $eventCategory, $eventId, $registrantId, $finalData, $members;

    // DELEGATE PASS TYPE
    public $rateType, $finalUnitPrice;

    // COMPANY INFO
    public $delegatePassType, $rateTypeString, $companyName, $companySector, $companyAddress, $companyCountry, $companyCity, $companyLandlineNumber, $assistantEmailAddress, $companyMobileNumber;

    // DELEGATE DETAILS
    public $delegateId, $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $nationality, $jobTitle, $badgeType, $promoCode, $promoCodeDiscount, $promoCodeSuccess, $promoCodeFail, $type;

    // MODALS
    public $showDelegateModal = false;
    public $showCompanyModal = false;


    public function mount($eventCategory, $eventId, $registrantId, $finalData)
    {
        $this->countries = config('app.countries');
        $this->companySectors = config('app.companySectors');
        $this->salutations = config('app.salutations');

        $this->eventCategory = $eventCategory;
        $this->eventId = $eventId;
        $this->registrantId = $registrantId;
        $this->finalData = $finalData;
    }

    public function render()
    {
        return view('livewire.registrants.registrant-details');
    }

    public function updateMainDelegate()
    {
        $this->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'emailAddress' => 'required',
            'mobileNumber' => 'required',
            'nationality' => 'required',
            'jobTitle' => 'required',
            'badgeType' => 'required',
        ]);

        if ($this->type == "main") {
            MainDelegates::find($this->delegateId)->fill([
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
                'discount' => $this->promoCodeDiscount,
            ])->save();

            $this->finalData['salutation'] = $this->salutation;
            $this->finalData['first_name'] = $this->firstName;
            $this->finalData['middle_name'] = $this->middleName;
            $this->finalData['last_name'] = $this->lastName;
            $this->finalData['name'] = $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName;
            $this->finalData['email_address'] = $this->emailAddress;
            $this->finalData['mobile_number'] = $this->mobileNumber;
            $this->finalData['nationality'] = $this->nationality;
            $this->finalData['job_title'] = $this->jobTitle;
            $this->finalData['badge_type'] = $this->badgeType;
            $this->finalData['pcode_used'] = $this->promoCode;
            $this->finalData['discount'] = $this->promoCodeDiscount;
        } else {
            AdditionalDelegates::find($this->delegateId)->fill([
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
                'discount' => $this->promoCodeDiscount,
            ])->save();

            for ($i = 0; $i < count($this->finalData['subDelegates']); $i++) {
                if ($this->finalData['subDelegates'][$i]['subDelegateId'] == $this->delegateId) {
                    $this->finalData['subDelegates'][$i]['salutation'] = $this->salutation;
                    $this->finalData['subDelegates'][$i]['first_name'] = $this->firstName;
                    $this->finalData['subDelegates'][$i]['middle_name'] = $this->middleName;
                    $this->finalData['subDelegates'][$i]['last_name'] = $this->lastName;
                    $this->finalData['subDelegates'][$i]['name'] = $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName;
                    $this->finalData['subDelegates'][$i]['email_address'] = $this->emailAddress;
                    $this->finalData['subDelegates'][$i]['mobile_number'] = $this->mobileNumber;
                    $this->finalData['subDelegates'][$i]['nationality'] = $this->nationality;
                    $this->finalData['subDelegates'][$i]['job_title'] = $this->jobTitle;
                    $this->finalData['subDelegates'][$i]['badge_type'] = $this->badgeType;
                    $this->finalData['subDelegates'][$i]['pcode_used'] = $this->promoCode;
                    $this->finalData['subDelegates'][$i]['discount'] = $this->promoCodeDiscount;
                }
            }
        }

        $this->showDelegateModal = false;
        $this->resetEditModalFields();
    }

    public function openEditMainDelegateModal()
    {
        $this->delegateId = $this->finalData['mainDelegateId'];
        $this->salutation = $this->finalData['salutation'];
        $this->firstName = $this->finalData['first_name'];
        $this->middleName = $this->finalData['middle_name'];
        $this->lastName = $this->finalData['last_name'];
        $this->emailAddress = $this->finalData['email_address'];
        $this->mobileNumber = $this->finalData['mobile_number'];
        $this->nationality = $this->finalData['nationality'];
        $this->jobTitle = $this->finalData['job_title'];
        $this->badgeType = $this->finalData['badge_type'];
        $this->promoCode = $this->finalData['pcode_used'];
        $this->promoCodeDiscount = $this->finalData['discount'];
        $this->type = "main";

        if ($this->finalData['pcode_used'] != null) {
            $this->promoCodeSuccess = $this->finalData['discount'];
        }
        $this->showDelegateModal = true;
    }

    public function closeEditMainDelegateModal()
    {
        $this->showDelegateModal = false;
        $this->resetEditModalFields();
    }

    public function applyPromoCode()
    {
        if ($this->badgeType == null) {
            $this->promoCodeFail = "Please choose your registration type first.";
        } else {
            if ($this->promoCode == null) {
                $this->promoCodeFail = "Promo code is required.";
            } else {
                $promoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('active', true)->where('promo_code', $this->promoCode)->where('badge_type', $this->badgeType)->first();
                if ($promoCode == null) {
                    $this->promoCodeFail = "Invalid Code";
                } else {
                    if ($promoCode->total_usage < $promoCode->number_of_codes) {
                        $validityDateTime = Carbon::parse($promoCode->validity);
                        if (Carbon::now()->lt($validityDateTime)) {
                            $this->promoCodeFail = null;
                            $this->promoCodeDiscount = $promoCode->discount;
                            $this->promoCodeSuccess = "$promoCode->discount% discount";
                        } else {
                            $this->promoCodeFail = "Code is expired already";
                        }
                    } else {
                        $this->promoCodeFail = "Code has reached its capacity";
                    }
                }
            }
        }
    }

    public function removePromoCode()
    {
        $this->promoCode = null;
        $this->promoCodeDiscount = null;
        $this->promoCodeFail = null;
        $this->promoCodeSuccess = null;
    }

    public function resetEditModalFields()
    {
        $this->delegateId = null;
        $this->salutation = null;
        $this->firstName = null;
        $this->middleName = null;
        $this->lastName = null;
        $this->emailAddress = null;
        $this->mobileNumber = null;
        $this->nationality = null;
        $this->jobTitle = null;
        $this->badgeType = null;
        $this->promoCode = null;
        $this->promoCodeDiscount = null;
        $this->promoCodeSuccess = null;
        $this->promoCodeFail = null;
        $this->type = null;
    }

    public function openEditSubDelegateModal($delegateId)
    {
        foreach ($this->finalData['subDelegates'] as $subDelegate) {
            if ($subDelegate['subDelegateId'] == $delegateId) {
                $this->delegateId = $subDelegate['subDelegateId'];
                $this->salutation = $subDelegate['salutation'];
                $this->firstName = $subDelegate['first_name'];
                $this->middleName = $subDelegate['middle_name'];
                $this->lastName = $subDelegate['last_name'];
                $this->emailAddress = $subDelegate['email_address'];
                $this->mobileNumber = $subDelegate['mobile_number'];
                $this->nationality = $subDelegate['nationality'];
                $this->jobTitle = $subDelegate['job_title'];
                $this->badgeType = $subDelegate['badge_type'];
                $this->promoCode = $subDelegate['pcode_used'];
                $this->promoCodeDiscount = $subDelegate['discount'];
                $this->type = "sub";

                if ($subDelegate['pcode_used'] != null) {
                    $this->promoCodeSuccess = $subDelegate['discount'];
                }
                $this->showDelegateModal = true;
                break;
            }
        }
    }





    public function updateCompanyDetails()
    {
        $this->validate([
            'delegatePassType' => 'required',
            'companyName' => 'required',
            'companySector' => 'required',
            'companyAddress' => 'required',
            'companyCountry' => 'required',
            'companyCity' => 'required',
            'companyMobileNumber' => 'required',
        ]);

        if ($this->finalData['rate_type'] == "Standard") {
            if ($this->delegatePassType == "member") {
                $this->rateTypeString = "Standard Member Rate";
            } else {
                $this->rateTypeString = "Standard Non-Member Rate";
            }
        } else {
            if ($this->delegatePassType == "member") {
                $this->rateTypeString = "Early Bird Member Rate";
            } else {
                $this->rateTypeString = "Early Bird Non-Member Rate";
            }
        }


        MainDelegates::find($this->finalData['mainDelegateId'])->fill([
            'pass_type' => $this->delegatePassType,
            'rate_type_string' => $this->rateTypeString,
            'company_name' => $this->companyName,
            'company_sector' => $this->companySector,
            'company_address' => $this->companyAddress,
            'company_country' => $this->companyCountry,
            'company_city' => $this->companyCity,
            'company_telephone_number' => $this->companyLandlineNumber,
            'company_mobile_number' => $this->companyMobileNumber,
            'assistant_email_address' => $this->assistantEmailAddress,
        ])->save();

        $this->finalData['rate_type_string'] = $this->rateTypeString;
        $this->finalData['pass_type'] = $this->delegatePassType;
        $this->finalData['company_name'] = $this->companyName;
        $this->finalData['company_sector'] = $this->companySector;
        $this->finalData['company_address'] = $this->companyAddress;
        $this->finalData['company_country'] = $this->companyCountry;
        $this->finalData['company_city'] = $this->companyCity;
        $this->finalData['company_telephone_number'] = $this->companyLandlineNumber;
        $this->finalData['company_mobile_number'] = $this->companyMobileNumber;
        $this->finalData['assistant_email_address'] = $this->assistantEmailAddress;

        $this->resetEditCompanyModalFields();
        $this->showCompanyModal = false;
    }

    public function openEditCompanyDetailsModal()
    {
        $this->members = Members::where('active', true)->get();

        $this->delegatePassType = $this->finalData['pass_type'];
        $this->companyName = $this->finalData['company_name'];
        $this->companySector = $this->finalData['company_sector'];
        $this->companyAddress = $this->finalData['company_address'];
        $this->companyCountry = $this->finalData['company_country'];
        $this->companyCity = $this->finalData['company_city'];
        $this->companyLandlineNumber = $this->finalData['company_telephone_number'];
        $this->companyMobileNumber = $this->finalData['company_mobile_number'];
        $this->assistantEmailAddress = $this->finalData['assistant_email_address'];

        $this->showCompanyModal = true;
    }

    public function closeEditCompanyDetailsModal()
    {
        $this->resetEditCompanyModalFields();
        $this->showCompanyModal = false;
    }

    public function resetEditCompanyModalFields()
    {
        $this->members = null;
        $this->delegatePassType = null;
        $this->companyName = null;
        $this->companySector = null;
        $this->companyAddress = null;
        $this->companyCountry = null;
        $this->companyCity = null;
        $this->companyLandlineNumber = null;
        $this->companyMobileNumber = null;
        $this->assistantEmailAddress = null;
    }


    public function markAsPaid()
    {
        if($this->finalData['invoiceData']['total_amount'] == 0){
            $paymentStatus = "free";
        } else {
            $paymentStatus = "paid";
        }
        MainDelegates::find($this->finalData['mainDelegateId'])->fill([
            'registration_status' => "confirmed",
            'payment_status' => $paymentStatus,
            'paid_date_time' => Carbon::now(),
        ])->save();

        $details = [
            'name' => $this->finalData['name'],
        ];
        
        Mail::to($this->finalData['email_address'])->send(new RegistrationMarkPaid($details));

        if($this->finalData['assistant_email_address'] != null){
        Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationMarkPaid($details));
        }

        if(count($this->finalData['subDelegates']) > 0){
            foreach($this->finalData['subDelegates'] as $subDelegate){
                $details = [
                    'name' => $subDelegate['name'],
                ];
                Mail::to($subDelegate['email_address'])->send(new RegistrationMarkPaid($details));
            }
        }

        $this->finalData['registration_status'] = "confirmed";
        $this->finalData['payment_status'] = $paymentStatus;
        $this->finalData['paid_date_time'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
    }


    public function calculateTotal()
    {
        $this->getInvoice();
    }


    public function checkUnitPrice()
    {
        // CHECK UNIT PRICE
        $event = Events::where('id', $this->eventId)->where('category', $this->eventCategory)->first();

        if ($this->finalData['rate_type'] == "Standard") {
            if ($this->finalData['pass_type'] == "member") {
                return $event->std_member_rate;
            } else {
                return $event->std_nmember_rate;
            }
        } else {
            if ($this->finalData['pass_type'] == "member") {
                return $event->eb_member_rate;
            } else {
                return $event->eb_nmember_rate;
            }
        }
    }

    public function getInvoice()
    {
        $event = Events::where('id', $this->eventId)->where('category', $this->eventCategory)->first();
        $invoiceDetails = array();

        $mainDelegate = MainDelegates::where('id', $this->finalData['mainDelegateId'])->where('event_id', $this->eventId)->first();
        $mainDiscount = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('promo_code', $mainDelegate->pcode_used)->where('badge_type', $mainDelegate->badge_type)->value('discount');

        if($mainDiscount == 100){
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
            'totalDiscount' => $this->checkUnitPrice() * ($mainDiscount / 100),
            'totalNetAmount' =>  $this->checkUnitPrice() - ($this->checkUnitPrice() * ($mainDiscount / 100)),
            'promoCodeDiscount' => $mainDiscount,
        ]);

        $subDelegates = AdditionalDelegates::where('main_delegate_id', $this->finalData['mainDelegateId'])->get();
        if (!$subDelegates->isEmpty()) {
            foreach ($subDelegates as $subDelegate) {
                $subDiscount = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('promo_code', $subDelegate->pcode_used)->where('badge_type', $subDelegate->badge_type)->value('discount');

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
                    $totalDiscountTemp = ($this->checkUnitPrice() * ($invoiceDetails[$existingIndex]['promoCodeDiscount'] / 100)) * $quantityTemp;
                    $totalNetAmountTemp = ($this->checkUnitPrice() * $quantityTemp) - $totalDiscountTemp;

                    $invoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                    $invoiceDetails[$existingIndex]['totalDiscount'] = $totalDiscountTemp;
                    $invoiceDetails[$existingIndex]['totalNetAmount'] = $totalNetAmountTemp;
                } else {
                    
                    if($subDiscount == 100){
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
                        'totalDiscount' => $this->checkUnitPrice() * ($subDiscount / 100),
                        'totalNetAmount' =>  $this->checkUnitPrice() - ($this->checkUnitPrice() * ($subDiscount / 100)),
                        'promoCodeDiscount' => $subDiscount,
                    ]);
                }
            }
        }
        
        $net_amount = 0;
        $discount_price = 0;

        foreach($invoiceDetails as $invoiceDetail){
            $net_amount += $invoiceDetail['totalNetAmount'];
            $discount_price += $invoiceDetail['totalDiscount'];
        }
        $totalVat = $net_amount * ($event->event_vat / 100);
        $totalAmount = $net_amount + $totalVat;

        $invoiceData = [
            "finalEventStartDate" => Carbon::parse($event->event_start_date)->format('d M Y'),
            "finalEventEndDate" => Carbon::parse($event->event_end_date)->format('d M Y'),
            "eventName" => $event->name,
            "eventLocation" => $event->location,
            "eventVat" => $event->event_vat,
            'vat_price' => $totalVat,
            'net_amount' => $net_amount,
            'total_amount' => $totalAmount,
            'unit_price' => $this->checkUnitPrice(),
            'invoiceDetails' => $invoiceDetails,
            'total_amount_string' => ucwords($this->numberToWords($totalAmount)),
        ];

        $this->finalData['invoiceData'] = $invoiceData;
        
        if($this->finalData['registration_status'] == "confirmed"){
            if($this->finalData['invoiceData']['total_amount'] == 0){
                $this->finalData['payment_status'] = "free";
            } else {
                $this->finalData['payment_status'] = "paid";
            }
        } else {
            if($this->finalData['invoiceData']['total_amount'] == 0){
                $this->finalData['payment_status'] = "free";
            } else {
                $this->finalData['payment_status'] = "unpaid";
            }
        }

        MainDelegates::find($this->finalData['mainDelegateId'])->fill([
            'unit_price' => $this->checkUnitPrice(),
            'net_amount' => $net_amount,
            'vat_price' => $totalVat,
            'discount_price' => $discount_price,
            'total_amount' => $totalAmount,
            'payment_status' => $this->finalData['payment_status'],
        ])->save();


    }

    
    public function numberToWords($number){
        $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        return $formatter->format($number);
    }

    public function sendEmailReminder(){
        $event = Events::where('id', $this->eventId)->where('category', $this->eventCategory)->first();
        
        $details = [
            'name' => $this->finalData['name'],
            'eventName' => $event->name,
            'eventLink' => $event->link,
        ];

        Mail::to($this->finalData['email_address'])->send(new RegistrationReminder($details));

        if($this->finalData['assistant_email_address'] != null){
            Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationReminder($details));
        }

        if (count($this->finalData['subDelegates']) > 0) {
            foreach ($this->finalData['subDelegates'] as $subDelegate) {
                $details = [
                    'name' => $subDelegate['name'],
                    'eventName' => $event->name,
                    'eventLink' => $event->link,
                ];

                Mail::to($subDelegate['email_address'])->send(new RegistrationReminder($details));
            }
        }
    }
}
