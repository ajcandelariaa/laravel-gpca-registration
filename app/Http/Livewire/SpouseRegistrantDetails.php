<?php

namespace App\Http\Livewire;

use App\Mail\RegistrationFree;
use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Mail\RegistrationPaymentReminder;
use App\Mail\RegistrationUnpaid;
use Livewire\Component;
use App\Models\MainSpouse as MainSpouses;
use App\Models\Event as Events;
use App\Models\AdditionalSpouse as AdditionalSpouses;
use App\Models\SpouseTransaction as SpouseTransactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use NumberFormatter;

class SpouseRegistrantDetails extends Component
{
    public $countries, $salutations;

    public $eventCategory, $eventId, $registrantId, $finalData, $event;

    public $finalUnitPrice;

    // ADDITIONAL DETAILS
    public $referenceDelegateName, $day_one = false, $day_two = false, $day_three = false, $day_four = false, $selectedDays = [];

    // DELEGATE DETAILS
    public $mainSpouseId, $spouseId, $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $nationality, $country, $city, $type, $spouseIndex, $spouseInnerIndex;

    public $transactionRemarks, $spouseCancellationStep = 1, $replaceSpouse, $spouseRefund;

    public $replaceSpouseIndex, $replaceSpouseInnerIndex, $replaceSalutation, $replaceFirstName, $replaceMiddleName, $replaceLastName, $replaceEmailAddress, $replaceMobileNumber, $replaceNationality, $replaceCountry, $replaceCity, $replaceEmailAlreadyUsedError;

    public $mapPaymentMethod, $sendInvoice;

    // MODALS
    public $showSpouseModal = false, $showAdditionalDetailsModal = false;
    public $showTransactionRemarksModal = false;
    public $showSpouseCancellationModal = false;
    public $showMarkAsPaidModal = false;

    public $ccEmailNotif;

    protected $listeners = ['paymentReminderConfirmed' => 'sendEmailReminder', 'sendEmailRegistrationConfirmationConfirmed' => 'sendEmailRegistrationConfirmation', 'cancelRefundDelegateConfirmed' => 'cancelOrRefundSpouse', 'cancelReplaceDelegateConfirmed' => 'addReplaceSpouse', 'markAsPaidConfirmed' => 'markAsPaid'];

    public function mount($eventCategory, $eventId, $registrantId, $finalData)
    {
        $this->countries = config('app.countries');
        $this->salutations = config('app.salutations');
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
        return view('livewire.admin.events.transactions.spouse.spouse-registrant-details');
    }

    public function updateSpouse()
    {
        $this->validate(
            [
                'firstName' => 'required',
                'lastName' => 'required',
                'emailAddress' => 'required|email',
                'nationality' => 'required',
                'country' => 'required',
                'city' => 'required',
                'mobileNumber' => 'required',
            ],
            [
                'firstName.required' => "First name is required",
                'lastName.required' => "Last name is required",
                'emailAddress.required' => "Email address is required",
                'emailAddress.email' => "Email address must be a valid email",
                'mobileNumber.required' => "Mobile number is required",
                'nationality.required' => "Nationality is required",
                'country.required' => "Country is required",
                'city.required' => "City is required",
            ]
        );

        if ($this->type == "main") {
            MainSpouses::find($this->spouseId)->fill([
                'salutation' => $this->salutation,
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName,
                'last_name' => $this->lastName,
                'email_address' => $this->emailAddress,
                'mobile_number' => $this->mobileNumber,
                'nationality' => $this->nationality,
                'country' => $this->country,
                'city' => $this->city,
            ])->save();
        } else {
            AdditionalSpouses::find($this->spouseId)->fill([
                'salutation' => $this->salutation,
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName,
                'last_name' => $this->lastName,
                'email_address' => $this->emailAddress,
                'mobile_number' => $this->mobileNumber,
                'nationality' => $this->nationality,
                'country' => $this->country,
                'city' => $this->city,
            ])->save();
        }

        $this->finalData['allSpouses'][$this->spouseIndex][$this->spouseInnerIndex]['salutation'] = $this->salutation;
        $this->finalData['allSpouses'][$this->spouseIndex][$this->spouseInnerIndex]['first_name'] = $this->firstName;
        $this->finalData['allSpouses'][$this->spouseIndex][$this->spouseInnerIndex]['middle_name'] = $this->salutation;
        $this->finalData['allSpouses'][$this->spouseIndex][$this->spouseInnerIndex]['last_name'] = $this->lastName;
        $this->finalData['allSpouses'][$this->spouseIndex][$this->spouseInnerIndex]['name'] = $this->salutation . " " . $this->firstName . " " . $this->middleName . " " . $this->lastName;
        $this->finalData['allSpouses'][$this->spouseIndex][$this->spouseInnerIndex]['email_address'] = $this->emailAddress;
        $this->finalData['allSpouses'][$this->spouseIndex][$this->spouseInnerIndex]['mobile_number'] = $this->mobileNumber;
        $this->finalData['allSpouses'][$this->spouseIndex][$this->spouseInnerIndex]['nationality'] = $this->nationality;
        $this->finalData['allSpouses'][$this->spouseIndex][$this->spouseInnerIndex]['country'] = $this->country;
        $this->finalData['allSpouses'][$this->spouseIndex][$this->spouseInnerIndex]['city'] = $this->city;

        $this->calculateTotal();

        $this->showSpouseModal = false;
        $this->resetEditModalFields();
    }


