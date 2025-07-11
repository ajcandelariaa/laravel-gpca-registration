<?php

namespace App\Http\Livewire;

use App\Enums\AccessTypes;
use App\Mail\RegistrationFree;
use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Mail\RegistrationPaymentReminder;
use App\Mail\RegistrationUnpaid;
use Livewire\Component;
use App\Models\Member as Members;
use App\Models\PromoCode as PromoCodes;
use App\Models\MainDelegate as MainDelegates;
use App\Models\Event as Events;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\Transaction as Transactions;
use App\Models\EventRegistrationType as EventRegistrationTypes;
use App\Models\PromoCodeAddtionalBadgeType as PromoCodeAddtionalBadgeTypes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use NumberFormatter;

class RegistrantDetails extends Component
{
    public $countries, $companySectors, $salutations, $registrationTypes;

    public $eventFormattedDate;

    public $eventCategory, $eventId, $registrantId, $finalData, $members, $event;

    public $accessType, $rateType, $finalUnitPrice;

    // COMPANY INFO
    public $delegatePassType, $rateTypeString, $companyName, $alternativeCompanyName, $companySector, $companyAddress, $companyCountry, $companyCity, $companyLandlineNumber, $assistantEmailAddress, $companyMobileNumber;

    // DELEGATE DETAILS
    public $mainDelegateId, $delegateId, $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $nationality, $jobTitle, $badgeType, $promoCode, $promoCodeDiscount, $discountType, $promoCodeSuccess, $promoCodeFail, $type, $delegateIndex, $delegateInnerIndex, $country, $interests = [];

    public $transactionRemarks, $delegateCancellationStep = 1, $replaceDelegate, $delegateRefund;

    public $replaceDelegateIndex, $replaceDelegateInnerIndex, $replaceSalutation, $replaceFirstName, $replaceMiddleName, $replaceLastName, $replaceEmailAddress, $replaceMobileNumber, $replaceNationality, $replaceJobTitle, $replaceBadgeType, $replacePromoCode, $replaceDiscountType, $replacePromoCodeDiscount, $replacePromoCodeSuccess, $replacePromoCodeFail, $replaceEmailAlreadyUsedError, $replaceCountry, $replaceInterests = [];

    public $mapPaymentMethod, $mapSendEmailNotif, $sendInvoice;

    public $sendEmailActiveIndex, $sendEmailActiveInnerIndex;


    // MODALS
    public $showDelegateModal = false;
    public $showCompanyModal = false;
    public $showRegistrationDetailsModal = false;
    public $showTransactionRemarksModal = false;
    public $showDelegateCancellationModal = false;
    public $showMarkAsPaidModal = false;

    public $ccEmailNotif;

    protected $listeners = ['paymentReminderConfirmed' => 'sendEmailReminder', 'sendEmailRegistrationConfirmationConfirmed' => 'sendEmailRegistrationConfirmation', 'sendEmailRegistrationConfirmationSingleConfirmed' => 'sendEmailRegistrationConfirmationSingle', 'cancelRefundDelegateConfirmed' => 'cancelOrRefundDelegate', 'cancelReplaceDelegateConfirmed' => 'addReplaceDelegate', 'markAsPaidConfirmed' => 'markAsPaid'];

    public function mount($eventCategory, $eventId, $registrantId, $finalData)
    {
        $this->countries = config('app.countries');
        $this->companySectors = config('app.companySectors');
        $this->salutations = config('app.salutations');
        $this->registrationTypes = EventRegistrationTypes::where('event_id', $eventId)->where('event_category', $eventCategory)->where('active', true)->get();

        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();
        $this->eventCategory = $eventCategory;
        $this->eventId = $eventId;
        $this->registrantId = $registrantId;
        $this->finalData = $finalData;

        if ($this->finalData['registration_method'] == "imported") {
            $this->sendInvoice = false;
        } else {
            $this->sendInvoice = true;
        }

        if ($eventCategory == "DAW") {
            $this->ccEmailNotif = config('app.ccEmailNotif.daw');
        } else if ($eventCategory == "GLF") {
            $this->ccEmailNotif = config('app.ccEmailNotif.glf');
        } else {
            $this->ccEmailNotif = config('app.ccEmailNotif.default');
        }

        if ($this->event->category == "PSW" && $this->event->year == "2025") { 
            // $this->eventFormattedDate = "April 30 to May 01, 2025";
            $this->eventFormattedDate = Carbon::parse($this->event->event_start_date)->format('j F') . ' - ' . Carbon::parse($this->event->event_end_date)->format('j F Y');
        } else {
            $this->eventFormattedDate = Carbon::parse($this->event->event_start_date)->format('j') . '-' . Carbon::parse($this->event->event_end_date)->format('j F Y');
        }
    }

    public function render()
    {
        return view('livewire.admin.events.transactions.registrant-details');
    }

