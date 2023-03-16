<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Member as Members;
use Illuminate\Support\Str;


class RegistrationForm extends Component
{
    // public $phoneTest;
    public $totalSteps = 4;
    public $currentStep = 1;
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
    public $members;
    public $event;



    // DELEGATE PASS TYPE
    public $delegatePassType;

    // COMPANY INFO
    public $companyName;
    public $companySector;
    public $companyAddress;
    public $companyCountry;
    public $companyCity;
    public $companyLandlineNumber;
    public $companyMobileNumber;
    public $promoCode;
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

    // SUB DELEGATE
    public $subSalutation;
    public $subFirstName;
    public $subMiddleName;
    public $subLastName;
    public $subEmailAddress;
    public $subMobileNumber;
    public $subNationality;
    public $subJobTitle;

    public $showAddDelegateModal = false;
    public $additionalDelegates = [];

    // 3RD
    public $paymentMethod;






    public function mount($data)
    {
        $this->event = $data;
        $this->currentStep = 1;
    }

    public function render()
    {
        $this->members = Members::select('name', 'logo')->get();
        return view('livewire.registration.registration-form');
    }

    public function increaseStep()
    {
        $this->currentStep += 1;
    }

    public function decreaseStep()
    {
        $this->currentStep -= 1;
    }

    public function validateData()
    {
        if ($this->currentStep == 1) {
        }
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
        array_push($this->additionalDelegates, [
            'subSalutation' => $this->subSalutation,
            'subFirstName' => $this->subFirstName,
            'subMiddleName' => $this->subMiddleName,
            'subLastName' => $this->subLastName,
            'subEmailAddress' => $this->subEmailAddress,
            'subMobileNumber' => $this->subMobileNumber,
            'subNationality' => $this->subNationality,
            'subJobTitle' => $this->subJobTitle,
        ]);

        $this->showAddDelegateModal = false;
    }
    public function removeAdditionalDelegate($delegate)
    {
        $index = array_search($delegate, $this->additionalDelegates);
        if ($index !== false) {
            unset($this->additionalDelegates[$index]);
        }
    }
}
