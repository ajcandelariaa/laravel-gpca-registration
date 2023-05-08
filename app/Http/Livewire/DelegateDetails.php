<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Member as Members;
use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\Event as Events;

class DelegateDetails extends Component
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


    public $eventCategory, $eventId, $finalDelegate, $members, $event;

    public $eventBanner;

    // COMPANY INFO
    public $delegatePassType, $rateTypeString, $companyName, $companySector, $companyAddress, $companyCountry, $companyCity, $companyLandlineNumber, $companyMobileNumber;

    // DELEGATE DETAILS
    public $delegateId, $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $nationality, $jobTitle, $badgeType, $promoCode, $promoCodeDiscount, $promoCodeSuccess, $promoCodeFail, $delegateType;

    // MODALS
    public $showDelegateModal = false;
    public $showCompanyModal = false;

    public function mount($eventCategory, $eventId, $finalDelegate)
    {
        $this->countries = config('app.countries');
        $this->companySectors = config('app.companySectors');
        $this->salutations = config('app.salutations');
        $this->eventBanner = Events::where('id', $eventId)->where('category', $eventCategory)->value('banner');

        $this->eventCategory = $eventCategory;
        $this->eventId = $eventId;
        $this->finalDelegate = $finalDelegate;

        $this->event = Events::where('id', $eventId)->first();
    }


    public function render()
    {
        return view('livewire.delegates.delegate-details');
    }


    public function updateDelegate()
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

        if ($this->delegateType == "main") {
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
            ])->save();
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
            ])->save();
        }

        $this->finalDelegate['salutation'] = $this->salutation;
        $this->finalDelegate['first_name'] = $this->firstName;
        $this->finalDelegate['middle_name'] = $this->middleName;
        $this->finalDelegate['last_name'] = $this->lastName;
        $this->finalDelegate['email_address'] = $this->emailAddress;
        $this->finalDelegate['mobile_number'] = $this->mobileNumber;
        $this->finalDelegate['nationality'] = $this->nationality;
        $this->finalDelegate['job_title'] = $this->jobTitle;
        $this->finalDelegate['badge_type'] = $this->badgeType;

        $this->showDelegateModal = false;
        $this->resetEditDelegateFields();

        $this->dispatchBrowserEvent('swal:delegate-update', [
            'type' => 'success',  
            'message' => 'Delegate Updated Successfully!', 
            'text' => ''
        ]);
    }

    public function openEditDelegateModal()
    {
        $this->delegateType =  $this->finalDelegate['delegateType'];
        $this->delegateId = $this->finalDelegate['delegateId'];
        $this->salutation = $this->finalDelegate['salutation'];
        $this->firstName = $this->finalDelegate['first_name'];
        $this->middleName = $this->finalDelegate['middle_name'];
        $this->lastName = $this->finalDelegate['last_name'];
        $this->emailAddress = $this->finalDelegate['email_address'];
        $this->mobileNumber = $this->finalDelegate['mobile_number'];
        $this->nationality = $this->finalDelegate['nationality'];
        $this->jobTitle = $this->finalDelegate['job_title'];
        $this->badgeType = $this->finalDelegate['badge_type'];

        $this->showDelegateModal = true;
    }


    public function closeEditDelegateModal()
    {
        $this->showDelegateModal = false;
        $this->resetEditDelegateFields();
    }

    public function resetEditDelegateFields()
    {
        $this->delegateType = null;
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

        MainDelegates::find($this->finalDelegate['mainDelegateId'])->fill([
            'pass_type' => $this->delegatePassType,
            'company_name' => $this->companyName,
            'company_sector' => $this->companySector,
            'company_address' => $this->companyAddress,
            'company_country' => $this->companyCountry,
            'company_city' => $this->companyCity,
            'company_telephone_number' => $this->companyLandlineNumber,
            'company_mobile_number' => $this->companyMobileNumber,
        ])->save();

        $this->finalDelegate['pass_type'] = $this->delegatePassType;
        $this->finalDelegate['companyName'] = $this->companyName;
        $this->finalDelegate['company_sector'] = $this->companySector;
        $this->finalDelegate['company_address'] = $this->companyAddress;
        $this->finalDelegate['company_country'] = $this->companyCountry;
        $this->finalDelegate['company_city'] = $this->companyCity;
        $this->finalDelegate['company_telephone_number'] = $this->companyLandlineNumber;
        $this->finalDelegate['company_mobile_number'] = $this->companyMobileNumber;

        $this->resetEditCompanyModalFields();
        $this->showCompanyModal = false;

        $this->dispatchBrowserEvent('swal:company-update', [
            'type' => 'success',  
            'message' => 'Company Updated Successfully!', 
            'text' => ''
        ]);
    }


    public function openEditCompanyDetailsModal()
    {
        $this->members = Members::where('active', true)->get();

        $this->delegatePassType = $this->finalDelegate['pass_type'];
        $this->companyName = $this->finalDelegate['companyName'];
        $this->companySector = $this->finalDelegate['company_sector'];
        $this->companyAddress = $this->finalDelegate['company_address'];
        $this->companyCountry = $this->finalDelegate['company_country'];
        $this->companyCity = $this->finalDelegate['company_city'];
        $this->companyLandlineNumber = $this->finalDelegate['company_telephone_number'];
        $this->companyMobileNumber = $this->finalDelegate['company_mobile_number'];

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
    }
}
