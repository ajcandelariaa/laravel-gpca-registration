<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Member as Members;
use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use Carbon\Carbon;
use Illuminate\Support\Str;


class RegistrationForm extends Component
{
    public $companySectors = [
        'Academia / Educational & Research Institutes / Universities',
        'Brand owners',
        'Catalyst or Additive Manufacturers ',
        'Chemical / Petrochemical Producers    ',
        'Chemical Traders / Distributors ',
        'Engineering Company / EPC Contractors',
        'Equipment Manufacturers',
        'Governments & Regulators',
        'Industry Associations',
        'Investment / Financial / Audit / Insurance Firms',
        'Legal firms',
        'Logistics Service Providers',
        'NGOs',
        'Oil & Gas (Upstream) ',
        'Petroleum Producers / Refineries / Gas processing plants',
        'Plastics Convertors',
        'Power & Utilities',
        'Press/media ',
        'Retailers',
        'Shipping Lines',
        'Strategy Consultancies ',
        'Technology Consultancies',
        'Technology Services Providers',
        'Terminal Operators',
        'Venture Capitalists ',
        'Waste Management & Recycling',
    ];
    public $salutations = [
        'Mr.',
        'Mrs.',
        'Ms.',
        'Dr.',
        'Eng.',
    ];
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

    // public $phoneTest;
    public $members, $event;
    public $finalEbEndDate, $finalStdStartDate;
    public $currentStep = 1;
    public $showAddDelegateModal = false;
    public $showEditDelegateModal = false;
    public $additionalDelegates = [];

    // DELEGATE PASS TYPE
    public $delegatePassType;
    public $badgeType;

    // COMPANY INFO
    public $companyName, $companySector, $companyAddress, $companyCountry, $companyCity, $companyLandlineNumber, $companyMobileNumber, $heardWhere;

    // MAIN DELEGATE
    public $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $nationality, $jobTitle, $promoCode;

    // SUB DELEGATE
    public $subSalutation, $subFirstName, $subMiddleName, $subLastName, $subEmailAddress, $subMobileNumber, $subNationality, $subJobTitle, $subPromoCode;
    
    // SUB DELEGATE EDIT
    public $subIdEdit, $subSalutationEdit, $subFirstNameEdit, $subMiddleNameEdit, $subLastNameEdit, $subEmailAddressEdit, $subMobileNumberEdit, $subNationalityEdit, $subJobTitleEdit, $subPromoCodeEdit;

    // 3RD PAGE
    public $paymentMethod, $finalEventStartDate, $finalEventEndDate, $finalQuantity, $finalUnitPrice, $finalNetAmount, $finalDiscount, $finalVat, $finalTotal;

    // ERROR CHECKER
    public $delegatePassTypeError, $paymentMethodError;


    public function mount($data)
    {
        $this->event = $data;
        $this->currentStep = 1;

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
        $this->members = Members::where('active', true)->get();
        return view('livewire.registration.registration-form');
    }

    public function calculateAmount()
    {
        $today = Carbon::today();

        // CHECK UNIT PRICE
        if ($this->event->eb_end_date != null && $this->event->eb_member_rate != null && $this->event->eb_nmember_rate != null) {
            if ($today->lte(Carbon::parse($this->event->eb_end_date))) {
                if ($this->delegatePassType == "member") {
                    $this->finalUnitPrice = $this->event->eb_member_rate;
                } else {
                    $this->finalUnitPrice = $this->event->eb_nmember_rate;
                }
            } else {
                if ($this->delegatePassType == "member") {
                    $this->finalUnitPrice = $this->event->std_member_rate;
                } else {
                    $this->finalUnitPrice = $this->event->std_nmember_rate;
                }
            }
        } else {
            if ($this->delegatePassType == "member") {
                $this->finalUnitPrice = $this->event->std_member_rate;
            } else {
                $this->finalUnitPrice = $this->event->std_nmember_rate;
            }
        }

        $this->finalQuantity = count($this->additionalDelegates) + 1;
        $this->finalNetAmount = $this->finalQuantity * $this->finalUnitPrice;
        $this->finalVat = $this->finalNetAmount * ($this->event->event_vat / 100);
        $this->finalTotal = $this->finalNetAmount + $this->finalVat;
    }

