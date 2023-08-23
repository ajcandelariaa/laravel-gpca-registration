<?php

namespace App\Http\Livewire;

use App\Mail\RegistrationFree;
use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Mail\RegistrationPaymentReminder;
use App\Mail\RegistrationUnpaid;
use Livewire\Component;
use App\Models\Member as Members;
use App\Models\Event as Events;
use App\Models\RccAwardsMainParticipant as RccAwardsMainParticipants;
use App\Models\RccAwardsAdditionalParticipant as RccAwardsAdditionalParticipants;
use App\Models\RccAwardsDocument as RccAwardsDocuments;
use App\Models\RccAwardsParticipantTransaction as RccAwardsParticipantTransactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\WithFileUploads;
use NumberFormatter;

class RccAwardsRegistrantDetails extends Component
{
    use WithFileUploads;

    public $countries, $salutations, $awardsCategories;

    public $eventCategory, $eventId, $registrantId, $finalData, $members, $event;

    public $rateType, $finalUnitPrice;

    // ADDITIONAL DETAILS
    public $participantPassType, $rateTypeString, $companyName, $category, $subCategory, $entryForm, $supportingDocuments = [];

    // DELEGATE DETAILS
    public $mainParticipantId, $participantId, $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $address, $country, $city, $jobTitle, $type, $participantIndex, $participantInnerIndex;

    public $transactionRemarks, $participantCancellationStep = 1, $replaceParticipant, $participantRefund;

    public $replaceParticipantIndex, $replaceParticipantInnerIndex, $replaceSalutation, $replaceFirstName, $replaceMiddleName, $replaceLastName, $replaceEmailAddress, $replaceMobileNumber, $replaceAddress, $replaceCountry, $replaceCity, $replaceJobTitle, $replaceEmailAlreadyUsedError;

    public $mapPaymentMethod, $sendInvoice;

    // MODALS
    public $showParticipantModal = false, $showAdditionalDetailsModal = false;
    public $showTransactionRemarksModal = false;
    public $showParticipantCancellationModal = false;
    public $showMarkAsPaidModal = false;
    
    public $ccEmailNotif;

    protected $listeners = ['paymentReminderConfirmed' => 'sendEmailReminder', 'sendEmailRegistrationConfirmationConfirmed' => 'sendEmailRegistrationConfirmation', 'cancelRefundDelegateConfirmed' => 'cancelOrRefundParticipant', 'cancelReplaceDelegateConfirmed' => 'addReplaceParticipant', 'markAsPaidConfirmed' => 'markAsPaid'];

    public function mount($eventCategory, $eventId, $registrantId, $finalData)
    {
        $this->countries = config('app.countries');
        $this->salutations = config('app.salutations');
        $this->awardsCategories = config('app.rccAwardsCategories');
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
        return view('livewire.admin.events.transactions.rcca.rcc-awards-registrant-details');
    }