    public function openEditSpouseModal($index, $innerIndex)
    {
        $this->spouseIndex = $index;
        $this->spouseInnerIndex = $innerIndex;
        $this->mainSpouseId = $this->finalData['allSpouses'][$index][$innerIndex]['mainSpouseId'];
        $this->spouseId = $this->finalData['allSpouses'][$index][$innerIndex]['spouseId'];
        $this->salutation = $this->finalData['allSpouses'][$index][$innerIndex]['salutation'];
        $this->firstName = $this->finalData['allSpouses'][$index][$innerIndex]['first_name'];
        $this->middleName = $this->finalData['allSpouses'][$index][$innerIndex]['middle_name'];
        $this->lastName = $this->finalData['allSpouses'][$index][$innerIndex]['last_name'];
        $this->emailAddress = $this->finalData['allSpouses'][$index][$innerIndex]['email_address'];
        $this->mobileNumber = $this->finalData['allSpouses'][$index][$innerIndex]['mobile_number'];
        $this->nationality = $this->finalData['allSpouses'][$index][$innerIndex]['nationality'];
        $this->country = $this->finalData['allSpouses'][$index][$innerIndex]['country'];
        $this->city = $this->finalData['allSpouses'][$index][$innerIndex]['city'];
        $this->type = $this->finalData['allSpouses'][$index][$innerIndex]['spouseType'];
        $this->showSpouseModal = true;
    }

    public function closeEditSpouseModal()
    {
        $this->showSpouseModal = false;
        $this->resetEditModalFields();
    }

    public function resetEditModalFields()
    {
        $this->spouseIndex = null;
        $this->spouseInnerIndex = null;
        $this->mainSpouseId = null;
        $this->spouseId = null;
        $this->salutation = null;
        $this->firstName = null;
        $this->middleName = null;
        $this->lastName = null;
        $this->emailAddress = null;
        $this->mobileNumber = null;
        $this->nationality = null;
        $this->country = null;
        $this->city = null;
        $this->type = null;
    }

    public function updateAdditionalDetails()
    {
        $this->validate([
            'referenceDelegateName' => 'required',
            'selectedDays' => 'required',
        ]);

        $price = 0;

        foreach ($this->selectedDays as $selectedDay) {
            if ($selectedDay == 1) {
                $price += 200;
                $this->day_one = true;
            } else if ($selectedDay == 2) {
                $price += 220;
                $this->day_two = true;
            } else if ($selectedDay == 3) {
                $price += 200;
                $this->day_three = true;
            } else {
                $price += 200;
                $this->day_four = true;
            }
        }

        MainSpouses::find($this->finalData['mainSpouseId'])->fill([
            'day_one' => $this->day_one,
            'day_two' => $this->day_two,
            'day_three' => $this->day_three,
            'day_four' => $this->day_four,
            'reference_delegate_name' => $this->referenceDelegateName,
            'unit_price' => $price,
            'net_amount' => $price,
            'total_amount' => $price,
        ])->save();

        $this->finalData['invoiceData']['day_one'] = $this->day_one;
        $this->finalData['invoiceData']['day_two'] = $this->day_two;
        $this->finalData['invoiceData']['day_three'] = $this->day_three;
        $this->finalData['invoiceData']['day_four'] = $this->day_four;
        $this->finalData['reference_delegate_name'] = $this->referenceDelegateName;
        $this->finalData['invoiceData']['unit_price'] = $price;
        $this->finalData['invoiceData']['net_amount'] = $price;
        $this->finalData['invoiceData']['total_amount'] = $price;

        $this->resetEditAdditionalDetailsModalFields();
        $this->showAdditionalDetailsModal = false;
    }

