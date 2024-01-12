<?php

namespace App\Http\Livewire;

use App\Mail\RegistrationFree;
use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Mail\RegistrationPaymentReminder;
use App\Mail\RegistrationUnpaid;
use Livewire\Component;
use App\Models\MainVisitor as MainVisitors;
use App\Models\Event as Events;
use App\Models\AdditionalVisitor as AdditionalVisitors;
use App\Models\VisitorTransaction as VisitorTransactions;
use App\Models\Member as Members;
use App\Models\PromoCode as PromoCodes;
use App\Models\EventRegistrationType as EventRegistrationTypes;
use App\Models\PromoCodeAddtionalBadgeType as PromoCodeAddtionalBadgeTypes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use NumberFormatter;

class VisitorRegistrantDetails extends Component
{
    public $countries, $companySectors, $salutations, $registrationTypes;

    public $eventCategory, $eventId, $registrantId, $finalData, $members, $event;

    public $rateType, $finalUnitPrice;

    // COMPANY INFO
    public $visitorPassType, $rateTypeString, $companyName, $alternativeCompanyName, $companySector, $companyAddress, $companyCountry, $companyCity, $companyLandlineNumber, $assistantEmailAddress, $companyMobileNumber;

    // VISITOR DETAILS
    public $mainVisitorId, $visitorId, $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $nationality, $jobTitle, $badgeType, $promoCode, $promoCodeDiscount, $discountType, $promoCodeSuccess, $promoCodeFail, $type, $visitorIndex, $visitorInnerIndex;

    public $transactionRemarks, $visitorCancellationStep = 1, $replaceVisitor, $visitorRefund;

    public $replaceVisitorIndex, $replaceVisitorInnerIndex, $replaceSalutation, $replaceFirstName, $replaceMiddleName, $replaceLastName, $replaceEmailAddress, $replaceMobileNumber, $replaceNationality, $replaceJobTitle, $replaceBadgeType, $replacePromoCode, $replaceDiscountType, $replacePromoCodeDiscount, $replacePromoCodeSuccess, $replacePromoCodeFail, $replaceEmailAlreadyUsedError;

    public $mapPaymentMethod, $sendInvoice;

    // MODALS
    public $showVisitorModal = false;
    public $showCompanyModal = false;
    public $showTransactionRemarksModal = false;
    public $showVisitorCancellationModal = false;
    public $showMarkAsPaidModal = false;

    public $ccEmailNotif;

    protected $listeners = ['paymentReminderConfirmed' => 'sendEmailReminder', 'sendEmailRegistrationConfirmationConfirmed' => 'sendEmailRegistrationConfirmation', 'cancelRefundDelegateConfirmed' => 'cancelOrRefundVisitor', 'cancelReplaceDelegateConfirmed' => 'addReplaceVisitor', 'markAsPaidConfirmed' => 'markAsPaid'];

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