    public function increaseStep()
    {
        if ($this->currentStep == 1) {
            if ($this->delegatePassType != null && $this->badgeType != null) {
                $this->delegatePassTypeError = null;
                $this->currentStep += 1;
            } else {
                $this->delegatePassTypeError = "Please select first";
            }
        } else if ($this->currentStep == 2) {
            $this->validate([
                'companyName' => 'required',
                'companySector' => 'required',
                'companyAddress' => 'required',
                'companyCountry' => 'required',
                'companyCity' => 'required',
                'companyMobileNumber' => 'required',
                'heardWhere' => 'required',

                'firstName' => 'required',
                'lastName' => 'required',
                'emailAddress' => 'required',
                'nationality' => 'required',
                'mobileNumber' => 'required',
                'jobTitle' => 'required',
            ]);

            $this->calculateAmount();
            $this->currentStep += 1;
        } else {
            // do nothing
        }
    }

    public function decreaseStep()
    {
        $this->currentStep -= 1;
    }

    public function submit()
    {
        // $newRegistrant = MainDelegates::create([
        //     'event_id' => $this->event->id,
        //     'pass_type' => $this->delegatePassType,
        //     'badge_type' => $this->badgeType,

        //     'company_name' => $this->companyName,
        //     'company_sector' => $this->companySector,
        //     'company_address' => $this->companyAddress,
        //     'company_country' => $this->companyCountry,
        //     'company_city' => $this->companyCity,
        //     'company_telephone_number' => $this->companyLandlineNumber,
        //     'company_mobile_number' => $this->companyMobileNumber,

        //     'salutation' => $this->salutation,
        //     'first_name' => $this->firstName,
        //     'middle_name' => $this->middleName,
        //     'last_name' => $this->lastName,
        //     'email_address' => $this->emailAddress,
        //     'mobile_number' => $this->mobileNumber,
        //     'nationality' => $this->nationality,
        //     'job_title' => $this->jobTitle,
        //     'pcode_used' => $this->promoCode,

        //     'heard_where' => $this->heardWhere,
        //     'quantity' => $this->finalQuantity,
        //     'unit_price' => $this->finalUnitPrice,
        //     'net_amount' => $this->finalNetAmount,
        //     'vat_price' => $this->finalVat,
        //     'discount_price' => $this->finalDiscount,
        //     'total_amount' => $this->finalTotal,
        //     'mode_of_payment' => $this->paymentMethod,
        //     'status' => "pending",
        //     'registered_date_time' => Carbon::now(),
        // ]);

        // if (!empty($this->additionalDelegates)) {
        //     foreach ($this->additionalDelegates as $additionalDelegate) {
        //         AdditionalDelegates::create([
        //             'main_delegate_id' => $newRegistrant->id,
        //             'salutation' => $additionalDelegate['subSalutation'],
        //             'first_name' => $additionalDelegate['subFirstName'],
        //             'middle_name' => $additionalDelegate['subMiddleName'],
        //             'last_name' => $additionalDelegate['subLastName'],
        //             'job_title' => $additionalDelegate['subJobTitle'],
        //             'email_address' => $additionalDelegate['subEmailAddress'],
        //             'nationality' => $additionalDelegate['subNationality'],
        //             'mobile_number' => $additionalDelegate['subMobileNumber'],
        //             'pcode_used' => $additionalDelegate['subPromoCode'],
        //         ]);
        //     }
        // }
        if($this->currentStep == 3){
            if($this->paymentMethod == null){
                $this->paymentMethodError = "Please choose your payment method first";
            } else {
                $this->currentStep = 4;
            }
        }
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


    public function memberClicked()
    {
        if ($this->delegatePassType == 'nonMember') {
            $this->delegatePassType = 'member';
        } else if ($this->delegatePassType == 'member') {
            $this->delegatePassType = null;
        } else {
            $this->delegatePassType = 'member';
        }
    }

    public function nonMemberClicked()
    {
        if ($this->delegatePassType == 'member') {
            $this->delegatePassType = 'nonMember';
        } else if ($this->delegatePassType == 'nonMember') {
            $this->delegatePassType = null;
        } else {
            $this->delegatePassType = 'nonMember';
        }
    }

    public function openAddModal()
    {
        $this->showAddDelegateModal = true;
    }
    
    public function closeAddModal()
    {
        $this->showAddDelegateModal = false;

        $this->subSalutation = null;
        $this->subFirstName = null;
        $this->subMiddleName = null;
        $this->subLastName = null;
        $this->subEmailAddress = null;
        $this->subMobileNumber = null;
        $this->subNationality = null;
        $this->subJobTitle = null;
        $this->subPromoCode = null;
    }

    public function openEditModal($subDelegateId)
    {
        $this->showEditDelegateModal = true;
        foreach ($this->additionalDelegates as $additionalDelegate){
            if($additionalDelegate['subDelegateId'] == $subDelegateId){
                $this->subIdEdit = $additionalDelegate['subDelegateId'];
                $this->subSalutationEdit = $additionalDelegate['subSalutation'];
                $this->subFirstNameEdit = $additionalDelegate['subFirstName'];
                $this->subMiddleNameEdit = $additionalDelegate['subMiddleName'];
                $this->subLastNameEdit = $additionalDelegate['subLastName'];
                $this->subEmailAddressEdit = $additionalDelegate['subEmailAddress'];
                $this->subMobileNumberEdit = $additionalDelegate['subMobileNumber'];
                $this->subNationalityEdit = $additionalDelegate['subNationality'];
                $this->subJobTitleEdit = $additionalDelegate['subJobTitle'];
                $this->subPromoCodeEdit = $additionalDelegate['subPromoCode'];
            }
        }
    }
    
    public function closeEditModal()
    {
        $this->showEditDelegateModal = false;

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
    }

    public function saveAdditionalDelegate()
    {
        $this->validate([
            'subFirstName' => 'required',
            'subLastName' => 'required',
            'subEmailAddress' => 'required',
            'subMobileNumber' => 'required',
            'subNationality' => 'required',
            'subJobTitle' => 'required',
        ]);

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
            'subPromoCode' => $this->subPromoCode,
        ]);