    public function updateParticipant()
    {
        $this->validate(
            [
                'firstName' => 'required',
                'lastName' => 'required',
                'emailAddress' => 'required|email',
                'mobileNumber' => 'required',
                'address' => 'required',
                'country' => 'required',
                'city' => 'required',
                'jobTitle' => 'required',
            ],
            [
                'firstName.required' => "First name is required",
                'lastName.required' => "Last name is required",
                'emailAddress.required' => "Email address is required",
                'emailAddress.email' => "Email address must be a valid email",
                'mobileNumber.required' => "Mobile number is required",
                'address.required' => "Address is required",
                'country.required' => "Country is required",
                'city.required' => "City is required",
                'jobTitle.required' => "Job title is required",
            ]
        );

        if ($this->type == "main") {
            RccAwardsMainParticipants::find($this->participantId)->fill([
                'salutation' => $this->salutation,
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName,
                'last_name' => $this->lastName,
                'email_address' => $this->emailAddress,
                'mobile_number' => $this->mobileNumber,
                'address' => $this->address,
                'country' => $this->country,
                'city' => $this->city,
                'job_title' => $this->jobTitle,
            ])->save();
        } else {
            RccAwardsAdditionalParticipants::find($this->participantId)->fill([
                'salutation' => $this->salutation,
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName,
                'last_name' => $this->lastName,
                'email_address' => $this->emailAddress,
                'mobile_number' => $this->mobileNumber,
                'address' => $this->nationality,
                'country' => $this->country,
                'city' => $this->city,
                'job_title' => $this->jobTitle,
            ])->save();
        }

        $this->finalData['allParticipants'][$this->participantIndex][$this->participantInnerIndex]['salutation'] = $this->salutation;
        $this->finalData['allParticipants'][$this->participantIndex][$this->participantInnerIndex]['first_name'] = $this->firstName;
        $this->finalData['allParticipants'][$this->participantIndex][$this->participantInnerIndex]['middle_name'] = $this->salutation;
        $this->finalData['allParticipants'][$this->participantIndex][$this->participantInnerIndex]['last_name'] = $this->lastName;
        $this->finalData['allParticipants'][$this->participantIndex][$this->participantInnerIndex]['name'] = $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName;
        $this->finalData['allParticipants'][$this->participantIndex][$this->participantInnerIndex]['email_address'] = $this->emailAddress;
        $this->finalData['allParticipants'][$this->participantIndex][$this->participantInnerIndex]['mobile_number'] = $this->mobileNumber;
        $this->finalData['allParticipants'][$this->participantIndex][$this->participantInnerIndex]['address'] = $this->address;
        $this->finalData['allParticipants'][$this->participantIndex][$this->participantInnerIndex]['country'] = $this->country;
        $this->finalData['allParticipants'][$this->participantIndex][$this->participantInnerIndex]['city'] = $this->city;
        $this->finalData['allParticipants'][$this->participantIndex][$this->participantInnerIndex]['job_title'] = $this->jobTitle;

        $this->showParticipantModal = false;
        $this->resetEditModalFields();
    }


    public function openEditParticipantModal($index, $innerIndex)
    {
        $this->participantIndex = $index;
        $this->participantInnerIndex = $innerIndex;
        $this->mainParticipantId = $this->finalData['allParticipants'][$index][$innerIndex]['mainParticipantId'];
        $this->participantId = $this->finalData['allParticipants'][$index][$innerIndex]['participantId'];
        $this->salutation = $this->finalData['allParticipants'][$index][$innerIndex]['salutation'];
        $this->firstName = $this->finalData['allParticipants'][$index][$innerIndex]['first_name'];
        $this->middleName = $this->finalData['allParticipants'][$index][$innerIndex]['middle_name'];
        $this->lastName = $this->finalData['allParticipants'][$index][$innerIndex]['last_name'];
        $this->emailAddress = $this->finalData['allParticipants'][$index][$innerIndex]['email_address'];
        $this->mobileNumber = $this->finalData['allParticipants'][$index][$innerIndex]['mobile_number'];
        $this->address = $this->finalData['allParticipants'][$index][$innerIndex]['address'];
        $this->country = $this->finalData['allParticipants'][$index][$innerIndex]['country'];
        $this->city = $this->finalData['allParticipants'][$index][$innerIndex]['city'];
        $this->jobTitle = $this->finalData['allParticipants'][$index][$innerIndex]['job_title'];
        $this->type = $this->finalData['allParticipants'][$index][$innerIndex]['participantType'];

        $this->showParticipantModal = true;
    }

    
    public function openEditAdditionalDetailsModal(){
        $this->members = Members::where('active', true)->get();
        $this->resetEditAdditionalDetailsFields();
        
        $this->category = $this->finalData['category'];
        $this->subCategory = $this->finalData['sub_category'];
        $this->participantPassType = $this->finalData['pass_type'];
        $this->companyName = $this->finalData['company_name'];
        $this->showAdditionalDetailsModal = true;
    }