    public function updateDelegate()
    {
        $this->validate(
            [
                'firstName' => 'required',
                'lastName' => 'required',
                'emailAddress' => 'required|email',
                'nationality' => 'required',
                'mobileNumber' => 'required',
                'jobTitle' => 'required',
                'badgeType' => 'required',
                'country' => 'required',
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
                'country.required' => "Country is required",
            ]
        );

        // PUT CONFIRMATION HERE

        if ($this->promoCodeSuccess != null) {
            PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('active', true)->where('promo_code', $this->promoCode)->where('badge_type', $this->badgeType)->increment('total_usage');
        } else {
            $this->promoCode = null;
        }

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
                'country' => $this->country,
                'interests' => json_encode($this->interests),
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
                'pcode_used' => $this->promoCode,
                'country' => $this->country,
                'interests' => json_encode($this->interests),
            ])->save();
        }

        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['salutation'] = $this->salutation;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['first_name'] = $this->firstName;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['middle_name'] = $this->salutation;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['last_name'] = $this->lastName;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['name'] = $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['email_address'] = $this->emailAddress;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['mobile_number'] = $this->mobileNumber;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['nationality'] = $this->nationality;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['job_title'] = $this->jobTitle;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['badge_type'] = $this->badgeType;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['pcode_used'] = $this->promoCode;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['discount'] = $this->promoCodeDiscount;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['discount_type'] = $this->discountType;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['country'] = $this->country;
        $this->finalData['allDelegates'][$this->delegateIndex][$this->delegateInnerIndex]['interests'] = $this->interests;

        $this->calculateTotal();

        $this->showDelegateModal = false;
        $this->resetEditModalFields();
    }

    public function openEditDelegateModal($index, $innerIndex)
    {
        // dd($this->finalData['allDelegates'][$index][$innerIndex]);
        $this->delegateIndex = $index;
        $this->delegateInnerIndex = $innerIndex;
        $this->mainDelegateId = $this->finalData['allDelegates'][$index][$innerIndex]['mainDelegateId'];
        $this->delegateId = $this->finalData['allDelegates'][$index][$innerIndex]['delegateId'];
        $this->salutation = $this->finalData['allDelegates'][$index][$innerIndex]['salutation'];
        $this->firstName = $this->finalData['allDelegates'][$index][$innerIndex]['first_name'];
        $this->middleName = $this->finalData['allDelegates'][$index][$innerIndex]['middle_name'];
        $this->lastName = $this->finalData['allDelegates'][$index][$innerIndex]['last_name'];
        $this->emailAddress = $this->finalData['allDelegates'][$index][$innerIndex]['email_address'];
        $this->mobileNumber = $this->finalData['allDelegates'][$index][$innerIndex]['mobile_number'];
        $this->nationality = $this->finalData['allDelegates'][$index][$innerIndex]['nationality'];
        $this->jobTitle = $this->finalData['allDelegates'][$index][$innerIndex]['job_title'];
        $this->badgeType = $this->finalData['allDelegates'][$index][$innerIndex]['badge_type'];
        $this->promoCode = $this->finalData['allDelegates'][$index][$innerIndex]['pcode_used'];
        $this->promoCodeDiscount = $this->finalData['allDelegates'][$index][$innerIndex]['discount'];
        $this->discountType = $this->finalData['allDelegates'][$index][$innerIndex]['discount_type'];
        $this->type = $this->finalData['allDelegates'][$index][$innerIndex]['delegateType'];
        $this->country = $this->finalData['allDelegates'][$index][$innerIndex]['country'];
        $this->interests = $this->finalData['allDelegates'][$index][$innerIndex]['interests'];

        if ($this->discountType == "fixed") {
            $this->promoCodeSuccess = "Fixed rate applied";
        } else {
            if ($this->promoCode != null) {
                $this->promoCodeSuccess = $this->promoCodeDiscount;
            }
        }

        $this->showDelegateModal = true;
    }

    public function closeEditDelegateModal()
    {
        $this->showDelegateModal = false;
        $this->resetEditModalFields();
    }

    public function applyPromoCode()
    {
        $this->validate([
            'badgeType' => 'required',
            'promoCode' => 'required',
        ], [
            'badgeType.required' => 'Please choose your registration type first',
            'promoCode.required' => 'Promo code is required',
        ]);

        $promoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('active', true)->where('promo_code', $this->promoCode)->first();

        $promoCodeChecker = true;

        if ($promoCode != null) {
            if ($promoCode->badge_type == $this->badgeType) {
                $promoCodeChecker = true;
            } else {
                $promoCodeAdditionalBadgeType = PromoCodeAddtionalBadgeTypes::where('event_id', $this->eventId)->where('promo_code_id', $promoCode->id)->where('badge_type', $this->badgeType)->first();

                if ($promoCodeAdditionalBadgeType != null) {
                    $promoCodeChecker = true;
                } else {
                    $promoCodeChecker = false;
                }
            }
        } else {
            $promoCodeChecker = false;
        }

        if ($promoCodeChecker) {
            if ($promoCode->total_usage < $promoCode->number_of_codes) {
                $validityDateTime = Carbon::parse($promoCode->validity);
                if (Carbon::now()->lt($validityDateTime)) {
                    $this->promoCodeFail = null;
                    $this->discountType = $promoCode->discount_type;
                    $this->promoCodeDiscount = $promoCode->discount;

                    if ($promoCode->discount_type == "percentage") {
                        $this->promoCodeSuccess = "$promoCode->discount% discount";
                    } else if ($promoCode->discount_type == "price") {
                        $this->promoCodeSuccess = "$$promoCode->discount discount";
                    } else {
                        $this->promoCodeSuccess = "Fixed rate applied";
                    }
                } else {
                    $this->promoCodeFail = "Code is expired already";
                }
            } else {
                $this->promoCodeFail = "Code has reached its capacity";
            }
        } else {
            $this->promoCodeFail = "Invalid Code";
        }
    }

    public function removePromoCode()
    {
        $this->promoCode = null;
        $this->promoCodeDiscount = null;
        $this->discountType = null;
        $this->promoCodeFail = null;
        $this->promoCodeSuccess = null;
    }

    public function resetEditModalFields()
    {
        $this->delegateIndex = null;
        $this->delegateInnerIndex = null;
        $this->mainDelegateId = null;
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
        $this->discountType = null;
        $this->promoCodeSuccess = null;
        $this->promoCodeFail = null;
        $this->type = null;
        $this->country = null;
        $this->interests = null;
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

        if ($this->finalData['rate_type'] == "standard" || $this->finalData['rate_type'] == "Standard") {
            if ($this->delegatePassType == "fullMember") {
                $this->rateTypeString = "Full member standard rate" . $this->getAccessTypesDescription($this->accessType, true);
            } else if ($this->delegatePassType == "member") {
                $this->rateTypeString = "Member standard rate" . $this->getAccessTypesDescription($this->accessType, true);
            } else {
                $this->rateTypeString = "Non-Member standard rate" . $this->getAccessTypesDescription($this->accessType, true);
            }
        } else {
            if ($this->delegatePassType == "fullMember") {
                $this->rateTypeString = "Full member early bird rate" . $this->getAccessTypesDescription($this->accessType, true);
            } else if ($this->delegatePassType == "member") {
                $this->rateTypeString = "Member early bird rate" . $this->getAccessTypesDescription($this->accessType, true);
            } else {
                $this->rateTypeString = "Non-Member early bird rate" . $this->getAccessTypesDescription($this->accessType, true);
            }
        }

        if (trim($this->alternativeCompanyName) == ""  || $this->alternativeCompanyName == null) {
            $this->alternativeCompanyName = null;
        }

        MainDelegates::find($this->finalData['mainDelegateId'])->fill([
            'pass_type' => $this->delegatePassType,
            'rate_type_string' => $this->rateTypeString,
            'company_name' => $this->companyName,
            'alternative_company_name' => $this->alternativeCompanyName,
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
        $this->finalData['alternative_company_name'] = $this->alternativeCompanyName;
        $this->finalData['company_sector'] = $this->companySector;
        $this->finalData['company_address'] = $this->companyAddress;
        $this->finalData['company_country'] = $this->companyCountry;
        $this->finalData['company_city'] = $this->companyCity;
        $this->finalData['company_telephone_number'] = $this->companyLandlineNumber;
        $this->finalData['company_mobile_number'] = $this->companyMobileNumber;
        $this->finalData['assistant_email_address'] = $this->assistantEmailAddress;

        $this->calculateTotal();

        $this->resetEditCompanyModalFields();
        $this->showCompanyModal = false;
    }

    public function openEditCompanyDetailsModal()
    {
        // dd($this->finalData);
        $this->members = Members::where('active', true)->get();

        $this->delegatePassType = $this->finalData['pass_type'];
        $this->companyName = $this->finalData['company_name'];
        $this->alternativeCompanyName = $this->finalData['alternative_company_name'];
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
        $this->alternativeCompanyName = null;
        $this->companySector = null;
        $this->companyAddress = null;
        $this->companyCountry = null;
        $this->companyCity = null;
        $this->companyLandlineNumber = null;
        $this->companyMobileNumber = null;
        $this->assistantEmailAddress = null;
    }

    public function updateRegistrationDetails()
    {
        $this->validate([
            'accessType' => 'required',
            'rateType' => 'required',
        ]);

        if ($this->rateType == "Standard") {
            if ($this->finalData['pass_type'] == "fullMember") {
                $this->rateTypeString = "Full member standard rate" . $this->getAccessTypesDescription($this->accessType, true);
            } else if ($this->finalData['pass_type'] == "member") {
                $this->rateTypeString = "Member standard rate" . $this->getAccessTypesDescription($this->accessType, true);
            } else {
                $this->rateTypeString = "Non-Member standard rate" . $this->getAccessTypesDescription($this->accessType, true);
            }
        } else {
            if ($this->finalData['pass_type'] == "fullMember") {
                $this->rateTypeString = "Full member early bird rate" . $this->getAccessTypesDescription($this->accessType, true);
            } else if ($this->finalData['pass_type'] == "member") {
                $this->rateTypeString = "Member early bird rate" . $this->getAccessTypesDescription($this->accessType, true);
            } else {
                $this->rateTypeString = "Non-Member early bird rate" . $this->getAccessTypesDescription($this->accessType, true);
            }
        }

        MainDelegates::find($this->finalData['mainDelegateId'])->fill([
            'access_type' => $this->accessType,
            'rate_type' => $this->rateType,
            'rate_type_string' => $this->rateTypeString,
        ])->save();

        $this->finalData['access_type'] = $this->accessType;
        $this->finalData['rate_type'] = $this->rateType;
        $this->finalData['rate_type_string'] = $this->rateTypeString;

        if ($this->event->category == "ANC" && $this->event->year == "2024") {
            if ($this->accessType == AccessTypes::CONFERENCE_ONLY->value) {
                $this->finalData['invoiceData']['invoiceDescription'] = $this->event->name . ' – 11-12 September 2024  at ' . $this->event->location;
            } else if ($this->accessType == AccessTypes::WORKSHOP_ONLY->value) {
                $this->finalData['invoiceData']['invoiceDescription'] = "Operational Excellence in the GCC Agri-Nutrients Industry Workshop – 10th September 2024 at " .  $this->event->location;
            } else {
                $this->finalData['invoiceData']['invoiceDescription'] = "Operational Excellence in the GCC Agri-Nutrients Industry Workshop and " . $this->event->name . ' – ' . $this->eventFormattedDate . ' at ' . $this->event->location;
            }
        } else if ($this->event->category == "PSC" && $this->event->year == "2024") {
            if ($this->accessType == AccessTypes::CONFERENCE_ONLY->value) {
                $this->finalData['invoiceData']['invoiceDescription'] = $this->event->name . ' – 08-10 October 2024 at ' . $this->event->location;
            } else if ($this->accessType == AccessTypes::WORKSHOP_ONLY->value) {
                $this->finalData['invoiceData']['invoiceDescription'] = "Process Safety Workshops – 7th October 2024 at " .  $this->event->location;
            } else {
                $this->finalData['invoiceData']['invoiceDescription'] = "Process Safety Workshops and " . $this->event->name . ' – ' . $this->eventFormattedDate . ' at ' . $this->event->location;
            }
        } else if ($this->event->category == "SCC" && $this->event->year == "2025") {
            if ($this->accessType == AccessTypes::CONFERENCE_ONLY->value) {
                $this->finalData['invoiceData']['invoiceDescription'] = $this->event->name . ' – 27-28 May 2025 at ' . $this->event->location;
            } else if ($this->accessType == AccessTypes::WORKSHOP_ONLY->value) {
                $this->finalData['invoiceData']['invoiceDescription'] = "Gulf SQAS Workshop – 26th May 2025 at the Sofitel Dubai Downtown";
            } else {
                $this->finalData['invoiceData']['invoiceDescription'] = "Gulf SQAS Workshop – 26th May 2025 at the Sofitel Dubai Downtown and " . $this->event->name . ' – ' . $this->eventFormattedDate . ' at ' . $this->event->location;
            }
        } else if ($this->event->category == "ANC" && $this->event->year == "2025") {
            if ($this->accessType == AccessTypes::CONFERENCE_ONLY->value) {
                $this->finalData['invoiceData']['invoiceDescription'] = $this->event->name . ' – 30 September-01 October 2025  at ' . $this->event->location;
            } else if ($this->accessType == AccessTypes::WORKSHOP_ONLY->value) {
                $this->finalData['invoiceData']['invoiceDescription'] = "3rd Operational Excellence Workshop – 29th September 2025 at " .  $this->event->location;
            } else {
                $this->finalData['invoiceData']['invoiceDescription'] = "3rd Operational Excellence Workshop – 29th September 2025 and " . $this->event->name . ' - 30 September-01 October 2025  at ' . $this->event->location;
            }
        } else if ($this->event->category == "RCC" && $this->event->year == "2025") {
            if ($this->accessType == AccessTypes::CONFERENCE_ONLY->value) {
                $this->finalData['invoiceData']['invoiceDescription'] = $this->event->name . ' – 14-15 October 2025 at ' . $this->event->location;
            } else if ($this->accessType == AccessTypes::WORKSHOP_ONLY->value) {
                $this->finalData['invoiceData']['invoiceDescription'] = "Pre-Conference Workshops – 13th October 2025 at " .  $this->event->location;
            } else {
                $this->finalData['invoiceData']['invoiceDescription'] = "Pre-Conference Workshops – 13th October 2025 and " . $this->event->name . ' - 14-15 October 2025 at ' . $this->event->location;
            }
        } else {
            $this->finalData['invoiceData']['invoiceDescription'] = $this->event->name . ' – ' . $this->eventFormattedDate . ' at ' . $this->event->location;
        }

        $this->calculateTotal();

        $this->accessType = null;
        $this->rateType = null;
        $this->rateTypeString = null;
        $this->showRegistrationDetailsModal = false;
    }

    public function openEditRegistrationDetailsModal()
    {
        $this->accessType = $this->finalData['access_type'];
        $this->rateType = $this->finalData['rate_type'];
        $this->showRegistrationDetailsModal = true;
    }

    public function closeEditRegistrationDetailsModal()
    {
        $this->accessType = null;
        $this->rateType = null;
        $this->rateTypeString = null;
        $this->showRegistrationDetailsModal = false;
    }

    public function openMarkAsPaidModal()
    {
        $this->showMarkAsPaidModal = true;
    }

    public function closeMarkAsPaidModal()
    {
        $this->showMarkAsPaidModal = false;
        $this->mapPaymentMethod = null;
        $this->mapSendEmailNotif = null;
    }

    public function markAsPaidConfirmation()
    {
        $this->validate([
            'mapPaymentMethod' => 'required',
            'mapSendEmailNotif' => 'required',
        ]);

        $messageText = "";

        if ($this->mapSendEmailNotif == "yes") {
            $messageText = "Please note that they will receive email notification";
        } else {
            $messageText = "Please note that they will not receive any email notification";
        }

        $this->dispatchBrowserEvent('swal:mark-as-paid-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure you want to mark this as paid?',
            'text' => $messageText,
        ]);
    }

    public function markAsPaid()
    {
        // dd($this->finalData);
        if ($this->finalData['invoiceData']['total_amount'] == 0) {
            $paymentStatus = "free";
        } else {
            $paymentStatus = "paid";
        }

        MainDelegates::find($this->finalData['mainDelegateId'])->fill([
            'registration_status' => "confirmed",
            'payment_status' => $paymentStatus,
            'mode_of_payment' => $this->mapPaymentMethod,
            'paid_date_time' => Carbon::now(),
        ])->save();

        $eventFormattedData = Carbon::parse($this->event->event_start_date)->format('d') . '-' . Carbon::parse($this->event->event_end_date)->format('d M Y');
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->finalData['mainDelegateId'];

        $assistantDetails1 = [];
        $assistantDetails2 = [];

        if ($this->mapSendEmailNotif == "yes") {
            foreach ($this->finalData['allDelegates'] as $delegatesIndex => $delegates) {
                foreach ($delegates as $innerDelegateIndex => $innerDelegate) {
                    if (end($delegates) == $innerDelegate) {
                        if (!$innerDelegate['delegate_cancelled']) {

                            $delegateVatPrice = $this->finalData['invoiceData']['unit_price'] * ($this->event->event_vat / 100);
                            $amountPaid = $this->finalData['invoiceData']['unit_price'] + $delegateVatPrice;

                            $promoCode = PromoCodes::where('event_id', $this->event->id)->where('promo_code', $innerDelegate['pcode_used'])->first();

                            if ($promoCode != null) {
                                if ($promoCode->discount_type == "percentage") {
                                    $delegateDiscountPrice = $this->finalData['invoiceData']['unit_price'] * ($promoCode->discount / 100);
                                    $delegateDiscountedPrice = $this->finalData['invoiceData']['unit_price'] - $delegateDiscountPrice;
                                    $delegateVatPrice = $delegateDiscountedPrice * ($this->event->event_vat / 100);
                                    $amountPaid = $delegateDiscountedPrice + $delegateVatPrice;
                                } else if ($promoCode->discount_type == "price") {
                                    $delegateDiscountedPrice = $this->finalData['invoiceData']['unit_price'] - $promoCode->discount;
                                    $delegateVatPrice = $delegateDiscountedPrice * ($this->event->event_vat / 100);
                                    $amountPaid = $delegateDiscountedPrice + $delegateVatPrice;
                                } else {
                                    $delegateVatPrice = $promoCode->new_rate * ($this->event->event_vat / 100);
                                    $amountPaid = $promoCode->new_rate + $delegateVatPrice;
                                }
                            }

                            if ($this->finalData['alternative_company_name'] == null) {
                                $finalCompanyName = $this->finalData['company_name'];
                            } else {
                                $finalCompanyName = $this->finalData['alternative_company_name'];
                            }

                            $combinedStringPrint = "gpca@reg" . ',' . $this->event->id . ',' . $this->event->category . ',' . $innerDelegate['delegateId'] . ',' . $innerDelegate['delegateType'];
                            $finalCryptStringPrint = base64_encode($combinedStringPrint);
                            $qrCodeForPrint = 'ca' . $finalCryptStringPrint . 'gp';

                            $details1 = [
                                'name' => $innerDelegate['name'],
                                'eventLink' => $this->event->link,
                                'eventName' => $this->event->name,
                                'eventDates' => $eventFormattedData,
                                'eventLocation' => $this->event->location,
                                'eventCategory' => $this->event->category,
                                'eventYear' => $this->event->year,

                                'accessType' => $this->finalData['access_type'],
                                'jobTitle' => $innerDelegate['job_title'],
                                'companyName' => $finalCompanyName,
                                'badgeType' => $innerDelegate['badge_type'],
                                'amountPaid' => $amountPaid,
                                'transactionId' => $innerDelegate['transactionId'],
                                'invoiceLink' => $invoiceLink,
                                'badgeLink' => env('APP_URL') . "/" . $this->event->category . "/" . $this->event->id . "/view-badge" . "/" . $innerDelegate['delegateType'] . "/" . $innerDelegate['delegateId'],
                                'qrCodeForPrint' => $qrCodeForPrint,
                            ];


                            $details2 = [
                                'name' => $innerDelegate['name'],
                                'eventLink' => $this->event->link,
                                'eventName' => $this->event->name,
                                'eventCategory' => $this->event->category,
                                'eventYear' => $this->event->year,

                                'invoiceAmount' => $this->finalData['invoiceData']['total_amount'],
                                'amountPaid' => $this->finalData['invoiceData']['total_amount'],
                                'balance' => 0,
                                'invoiceLink' => $invoiceLink,
                            ];

                            if ($delegatesIndex == 0) {
                                $assistantDetails1 = $details1;
                                $assistantDetails2 = $details2;
                            }

                            try {
                                Mail::to($innerDelegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaid($details1, $this->sendInvoice));

                                if ($innerDelegate['delegateType'] == "main") {
                                    MainDelegates::find($innerDelegate['delegateId'])->fill([
                                        'registration_confirmation_sent_count' => $innerDelegate['registration_confirmation_sent_count'] + 1,
                                        'registration_confirmation_sent_datetime' => Carbon::now(),
                                    ])->save();
                                } else {
                                    AdditionalDelegates::find($innerDelegate['delegateId'])->fill([
                                        'registration_confirmation_sent_count' => $innerDelegate['registration_confirmation_sent_count'] + 1,
                                        'registration_confirmation_sent_datetime' => Carbon::now(),
                                    ])->save();
                                }

                                $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_count'] = $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_count'] + 1;
                                $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
                            } catch (\Exception $e) {
                                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaid($details1, $this->sendInvoice));
                            }

                            //$this->event->category != "GLF" && $this->event->category != "DFCLW1"
                            if ($this->event->category != "DFCLW1") {
                                if ($this->sendInvoice) {
                                    if ($delegatesIndex == 0) {
                                        try {
                                            Mail::to($innerDelegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
                                        } catch (\Exception $e) {
                                            Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($this->mapSendEmailNotif == "yes") {
            $assistantDetails1['amountPaid'] = $this->finalData['invoiceData']['total_amount'];

            if ($this->finalData['assistant_email_address'] != null) {
                try {
                    Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationPaid($assistantDetails1, $this->sendInvoice));
                } catch (\Exception $e) {
                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaid($assistantDetails1, $this->sendInvoice));
                }
                //$this->event->category != "GLF" && $this->event->category != "DFCLW1"
                if ($this->event->category != "DFCLW1") {
                    if ($this->sendInvoice) {
                        try {
                            Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationPaymentConfirmation($assistantDetails2, $this->sendInvoice));
                        } catch (\Exception $e) {
                            Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentConfirmation($assistantDetails2, $this->sendInvoice));
                        }
                    }
                }
            }
        }

        $this->finalData['registration_status'] = "confirmed";
        $this->finalData['payment_status'] = $paymentStatus;
        $this->finalData['mode_of_payment'] = $this->mapPaymentMethod;
        $this->finalData['paid_date_time'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
        $this->showMarkAsPaidModal = false;
        $this->mapPaymentMethod = null;
        $this->mapSendEmailNotif = null;
        $this->dispatchBrowserEvent('swal:mark-as-paid-success', [
            'type' => 'success',
            'message' => 'Marked paid successfully!',
            'text' => "",
        ]);
    }


    public function checkUnitPrice()
    {
        // CHECK UNIT PRICE
        // dd($this->finalData);
        if ($this->accessType == AccessTypes::CONFERENCE_ONLY->value) {
            if ($this->finalData['rate_type'] == "standard" || $this->finalData['rate_type'] == "Standard") {
                if ($this->finalData['pass_type'] == "fullMember") {
                    return $this->event->co_std_full_member_rate;
                } else if ($this->finalData['pass_type'] == "member") {
                    return $this->event->co_std_member_rate;
                } else {
                    return $this->event->co_std_nmember_rate;
                }
            } else {
                if ($this->finalData['pass_type'] == "fullMember") {
                    return $this->event->co_eb_full_member_rate;
                } else if ($this->finalData['pass_type'] == "member") {
                    return $this->event->co_eb_member_rate;
                } else {
                    return $this->event->co_eb_nmember_rate;
                }
            }
        } else if ($this->accessType == AccessTypes::WORKSHOP_ONLY->value) {
            if ($this->finalData['rate_type'] == "standard" || $this->finalData['rate_type'] == "Standard") {
                if ($this->finalData['pass_type'] == "fullMember") {
                    return $this->event->wo_std_full_member_rate;
                } else if ($this->finalData['pass_type'] == "member") {
                    return $this->event->wo_std_member_rate;
                } else {
                    return $this->event->wo_std_nmember_rate;
                }
            } else {
                if ($this->finalData['pass_type'] == "fullMember") {
                    return $this->event->wo_eb_full_member_rate;
                } else if ($this->finalData['pass_type'] == "member") {
                    return $this->event->wo_eb_member_rate;
                } else {
                    return $this->event->wo_eb_nmember_rate;
                }
            }
        } else {
            if ($this->finalData['rate_type'] == "standard" || $this->finalData['rate_type'] == "Standard") {
                if ($this->finalData['pass_type'] == "fullMember") {
                    return $this->event->std_full_member_rate;
                } else if ($this->finalData['pass_type'] == "member") {
                    return $this->event->std_member_rate;
                } else {
                    return $this->event->std_nmember_rate;
                }
            } else {
                if ($this->finalData['pass_type'] == "fullMember") {
                    return $this->event->eb_full_member_rate;
                } else if ($this->finalData['pass_type'] == "member") {
                    return $this->event->eb_member_rate;
                } else {
                    return $this->event->eb_nmember_rate;
                }
            }
        }
    }


    public function calculateTotal()
    {
        $invoiceDetails = array();
        $countFinalQuantity = 0;

        $mainDelegate = MainDelegates::where('id', $this->finalData['mainDelegateId'])->where('event_id', $this->eventId)->first();

        $addMainDelegate = true;
        if ($mainDelegate->delegate_cancelled) {
            if ($mainDelegate->delegate_refunded || $mainDelegate->delegate_replaced) {
                $addMainDelegate = false;
            }
        }

        if ($mainDelegate->delegate_replaced_by_id == null & (!$mainDelegate->delegate_refunded)) {
            $countFinalQuantity++;
        }

        if ($addMainDelegate) {
            $promoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('promo_code', $mainDelegate->pcode_used)->first();

            if ($promoCode != null) {

                if ($promoCode->badge_type == $mainDelegate->badge_type) {
                    $promoCodeUsed = $mainDelegate->pcode_used;
                    $mainDiscount = $promoCode->discount;
                    $mainDiscountType = $promoCode->discount_type;
                } else {
                    $promoCodeAdditionalBadgeType = PromoCodeAddtionalBadgeTypes::where('event_id', $this->eventId)->where('promo_code_id', $promoCode->id)->where('badge_type', $mainDelegate->badge_type)->first();

                    if ($promoCodeAdditionalBadgeType != null) {
                        $promoCodeUsed = $mainDelegate->pcode_used;
                        $mainDiscount = $promoCode->discount;
                        $mainDiscountType = $promoCode->discount_type;
                    } else {
                        $promoCodeUsed = null;
                        $mainDiscount = 0;
                        $mainDiscountType = null;
                    }
                }
            } else {
                $promoCodeUsed = null;
                $mainDiscount = 0;
                $mainDiscountType = null;
            }

            if ($mainDiscountType != null) {
                if ($mainDiscountType == "percentage") {

                    if ($mainDiscount == 100) {
                        if ($this->getAccessTypesDescription($this->accessType, true) == null) {
                            $delegateDescription = "Delegate Registration Fee - Complimentary";
                        } else {
                            $delegateDescription = "Delegate Registration Fee - Complimentary" . $this->getAccessTypesDescription($this->accessType, true);
                        }
                    } else if ($mainDiscount > 0 && $mainDiscount < 100) {
                        $delegateDescription = "Delegate Registration Fee - " . $mainDelegate->rate_type_string . " (" . $mainDiscount . "% discount)";
                    } else {
                        $delegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                    }

                    $tempTotalUnitPrice = $this->checkUnitPrice();
                    $tempTotalDiscount = $this->checkUnitPrice() * ($mainDiscount / 100);
                    $tempTotalNetAmount = $this->checkUnitPrice() - ($this->checkUnitPrice() * ($mainDiscount / 100));
                } else if ($mainDiscountType == "price") {
                    $tempTotalUnitPrice = $this->checkUnitPrice();
                    $tempTotalDiscount = $mainDiscount;
                    $tempTotalNetAmount = $this->checkUnitPrice() - $mainDiscount;
                    $delegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                } else {
                    $tempTotalUnitPrice = $promoCode->new_rate;
                    $tempTotalDiscount = 0;
                    $tempTotalNetAmount = $promoCode->new_rate;
                    $delegateDescription = $promoCode->new_rate_description;
                }
            } else {
                $tempTotalUnitPrice = $this->checkUnitPrice();
                $tempTotalDiscount = 0;
                $tempTotalNetAmount = $this->checkUnitPrice();
                $delegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
            }

            array_push($invoiceDetails, [
                'delegateDescription' => $delegateDescription,
                'delegateNames' => [
                    $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                ],
                'badgeType' => $mainDelegate->badge_type,
                'quantity' => 1,
                'totalUnitPrice' => $tempTotalUnitPrice,
                'totalDiscount' => $tempTotalDiscount,
                'totalNetAmount' =>  $tempTotalNetAmount,
                'promoCodeDiscount' => $mainDiscount,
                'promoCodeUsed' => $promoCodeUsed,
            ]);
        }


        $subDelegates = AdditionalDelegates::where('main_delegate_id', $this->finalData['mainDelegateId'])->get();
        if (!$subDelegates->isEmpty()) {
            foreach ($subDelegates as $subDelegate) {

                if ($subDelegate->delegate_replaced_by_id == null & (!$subDelegate->delegate_refunded)) {
                    $countFinalQuantity++;
                }

                $addSubDelegate = true;
                if ($subDelegate->delegate_cancelled) {
                    if ($subDelegate->delegate_refunded || $subDelegate->delegate_replaced) {
                        $addSubDelegate = false;
                    }
                }


                if ($addSubDelegate) {
                    $subPromoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('promo_code', $subDelegate->pcode_used)->first();

                    $checkIfExisting = false;
                    $existingIndex = 0;


                    if ($subPromoCode != null) {
                        if ($subPromoCode->badge_type == $subDelegate->badge_type) {
                            $subPromoCodeUsed = $subDelegate->pcode_used;
                            $subDiscount = $subPromoCode->discount;
                            $subDiscountType = $subPromoCode->discount_type;
                        } else {
                            $subPromoCodeAdditionalBadgeType = PromoCodeAddtionalBadgeTypes::where('event_id', $this->eventId)->where('promo_code_id', $subPromoCode->id)->where('badge_type', $subDelegate->badge_type)->first();

                            if ($subPromoCodeAdditionalBadgeType != null) {
                                $subPromoCodeUsed = $subDelegate->pcode_used;
                                $subDiscount = $subPromoCode->discount;
                                $subDiscountType = $subPromoCode->discount_type;
                            } else {
                                $subPromoCodeUsed = null;
                                $subDiscount = 0;
                                $subDiscountType = null;
                            }
                        }
                    } else {
                        $subPromoCodeUsed = null;
                        $subDiscount = 0;
                        $subDiscountType = null;
                    }


                    for ($j = 0; $j < count($invoiceDetails); $j++) {
                        if ($subDelegate->badge_type == $invoiceDetails[$j]['badgeType'] && $subPromoCodeUsed == $invoiceDetails[$j]['promoCodeUsed']) {
                            $existingIndex = $j;
                            $checkIfExisting = true;
                            break;
                        }
                    }

                    if ($checkIfExisting) {
                        array_push(
                            $invoiceDetails[$existingIndex]['delegateNames'],
                            $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name
                        );

                        $quantityTemp = $invoiceDetails[$existingIndex]['quantity'] + 1;

                        if ($subDiscountType != null) {
                            if ($subDiscountType == "percentage") {
                                $totalDiscountTemp = ($this->checkUnitPrice() * ($invoiceDetails[$existingIndex]['promoCodeDiscount'] / 100)) * $quantityTemp;
                                $totalNetAmountTemp = ($this->checkUnitPrice() * $quantityTemp) - $totalDiscountTemp;
                            } else if ($subDiscountType == "price") {
                                $totalDiscountTemp = $invoiceDetails[$existingIndex]['promoCodeDiscount'] * $quantityTemp;
                                $totalNetAmountTemp = ($this->checkUnitPrice() * $quantityTemp) - $totalDiscountTemp;
                            } else {
                                $totalDiscountTemp = 0;
                                $totalNetAmountTemp = $subPromoCode->new_rate * $quantityTemp;
                            }
                        } else {
                            $totalDiscountTemp = $invoiceDetails[$existingIndex]['promoCodeDiscount'];
                            $totalNetAmountTemp = ($this->checkUnitPrice() * $quantityTemp) - $totalDiscountTemp;
                        }


                        $invoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                        $invoiceDetails[$existingIndex]['totalDiscount'] = $totalDiscountTemp;
                        $invoiceDetails[$existingIndex]['totalNetAmount'] = $totalNetAmountTemp;
                    } else {

                        if ($subDiscountType != null) {
                            if ($subDiscountType == "percentage") {
                                if ($subDiscount == 100) {
                                    if ($this->getAccessTypesDescription($this->accessType, true) == null) {
                                        $subDelegateDescription = "Delegate Registration Fee - Complimentary";
                                    } else {
                                        $subDelegateDescription = "Delegate Registration Fee - Complimentary" . $this->getAccessTypesDescription($this->accessType, true);
                                    }
                                } else if ($subDiscount > 0 && $subDiscount < 100) {
                                    $subDelegateDescription = "Delegate Registration Fee - " . $mainDelegate->rate_type_string . " (" . $subDiscount . "% discount)";
                                } else {
                                    $subDelegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                                }

                                $tempSubTotalUnitPrice = $this->checkUnitPrice();
                                $tempSubTotalDiscount = $this->checkUnitPrice() * ($subDiscount / 100);
                                $tempSubTotalNetAmount = $this->checkUnitPrice() - ($this->checkUnitPrice() * ($subDiscount / 100));
                            } else if ($subDiscountType == "price") {
                                $subDelegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";

                                $tempSubTotalUnitPrice = $this->checkUnitPrice();
                                $tempSubTotalDiscount = $subDiscount;
                                $tempSubTotalNetAmount = $this->checkUnitPrice() - $subDiscount;
                            } else {
                                $subDelegateDescription = $subPromoCode->new_rate_description;

                                $tempSubTotalUnitPrice = $subPromoCode->new_rate;
                                $tempSubTotalDiscount = 0;
                                $tempSubTotalNetAmount = $subPromoCode->new_rate;
                            }
                        } else {
                            $subDelegateDescription = "Delegate Registration Fee - {$mainDelegate->rate_type_string}";
                            $tempSubTotalUnitPrice = $this->checkUnitPrice();
                            $tempSubTotalDiscount = 0;
                            $tempSubTotalNetAmount = $this->checkUnitPrice();
                        }

                        array_push($invoiceDetails, [
                            'delegateDescription' => $subDelegateDescription,
                            'delegateNames' => [
                                $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name,
                            ],
                            'badgeType' => $subDelegate->badge_type,
                            'quantity' => 1,
                            'totalUnitPrice' => $tempSubTotalUnitPrice,
                            'totalDiscount' => $tempSubTotalDiscount,
                            'totalNetAmount' =>  $tempSubTotalNetAmount,
                            'promoCodeDiscount' => $subDiscount,
                            'promoCodeUsed' => $subPromoCodeUsed,
                        ]);
                    }
                }
            }
        }

        $net_amount = 0;
        $discount_price = 0;

        foreach ($invoiceDetails as $invoiceDetail) {
            $net_amount += $invoiceDetail['totalNetAmount'];
            $discount_price += $invoiceDetail['totalDiscount'];
        }
        $totalVat = $net_amount * ($this->event->event_vat / 100);
        $totalAmount = $net_amount + $totalVat;

        $this->finalData['invoiceData']['vat_price'] = $totalVat;
        $this->finalData['invoiceData']['net_amount'] = $net_amount;
        $this->finalData['invoiceData']['total_amount'] = $totalAmount;
        $this->finalData['invoiceData']['unit_price'] = $this->checkUnitPrice();
        $this->finalData['invoiceData']['invoiceDetails'] = $invoiceDetails;
        $this->finalData['invoiceData']['finalQuantity'] = $countFinalQuantity;
        $this->finalData['invoiceData']['total_amount_string'] = ucwords($this->numberToWords($totalAmount));

        if ($this->finalData['registration_status'] == "confirmed") {
            if ($this->finalData['invoiceData']['total_amount'] == 0) {
                $this->finalData['payment_status'] = "free";
            } else {
                $this->finalData['payment_status'] = "paid";
            }
        } else if ($this->finalData['registration_status'] == "pending" || $this->finalData['registration_status'] == "droppedOut") {
            if ($this->finalData['invoiceData']['total_amount'] == 0) {
                $this->finalData['payment_status'] = "free";
            } else {
                $this->finalData['payment_status'] = "unpaid";
            }
        } else {
            //do nothing
        }

        MainDelegates::find($this->finalData['mainDelegateId'])->fill([
            'vat_price' => $totalVat,
            'net_amount' => $net_amount,
            'total_amount' => $totalAmount,
            'unit_price' => $this->checkUnitPrice(),
            'discount_price' => $discount_price,
            'payment_status' => $this->finalData['payment_status'],
        ])->save();
    }


    public function numberToWords($number)
    {
        $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        return $formatter->format($number);
    }

    public function sendEmailRegistrationConfirmationConfirmationSingle($index, $innerIndex)
    {
        $this->sendEmailActiveIndex = $index;
        $this->sendEmailActiveInnerIndex = $innerIndex;
        $this->dispatchBrowserEvent('swal:send-email-registration-confirmation-confirmation-single', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);
    }

    public function sendEmailRegistrationConfirmationSingle()
    {
        $delegate = $this->finalData['allDelegates'][$this->sendEmailActiveIndex][$this->sendEmailActiveInnerIndex];

        $eventFormattedData = Carbon::parse($this->event->event_start_date)->format('d') . '-' . Carbon::parse($this->event->event_end_date)->format('d M Y');
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $delegate['mainDelegateId'];

        if ($this->event->eb_end_date == null) {
            $earlyBirdValidityDate = Carbon::createFromFormat('Y-m-d', $this->event->std_start_date)->subDay();
        } else {
            $earlyBirdValidityDate = Carbon::createFromFormat('Y-m-d', $this->event->eb_end_date);
        }

        $delegateVatPrice = $this->finalData['invoiceData']['unit_price'] * ($this->event->event_vat / 100);
        $amountPaid = $this->finalData['invoiceData']['unit_price'] + $delegateVatPrice;

        $promoCode = PromoCodes::where('event_id', $this->event->id)->where('promo_code', $delegate['pcode_used'])->first();

        if ($promoCode != null) {
            if ($promoCode->discount_type == "percentage") {
                $delegateDiscountPrice = $this->finalData['invoiceData']['unit_price'] * ($promoCode->discount / 100);
                $delegateDiscountedPrice = $this->finalData['invoiceData']['unit_price'] - $delegateDiscountPrice;
                $delegateVatPrice = $delegateDiscountedPrice * ($this->event->event_vat / 100);
                $amountPaid = $delegateDiscountedPrice + $delegateVatPrice;
            } else if ($promoCode->discount_type == "price") {
                $delegateDiscountedPrice = $this->finalData['invoiceData']['unit_price'] - $promoCode->discount;
                $delegateVatPrice = $delegateDiscountedPrice * ($this->event->event_vat / 100);
                $amountPaid = $delegateDiscountedPrice + $delegateVatPrice;
            } else {
                $delegateVatPrice = $promoCode->new_rate * ($this->event->event_vat / 100);
                $amountPaid = $promoCode->new_rate + $delegateVatPrice;
            }
        }

        if ($this->finalData['alternative_company_name'] == null) {
            $finalCompanyName = $this->finalData['company_name'];
        } else {
            $finalCompanyName = $this->finalData['alternative_company_name'];
        }

        $combinedStringPrint = "gpca@reg" . ',' . $this->event->id . ',' . $this->event->category . ',' . $delegate['delegateId'] . ',' . $delegate['delegateType'];
        $finalCryptStringPrint = base64_encode($combinedStringPrint);
        $qrCodeForPrint = 'ca' . $finalCryptStringPrint . 'gp';

        $details1 = [
            'name' => $delegate['name'],
            'eventLink' => $this->event->link,
            'eventName' => $this->event->name,
            'eventDates' => $eventFormattedData,
            'eventLocation' => $this->event->location,
            'eventCategory' => $this->event->category,
            'eventYear' => $this->event->year,

            'accessType' => $this->finalData['access_type'],
            'jobTitle' => $delegate['job_title'],
            'companyName' => $finalCompanyName,
            'badgeType' => $delegate['badge_type'],
            'amountPaid' => $amountPaid,
            'transactionId' => $delegate['transactionId'],
            'invoiceLink' => $invoiceLink,
            'earlyBirdValidityDate' => $earlyBirdValidityDate->format('jS F'),
            'badgeLink' => env('APP_URL') . "/" . $this->event->category . "/" . $this->event->id . "/view-badge" . "/" . $delegate['delegateType'] . "/" . $delegate['delegateId'],
            'qrCodeForPrint' => $qrCodeForPrint,
        ];

        if ($this->finalData['payment_status'] == "unpaid") {
            if ($amountPaid == 0) {
                try {
                    Mail::to($delegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationFree($details1, $this->sendInvoice));
                    if ($delegate['delegateType'] == "main") {
                        MainDelegates::find($delegate['delegateId'])->fill([
                            'registration_confirmation_sent_count' => $delegate['registration_confirmation_sent_count'] + 1,
                            'registration_confirmation_sent_datetime' => Carbon::now(),
                        ])->save();
                    } else {
                        AdditionalDelegates::find($delegate['delegateId'])->fill([
                            'registration_confirmation_sent_count' => $delegate['registration_confirmation_sent_count'] + 1,
                            'registration_confirmation_sent_datetime' => Carbon::now(),
                        ])->save();
                    }
                } catch (\Exception $e) {
                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($details1, $this->sendInvoice));
                }
            } else {
                try {
                    Mail::to($delegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationUnpaid($details1, $this->sendInvoice));
                    if ($delegate['delegateType'] == "main") {
                        MainDelegates::find($delegate['delegateId'])->fill([
                            'registration_confirmation_sent_count' => $delegate['registration_confirmation_sent_count'] + 1,
                            'registration_confirmation_sent_datetime' => Carbon::now(),
                        ])->save();
                    } else {
                        AdditionalDelegates::find($delegate['delegateId'])->fill([
                            'registration_confirmation_sent_count' => $delegate['registration_confirmation_sent_count'] + 1,
                            'registration_confirmation_sent_datetime' => Carbon::now(),
                        ])->save();
                    }
                } catch (\Exception $e) {
                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationUnpaid($details1, $this->sendInvoice));
                }
            }
        } else if ($this->finalData['payment_status'] == "free" && $this->finalData['registration_status'] == "pending") {
            try {
                Mail::to($delegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationFree($details1, $this->sendInvoice));
                if ($delegate['delegateType'] == "main") {
                    MainDelegates::find($delegate['delegateId'])->fill([
                        'registration_confirmation_sent_count' => $delegate['registration_confirmation_sent_count'] + 1,
                        'registration_confirmation_sent_datetime' => Carbon::now(),
                    ])->save();
                } else {
                    AdditionalDelegates::find($delegate['delegateId'])->fill([
                        'registration_confirmation_sent_count' => $delegate['registration_confirmation_sent_count'] + 1,
                        'registration_confirmation_sent_datetime' => Carbon::now(),
                    ])->save();
                }
            } catch (\Exception $e) {
                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($details1, $this->sendInvoice));
            }
        } else {
            try {
                Mail::to($delegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaid($details1, $this->sendInvoice));
                if ($delegate['delegateType'] == "main") {
                    MainDelegates::find($delegate['delegateId'])->fill([
                        'registration_confirmation_sent_count' => $delegate['registration_confirmation_sent_count'] + 1,
                        'registration_confirmation_sent_datetime' => Carbon::now(),
                    ])->save();
                } else {
                    AdditionalDelegates::find($delegate['delegateId'])->fill([
                        'registration_confirmation_sent_count' => $delegate['registration_confirmation_sent_count'] + 1,
                        'registration_confirmation_sent_datetime' => Carbon::now(),
                    ])->save();
                }
            } catch (\Exception $e) {
                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaid($details1, $this->sendInvoice));
            }
        }

        $this->finalData['allDelegates'][$this->sendEmailActiveIndex][$this->sendEmailActiveInnerIndex]['registration_confirmation_sent_count'] = $this->finalData['allDelegates'][$this->sendEmailActiveIndex][$this->sendEmailActiveInnerIndex]['registration_confirmation_sent_count'] + 1;
        $this->finalData['allDelegates'][$this->sendEmailActiveIndex][$this->sendEmailActiveInnerIndex]['registration_confirmation_sent_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

        $this->dispatchBrowserEvent('swal:send-email-registration-success', [
            'type' => 'success',
            'message' => 'Registration Confirmation sent!',
            'text' => "",
        ]);
    }

    public function sendEmailRegistrationConfirmationConfirmation()
    {
        $this->dispatchBrowserEvent('swal:send-email-registration-confirmation-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);
    }

    public function sendEmailRegistrationConfirmation()
    {
        $eventFormattedData = Carbon::parse($this->event->event_start_date)->format('d') . '-' . Carbon::parse($this->event->event_end_date)->format('d M Y');
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->finalData['mainDelegateId'];

        if ($this->event->eb_end_date == null) {
            $earlyBirdValidityDate = Carbon::createFromFormat('Y-m-d', $this->event->std_start_date)->subDay();
        } else {
            $earlyBirdValidityDate = Carbon::createFromFormat('Y-m-d', $this->event->eb_end_date);
        }

        $assistantDetails1 = [];
        $assistantDetails2 = [];

        foreach ($this->finalData['allDelegates'] as $delegatesIndex => $delegates) {
            foreach ($delegates as $innerDelegateIndex => $innerDelegate) {
                if (end($delegates) == $innerDelegate) {
                    if (!$innerDelegate['delegate_cancelled']) {

                        $delegateVatPrice = $this->finalData['invoiceData']['unit_price'] * ($this->event->event_vat / 100);
                        $amountPaid = $this->finalData['invoiceData']['unit_price'] + $delegateVatPrice;

                        $promoCode = PromoCodes::where('event_id', $this->event->id)->where('promo_code', $innerDelegate['pcode_used'])->first();

                        if ($promoCode != null) {
                            if ($promoCode->discount_type == "percentage") {
                                $delegateDiscountPrice = $this->finalData['invoiceData']['unit_price'] * ($promoCode->discount / 100);
                                $delegateDiscountedPrice = $this->finalData['invoiceData']['unit_price'] - $delegateDiscountPrice;
                                $delegateVatPrice = $delegateDiscountedPrice * ($this->event->event_vat / 100);
                                $amountPaid = $delegateDiscountedPrice + $delegateVatPrice;
                            } else if ($promoCode->discount_type == "price") {
                                $delegateDiscountedPrice = $this->finalData['invoiceData']['unit_price'] - $promoCode->discount;
                                $delegateVatPrice = $delegateDiscountedPrice * ($this->event->event_vat / 100);
                                $amountPaid = $delegateDiscountedPrice + $delegateVatPrice;
                            } else {
                                $delegateVatPrice = $promoCode->new_rate * ($this->event->event_vat / 100);
                                $amountPaid = $promoCode->new_rate + $delegateVatPrice;
                            }
                        }

                        if ($this->finalData['alternative_company_name'] == null) {
                            $finalCompanyName = $this->finalData['company_name'];
                        } else {
                            $finalCompanyName = $this->finalData['alternative_company_name'];
                        }

                        $combinedStringPrint = "gpca@reg" . ',' . $this->event->id . ',' . $this->event->category . ',' . $innerDelegate['delegateId'] . ',' . $innerDelegate['delegateType'];
                        $finalCryptStringPrint = base64_encode($combinedStringPrint);
                        $qrCodeForPrint = 'ca' . $finalCryptStringPrint . 'gp';

                        $details1 = [
                            'name' => $innerDelegate['name'],
                            'eventLink' => $this->event->link,
                            'eventName' => $this->event->name,
                            'eventDates' => $eventFormattedData,
                            'eventLocation' => $this->event->location,
                            'eventCategory' => $this->event->category,
                            'eventYear' => $this->event->year,

                            'accessType' => $this->finalData['access_type'],
                            'jobTitle' => $innerDelegate['job_title'],
                            'companyName' => $finalCompanyName,
                            'badgeType' => $innerDelegate['badge_type'],
                            'amountPaid' => $amountPaid,
                            'transactionId' => $innerDelegate['transactionId'],
                            'invoiceLink' => $invoiceLink,
                            'earlyBirdValidityDate' => $earlyBirdValidityDate->format('jS F'),
                            'badgeLink' => env('APP_URL') . "/" . $this->event->category . "/" . $this->event->id . "/view-badge" . "/" . $innerDelegate['delegateType'] . "/" . $innerDelegate['delegateId'],
                            'qrCodeForPrint' => $qrCodeForPrint,
                        ];

                        $details2 = [
                            'name' => $innerDelegate['name'],
                            'eventLink' => $this->event->link,
                            'eventName' => $this->event->name,
                            'eventCategory' => $this->event->category,
                            'eventYear' => $this->event->year,

                            'invoiceAmount' => $this->finalData['invoiceData']['total_amount'],
                            'amountPaid' => $this->finalData['invoiceData']['total_amount'],
                            'balance' => 0,
                            'invoiceLink' => $invoiceLink,
                        ];

                        if ($delegatesIndex == 0) {
                            $assistantDetails1 = $details1;
                            $assistantDetails2 = $details2;
                        }

                        if ($this->finalData['payment_status'] == "unpaid") {
                            if ($amountPaid == 0) {
                                try {
                                    Mail::to($innerDelegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationFree($details1, $this->sendInvoice));

                                    if ($innerDelegate['delegateType'] == "main") {
                                        MainDelegates::find($innerDelegate['delegateId'])->fill([
                                            'registration_confirmation_sent_count' => $innerDelegate['registration_confirmation_sent_count'] + 1,
                                            'registration_confirmation_sent_datetime' => Carbon::now(),
                                        ])->save();
                                    } else {
                                        AdditionalDelegates::find($innerDelegate['delegateId'])->fill([
                                            'registration_confirmation_sent_count' => $innerDelegate['registration_confirmation_sent_count'] + 1,
                                            'registration_confirmation_sent_datetime' => Carbon::now(),
                                        ])->save();
                                    }

                                    $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_count'] = $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_count'] + 1;
                                    $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
                                } catch (\Exception $e) {
                                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($details1, $this->sendInvoice));
                                }
                            } else {
                                try {
                                    Mail::to($innerDelegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationUnpaid($details1, $this->sendInvoice));

                                    if ($innerDelegate['delegateType'] == "main") {
                                        MainDelegates::find($innerDelegate['delegateId'])->fill([
                                            'registration_confirmation_sent_count' => $innerDelegate['registration_confirmation_sent_count'] + 1,
                                            'registration_confirmation_sent_datetime' => Carbon::now(),
                                        ])->save();
                                    } else {
                                        AdditionalDelegates::find($innerDelegate['delegateId'])->fill([
                                            'registration_confirmation_sent_count' => $innerDelegate['registration_confirmation_sent_count'] + 1,
                                            'registration_confirmation_sent_datetime' => Carbon::now(),
                                        ])->save();
                                    }

                                    $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_count'] = $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_count'] + 1;
                                    $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
                                } catch (\Exception $e) {
                                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationUnpaid($details1, $this->sendInvoice));
                                }
                            }
                        } else if ($this->finalData['payment_status'] == "free" && $this->finalData['registration_status'] == "pending") {
                            try {
                                Mail::to($innerDelegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationFree($details1, $this->sendInvoice));

                                if ($innerDelegate['delegateType'] == "main") {
                                    MainDelegates::find($innerDelegate['delegateId'])->fill([
                                        'registration_confirmation_sent_count' => $innerDelegate['registration_confirmation_sent_count'] + 1,
                                        'registration_confirmation_sent_datetime' => Carbon::now(),
                                    ])->save();
                                } else {
                                    AdditionalDelegates::find($innerDelegate['delegateId'])->fill([
                                        'registration_confirmation_sent_count' => $innerDelegate['registration_confirmation_sent_count'] + 1,
                                        'registration_confirmation_sent_datetime' => Carbon::now(),
                                    ])->save();
                                }

                                $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_count'] = $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_count'] + 1;
                                $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
                            } catch (\Exception $e) {
                                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($details1, $this->sendInvoice));
                            }
                        } else {
                            try {
                                Mail::to($innerDelegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaid($details1, $this->sendInvoice));

                                if ($innerDelegate['delegateType'] == "main") {
                                    MainDelegates::find($innerDelegate['delegateId'])->fill([
                                        'registration_confirmation_sent_count' => $innerDelegate['registration_confirmation_sent_count'] + 1,
                                        'registration_confirmation_sent_datetime' => Carbon::now(),
                                    ])->save();
                                } else {
                                    AdditionalDelegates::find($innerDelegate['delegateId'])->fill([
                                        'registration_confirmation_sent_count' => $innerDelegate['registration_confirmation_sent_count'] + 1,
                                        'registration_confirmation_sent_datetime' => Carbon::now(),
                                    ])->save();
                                }

                                $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_count'] = $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_count'] + 1;
                                $this->finalData['allDelegates'][$delegatesIndex][$innerDelegateIndex]['registration_confirmation_sent_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
                            } catch (\Exception $e) {
                                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaid($details1, $this->sendInvoice));
                            }
                            //$this->event->category != "GLF" && $this->event->category != "DFCLW1"
                            if ($this->event->category != "DFCLW1") {
                                if ($this->sendInvoice) {
                                    if ($delegatesIndex == 0) {
                                        try {
                                            Mail::to($innerDelegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
                                        } catch (\Exception $e) {
                                            Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $assistantDetails1['amountPaid'] = $this->finalData['invoiceData']['total_amount'];

        if ($this->finalData['assistant_email_address'] != null) {
            if ($this->finalData['payment_status'] == "unpaid") {
                try {
                    Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationUnpaid($assistantDetails1, $this->sendInvoice));
                } catch (\Exception $e) {
                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationUnpaid($assistantDetails1, $this->sendInvoice));
                }
            } else if ($this->finalData['payment_status'] == "free" && $this->finalData['registration_status'] == "pending") {
                try {
                    Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationFree($assistantDetails1, $this->sendInvoice));
                } catch (\Exception $e) {
                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($assistantDetails1, $this->sendInvoice));
                }
            } else {
                try {
                    Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationPaid($assistantDetails1, $this->sendInvoice));
                } catch (\Exception $e) {
                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaid($assistantDetails1, $this->sendInvoice));
                }
                //$this->event->category != "GLF" && $this->event->category != "DFCLW1"
                if ($this->event->category != "DFCLW1") {
                    if ($this->sendInvoice) {
                        try {
                            Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationPaymentConfirmation($assistantDetails2, $this->sendInvoice));
                        } catch (\Exception $e) {
                            Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentConfirmation($assistantDetails2, $this->sendInvoice));
                        }
                    }
                }
            }
        }

        $this->dispatchBrowserEvent('swal:send-email-registration-success', [
            'type' => 'success',
            'message' => 'Registration Confirmation sent!',
            'text' => "",
        ]);
    }

    public function sendEmailReminderConfirmation()
    {
        $this->dispatchBrowserEvent('swal:payment-reminder-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);
    }

    public function sendEmailReminder()
    {
        $invoiceLink = env('APP_URL') . '/' . $this->eventCategory . '/' . $this->eventId . '/view-invoice/' . $this->registrantId;
        if ($this->event->eb_end_date == null) {
            $earlyBirdValidityDate = Carbon::createFromFormat('Y-m-d', $this->event->std_start_date)->subDay();
        } else {
            $earlyBirdValidityDate = Carbon::createFromFormat('Y-m-d', $this->event->eb_end_date);
        }

        foreach ($this->finalData['allDelegates'] as $delegates) {
            foreach ($delegates as $innerDelegate) {
                if (end($delegates) == $innerDelegate) {
                    if (!$innerDelegate['delegate_cancelled']) {
                        $details = [
                            'name' => $innerDelegate['name'],
                            'eventName' => $this->event->name,
                            'eventCategory' => $this->event->category,
                            'eventLink' => $this->event->link,
                            'invoiceLink' => $invoiceLink,
                            'earlyBirdValidityDate' => $earlyBirdValidityDate->format('jS F'),
                            'eventYear' => $this->event->year,
                        ];
                        try {
                            Mail::to($innerDelegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaymentReminder($details, $this->sendInvoice));
                        } catch (\Exception $e) {
                            Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentReminder($details, $this->sendInvoice));
                        }
                    }
                }
            }
        }

        if ($this->finalData['assistant_email_address'] != null) {
            try {
                Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationPaymentReminder($details, $this->sendInvoice));
            } catch (\Exception $e) {
                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentReminder($details, $this->sendInvoice));
            }
        }

        $this->dispatchBrowserEvent('swal:payment-reminder-success', [
            'type' => 'success',
            'message' => 'Payment Reminder Sent!',
            'text' => "",
        ]);
    }

    public function checkEmailIfExistsInDatabase($emailAddress)
    {
        $allDelegates = Transactions::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->get();

        $countMainDelegate = 0;
        $countSubDelegate = 0;

        if (!$allDelegates->isEmpty()) {
            foreach ($allDelegates as $delegate) {
                if ($delegate->delegate_type == "main") {
                    $mainDelegate = MainDelegates::where('id', $delegate->delegate_id)->where('email_address', $emailAddress)->where('registration_status', '!=', 'droppedOut')->where('delegate_cancelled', '!=', true)->first();
                    if ($mainDelegate != null) {
                        $countMainDelegate++;
                    }
                } else {
                    $subDelegate = AdditionalDelegates::where('id', $delegate->delegate_id)->where('email_address', $emailAddress)->where('delegate_cancelled', '!=', true)->first();
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

    public function openEditTransactionRemarksModal()
    {
        $this->transactionRemarks = $this->finalData['transaction_remarks'];
        $this->showTransactionRemarksModal = true;
    }

    public function closeEditTransactionRemarksModal()
    {
        $this->transactionRemarks = null;
        $this->showTransactionRemarksModal = false;
    }

    public function updateTransactionRemarks()
    {
        MainDelegates::find($this->finalData['mainDelegateId'])->fill([
            'transaction_remarks' => $this->transactionRemarks,
        ])->save();

        $this->finalData['transaction_remarks'] = $this->transactionRemarks;
        $this->transactionRemarks = null;
        $this->showTransactionRemarksModal = false;
    }

    public function openDelegateCancellationModal($index, $innerIndex)
    {
        $this->replaceDelegateIndex = $index;
        $this->replaceDelegateInnerIndex = $innerIndex;
        $this->showDelegateCancellationModal = true;
    }

    public function closeDelegateCancellationModal()
    {
        $this->removeReplaceData();
        $this->showDelegateCancellationModal = false;
    }

    public function nextDelegateCancellation()
    {
        $this->delegateCancellationStep++;
    }

    public function prevDelegateCancellation()
    {
        $this->delegateCancellationStep--;
    }

    public function submitDelegateCancellation()
    {
        if ($this->delegateCancellationStep == 2) {
            if ($this->replaceDelegate == "No") {
                $this->validate(
                    [
                        'delegateRefund' => 'required',
                    ],
                    [
                        'delegateRefund.required' => "This needs to be fill up.",
                    ],
                );

                if ($this->delegateRefund == "Yes") {
                    $message = "Are you sure want to cancel and refund this delegate?";
                } else {
                    $message = "Are you sure want to cancel and not refund this delegate?";
                }

                $this->dispatchBrowserEvent('swal:delegate-cancel-refund-confirmation', [
                    'type' => 'warning',
                    'message' => $message,
                    'text' => "",
                ]);
            } else {
                $this->replaceEmailAlreadyUsedError = null;

                $this->validate(
                    [
                        'replaceFirstName' => 'required',
                        'replaceLastName' => 'required',
                        'replaceEmailAddress' => 'required|email',
                        'replaceNationality' => 'required',
                        'replaceMobileNumber' => 'required',
                        'replaceJobTitle' => 'required',
                        'replaceBadgeType' => 'required',
                        'replaceCountry' => 'required',
                    ],
                    [
                        'replaceFirstName.required' => "First name is required",
                        'replaceLastName.required' => "Last name is required",
                        'replaceEmailAddress.required' => "Email address is required",
                        'replaceEmailAddress.email' => "Email address must be a valid email",
                        'replaceNationality.required' => "Nationality is required",
                        'replaceMobileNumber.required' => "Mobile number is required",
                        'replaceJobTitle.required' => "Job title is required",
                        'replaceBadgeType.required' => "Registration type is required",
                        'replaceCountry.required' => "Country is required",
                    ]
                );

                if ($this->checkEmailIfExistsInDatabase($this->replaceEmailAddress)) {
                    $this->replaceEmailAlreadyUsedError = "Email is already registered, please use another email!";
                } else {
                    $this->replaceEmailAlreadyUsedError = null;
                    $this->dispatchBrowserEvent('swal:delegate-cancel-replace-confirmation', [
                        'type' => 'warning',
                        'message' => 'Are you sure you want to cancel and replace this delegate?',
                        'text' => "",
                    ]);
                }
            }
        }
    }

    public function cancelOrRefundDelegate()
    {
        if ($this->delegateRefund == "Yes") {
            // refunded
            if ($this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegateType'] == "main") {
                MainDelegates::find($this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['mainDelegateId'])->fill([
                    'delegate_cancelled' => true,
                    'delegate_refunded' => true,
                    'delegate_cancelled_datetime' => Carbon::now(),
                    'delegate_refunded_datetime' => Carbon::now(),
                ])->save();
            } else {
                AdditionalDelegates::find($this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegateId'])->fill([
                    'delegate_cancelled' => true,
                    'delegate_refunded' => true,
                    'delegate_cancelled_datetime' => Carbon::now(),
                    'delegate_refunded_datetime' => Carbon::now(),
                ])->save();
            }

            $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_cancelled'] = true;
            $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_refunded'] = true;
            $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
            $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_refunded_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

            if ($this->finalData['finalQuantity'] == 1) {
                MainDelegates::find($this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['mainDelegateId'])->fill([
                    'registration_status' => "cancelled",
                    'payment_status' => "refunded",
                ])->save();

                $this->finalData['registration_status'] = 'cancelled';
                $this->finalData['payment_status'] = 'refunded';
                $this->finalData['finalQuantity'] = 0;
            }

            $this->dispatchBrowserEvent('swal:delegate-cancel-refund-success', [
                'type' => 'success',
                'message' => 'Delegate cancelled and refunded succesfully!',
                'text' => "",
            ]);
        } else {
            // not refunded
            if ($this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegateType'] == "main") {
                MainDelegates::find($this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['mainDelegateId'])->fill([
                    'delegate_cancelled' => true,
                    'delegate_cancelled_datetime' => Carbon::now(),
                ])->save();
            } else {
                AdditionalDelegates::find($this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegateId'])->fill([
                    'delegate_cancelled' => true,
                    'delegate_cancelled_datetime' => Carbon::now(),
                ])->save();
            }

            $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_cancelled'] = true;
            $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

            if ($this->finalData['finalQuantity'] == 1) {
                MainDelegates::find($this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['mainDelegateId'])->fill([
                    'registration_status' => "cancelled",
                ])->save();

                $this->finalData['registration_status'] = 'cancelled';
            }

            $this->dispatchBrowserEvent('swal:delegate-cancel-refund-success', [
                'type' => 'success',
                'message' => 'Delegate cancelled but not refunded succesfully!',
                'text' => "",
            ]);
        }
        $this->calculateTotal();
        $this->showDelegateCancellationModal = false;
    }

    public function addReplaceDelegate()
    {
        if ($this->replacePromoCodeSuccess != null) {
            PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('active', true)->where('promo_code', $this->replacePromoCode)->increment('total_usage');

            $subPromoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('promo_code', $this->replacePromoCode)->where('badge_type', $this->replaceBadgeType)->first();

            if ($subPromoCode != null) {
                $subChecker = false;

                if ($subPromoCode->badge_type == $this->replaceBadgeType) {
                    $subChecker = true;
                } else {
                    $promoCodeAdditionalBadgeType = PromoCodeAddtionalBadgeTypes::where('event_id', $this->eventId)->where('promo_code_id', $subPromoCode->id)->where('badge_type', $this->replaceBadgeType)->first();

                    if ($promoCodeAdditionalBadgeType != null) {
                        $subChecker = true;
                    } else {
                        $subChecker = false;
                    }
                }

                if ($subChecker) {
                    $subDiscountType = $subPromoCode->discount_type;

                    if ($subPromoCode->discount_type == 'percentage') {
                        $subDiscount = $subPromoCode->discount;
                    } else if ($subPromoCode->discount_type == 'price') {
                        $subDiscount = $subPromoCode->discount;
                    } else {
                        $subDiscount = 0;
                    }
                } else {
                    $subDiscount = 0;
                    $subDiscountType = null;
                }
            } else {
                $subDiscount = 0;
                $subDiscountType = null;
            }
        } else {
            $this->replacePromoCode = null;
            $subDiscount = null;
            $subDiscountType = null;
        }

        $replacedDelegate = AdditionalDelegates::create([
            'main_delegate_id' => $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['mainDelegateId'],
            'salutation' => $this->replaceSalutation,
            'first_name' => $this->replaceFirstName,
            'middle_name' => $this->replaceMiddleName,
            'last_name' => $this->replaceLastName,
            'job_title' => $this->replaceJobTitle,
            'email_address' => $this->replaceEmailAddress,
            'nationality' => $this->replaceNationality,
            'mobile_number' => $this->replaceMobileNumber,
            'badge_type' => $this->replaceBadgeType,
            'pcode_used' => $this->replacePromoCode,
            'country' => $this->replaceCountry,
            'interests' => json_encode($this->replaceInterests),

            'delegate_replaced_type' => $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_replaced_type'],
            'delegate_replaced_from_id' => $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegateId'],
            'delegate_original_from_id' => $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_original_from_id'],
        ]);


        $transaction = Transactions::create([
            'event_id' => $this->eventId,
            'event_category' => $this->eventCategory,
            'delegate_id' => $replacedDelegate->id,
            'delegate_type' => "sub",
        ]);

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }
        $lastDigit = 1000 + intval($transaction->id);
        $finalTransactionId = $this->event->year . $eventCode . $lastDigit;

        array_push($this->finalData['allDelegates'][$this->replaceDelegateIndex], [
            'transactionId' => $finalTransactionId,
            'mainDelegateId' => $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['mainDelegateId'],
            'delegateId' => $replacedDelegate->id,
            'delegateType' => "sub",

            'name' => $this->replaceSalutation . " " . $this->replaceFirstName . " " . $this->replaceMiddleName . " " . $this->replaceLastName,
            'salutation' => $this->replaceSalutation,
            'first_name' => $this->replaceFirstName,
            'middle_name' => $this->replaceMiddleName,
            'last_name' => $this->replaceLastName,
            'job_title' => $this->replaceJobTitle,
            'email_address' => $this->replaceEmailAddress,
            'nationality' => $this->replaceNationality,
            'mobile_number' => $this->replaceMobileNumber,
            'badge_type' => $this->replaceBadgeType,
            'pcode_used' => $this->replacePromoCode,
            'discount' => $subDiscount,
            'discount_type' => $subDiscountType,
            'country' => $this->replaceCountry,
            'interests' => $this->replaceInterests,

            'is_replacement' => true,
            'delegate_cancelled' => false,
            'delegate_replaced' => false,
            'delegate_refunded' => false,

            'delegate_replaced_type' => "sub",
            'delegate_original_from_id' => $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_original_from_id'],
            'delegate_replaced_from_id' => $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegateId'],
            'delegate_replaced_by_id' => null,

            'delegate_cancelled_datetime' => null,
            'delegate_refunded_datetime' => null,
            'delegate_replaced_datetime' => null,

            'registration_confirmation_sent_count' => $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['registration_confirmation_sent_count'],
            'registration_confirmation_sent_datetime' => $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['registration_confirmation_sent_datetime'],
        ]);

        if ($this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegateType'] == "main") {
            MainDelegates::find($this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['mainDelegateId'])->fill([
                'delegate_cancelled' => true,
                'delegate_cancelled_datetime' => Carbon::now(),
                'delegate_replaced' => true,
                'delegate_replaced_by_id' => $replacedDelegate->id,
                'delegate_replaced_datetime' => Carbon::now(),
            ])->save();
        } else {
            AdditionalDelegates::find($this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegateId'])->fill([
                'delegate_cancelled' => true,
                'delegate_cancelled_datetime' => Carbon::now(),
                'delegate_replaced' => true,
                'delegate_replaced_by_id' => $replacedDelegate->id,
                'delegate_replaced_datetime' => Carbon::now(),
            ])->save();
        }


        MainDelegates::where('id', $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['mainDelegateId'])->increment('quantity');

        $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_cancelled'] = true;
        $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_replaced'] = true;
        $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
        $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['delegate_replaced_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
        $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['registration_confirmation_sent_count'] = 0;
        $this->finalData['allDelegates'][$this->replaceDelegateIndex][$this->replaceDelegateInnerIndex]['registration_confirmation_sent_datetime'] = null;


        $this->dispatchBrowserEvent('swal:delegate-cancel-replace-success', [
            'type' => 'success',
            'message' => 'Delegate replaced succesfully!',
            'text' => "",
        ]);
        $this->calculateTotal();
        $this->removeReplaceData();
        $this->showDelegateCancellationModal = false;
    }

    public function replaceApplyPromoCode()
    {
        $this->validate([
            'replaceBadgeType' => 'required',
            'replacePromoCode' => 'required',
        ], [
            'replaceBadgeType.required' => 'Please choose your registration type first',
            'replacePromoCode.required' => 'Promo code is required',
        ]);

        $promoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('active', true)->where('promo_code', $this->replacePromoCode)->first();

        $promoCodeChecker = true;

        if ($promoCode != null) {
            if ($promoCode->badge_type == $this->replaceBadgeType) {
                $promoCodeChecker = true;
            } else {
                $promoCodeAdditionalBadgeType = PromoCodeAddtionalBadgeTypes::where('event_id', $this->eventId)->where('promo_code_id', $promoCode->id)->where('badge_type', $this->replaceBadgeType)->first();

                if ($promoCodeAdditionalBadgeType != null) {
                    $promoCodeChecker = true;
                } else {
                    $promoCodeChecker = false;
                }
            }
        } else {
            $promoCodeChecker = false;
        }


        if ($promoCodeChecker) {
            if ($promoCode->total_usage < $promoCode->number_of_codes) {
                $validityDateTime = Carbon::parse($promoCode->validity);
                if (Carbon::now()->lt($validityDateTime)) {
                    $this->replacePromoCodeFail = null;
                    $this->replacePromoCodeDiscount = $promoCode->discount;
                    $this->replaceDiscountType = $promoCode->discount_type;

                    if ($promoCode->discount_type == "percentage") {
                        $this->replacePromoCodeSuccess = "$promoCode->discount% discount";
                    } else if ($promoCode->discount_type == "price") {
                        $this->replacePromoCodeSuccess = "$$promoCode->discount discount";
                    } else {
                        $this->promoCodeSuccess = "Fixed rate applied";
                    }
                } else {
                    $this->replacePromoCodeFail = "Code is expired already";
                }
            } else {
                $this->replacePromoCodeFail = "Code has reached its capacity";
            }
        } else {
            $this->replacePromoCodeFail = "Invalid Code";
        }
    }

    public function replaceRemovePromoCode()
    {
        $this->replacePromoCode = null;
        $this->replacePromoCodeDiscount = null;
        $this->replaceDiscountType = null;
        $this->replacePromoCodeFail = null;
        $this->replacePromoCodeSuccess = null;
    }

    public function removeReplaceData()
    {
        $this->delegateCancellationStep = 1;
        $this->replaceDelegateIndex = null;
        $this->replaceDelegateInnerIndex = null;

        $this->replaceSalutation = null;
        $this->replaceFirstName = null;
        $this->replaceMiddleName = null;
        $this->replaceLastName = null;
        $this->replaceEmailAddress = null;
        $this->replaceMobileNumber = null;
        $this->replaceNationality = null;
        $this->replaceJobTitle = null;
        $this->replaceBadgeType = null;

        $this->replacePromoCode = null;
        $this->replacePromoCodeDiscount = null;
        $this->replaceDiscountType = null;
        $this->replacePromoCodeFail = null;
        $this->replacePromoCodeSuccess = null;

        $this->replaceEmailAlreadyUsedError = null;
        $this->replaceCountry = null;
        $this->replaceInterests = null;
    }

    public function getAccessTypesDescription($accessType, $enableNullForFullEvent)
    {
        if ($accessType == AccessTypes::CONFERENCE_ONLY->value) {
            return " - Conference only";
        } else if ($accessType == AccessTypes::WORKSHOP_ONLY->value) {
            return " - Workshop only";
        } else {
            if ($enableNullForFullEvent) {
                if (
                    $this->event->co_eb_full_member_rate != null ||
                    $this->event->co_eb_member_rate != null ||
                    $this->event->co_eb_nmember_rate != null ||
                    $this->event->co_std_full_member_rate != null ||
                    $this->event->co_std_member_rate != null ||
                    $this->event->co_std_nmember_rate != null ||
                    $this->event->wo_eb_full_member_rate != null ||
                    $this->event->wo_eb_member_rate != null ||
                    $this->event->wo_eb_nmember_rate != null ||
                    $this->event->wo_std_full_member_rate != null ||
                    $this->event->wo_std_member_rate != null ||
                    $this->event->wo_std_nmember_rate != null
                ) {
                    return " - Full event";
                } else {
                    return null;
                }
            } else {
                return " - Full event";
            }
        }
    }
}