        $this->subSalutation = null;
        $this->subFirstName = null;
        $this->subMiddleName = null;
        $this->subLastName = null;
        $this->subEmailAddress = null;
        $this->subMobileNumber = null;
        $this->subNationality = null;
        $this->subJobTitle = null;
        $this->subPromoCode = null;

        $this->showAddDelegateModal = false;
    }

    public function removeAdditionalDelegate($subDelegateId)
    {
        $arrayTemp = array_filter($this->additionalDelegates, function ($item) use ($subDelegateId) {
            return $item['subDelegateId'] != $subDelegateId;
        });

        $this->additionalDelegates = [];

        foreach($arrayTemp as $delegate){
            array_push($this->additionalDelegates, $delegate);
        }
    }

    public function editAdditionalDelegate($subDelegateId){
        for($i=0; $i < count($this->additionalDelegates); $i++){
            if($this->additionalDelegates[$i]['subDelegateId'] == $subDelegateId){
                $this->additionalDelegates[$i]['subSalutation'] = $this->subSalutationEdit;
                $this->additionalDelegates[$i]['subFirstName'] = $this->subFirstNameEdit;
                $this->additionalDelegates[$i]['subMiddleName'] = $this->subMiddleNameEdit;
                $this->additionalDelegates[$i]['subLastName'] = $this->subLastNameEdit;
                $this->additionalDelegates[$i]['subEmailAddress'] = $this->subEmailAddressEdit;
                $this->additionalDelegates[$i]['subMobileNumber'] = $this->subMobileNumberEdit;
                $this->additionalDelegates[$i]['subNationality'] = $this->subNationalityEdit;
                $this->additionalDelegates[$i]['subJobTitle'] = $this->subJobTitleEdit;
                $this->additionalDelegates[$i]['subPromoCode'] = $this->subPromoCodeEdit;
                
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

                $this->showEditDelegateModal = false;
            }
        }
    }
}