    public function updateAdditionalDetails(){
        $this->validate(
            [
                'category' => 'required',
            ],
            [
                'category.required' => "Category is required",
            ]
        );

        if ($this->category == "Sustainable Environmental Protection" || $this->category == "Community Awareness") {
            $this->validate(
                [
                    'subCategory' => 'required',
                ],
                [
                    'subCategory.required' => "Sub category is required",
                ]
            );
        }

        $this->validate(
            [
                'companyName' => 'required',
            ],
            [
                'companyName.required' => "Company name is required",
            ]
        );

        
        if ($this->finalData['rate_type'] == "Standard") {
            if ($this->participantPassType == "fullMember") {
                $this->rateTypeString = "Full member standard rate";
            } else if ($this->participantPassType == "member") {
                $this->rateTypeString = "Member standard rate";
            } else {
                $this->rateTypeString = "Non-Member standard rate";
            }
        } else {
            if ($this->participantPassType == "fullMember") {
                $this->rateTypeString = "Full member early bird rate";
            } else if ($this->participantPassType == "member") {
                $this->rateTypeString = "Member early bird rate";
            } else {
                $this->rateTypeString = "Non-member early bird rate";
            }
        }

        if ($this->category != "Sustainable Environmental Protection" && $this->category != "Community Awareness") {
            $this->subCategory = null;
        }

        RccAwardsMainParticipants::find($this->finalData['mainParticipantId'])->fill([
            'pass_type' => $this->participantPassType,
            'rate_type_string' => $this->rateTypeString,
            'company_name' => $this->companyName,
            'category' => $this->category,
            'sub_category' => $this->subCategory,
        ])->save();


        $this->finalData['rate_type_string'] = $this->rateTypeString;
        $this->finalData['pass_type'] = $this->participantPassType;
        $this->finalData['company_name'] = $this->companyName;
        $this->finalData['category'] = $this->category;
        $this->finalData['sub_category'] = $this->subCategory;

        $this->calculateTotal();

        $this->resetEditAdditionalDetailsFields();
        $this->showAdditionalDetailsModal = false;
    }

    public function closeEditAdditionalDetailsModal()
    {
        $this->resetEditAdditionalDetailsFields();
        $this->showAdditionalDetailsModal = false;
    }

    public function resetEditAdditionalDetailsFields()
    {
        $this->category = null;
        $this->subCategory = null;
        $this->participantPassType = null;
        $this->rateTypeString = null;
        $this->companyName = null;
    }

    public function closeEditParticipantModal()
    {
        $this->showParticipantModal = false;
        $this->resetEditModalFields();
    }



