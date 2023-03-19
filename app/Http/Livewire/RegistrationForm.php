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
    // public $phoneTest;
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

    public $members;
    public $event;

    public $finalEbEndDate, $finalStdStartDate;

    public $totalSteps = 4;
    public $currentStep = 1;

    // DELEGATE PASS TYPE
    public $delegatePassType;
    public $badgeType;

    // COMPANY INFO
    public $companyName;
    public $companySector;
    public $companyAddress;
    public $companyCountry;
    public $companyCity;
    public $companyLandlineNumber;
    public $companyMobileNumber;
    public $heardWhere;

    // MAIN DELEGATE
    public $salutation;
    public $firstName;
    public $middleName;
    public $lastName;
    public $emailAddress;
    public $mobileNumber;
    public $nationality;
    public $jobTitle;
    public $promoCode;

    // SUB DELEGATE
    public $subSalutation;
    public $subFirstName;
    public $subMiddleName;
    public $subLastName;
    public $subEmailAddress;
    public $subMobileNumber;
    public $subNationality;
    public $subJobTitle;
    public $subPromoCode;

    public $showAddDelegateModal = false;
    public $additionalDelegates = [];

    // 3RD
    public $paymentMethod;
    public $finalEventStartDate;
    public $finalEventEndDate;
    public $finalQuantity;
    public $finalUnitPrice;
    public $finalNetAmount;
    public $finalDiscount;
    public $finalVat;
    public $finalTotal;

    // ERROR CHECKER
    public $delegatePassTypeError;


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
        } else if ($this->currentStep == 3) {
            $newRegistrant = MainDelegates::create([
                'event_id' => $this->event->id,
                'pass_type' => $this->delegatePassType,
                'badge_type' => $this->badgeType,
                
                'company_name' => $this->companyName,
                'company_sector' => $this->companySector,
                'company_address' => $this->companyAddress,
                'company_country' => $this->companyCountry,
                'company_city' => $this->companyCity,
                'company_telephone_number' => $this->companyLandlineNumber,
                'company_mobile_number' => $this->companyMobileNumber,
                
                'salutation' => $this->salutation,
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName,
                'last_name' => $this->lastName,
                'email_address' => $this->emailAddress,
                'mobile_number' => $this->mobileNumber,
                'nationality' => $this->nationality,
                'job_title' => $this->jobTitle,
                'pcode_used' => $this->promoCode,

                'heard_where' => $this->heardWhere,
                'quantity' => $this->finalQuantity,
                'unit_price' => $this->finalUnitPrice,
                'net_amount' => $this->finalNetAmount,
                'vat_price' => $this->finalVat,
                'discount_price' => $this->finalDiscount,
                'total_amount' => $this->finalTotal,
                'mode_of_payment' => $this->paymentMethod,
                'status' => "pending",
                'registered_date_time' => Carbon::now(),
            ]);

            if (!empty($this->additionalDelegates)) {
                foreach ($this->additionalDelegates as $additionalDelegate) {
                    AdditionalDelegates::create([
                        'main_delegate_id' => $newRegistrant->id,
                        'salutation' => $additionalDelegate['subSalutation'],
                        'first_name' => $additionalDelegate['subFirstName'],
                        'middle_name' => $additionalDelegate['subMiddleName'],
                        'last_name' => $additionalDelegate['subLastName'],
                        'job_title' => $additionalDelegate['subJobTitle'],
                        'email_address' => $additionalDelegate['subEmailAddress'],
                        'nationality' => $additionalDelegate['subNationality'],
                        'mobile_number' => $additionalDelegate['subMobileNumber'],
                        'pcode_used' => $additionalDelegate['subPromoCode'],
                    ]);
                }
            }
            $this->currentStep += 1;
        } else {
        }
    }

    public function decreaseStep()
    {
        $this->currentStep -= 1;
    }


    public function btClicked()
    {
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

    public function openModal()
    {
        $this->showAddDelegateModal = true;
    }
    public function closeModal()
    {
        $this->showAddDelegateModal = false;
        // Reset form fields here
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
        $this->additionalDelegates = array_filter($this->additionalDelegates, function ($item) use ($subDelegateId) {
            return $item['subDelegateId'] != $subDelegateId;
        });
    }
    public function pop()
    {
        array_pop($this->additionalDelegates);
    }
}