    public function openEditAdditionalDetailsModal()
    {
        $this->referenceDelegateName = $this->finalData['reference_delegate_name'];

        if ($this->finalData['invoiceData']['day_one']) {
            $this->selectedDays[] = 1;
        }

        if ($this->finalData['invoiceData']['day_two']) {
            $this->selectedDays[] = 2;
        }

        if ($this->finalData['invoiceData']['day_three']) {
            $this->selectedDays[] = 3;
        }

        if ($this->finalData['invoiceData']['day_four']) {
            $this->selectedDays[] = 4;
        }

        $this->showAdditionalDetailsModal = true;
    }

    public function closeEditAdditionalDetailsModal()
    {
        $this->resetEditAdditionalDetailsModalFields();
        $this->showAdditionalDetailsModal = false;
    }

    public function resetEditAdditionalDetailsModalFields()
    {
        $this->referenceDelegateName = null;
        $this->day_one = false;
        $this->day_two = false;
        $this->day_three = false;
        $this->day_four = false;
        $this->selectedDays = [];
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
        $paymentStatus = "paid";

        MainSpouses::find($this->finalData['mainSpouseId'])->fill([
            'registration_status' => "confirmed",
            'payment_status' => $paymentStatus,
            'mode_of_payment' => $this->mapPaymentMethod,
            'paid_date_time' => Carbon::now(),

            'registration_confirmation_sent_count' => $this->finalData['registration_confirmation_sent_count'] + 1,
            'registration_confirmation_sent_datetime' => Carbon::now(),
        ])->save();

        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->finalData['mainSpouseId'];

        $eventDatesDescription = " ";

        if ($this->finalData['invoiceData']['day_one']) {
            $eventDatesDescription = $eventDatesDescription . "1, ";
        }

        if ($this->finalData['invoiceData']['day_two']) {
            $eventDatesDescription = $eventDatesDescription . "2, ";
        }

        if ($this->finalData['invoiceData']['day_three']) {
            $eventDatesDescription = $eventDatesDescription . "3, ";
        }

        if ($this->finalData['invoiceData']['day_four']) {
            $eventDatesDescription = $eventDatesDescription . "4, ";
        }

        $eventDatesDescription = "December$eventDatesDescription 2023";

        foreach ($this->finalData['allSpouses'] as $spousesIndex => $spouses) {
            foreach ($spouses as $innerSpouse) {
                if (end($spouses) == $innerSpouse) {
                    if (!$innerSpouse['spouse_cancelled']) {
                        $details1 = [
                            'eventCategory' => $this->event->category,
                            'eventYear' => $this->event->year,
                            'eventName' => $this->event->name,

                            'eventDatesDescription' => $eventDatesDescription,

                            'name' => $innerSpouse['name'],
                            'referenceDelegateName' => $this->finalData['reference_delegate_name'],

                            'emailAddress' => $innerSpouse['email_address'],
                            'mobileNumber' => $innerSpouse['mobile_number'],
                            'country' => $innerSpouse['country'],
                            'city' => $innerSpouse['city'],
                            'amountPaid' => $this->finalData['invoiceData']['unit_price'],
                            'transactionId' => $innerSpouse['transactionId'],

                            'invoiceLink' => $invoiceLink,
                        ];

                        $details2 = [
                            'name' => $innerSpouse['name'],
                            'eventLink' => $this->event->link,
                            'eventName' => $this->event->name,
                            'eventCategory' => $this->event->category,
                            'eventYear' => $this->event->year,

                            'invoiceAmount' => $this->finalData['invoiceData']['total_amount'],
                            'amountPaid' => $this->finalData['invoiceData']['total_amount'],
                            'balance' => 0,
                            'invoiceLink' => $invoiceLink,
                        ];

                        try {
                            Mail::to($innerSpouse['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaid($details1));
                        } catch (\Exception $e) {
                            Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaid($details1));
                        }

                        if ($this->sendInvoice) {
                            if ($spousesIndex == 0) {
                                try {
                                    Mail::to($innerSpouse['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
                                } catch (\Exception $e) {
                                    Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
                                }
                            }
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
        return $this->event->std_nmember_rate;
    }


    public function calculateTotal()
    {
        $invoiceDetails = array();
        $countFinalQuantity = 0;

        $mainSpouse = MainSpouses::where('id', $this->finalData['mainSpouseId'])->where('event_id', $this->eventId)->first();

        $addMainSpouse = true;
        if ($mainSpouse->spouse_cancelled) {
            if ($mainSpouse->spouse_refunded || $mainSpouse->spouse_replaced) {
                $addMainSpouse = false;
            }
        }

        if ($mainSpouse->spouse_replaced_by_id == null & (!$mainSpouse->spouse_refunded)) {
            $countFinalQuantity++;
        }

        if ($addMainSpouse) {
            $delegateDescription = "Spouse Registration Fee";

            array_push($invoiceDetails, [
                'delegateDescription' => $delegateDescription,
                'delegateNames' => [
                    $mainSpouse->first_name . " " . $mainSpouse->middle_name . " " . $mainSpouse->last_name,
                ],
                'badgeType' => null,
                'quantity' => 1,
                'totalDiscount' => 0,
                'totalNetAmount' =>  $this->checkUnitPrice(),
                'promoCodeDiscount' => 0,
            ]);
        }


        $subSpouses = AdditionalSpouses::where('main_spouse_id', $this->finalData['mainSpouseId'])->get();
        if (!$subSpouses->isEmpty()) {
            foreach ($subSpouses as $subSpouse) {

                if ($subSpouse->spouse_replaced_by_id == null & (!$subSpouse->spouse_refunded)) {
                    $countFinalQuantity++;
                }

                $addSubSpouse = true;
                if ($subSpouse->spouse_cancelled) {
                    if ($subSpouse->spouse_refunded || $subSpouse->spouse_replaced) {
                        $addSubSpouse = false;
                    }
                }


                if ($addSubSpouse) {
                    $existingIndex = 0;
                    if (count($invoiceDetails) == 0) {
                        array_push($invoiceDetails, [
                            'delegateDescription' => "Spouse Registration Fee",
                            'delegateNames' => [
                                $subSpouse->first_name . " " . $subSpouse->middle_name . " " . $subSpouse->last_name,
                            ],
                            'badgeType' => null,
                            'quantity' => 1,
                            'totalDiscount' => 0,
                            'totalNetAmount' =>  $this->checkUnitPrice(),
                            'promoCodeDiscount' => 0,
                        ]);
                    } else {
                        array_push(
                            $invoiceDetails[$existingIndex]['delegateNames'],
                            $subSpouse->first_name . " " . $subSpouse->middle_name . " " . $subSpouse->last_name
                        );

                        $quantityTemp = $invoiceDetails[$existingIndex]['quantity'] + 1;
                        $totalNetAmountTemp = $this->checkUnitPrice() * $quantityTemp;

                        $invoiceDetails[$existingIndex]['quantity'] = $quantityTemp;
                        $invoiceDetails[$existingIndex]['totalNetAmount'] = $totalNetAmountTemp;
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

        MainSpouses::find($this->finalData['mainSpouseId'])->fill([
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
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->finalData['mainSpouseId'];

        MainSpouses::find($this->finalData['mainSpouseId'])->fill([
            'registration_confirmation_sent_count' => $this->finalData['registration_confirmation_sent_count'] + 1,
            'registration_confirmation_sent_datetime' => Carbon::now(),
        ])->save();

        $eventDatesDescription = " ";

        if ($this->finalData['invoiceData']['day_one']) {
            $eventDatesDescription = $eventDatesDescription . "1, ";
        }

        if ($this->finalData['invoiceData']['day_two']) {
            $eventDatesDescription = $eventDatesDescription . "2, ";
        }

        if ($this->finalData['invoiceData']['day_three']) {
            $eventDatesDescription = $eventDatesDescription . "3, ";
        }

        if ($this->finalData['invoiceData']['day_four']) {
            $eventDatesDescription = $eventDatesDescription . "4, ";
        }

        $eventDatesDescription = "December$eventDatesDescription 2023";

        foreach ($this->finalData['allSpouses'] as $spousesIndex => $spouses) {
            foreach ($spouses as $innerSpouse) {
                if (end($spouses) == $innerSpouse) {
                    if (!$innerSpouse['spouse_cancelled']) {
                        $details1 = [
                            'eventCategory' => $this->event->category,
                            'eventYear' => $this->event->year,
                            'eventName' => $this->event->name,

                            'eventDatesDescription' => $eventDatesDescription,

                            'name' => $innerSpouse['name'],
                            'referenceDelegateName' => $this->finalData['reference_delegate_name'],

                            'emailAddress' => $innerSpouse['email_address'],
                            'mobileNumber' => $innerSpouse['mobile_number'],
                            'country' => $innerSpouse['country'],
                            'city' => $innerSpouse['city'],
                            'amountPaid' => $this->finalData['invoiceData']['unit_price'],
                            'transactionId' => $innerSpouse['transactionId'],

                            'invoiceLink' => $invoiceLink,
                        ];

                        $details2 = [
                            'name' => $innerSpouse['name'],
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
                            try {
                                Mail::to($innerSpouse['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationUnpaid($details1, $this->sendInvoice));
                            } catch (\Exception $e) {
                                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationUnpaid($details1, $this->sendInvoice));
                            }
                        } else if ($this->finalData['payment_status'] == "free" && $this->finalData['registration_status'] == "pending") {
                            try {
                                Mail::to($innerSpouse['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationFree($details1, $this->sendInvoice));
                            } catch (\Exception $e) {
                                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($details1, $this->sendInvoice));
                            }
                        } else {
                            try {
                                Mail::to($innerSpouse['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaid($details1, $this->sendInvoice));
                            } catch (\Exception $e) {
                                Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaid($details1, $this->sendInvoice));
                            }
                            if ($this->sendInvoice) {
                                if ($spousesIndex == 0) {
                                    try {
                                        Mail::to($innerSpouse['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaymentConfirmation($details2, $this->sendInvoice));
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

        foreach ($this->finalData['allSpouses'] as $spouses) {
            foreach ($spouses as $innerDelegate) {
                if (end($spouses) == $innerDelegate) {
                    if (!$innerDelegate['spouse_cancelled']) {
                        $details = [
                            'name' => $innerDelegate['name'],
                            'eventName' => $this->event->name,
                            'eventLink' => $this->event->link,
                            'eventCategory' => $this->event->category,
                            'invoiceLink' => $invoiceLink,
                            'eventYear' => $this->event->year,
                        ];
                        try {
                            Mail::to($innerDelegate['email_address'])->cc($this->ccEmailNotif)->send(new RegistrationPaymentReminder($details));
                        } catch (\Exception $e) {
                            Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentReminder($details));
                        }
                    }
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
        $allSpouses = SpouseTransactions::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->get();

        $countMainSpouse = 0;
        $countSubSpouse = 0;

        if (!$allSpouses->isEmpty()) {
            foreach ($allSpouses as $spouse) {
                if ($spouse->spouse_type == "main") {
                    $mainSpouse = MainSpouses::where('id', $spouse->spouse_id)->where('email_address', $emailAddress)->where('registration_status', '!=', 'droppedOut')->where('spouse_cancelled', '!=', true)->first();
                    if ($mainSpouse != null) {
                        $countMainSpouse++;
                    }
                } else {
                    $subSpouse = AdditionalSpouses::where('id', $spouse->spouse_id)->where('email_address', $emailAddress)->where('spouse_cancelled', '!=', true)->first();
                    if ($subSpouse != null) {
                        $registrationStatsMain = MainSpouses::where('id', $subSpouse->main_spouse_id)->value('registration_status');
                        if ($registrationStatsMain != "droppedOut") {
                            $countSubSpouse++;
                        }
                    }
                }
            }
        }

        if ($countMainSpouse == 0 && $countSubSpouse == 0) {
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
        MainSpouses::find($this->finalData['mainSpouseId'])->fill([
            'transaction_remarks' => $this->transactionRemarks,
        ])->save();

        $this->finalData['transaction_remarks'] = $this->transactionRemarks;
        $this->transactionRemarks = null;
        $this->showTransactionRemarksModal = false;
    }

    public function openSpouseCancellationModal($index, $innerIndex)
    {
        $this->replaceSpouseIndex = $index;
        $this->replaceSpouseInnerIndex = $innerIndex;
        $this->showSpouseCancellationModal = true;
    }

    public function closeSpouseCancellationModal()
    {
        $this->removeReplaceData();
        $this->showSpouseCancellationModal = false;
    }

    public function nextSpouseCancellation()
    {
        $this->spouseCancellationStep++;
    }

    public function prevSpouseCancellation()
    {
        $this->spouseCancellationStep--;
    }

    public function submitSpouseCancellation()
    {
        if ($this->spouseCancellationStep == 2) {
            if ($this->replaceSpouse == "No") {
                $this->validate(
                    [
                        'spouseRefund' => 'required',
                    ],
                    [
                        'spouseRefund.required' => "This needs to be fill up.",
                    ],
                );

                if ($this->spouseRefund == "Yes") {
                    $message = "Are you sure want to cancel and refund this spouse?";
                } else {
                    $message = "Are you sure want to cancel and not refund this spouse?";
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
                        'replaceCountry' => 'required',
                        'replaceCity' => 'required',
                    ],
                    [
                        'replaceFirstName.required' => "First name is required",
                        'replaceLastName.required' => "Last name is required",
                        'replaceEmailAddress.required' => "Email address is required",
                        'replaceEmailAddress.email' => "Email address must be a valid email",
                        'replaceNationality.required' => "Nationality is required",
                        'replaceMobileNumber.required' => "Mobile number is required",
                        'replaceCountry.required' => "Country is required",
                        'replaceCity.required' => "City is required",
                    ]
                );

                if ($this->checkEmailIfExistsInDatabase($this->replaceEmailAddress)) {
                    $this->replaceEmailAlreadyUsedError = "Email is already registered, please use another email!";
                } else {
                    $this->replaceEmailAlreadyUsedError = null;
                    $this->dispatchBrowserEvent('swal:delegate-cancel-replace-confirmation', [
                        'type' => 'warning',
                        'message' => 'Are you sure you want to cancel and replace this spouse?',
                        'text' => "",
                    ]);
                }
            }
        }
    }

    public function cancelOrRefundSpouse()
    {
        if ($this->spouseRefund == "Yes") {
            // refunded
            if ($this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouseType'] == "main") {
                MainSpouses::find($this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['mainSpouseId'])->fill([
                    'spouse_cancelled' => true,
                    'spouse_refunded' => true,
                    'spouse_cancelled_datetime' => Carbon::now(),
                    'spouse_refunded_datetime' => Carbon::now(),
                ])->save();
            } else {
                AdditionalSpouses::find($this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouseId'])->fill([
                    'spouse_cancelled' => true,
                    'spouse_refunded' => true,
                    'spouse_cancelled_datetime' => Carbon::now(),
                    'spouse_refunded_datetime' => Carbon::now(),
                ])->save();
            }

            $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_cancelled'] = true;
            $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_refunded'] = true;
            $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
            $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_refunded_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

            if ($this->finalData['finalQuantity'] == 1) {
                MainSpouses::find($this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['mainSpouseId'])->fill([
                    'registration_status' => "cancelled",
                    'payment_status' => "refunded",
                ])->save();

                $this->finalData['registration_status'] = 'cancelled';
                $this->finalData['payment_status'] = 'refunded';
                $this->finalData['finalQuantity'] = 0;
            }

            $this->dispatchBrowserEvent('swal:delegate-cancel-refund-success', [
                'type' => 'success',
                'message' => 'Spouse cancelled and refunded succesfully!',
                'text' => "",
            ]);
        } else {
            // not refunded
            if ($this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouseType'] == "main") {
                MainSpouses::find($this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['mainSpouseId'])->fill([
                    'spouse_cancelled' => true,
                    'spouse_cancelled_datetime' => Carbon::now(),
                ])->save();
            } else {
                AdditionalSpouses::find($this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouseId'])->fill([
                    'spouse_cancelled' => true,
                    'spouse_cancelled_datetime' => Carbon::now(),
                ])->save();
            }

            $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_cancelled'] = true;
            $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

            if ($this->finalData['finalQuantity'] == 1) {
                MainSpouses::find($this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['mainSpouseId'])->fill([
                    'registration_status' => "cancelled",
                ])->save();

                $this->finalData['registration_status'] = 'cancelled';
            }

            $this->dispatchBrowserEvent('swal:delegate-cancel-refund-success', [
                'type' => 'success',
                'message' => 'Spouse cancelled but not refunded succesfully!',
                'text' => "",
            ]);
        }
        $this->showSpouseCancellationModal = false;
    }



    public function addReplaceSpouse()
    {
        $replacedSpouse = AdditionalSpouses::create([
            'main_spouse_id' => $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['mainSpouseId'],
            'salutation' => $this->replaceSalutation,
            'first_name' => $this->replaceFirstName,
            'middle_name' => $this->replaceMiddleName,
            'last_name' => $this->replaceLastName,
            'email_address' => $this->replaceEmailAddress,
            'mobile_number' => $this->replaceMobileNumber,
            'nationality' => $this->replaceNationality,
            'country' => $this->replaceCountry,
            'city' => $this->replaceCity,

            'spouse_replaced_type' => $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_replaced_type'],
            'spouse_replaced_from_id' => $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouseId'],
            'spouse_original_from_id' => $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_original_from_id'],
        ]);


        $transaction = SpouseTransactions::create([
            'event_id' => $this->eventId,
            'event_category' => $this->eventCategory,
            'spouse_id' => $replacedSpouse->id,
            'spouse_type' => "sub",
        ]);

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }
        $lastDigit = 1000 + intval($transaction->id);
        $finalTransactionId = $this->event->year . $eventCode . $lastDigit;

        array_push($this->finalData['allSpouses'][$this->replaceSpouseIndex], [
            'transactionId' => $finalTransactionId,
            'mainSpouseId' => $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['mainSpouseId'],
            'spouseId' => $replacedSpouse->id,
            'spouseType' => "sub",

            'name' => $this->replaceSalutation . " " . $this->replaceFirstName . " " . $this->replaceMiddleName . " " . $this->replaceLastName,
            'salutation' => $this->replaceSalutation,
            'first_name' => $this->replaceFirstName,
            'middle_name' => $this->replaceMiddleName,
            'last_name' => $this->replaceLastName,
            'email_address' => $this->replaceEmailAddress,
            'mobile_number' => $this->replaceMobileNumber,
            'nationality' => $this->replaceNationality,
            'country' => $this->replaceCountry,
            'city' => $this->replaceCity,

            'is_replacement' => true,
            'spouse_cancelled' => false,
            'spouse_replaced' => false,
            'spouse_refunded' => false,

            'spouse_replaced_type' => "sub",
            'spouse_original_from_id' => $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_original_from_id'],
            'spouse_replaced_from_id' => $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouseId'],
            'spouse_replaced_by_id' => null,

            'spouse_cancelled_datetime' => null,
            'spouse_refunded_datetime' => null,
            'spouse_replaced_datetime' => null,
        ]);
        if ($this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouseType'] == "main") {
            MainSpouses::find($this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['mainSpouseId'])->fill([
                'spouse_cancelled' => true,
                'spouse_cancelled_datetime' => Carbon::now(),
                'spouse_replaced' => true,
                'spouse_replaced_by_id' => $replacedSpouse->id,
                'spouse_replaced_datetime' => Carbon::now(),
            ])->save();
        } else {
            AdditionalSpouses::find($this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouseId'])->fill([
                'spouse_cancelled' => true,
                'spouse_cancelled_datetime' => Carbon::now(),
                'spouse_replaced' => true,
                'spouse_replaced_by_id' => $replacedSpouse->id,
                'spouse_replaced_datetime' => Carbon::now(),
            ])->save();
        }


        MainSpouses::where('id', $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['mainSpouseId'])->increment('quantity');

        $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_cancelled'] = true;
        $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_replaced'] = true;
        $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_cancelled_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');
        $this->finalData['allSpouses'][$this->replaceSpouseIndex][$this->replaceSpouseInnerIndex]['spouse_replaced_datetime'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

        $this->dispatchBrowserEvent('swal:delegate-cancel-replace-success', [
            'type' => 'success',
            'message' => 'Spouse replaced succesfully!',
            'text' => "",
        ]);
        $this->calculateTotal();
        $this->removeReplaceData();
        $this->showSpouseCancellationModal = false;
    }

    public function removeReplaceData()
    {
        $this->spouseCancellationStep = 1;
        $this->replaceSpouseIndex = null;
        $this->replaceSpouseInnerIndex = null;

        $this->replaceSalutation = null;
        $this->replaceFirstName = null;
        $this->replaceMiddleName = null;
        $this->replaceLastName = null;
        $this->replaceEmailAddress = null;
        $this->replaceMobileNumber = null;
        $this->replaceNationality = null;
        $this->replaceCountry = null;
        $this->replaceCity = null;

        $this->replaceEmailAlreadyUsedError = null;
    }
}