        $this->ccEmailNotif = config('app.ccEmailNotif.default');
    }

    public function render()
    {
        return view('livewire.admin.events.transactions.visitor.visitor-registrant-details');
    }

    public function updateVisitor()
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

        if ($this->promoCodeSuccess != null) {
            PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('active', true)->where('promo_code', $this->promoCode)->where('badge_type', $this->badgeType)->increment('total_usage');
        } else {
            $this->promoCode = null;
        }

        if ($this->type == "main") {
            MainVisitors::find($this->visitorId)->fill([
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
            ])->save();
        } else {
            AdditionalVisitors::find($this->visitorId)->fill([
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
            ])->save();
        }

        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['salutation'] = $this->salutation;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['first_name'] = $this->firstName;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['middle_name'] = $this->salutation;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['last_name'] = $this->lastName;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['name'] = $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['email_address'] = $this->emailAddress;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['mobile_number'] = $this->mobileNumber;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['nationality'] = $this->nationality;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['job_title'] = $this->jobTitle;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['badge_type'] = $this->badgeType;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['pcode_used'] = $this->promoCode;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['discount'] = $this->promoCodeDiscount;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['discount_type'] = $this->discountType;

        $this->calculateTotal();

        $this->showVisitorModal = false;
        $this->resetEditModalFields();
    }


    public function openEditVisitorModal($index, $innerIndex)
    {
        $this->visitorIndex = $index;
        $this->visitorInnerIndex = $innerIndex;
        $this->mainVisitorId = $this->finalData['allVisitors'][$index][$innerIndex]['mainVisitorId'];
        $this->visitorId = $this->finalData['allVisitors'][$index][$innerIndex]['visitorId'];
        $this->salutation = $this->finalData['allVisitors'][$index][$innerIndex]['salutation'];
        $this->firstName = $this->finalData['allVisitors'][$index][$innerIndex]['first_name'];
        $this->middleName = $this->finalData['allVisitors'][$index][$innerIndex]['middle_name'];
        $this->lastName = $this->finalData['allVisitors'][$index][$innerIndex]['last_name'];
        $this->emailAddress = $this->finalData['allVisitors'][$index][$innerIndex]['email_address'];
        $this->mobileNumber = $this->finalData['allVisitors'][$index][$innerIndex]['mobile_number'];
        $this->nationality = $this->finalData['allVisitors'][$index][$innerIndex]['nationality'];
        $this->jobTitle = $this->finalData['allVisitors'][$index][$innerIndex]['job_title'];
        $this->badgeType = $this->finalData['allVisitors'][$index][$innerIndex]['badge_type'];
        $this->promoCode = $this->finalData['allVisitors'][$index][$innerIndex]['pcode_used'];
        $this->promoCodeDiscount = $this->finalData['allVisitors'][$index][$innerIndex]['discount'];
        $this->discountType = $this->finalData['allVisitors'][$index][$innerIndex]['discount_type'];
        $this->type = $this->finalData['allVisitors'][$index][$innerIndex]['visitorType'];


        if ($this->discountType == "fixed") {
            $this->promoCodeSuccess = "Fixed rate applied";
        } else {
            if ($this->promoCode != null) {
                $this->promoCodeSuccess = $this->promoCodeDiscount;
            }
        }

        $this->showVisitorModal = true;
    }

    public function closeEditVisitorModal()
    {
        $this->showVisitorModal = false;
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
        $this->visitorIndex = null;
        $this->visitorInnerIndex = null;
        $this->mainVisitorId = null;
        $this->visitorId = null;
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
    }


    public function updateCompanyDetails()
    {
        $this->validate([
            'visitorPassType' => 'required',
            'companyName' => 'required',
            'companySector' => 'required',
            'companyAddress' => 'required',
            'companyCountry' => 'required',
            'companyCity' => 'required',
            'companyMobileNumber' => 'required',
        ]);

        if ($this->finalData['rate_type'] == "standard" || $this->finalData['rate_type'] == "Standard") {
            if ($this->visitorPassType == "fullMember") {
                $this->rateTypeString = "Full member standard rate";
            } else if ($this->visitorPassType == "member") {
                $this->rateTypeString = "Member standard rate";
            } else {
                $this->rateTypeString = "Non-Member standard rate";
            }
        } else {
            if ($this->visitorPassType == "fullMember") {
                $this->rateTypeString = "Full member early bird rate";
            } else if ($this->visitorPassType == "member") {
                $this->rateTypeString = "Member early bird rate";
            } else {
                $this->rateTypeString = "Non-Member early bird rate";
            }
        }

        if (trim($this->alternativeCompanyName) == ""  || $this->alternativeCompanyName == null) {
            $this->alternativeCompanyName = null;
        }

        MainVisitors::find($this->finalData['mainVisitorId'])->fill([
            'pass_type' => $this->visitorPassType,
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
        $this->finalData['pass_type'] = $this->visitorPassType;
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

        $this->visitorPassType = $this->finalData['pass_type'];
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
        $this->visitorPassType = null;
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


    public function openMarkAsPaidModal()
    {
        $this->showMarkAsPaidModal = true;
    }

    public function closeMarkAsPaidModal()
    {
        $this->showMarkAsPaidModal = false;
        $this->mapPaymentMethod = null;
    }

    public function markAsPaidConfirmation()
    {
        $this->validate([
            'mapPaymentMethod' => 'required',
        ], [
            'mapPaymentMethod.required' => "Payment method is required",
        ]);

        $this->dispatchBrowserEvent('swal:mark-as-paid-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure you want to mark this as paid?',
            'text' => "",
        ]);
    }



    public function markAsPaid()
    {
        if ($this->finalData['invoiceData']['total_amount'] == 0) {
            $paymentStatus = "free";
        } else {
            $paymentStatus = "paid";
        }

        MainVisitors::find($this->finalData['mainVisitorId'])->fill([
            'registration_status' => "confirmed",
            'payment_status' => $paymentStatus,
            'mode_of_payment' => $this->mapPaymentMethod,
            'paid_date_time' => Carbon::now(),

            'registration_confirmation_sent_count' => $this->finalData['registration_confirmation_sent_count'] + 1,
            'registration_confirmation_sent_datetime' => Carbon::now(),
        ])->save();

        $eventFormattedData = Carbon::parse($this->event->event_start_date)->format('d') . '-' . Carbon::parse($this->event->event_end_date)->format('d M Y');
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->finalData['mainVisitorId'];

        $assistantDetails1 = [];
        $assistantDetails2 = [];

        foreach ($this->finalData['allVisitors'] as $visitorsIndex => $visitors) {
            foreach ($visitors as $innerVisitor) {
                if (end($visitors) == $innerVisitor) {
                    if (!$innerVisitor['visitor_cancelled']) {

                        $amountPaid = $this->finalData['invoiceData']['unit_price'];

                        $promoCode = PromoCodes::where('event_id', $this->event->id)->where('promo_code', $innerVisitor['pcode_used'])->first();

                        if ($promoCode != null) {
                            if ($promoCode->discount_type == "percentage") {
                                $amountPaid = $this->finalData['invoiceData']['unit_price'] - ($this->finalData['invoiceData']['unit_price'] * ($promoCode->discount / 100));
                            } else if ($promoCode->discount_type == "price") {
                                $amountPaid = $this->finalData['invoiceData']['unit_price'] - $promoCode->discount;
                            } else {
                                $amountPaid = $promoCode->new_rate;
                            }
                        }

                        if ($this->finalData['alternative_company_name'] == null) {
                            $finalCompanyName = $this->finalData['company_name'];
                        } else {
                            $finalCompanyName = $this->finalData['alternative_company_name'];
                        }


                        $details1 = [
                            'name' => $innerVisitor['name'],
                            'eventLink' => $this->event->link,
                            'eventName' => $this->event->name,
                            'eventDates' => $eventFormattedData,
                            'eventLocation' => $this->event->location,
                            'eventCategory' => $this->event->category,
                            'eventYear' => $this->event->year,

                            'jobTitle' => $innerVisitor['job_title'],
                            'companyName' => $finalCompanyName,
                            'amountPaid' => $amountPaid,
                            'transactionId' => $innerVisitor['transactionId'],
                            'invoiceLink' => $invoiceLink,
                            'badgeLink' => env('APP_URL') . "/" . $this->event->category . "/" . $this->event->id . "/view-badge" . "/" . $innerVisitor['visitorType'] . "/" . $innerVisitor['visitorId'],
                        ];

                        $details2 = [
                            'name' => $innerVisitor['name'],
                            'eventLink' => $this->event->link,
                            'eventName' => $this->event->name,
                            'eventCategory' => $this->event->category,
                            'eventYear' => $this->event->year,

                            'invoiceAmount' => $this->finalData['invoiceData']['total_amount'],
                            'amountPaid' => $this->finalData['invoiceData']['total_amount'],
                            'balance' => 0,
                            'invoiceLink' => $invoiceLink,
                        ];

                        if ($visitorsIndex == 0) {
                            $assistantDetails1 = $details1;
                            $assistantDetails2 = $details2;
                        }

                        try {
                            Mail::to($innerVisitor['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaid($details1, $this->sendInvoice));
                        } catch (\Exception $e) {
                            Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaid($details1, $this->sendInvoice));
                        }
                        if ($this->sendInvoice) {
                            if ($visitorsIndex == 0) {
                                try {
                                    Mail::to($innerVisitor['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
                                } catch (\Exception $e) {
                                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
                                }
                            }
                        }
                    }
                }
            }
        }

        $assistantDetails1['amountPaid'] = $this->finalData['invoiceData']['total_amount'];

        if ($this->finalData['assistant_email_address'] != null) {
            try {
                Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationPaid($assistantDetails1, $this->sendInvoice));
            } catch (\Exception $e) {
                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaid($assistantDetails1, $this->sendInvoice));
            }
            if ($this->sendInvoice) {
                try {
                    Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationPaymentConfirmation($assistantDetails2, $this->sendInvoice));
                } catch (\Exception $e) {
                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentConfirmation($assistantDetails2, $this->sendInvoice));
                }
            }
        }

        $this->finalData['registration_status'] = "confirmed";
        $this->finalData['payment_status'] = $paymentStatus;
        $this->finalData['mode_of_payment'] = $this->mapPaymentMethod;
        $this->finalData['paid_date_time'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

        $this->finalData['registration_confirmation_sent_count'] = $this->finalData['registration_confirmation_sent_count'] + 1;
        $this->finalData['registration_confirmation_sent_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

        $this->showMarkAsPaidModal = false;
        $this->mapPaymentMethod = null;
        $this->dispatchBrowserEvent('swal:mark-as-paid-success', [
            'type' => 'success',
            'message' => 'Marked paid successfully!',
            'text' => "",
        ]);
    }

    public function checkUnitPrice()
    {
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


    public function calculateTotal()
    {
        $invoiceDetails = array();
        $countFinalQuantity = 0;

        $mainVisitor = MainVisitors::where('id', $this->finalData['mainVisitorId'])->where('event_id', $this->eventId)->first();

        $addMainVisitor = true;
        if ($mainVisitor->visitor_cancelled) {
            if ($mainVisitor->visitor_refunded || $mainVisitor->visitor_replaced) {
                $addMainVisitor = false;
            }
        }

        if ($mainVisitor->visitor_replaced_by_id == null & (!$mainVisitor->visitor_refunded)) {
            $countFinalQuantity++;
        }

        if ($addMainVisitor) {
            $promoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('promo_code', $mainVisitor->pcode_used)->first();

            if ($promoCode != null) {
                if ($promoCode->badge_type == $mainVisitor->badge_type) {
                    $promoCodeUsed = $mainVisitor->pcode_used;
                    $mainDiscount = $promoCode->discount;
                    $mainDiscountType = $promoCode->discount_type;
                } else {
                    $promoCodeAdditionalBadgeType = PromoCodeAddtionalBadgeTypes::where('event_id', $this->eventId)->where('promo_code_id', $promoCode->id)->where('badge_type', $mainVisitor->badge_type)->first();

                    if ($promoCodeAdditionalBadgeType != null) {
                        $promoCodeUsed = $mainVisitor->pcode_used;
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
                        $visitorDescription = "Visitor Registration Fee - Complimentary";
                    } else if ($mainDiscount > 0 && $mainDiscount < 100) {
                        $visitorDescription = "Visitor Registration Fee - " . $mainVisitor->rate_type_string . " (" . $mainDiscount . "% discount)";
                    } else {
                        $visitorDescription = "Visitor Registration Fee - {$mainVisitor->rate_type_string}";
                    }

                    $tempTotalUnitPrice = $this->checkUnitPrice();
                    $tempTotalDiscount = $this->checkUnitPrice() * ($mainDiscount / 100);
                    $tempTotalNetAmount = $this->checkUnitPrice() - ($this->checkUnitPrice() * ($mainDiscount / 100));
                } else if ($mainDiscountType == "price") {
                    $tempTotalUnitPrice = $this->checkUnitPrice();
                    $tempTotalDiscount = $mainDiscount;
                    $tempTotalNetAmount = $this->checkUnitPrice() - $mainDiscount;
                    $visitorDescription = "Visitor Registration Fee - {$mainVisitor->rate_type_string}";
                } else {
                    $tempTotalUnitPrice = $promoCode->new_rate;
                    $tempTotalDiscount = 0;
                    $tempTotalNetAmount = $promoCode->new_rate;
                    $visitorDescription = $promoCode->new_rate_description;
                }
            } else {
                $tempTotalUnitPrice = $this->checkUnitPrice();
                $tempTotalDiscount = 0;
                $tempTotalNetAmount = $this->checkUnitPrice();
                $visitorDescription = "Visitor Registration Fee - {$mainVisitor->rate_type_string}";
            }

            array_push($invoiceDetails, [
                'visitorDescription' => $visitorDescription,
                'visitorNames' => [
                    $mainVisitor->first_name . " " . $mainVisitor->middle_name . " " . $mainVisitor->last_name,
                ],
                'badgeType' => $mainVisitor->badge_type,
                'quantity' => 1,
                'totalUnitPrice' => $tempTotalUnitPrice,
                'totalDiscount' => $tempTotalDiscount,
                'totalNetAmount' =>  $tempTotalNetAmount,
                'promoCodeDiscount' => $mainDiscount,
                'promoCodeUsed' => $promoCodeUsed,
            ]);
        }


        $subVisitors = AdditionalVisitors::where('main_visitor_id', $this->finalData['mainVisitorId'])->get();
        if (!$subVisitors->isEmpty()) {
            foreach ($subVisitors as $subVisitor) {

                if ($subVisitor->visitor_replaced_by_id == null & (!$subVisitor->visitor_refunded)) {
                    $countFinalQuantity++;
                }

                $addSubVisitor = true;
                if ($subVisitor->visitor_cancelled) {
                    if ($subVisitor->visitor_refunded || $subVisitor->visitor_replaced) {
                        $addSubVisitor = false;
                    }
                }


                if ($addSubVisitor) {
                    $subPromoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('promo_code', $subVisitor->pcode_used)->first();

                    $checkIfExisting = false;
                    $existingIndex = 0;


                    if ($subPromoCode != null) {
                        if ($subPromoCode->badge_type == $subVisitor->badge_type) {
                            $subPromoCodeUsed = $subVisitor->pcode_used;
                            $subDiscount = $subPromoCode->discount;
                            $subDiscountType = $subPromoCode->discount_type;
                        } else {
                            $subPromoCodeAdditionalBadgeType = PromoCodeAddtionalBadgeTypes::where('event_id', $this->eventId)->where('promo_code_id', $subPromoCode->id)->where('badge_type', $subVisitor->badge_type)->first();

                            if ($subPromoCodeAdditionalBadgeType != null) {
                                $subPromoCodeUsed = $subVisitor->pcode_used;
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
                        if ($subVisitor->badge_type == $invoiceDetails[$j]['badgeType'] && $subPromoCodeUsed == $invoiceDetails[$j]['promoCodeUsed']) {
                            $existingIndex = $j;
                            $checkIfExisting = true;
                            break;
                        }
                    }

                    if ($checkIfExisting) {
                        array_push(
                            $invoiceDetails[$existingIndex]['visitorNames'],
                            $subVisitor->first_name . " " . $subVisitor->middle_name . " " . $subVisitor->last_name
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
                                    $subVisitorDescription = "Visitor Registration Fee - Complimentary";
                                } else if ($subDiscount > 0 && $subDiscount < 100) {
                                    $subVisitorDescription = "Visitor Registration Fee - " . $mainVisitor->rate_type_string . " (" . $subDiscount . "% discount)";
                                } else {
                                    $subVisitorDescription = "Visitor Registration Fee - {$mainVisitor->rate_type_string}";
                                }

                                $tempSubTotalUnitPrice = $this->checkUnitPrice();
                                $tempSubTotalDiscount = $this->checkUnitPrice() * ($subDiscount / 100);
                                $tempSubTotalNetAmount = $this->checkUnitPrice() - ($this->checkUnitPrice() * ($subDiscount / 100));
                            } else if ($subDiscountType == "price") {
                                $subVisitorDescription = "Visitor Registration Fee - {$mainVisitor->rate_type_string}";

                                $tempSubTotalUnitPrice = $this->checkUnitPrice();
                                $tempSubTotalDiscount = $subDiscount;
                                $tempSubTotalNetAmount = $this->checkUnitPrice() - $subDiscount;
                            } else {
                                $subVisitorDescription = $subPromoCode->new_rate_description;

                                $tempSubTotalUnitPrice = $subPromoCode->new_rate;
                                $tempSubTotalDiscount = 0;
                                $tempSubTotalNetAmount = $subPromoCode->new_rate;
                            }
                        } else {
                            $subVisitorDescription = "Visitor Registration Fee - {$mainVisitor->rate_type_string}";
                            $tempSubTotalUnitPrice = $this->checkUnitPrice();
                            $tempSubTotalDiscount = 0;
                            $tempSubTotalNetAmount = $this->checkUnitPrice();
                        }

                        array_push($invoiceDetails, [
                            'visitorDescription' => $subVisitorDescription,
                            'visitorNames' => [
                                $subVisitor->first_name . " " . $subVisitor->middle_name . " " . $subVisitor->last_name,
                            ],
                            'badgeType' => $subVisitor->badge_type,
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

        MainVisitors::find($this->finalData['mainVisitorId'])->fill([
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
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->finalData['mainVisitorId'];

        if ($this->event->eb_end_date == null) {
            $earlyBirdValidityDate = Carbon::createFromFormat('Y-m-d', $this->event->std_start_date)->subDay();
        } else {
            $earlyBirdValidityDate = Carbon::createFromFormat('Y-m-d', $this->event->eb_end_date);
        }

        MainVisitors::find($this->finalData['mainVisitorId'])->fill([
            'registration_confirmation_sent_count' => $this->finalData['registration_confirmation_sent_count'] + 1,
            'registration_confirmation_sent_datetime' => Carbon::now(),
        ])->save();

        $assistantDetails1 = [];
        $assistantDetails2 = [];

        foreach ($this->finalData['allVisitors'] as $visitorsIndex => $visitors) {
            foreach ($visitors as $innerVisitor) {
                if (end($visitors) == $innerVisitor) {
                    if (!$innerVisitor['visitor_cancelled']) {

                        $amountPaid = $this->finalData['invoiceData']['unit_price'];

                        $promoCode = PromoCodes::where('event_id', $this->event->id)->where('promo_code', $innerVisitor['pcode_used'])->first();

                        if ($promoCode != null) {
                            if ($promoCode->discount_type == "percentage") {
                                $amountPaid = $this->finalData['invoiceData']['unit_price'] - ($this->finalData['invoiceData']['unit_price'] * ($promoCode->discount / 100));
                            } else if ($promoCode->discount_type == "price") {
                                $amountPaid = $this->finalData['invoiceData']['unit_price'] - $promoCode->discount;
                            } else {
                                $amountPaid = $promoCode->new_rate;
                            }
                        }

                        if ($this->finalData['alternative_company_name'] == null) {
                            $finalCompanyName = $this->finalData['company_name'];
                        } else {
                            $finalCompanyName = $this->finalData['alternative_company_name'];
                        }

                        $details1 = [
                            'name' => $innerVisitor['name'],
                            'eventLink' => $this->event->link,
                            'eventName' => $this->event->name,
                            'eventDates' => $eventFormattedData,
                            'eventLocation' => $this->event->location,
                            'eventCategory' => $this->event->category,
                            'eventYear' => $this->event->year,

                            'jobTitle' => $innerVisitor['job_title'],
                            'companyName' => $finalCompanyName,
                            'amountPaid' => $amountPaid,
                            'transactionId' => $innerVisitor['transactionId'],
                            'invoiceLink' => $invoiceLink,
                            'earlyBirdValidityDate' => $earlyBirdValidityDate->format('jS F'),
                            'badgeLink' => env('APP_URL') . "/" . $this->event->category . "/" . $this->event->id . "/view-badge" . "/" . $innerVisitor['visitorType'] . "/" . $innerVisitor['visitorId'],
                        ];

                        $details2 = [
                            'name' => $innerVisitor['name'],
                            'eventLink' => $this->event->link,
                            'eventName' => $this->event->name,
                            'eventCategory' => $this->event->category,
                            'eventYear' => $this->event->year,

                            'invoiceAmount' => $this->finalData['invoiceData']['total_amount'],
                            'amountPaid' => $this->finalData['invoiceData']['total_amount'],
                            'balance' => 0,
                            'invoiceLink' => $invoiceLink,
                        ];


                        if ($visitorsIndex == 0) {
                            $assistantDetails1 = $details1;
                            $assistantDetails2 = $details2;
                        }

                        if ($this->finalData['payment_status'] == "unpaid") {
                            if ($amountPaid == 0) {
                                try {
                                    Mail::to($innerVisitor['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationFree($details1, $this->sendInvoice));
                                } catch (\Exception $e) {
                                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($details1, $this->sendInvoice));
                                }
                            } else {
                                try {
                                    Mail::to($innerVisitor['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationUnpaid($details1, $this->sendInvoice));
                                } catch (\Exception $e) {
                                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationUnpaid($details1, $this->sendInvoice));
                                }
                            }
                        } else if ($this->finalData['payment_status'] == "free" && $this->finalData['registration_status'] == "pending") {
                            try {
                                Mail::to($innerVisitor['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationFree($details1, $this->sendInvoice));
                            } catch (\Exception $e) {
                                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($details1, $this->sendInvoice));
                            }
                        } else {
                            try {
                                Mail::to($innerVisitor['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaid($details1, $this->sendInvoice));
                            } catch (\Exception $e) {
                                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaid($details1, $this->sendInvoice));
                            }
                            if ($this->sendInvoice) {
                                if ($visitorsIndex == 0) {
                                    try {
                                        Mail::to($innerVisitor['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
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
                if ($this->sendInvoice) {
                    try {
                        Mail::to($this->finalData['assistant_email_address'])->send(new RegistrationPaymentConfirmation($assistantDetails2, $this->sendInvoice));
                    } catch (\Exception $e) {
                        Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentConfirmation($assistantDetails2, $this->sendInvoice));
                    }
                }
            }
        }


        $this->finalData['registration_confirmation_sent_count'] = $this->finalData['registration_confirmation_sent_count'] + 1;
        $this->finalData['registration_confirmation_sent_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

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

        foreach ($this->finalData['allVisitors'] as $visitors) {
            foreach ($visitors as $innerVisitor) {
                if (end($visitors) == $innerVisitor) {
                    if (!$innerVisitor['visitor_cancelled']) {
                        $details = [
                            'name' => $innerVisitor['name'],
                            'eventName' => $this->event->name,
                            'eventCategory' => $this->event->category,
                            'eventLink' => $this->event->link,
                            'invoiceLink' => $invoiceLink,
                            'eventYear' => $this->event->year,
                        ];
                        try {
                            Mail::to($innerVisitor['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaymentReminder($details, $this->sendInvoice));
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
        $allVisitors = VisitorTransactions::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->get();

        $countMainVisitor = 0;
        $countSubVisitor = 0;

        if (!$allVisitors->isEmpty()) {
            foreach ($allVisitors as $visitor) {
                if ($visitor->visitor_type == "main") {
                    $mainVisitor = MainVisitors::where('id', $visitor->visitor_id)->where('email_address', $emailAddress)->where('registration_status', '!=', 'droppedOut')->where('visitor_cancelled', '!=', true)->first();
                    if ($mainVisitor != null) {
                        $countMainVisitor++;
                    }
                } else {
                    $subVisitor = AdditionalVisitors::where('id', $visitor->visitor_id)->where('email_address', $emailAddress)->where('visitor_cancelled', '!=', true)->first();
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
        MainVisitors::find($this->finalData['mainVisitorId'])->fill([
            'transaction_remarks' => $this->transactionRemarks,
        ])->save();

        $this->finalData['transaction_remarks'] = $this->transactionRemarks;
        $this->transactionRemarks = null;
        $this->showTransactionRemarksModal = false;
    }

    public function openVisitorCancellationModal($index, $innerIndex)
    {
        $this->replaceVisitorIndex = $index;
        $this->replaceVisitorInnerIndex = $innerIndex;
        $this->showVisitorCancellationModal = true;
    }

    public function closeVisitorCancellationModal()
    {
        $this->removeReplaceData();
        $this->showVisitorCancellationModal = false;
    }

    public function nextVisitorCancellation()
    {
        $this->visitorCancellationStep++;
    }

    public function prevVisitorCancellation()
    {
        $this->visitorCancellationStep--;
    }

    public function submitVisitorCancellation()
    {
        if ($this->visitorCancellationStep == 2) {
            if ($this->replaceVisitor == "No") {
                $this->validate(
                    [
                        'visitorRefund' => 'required',
                    ],
                    [
                        'visitorRefund.required' => "This needs to be fill up.",
                    ],
                );

                if ($this->visitorRefund == "Yes") {
                    $message = "Are you sure want to cancel and refund this visitor?";
                } else {
                    $message = "Are you sure want to cancel and not refund this visitor?";
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
                    ]
                );

                if ($this->checkEmailIfExistsInDatabase($this->replaceEmailAddress)) {
                    $this->replaceEmailAlreadyUsedError = "Email is already registered, please use another email!";
                } else {
                    $this->replaceEmailAlreadyUsedError = null;
                    $this->dispatchBrowserEvent('swal:delegate-cancel-replace-confirmation', [
                        'type' => 'warning',
                        'message' => 'Are you sure you want to cancel and replace this visitor?',
                        'text' => "",
                    ]);
                }
            }
        }
    }

    public function cancelOrRefundVisitor()
    {
        if ($this->visitorRefund == "Yes") {
            // refunded
            if ($this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitorType'] == "main") {
                MainVisitors::find($this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['mainVisitorId'])->fill([
                    'visitor_cancelled' => true,
                    'visitor_refunded' => true,
                    'visitor_cancelled_datetime' => Carbon::now(),
                    'visitor_refunded_datetime' => Carbon::now(),
                ])->save();
            } else {
                AdditionalVisitors::find($this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitorId'])->fill([
                    'visitor_cancelled' => true,
                    'visitor_refunded' => true,
                    'visitor_cancelled_datetime' => Carbon::now(),
                    'visitor_refunded_datetime' => Carbon::now(),
                ])->save();
            }

            $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_cancelled'] = true;
            $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_refunded'] = true;
            $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
            $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_refunded_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

            if ($this->finalData['finalQuantity'] == 1) {
                MainVisitors::find($this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['mainVisitorId'])->fill([
                    'registration_status' => "cancelled",
                    'payment_status' => "refunded",
                ])->save();

                $this->finalData['registration_status'] = 'cancelled';
                $this->finalData['payment_status'] = 'refunded';
                $this->finalData['finalQuantity'] = 0;
            }

            $this->dispatchBrowserEvent('swal:delegate-cancel-refund-success', [
                'type' => 'success',
                'message' => 'Visitor cancelled and refunded succesfully!',
                'text' => "",
            ]);
        } else {
            // not refunded
            if ($this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitorType'] == "main") {
                MainVisitors::find($this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['mainVisitorId'])->fill([
                    'visitor_cancelled' => true,
                    'visitor_cancelled_datetime' => Carbon::now(),
                ])->save();
            } else {
                AdditionalVisitors::find($this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitorId'])->fill([
                    'visitor_cancelled' => true,
                    'visitor_cancelled_datetime' => Carbon::now(),
                ])->save();
            }

            $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_cancelled'] = true;
            $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

            if ($this->finalData['finalQuantity'] == 1) {
                MainVisitors::find($this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['mainVisitorId'])->fill([
                    'registration_status' => "cancelled",
                ])->save();

                $this->finalData['registration_status'] = 'cancelled';
            }

            $this->dispatchBrowserEvent('swal:delegate-cancel-refund-success', [
                'type' => 'success',
                'message' => 'Visitor cancelled but not refunded succesfully!',
                'text' => "",
            ]);
        }
        $this->showVisitorCancellationModal = false;
    }



    public function addReplaceVisitor()
    {
        if ($this->replacePromoCodeSuccess != null) {
            PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('active', true)->where('promo_code', $this->replacePromoCode)->increment('total_usage');

            $subPromoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('promo_code', $this->replacePromoCode)->where('badge_type', $this->replaceBadgeType)->first();

            if ($subPromoCode != null) {
                $subChecker = false;

                if($subPromoCode->badge_type == $this->replaceBadgeType){
                    $subChecker = true;
                } else {
                    $promoCodeAdditionalBadgeType = PromoCodeAddtionalBadgeTypes::where('event_id', $this->eventId)->where('promo_code_id', $subPromoCode->id)->where('badge_type', $this->replaceBadgeType)->first();

                    if($promoCodeAdditionalBadgeType != null){
                        $subChecker = true;
                    } else {
                        $subChecker = false;
                    }
                }

                if($subChecker){
                    $subDiscountType = $subPromoCode->discount_type;

                    if($subPromoCode->discount_type == 'percentage'){
                        $subDiscount = $subPromoCode->discount;
                    } else if($subPromoCode->discount_type == 'price'){
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


        $replacedVisitor = AdditionalVisitors::create([
            'main_visitor_id' => $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['mainVisitorId'],
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

            'visitor_replaced_type' => $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_replaced_type'],
            'visitor_replaced_from_id' => $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitorId'],
            'visitor_original_from_id' => $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_original_from_id'],
        ]);


        $transaction = VisitorTransactions::create([
            'event_id' => $this->eventId,
            'event_category' => $this->eventCategory,
            'visitor_id' => $replacedVisitor->id,
            'visitor_type' => "sub",
        ]);

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }
        $lastDigit = 1000 + intval($transaction->id);
        $finalTransactionId = $this->event->year . $eventCode . $lastDigit;

        array_push($this->finalData['allVisitors'][$this->replaceVisitorIndex], [
            'transactionId' => $finalTransactionId,
            'mainVisitorId' => $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['mainVisitorId'],
            'visitorId' => $replacedVisitor->id,
            'visitorType' => "sub",

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

            'is_replacement' => true,
            'visitor_cancelled' => false,
            'visitor_replaced' => false,
            'visitor_refunded' => false,

            'visitor_replaced_type' => "sub",
            'visitor_original_from_id' => $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_original_from_id'],
            'visitor_replaced_from_id' => $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitorId'],
            'visitor_replaced_by_id' => null,

            'visitor_cancelled_datetime' => null,
            'visitor_refunded_datetime' => null,
            'visitor_replaced_datetime' => null,
        ]);
        if ($this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitorType'] == "main") {
            MainVisitors::find($this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['mainVisitorId'])->fill([
                'visitor_cancelled' => true,
                'visitor_cancelled_datetime' => Carbon::now(),
                'visitor_replaced' => true,
                'visitor_replaced_by_id' => $replacedVisitor->id,
                'visitor_replaced_datetime' => Carbon::now(),
            ])->save();
        } else {
            AdditionalVisitors::find($this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitorId'])->fill([
                'visitor_cancelled' => true,
                'visitor_cancelled_datetime' => Carbon::now(),
                'visitor_replaced' => true,
                'visitor_replaced_by_id' => $replacedVisitor->id,
                'visitor_replaced_datetime' => Carbon::now(),
            ])->save();
        }


        MainVisitors::where('id', $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['mainVisitorId'])->increment('quantity');

        $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_cancelled'] = true;
        $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_replaced'] = true;
        $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
        $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['visitor_replaced_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

        $this->dispatchBrowserEvent('swal:delegate-cancel-replace-success', [
            'type' => 'success',
            'message' => 'Visitor replaced succesfully!',
            'text' => "",
        ]);
        $this->calculateTotal();
        $this->removeReplaceData();
        $this->showVisitorCancellationModal = false;
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
        $this->visitorCancellationStep = 1;
        $this->replaceVisitorIndex = null;
        $this->replaceVisitorInnerIndex = null;

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
    }
}
