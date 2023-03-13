<?php

namespace App\Http\Livewire;

use Livewire\Component;

class RegistrationForm extends Component
{
    public $currentStep = 1;
    public $paymentMethod;
    public $delegatePassType;
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

    public function render()
    {
        return view('livewire.registration.registration-form');
    }

    public function increaseStep(){
        $this->currentStep += 1;
    }

    public function decreaseStep(){
        $this->currentStep -= 1;
    }

    public function btClicked(){
        if($this->paymentMethod == 'creditCard'){
            $this->paymentMethod = 'bankTransfer';
        } else if($this->paymentMethod == 'bankTransfer'){
            $this->paymentMethod = '';
        } else {
            $this->paymentMethod = 'bankTransfer';
        }
    }

    public function ccClicked(){
        if($this->paymentMethod == 'bankTransfer'){
            $this->paymentMethod = 'creditCard';
        } else if($this->paymentMethod == 'creditCard'){
            $this->paymentMethod = '';
        } else {
            $this->paymentMethod = 'creditCard';
        }
    }

    
    public function memberClicked(){
        if($this->delegatePassType == 'nonMember'){
            $this->delegatePassType = 'member';
        } else if($this->delegatePassType == 'member'){
            $this->delegatePassType = '';
        } else {
            $this->delegatePassType = 'member';
        }
    }

    public function nonMemberClicked(){
        if($this->delegatePassType == 'member'){
            $this->delegatePassType = 'nonMember';
        } else if($this->delegatePassType == 'nonMember'){
            $this->delegatePassType = '';
        } else {
            $this->delegatePassType = 'nonMember';
        }
    }
}