    public function resetEditModalFields()
    {
        $this->participantIndex = null;
        $this->participantInnerIndex = null;
        $this->mainParticipantId = null;
        $this->participantId = null;
        $this->salutation = null;
        $this->firstName = null;
        $this->middleName = null;
        $this->lastName = null;
        $this->emailAddress = null;
        $this->mobileNumber = null;
        $this->address = null;
        $this->country = null;
        $this->city = null;
        $this->jobTitle = null;
        $this->type = null;
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

        RccAwardsMainParticipants::find($this->finalData['mainParticipantId'])->fill([
            'registration_status' => "confirmed",
            'payment_status' => $paymentStatus,
            'mode_of_payment' => $this->mapPaymentMethod,
            'paid_date_time' => Carbon::now(),

            'registration_confirmation_sent_count' => $this->finalData['registration_confirmation_sent_count'] + 1,
            'registration_confirmation_sent_datetime' => Carbon::now(),
        ])->save();

        $eventFormattedData = Carbon::parse($this->event->event_start_date)->format('j F Y');
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->finalData['mainParticipantId'];
        $downloadLink = env('APP_URL') . '/download-file/';

        foreach ($this->finalData['allParticipants'] as $participantsIndex => $participants) {
            foreach ($participants as $innerParticipant) {
                if (end($participants) == $innerParticipant) {
                    $details1 = [
                        'name' => $innerParticipant['name'],
                        'eventLink' => $this->event->link,
                        'eventName' => $this->event->name,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $this->event->location,
                        'eventCategory' => $this->event->category,
                        'eventYear' => $this->event->year,

                        'jobTitle' => $innerParticipant['job_title'],
                        'companyName' => $this->finalData['company_name'],
                        'emailAddress' => $innerParticipant['email_address'],
                        'mobileNumber' => $innerParticipant['mobile_number'],
                        'city' => $innerParticipant['city'],
                        'country' => $innerParticipant['country'],

                        'category' => $this->finalData['category'],
                        'subCategory' => ($this->finalData['sub_category'] != null) ? $this->finalData['sub_category'] : 'N/A',

                        'entryFormId' => $this->finalData['entryFormId'],
                        'supportingDocumentsDownloadId' => $this->finalData['supportingDocumentsDownloadId'],
                        'downloadLink' => $downloadLink,

                        'amountPaid' => $this->finalData['invoiceData']['total_amount'],
                        'transactionId' => $innerParticipant['transactionId'],
                        'invoiceLink' => $invoiceLink,

                        'badgeLink' => env('APP_URL')."/".$this->event->category."/".$this->event->id."/view-badge"."/".$innerParticipant['participantType']."/".$innerParticipant['participantId'],
                    ];

                    $details2 = [
                        'name' => $innerParticipant['name'],
                        'eventLink' => $this->event->link,
                        'eventName' => $this->event->name,
                        'eventCategory' => $this->event->category,
                        'eventYear' => $this->event->year,

                        'invoiceAmount' => $this->finalData['invoiceData']['total_amount'],
                        'amountPaid' => $this->finalData['invoiceData']['total_amount'],
                        'balance' => 0,
                        'invoiceLink' => $invoiceLink,
                    ];

                    Mail::to($innerParticipant['email_address'])->cc($this->ccEmailNotif)->queue(new RegistrationPaid($details1));

                    
                    if ($this->sendInvoice) {
                        if ($participantsIndex == 0) {
                            Mail::to($innerParticipant['email_address'])->cc($this->ccEmailNotif)->queue(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
                        }
                    }
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
        // CHECK UNIT PRICE
        if ($this->finalData['rate_type'] == "Standard") {
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

        $mainParticipant = RccAwardsMainParticipants::where('id', $this->finalData['mainParticipantId'])->where('event_id', $this->eventId)->first();

        $addMainParticipant = true;
        if ($mainParticipant->participant_cancelled) {
            if ($mainParticipant->participant_refunded || $mainParticipant->participant_replaced) {
                $addMainParticipant = false;
            }
        }

        if ($mainParticipant->participant_replaced_by_id == null & (!$mainParticipant->participant_refunded)) {
            $countFinalQuantity++;
        }

        if ($addMainParticipant) {
            array_push($invoiceDetails, [
                'delegateDescription' => "Participant Registration Fee",
                'delegateNames' => [
                    $mainParticipant->first_name . " " . $mainParticipant->middle_name . " " . $mainParticipant->last_name,
                ],
                'badgeType' => null,
                'quantity' => 1,
                'totalDiscount' => 0,
                'totalNetAmount' =>  $this->checkUnitPrice(),
                'promoCodeDiscount' => 0,
            ]);
        }


        $subParticipants = RccAwardsAdditionalParticipants::where('main_participant_id', $this->finalData['mainParticipantId'])->get();
        if (!$subParticipants->isEmpty()) {
            foreach ($subParticipants as $subParticipant) {

                if ($subParticipant->participant_replaced_by_id == null & (!$subParticipant->participant_refunded)) {
                    $countFinalQuantity++;
                }

                $addSubParticipant = true;
                if ($subParticipant->participant_cancelled) {
                    if ($subParticipant->participant_refunded || $subParticipant->participant_replaced) {
                        $addSubParticipant = false;
                    }
                }


                if ($addSubParticipant) {
                    if (count($invoiceDetails) == 0) {
                        array_push($invoiceDetails, [
                            'delegateDescription' => "Participant Registration Fee",
                            'delegateNames' => [
                                $subParticipant->first_name . " " . $subParticipant->middle_name . " " . $subParticipant->last_name,
                            ],
                            'badgeType' => null,
                            'quantity' => 1,
                            'totalDiscount' => 0,
                            'totalNetAmount' =>  $this->checkUnitPrice(),
                            'promoCodeDiscount' => 0,
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

        RccAwardsMainParticipants::find($this->finalData['mainParticipantId'])->fill([
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
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->finalData['mainParticipantId'];
        $downloadLink = env('APP_URL') . '/download-file/';

        RccAwardsMainParticipants::find($this->finalData['mainParticipantId'])->fill([
            'registration_confirmation_sent_count' => $this->finalData['registration_confirmation_sent_count'] + 1,
            'registration_confirmation_sent_datetime' => Carbon::now(),
        ])->save();

        foreach ($this->finalData['allParticipants'] as $participantsIndex => $participants) {
            foreach ($participants as $innerParticipant) {
                if (end($participants) == $innerParticipant) {
                    $details1 = [
                        'name' => $innerParticipant['name'],
                        'eventLink' => $this->event->link,
                        'eventName' => $this->event->name,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $this->event->location,
                        'eventCategory' => $this->event->category,
                        'eventYear' => $this->event->year,

                        'jobTitle' => $innerParticipant['job_title'],
                        'companyName' => $this->finalData['company_name'],
                        'emailAddress' => $innerParticipant['email_address'],
                        'mobileNumber' => $innerParticipant['mobile_number'],
                        'city' => $innerParticipant['city'],
                        'country' => $innerParticipant['country'],

                        'category' => $this->finalData['category'],
                        'subCategory' => ($this->finalData['sub_category'] != null) ? $this->finalData['sub_category'] : 'N/A',

                        'entryFormId' => $this->finalData['entryFormId'],
                        'supportingDocumentsDownloadId' => $this->finalData['supportingDocumentsDownloadId'],
                        'downloadLink' => $downloadLink,

                        'amountPaid' => $this->finalData['invoiceData']['total_amount'],
                        'transactionId' => $innerParticipant['transactionId'],
                        'invoiceLink' => $invoiceLink,

                        'badgeLink' => env('APP_URL')."/".$this->event->category."/".$this->event->id."/view-badge"."/".$innerParticipant['participantType']."/".$innerParticipant['participantId'],
                    ];

                    $details2 = [
                        'name' => $innerParticipant['name'],
                        'eventLink' => $this->event->link,
                        'eventName' => $this->event->name,
                        'eventCategory' => $this->event->category,
                        'eventYear' => $this->event->year,

                        'invoiceAmount' => $this->finalData['invoiceData']['total_amount'],
                        'amountPaid' => $this->finalData['invoiceData']['total_amount'],
                        'balance' => 0,
                        'invoiceLink' => $invoiceLink,
                    ];

                    if ($this->finalData['payment_status'] == "unpaid") {
                        Mail::to($innerParticipant['email_address'])->cc($this->ccEmailNotif)->queue(new RegistrationUnpaid($details1, $this->sendInvoice));
                    } else if ($this->finalData['payment_status'] == "free" && $this->finalData['registration_status'] == "pending") {
                        Mail::to($innerParticipant['email_address'])->cc($this->ccEmailNotif)->queue(new RegistrationFree($details1, $this->sendInvoice));
                    } else {
                        Mail::to($innerParticipant['email_address'])->cc($this->ccEmailNotif)->queue(new RegistrationPaid($details1, $this->sendInvoice));
                        if ($this->sendInvoice) {
                            if ($participantsIndex == 0) {
                                Mail::to($innerParticipant['email_address'])->cc($this->ccEmailNotif)->queue(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
                            }
                        }
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

        foreach ($this->finalData['allParticipants'] as $participants) {
            foreach ($participants as $innerParticipant) {
                if (end($participants) == $innerParticipant) {
                    $details = [
                        'name' => $innerParticipant['name'],
                        'eventName' => $this->event->name,
                        'eventLink' => $this->event->link,
                        'eventCategory' => $this->event->category,
                        'invoiceLink' => $invoiceLink,
                        'eventYear' => $this->event->year,
                    ];
                    Mail::to($innerParticipant['email_address'])->cc($this->ccEmailNotif)->queue(new RegistrationPaymentReminder($details));
                }
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
        $allParticipants = RccAwardsParticipantTransactions::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->get();

        $countMainParticipants = 0;
        $countSubParticipants = 0;

        if (!$allParticipants->isEmpty()) {
            foreach ($allParticipants as $participant) {
                if ($participant->participant_type == "main") {
                    $mainParticipants = RccAwardsMainParticipants::where('id', $participant->participant_id)->where('email_address', $emailAddress)->where('registration_status', '!=', 'droppedOut')->where('participant_cancelled', '!=', true)->first();
                    if ($mainParticipants != null) {
                        $countMainParticipants++;
                    }
                } else {
                    $subParticipants = RccAwardsAdditionalParticipants::where('id', $participant->participant_id)->where('email_address', $emailAddress)->where('participant_cancelled', '!=', true)->first();
                    if ($subParticipants != null) {
                        $registrationStatsMain = RccAwardsMainParticipants::where('id', $subParticipants->main_participant_id)->value('registration_status');
                        if ($registrationStatsMain != "droppedOut") {
                            $countSubParticipants++;
                        }
                    }
                }
            }
        }

        if ($countMainParticipants == 0 && $countSubParticipants == 0) {
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
        RccAwardsMainParticipants::find($this->finalData['mainParticipantId'])->fill([
            'transaction_remarks' => $this->transactionRemarks,
        ])->save();

        $this->finalData['transaction_remarks'] = $this->transactionRemarks;
        $this->transactionRemarks = null;
        $this->showTransactionRemarksModal = false;
    }

    public function openParticipantCancellationModal($index, $innerIndex)
    {
        $this->replaceParticipantIndex = $index;
        $this->replaceParticipantInnerIndex = $innerIndex;
        $this->showParticipantCancellationModal = true;
    }

    public function closeParticipantCancellationModal()
    {
        $this->removeReplaceData();
        $this->showParticipantCancellationModal = false;
    }

    public function nextParticipantCancellation()
    {
        $this->participantCancellationStep++;
    }

    public function prevParticipantCancellation()
    {
        $this->participantCancellationStep--;
    }

    public function submitParticipantCancellation()
    {
        if ($this->participantCancellationStep == 2) {
            if ($this->replaceParticipant == "No") {
                $this->validate(
                    [
                        'participantRefund' => 'required',
                    ],
                    [
                        'participantRefund.required' => "This needs to be fill up.",
                    ],
                );

                if ($this->participantRefund == "Yes") {
                    $message = "Are you sure want to cancel and refund this participant?";
                } else {
                    $message = "Are you sure want to cancel and not refund this participant?";
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
                        'replaceMobileNumber' => 'required',
                        'replaceAddress' => 'required',
                        'replaceCountry' => 'required',
                        'replaceCity' => 'required',
                        'replaceJobTitle' => 'required',
                    ],
                    [
                        'replaceFirstName.required' => "First name is required",
                        'replaceLastName.required' => "Last name is required",
                        'replaceEmailAddress.required' => "Email address is required",
                        'replaceEmailAddress.email' => "Email address must be a valid email",
                        'replaceMobileNumber.required' => "Mobile number is required",
                        'replaceAddress.required' => "Address is required",
                        'replaceCountry.required' => "Country is required",
                        'replaceCity.required' => "City is required",
                        'replaceJobTitle.required' => "Job title is required",
                    ]
                );

                if ($this->checkEmailIfExistsInDatabase($this->replaceEmailAddress)) {
                    $this->replaceEmailAlreadyUsedError = "Email is already registered, please use another email!";
                } else {
                    $this->replaceEmailAlreadyUsedError = null;
                    $this->dispatchBrowserEvent('swal:delegate-cancel-replace-confirmation', [
                        'type' => 'warning',
                        'message' => 'Are you sure you want to cancel and replace this participant?',
                        'text' => "",
                    ]);
                }
            }
        }
    }



    public function cancelOrRefundParticipant()
    {
        if ($this->participantRefund == "Yes") {
            // refunded
            if ($this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participantType'] == "main") {
                RccAwardsMainParticipants::find($this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['mainParticipantId'])->fill([
                    'participant_cancelled' => true,
                    'participant_refunded' => true,
                    'participant_cancelled_datetime' => Carbon::now(),
                    'participant_refunded_datetime' => Carbon::now(),
                ])->save();
            } else {
                RccAwardsAdditionalParticipants::find($this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participantId'])->fill([
                    'participant_cancelled' => true,
                    'participant_refunded' => true,
                    'participant_cancelled_datetime' => Carbon::now(),
                    'participant_refunded_datetime' => Carbon::now(),
                ])->save();
            }

            $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_cancelled'] = true;
            $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_refunded'] = true;
            $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
            $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_refunded_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

            if ($this->finalData['finalQuantity'] == 1) {
                RccAwardsMainParticipants::find($this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['mainParticipantId'])->fill([
                    'registration_status' => "cancelled",
                    'payment_status' => "refunded",
                ])->save();

                $this->finalData['registration_status'] = 'cancelled';
                $this->finalData['payment_status'] = 'refunded';
                $this->finalData['finalQuantity'] = 0;
            }

            $this->dispatchBrowserEvent('swal:delegate-cancel-refund-success', [
                'type' => 'success',
                'message' => 'Participant cancelled and refunded succesfully!',
                'text' => "",
            ]);
        } else {
            // not refunded
            if ($this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participantType'] == "main") {
                RccAwardsMainParticipants::find($this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['mainParticipantId'])->fill([
                    'participant_cancelled' => true,
                    'participant_cancelled_datetime' => Carbon::now(),
                ])->save();
            } else {
                RccAwardsAdditionalParticipants::find($this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participantId'])->fill([
                    'participant_cancelled' => true,
                    'participant_cancelled_datetime' => Carbon::now(),
                ])->save();
            }

            $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_cancelled'] = true;
            $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

            if ($this->finalData['finalQuantity'] == 1) {
                RccAwardsMainParticipants::find($this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['mainParticipantId'])->fill([
                    'registration_status' => "cancelled",
                ])->save();

                $this->finalData['registration_status'] = 'cancelled';
            }

            $this->dispatchBrowserEvent('swal:delegate-cancel-refund-success', [
                'type' => 'success',
                'message' => 'Participant cancelled but not refunded succesfully!',
                'text' => "",
            ]);
        }
        $this->showParticipantCancellationModal = false;
    }

    public function addReplaceParticipant()
    {
        $replacedParticipant = RccAwardsAdditionalParticipants::create([
            'main_participant_id' => $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['mainParticipantId'],
            'salutation' => $this->replaceSalutation,
            'first_name' => $this->replaceFirstName,
            'middle_name' => $this->replaceMiddleName,
            'last_name' => $this->replaceLastName,
            'email_address' => $this->replaceEmailAddress,
            'mobile_number' => $this->replaceMobileNumber,
            'address' => $this->replaceAddress,
            'country' => $this->replaceCountry,
            'city' => $this->replaceCity,
            'job_title' => $this->replaceJobTitle,

            'participant_replaced_type' => $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_replaced_type'],
            'participant_replaced_from_id' => $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participantId'],
            'participant_original_from_id' => $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_original_from_id'],
        ]);


        $transaction = RccAwardsParticipantTransactions::create([
            'event_id' => $this->eventId,
            'event_category' => $this->eventCategory,
            'participant_id' => $replacedParticipant->id,
            'participant_type' => "sub",
        ]);

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }
        $lastDigit = 1000 + intval($transaction->id);
        $finalTransactionId = $this->event->year . $eventCode . $lastDigit;

        array_push($this->finalData['allParticipants'][$this->replaceParticipantIndex], [
            'transactionId' => $finalTransactionId,
            'mainParticipantId' => $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['mainParticipantId'],
            'participantId' => $replacedParticipant->id,
            'participantType' => "sub",

            'name' => $this->replaceSalutation . " " . $this->replaceFirstName . " " . $this->replaceMiddleName . " " . $this->replaceLastName,
            'salutation' => $this->replaceSalutation,
            'first_name' => $this->replaceFirstName,
            'middle_name' => $this->replaceMiddleName,
            'last_name' => $this->replaceLastName,
            'email_address' => $this->replaceEmailAddress,
            'mobile_number' => $this->replaceMobileNumber,
            'address' => $this->replaceAddress,
            'country' => $this->replaceCountry,
            'city' => $this->replaceCity,
            'job_title' => $this->replaceJobTitle,

            'is_replacement' => true,
            'participant_cancelled' => false,
            'participant_replaced' => false,
            'participant_refunded' => false,

            'participant_replaced_type' => "sub",
            'participant_original_from_id' => $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_original_from_id'],
            'participant_replaced_from_id' => $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participantId'],
            'participant_replaced_by_id' => null,

            'participant_cancelled_datetime' => null,
            'participant_refunded_datetime' => null,
            'participant_replaced_datetime' => null,
        ]);
        if ($this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participantType'] == "main") {
            RccAwardsMainParticipants::find($this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['mainParticipantId'])->fill([
                'participant_cancelled' => true,
                'participant_cancelled_datetime' => Carbon::now(),
                'participant_replaced' => true,
                'participant_replaced_by_id' => $replacedParticipant->id,
                'participant_replaced_datetime' => Carbon::now(),
            ])->save();
        } else {
            RccAwardsAdditionalParticipants::find($this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participantId'])->fill([
                'participant_cancelled' => true,
                'participant_cancelled_datetime' => Carbon::now(),
                'participant_replaced' => true,
                'participant_replaced_by_id' => $replacedParticipant->id,
                'participant_replaced_datetime' => Carbon::now(),
            ])->save();
        }


        RccAwardsMainParticipants::where('id', $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['mainParticipantId'])->increment('quantity');

        $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_cancelled'] = true;
        $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_replaced'] = true;
        $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
        $this->finalData['allParticipants'][$this->replaceParticipantIndex][$this->replaceParticipantInnerIndex]['participant_replaced_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

        $this->dispatchBrowserEvent('swal:delegate-cancel-replace-success', [
            'type' => 'success',
            'message' => 'Participant replaced succesfully!',
            'text' => "",
        ]);
        $this->calculateTotal();
        $this->removeReplaceData();
        $this->showParticipantCancellationModal = false;
    }

    public function removeReplaceData()
    {
        $this->participantCancellationStep = 1;
        $this->replaceParticipantIndex = null;
        $this->replaceParticipantInnerIndex = null;

        $this->replaceSalutation = null;
        $this->replaceFirstName = null;
        $this->replaceMiddleName = null;
        $this->replaceLastName = null;
        $this->replaceEmailAddress = null;
        $this->replaceMobileNumber = null;
        $this->replaceAddress = null;
        $this->replaceCountry = null;
        $this->replaceCity = null;
        $this->replaceJobTitle = null;

        $this->replaceEmailAlreadyUsedError = null;
    }
}
