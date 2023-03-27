<?php

namespace App\Http\Livewire;

use Livewire\Component;

class RegistrantDetails extends Component
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


    public $eventCategory, $eventId, $registrantId, $finalData;

    // DELEGATE PASS TYPE
    public $delegatePassType, $rateType;

    // COMPANY INFO
    public $companyName, $companySector, $companyAddress, $companyCountry, $companyCity, $companyLandlineNumber, $companyMobileNumber, $heardWhere;

    // MAIN DELEGATE
    public $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $nationality, $jobTitle, $badgeType, $promoCode, $promoCodeDiscount;

    // SUB DELEGATE
    public $subSalutation, $subFirstName, $subMiddleName, $subLastName, $subEmailAddress, $subMobileNumber, $subNationality, $subJobTitle, $subBadgeType, $subPromoCode, $subPromoCodeDiscount;

    public function mount($eventCategory, $eventId, $registrantId, $finalData, ){
        $this->eventCategory = $eventCategory;
        $this->eventId = $eventId;
        $this->registrantId = $registrantId;
        $this->finalData = $finalData;
    }

    public function render()
    {
        return view('livewire.registrant-details');
    }





    public function updateMainDelegate(){

    }

    public function openEditMainDelegateModal(){

    }
    
    public function closeEditMainDelegateModal(){
        
    }




    public function updateSubDelegate(){

    }

    public function openEditSubDelegateModal(){

    }
    
    public function closeEditSubDelegateModal(){
        
    }





    public function updateCompanyDetails(){
        
    }

    public function openEditCompanyDetailsModal(){

    }
    
    public function closeEditCompanyDetailsModal(){
        
    }




    public function updatePaymentStatus(){

    }
}
