<?php

namespace App\Http\Livewire;

use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Mail\RegistrationPaymentReminder;
use Livewire\Component;
use App\Models\MainVisitor as MainVisitors;
use App\Models\Event as Events;
use App\Models\AdditionalVisitor as AdditionalVisitors;
use App\Models\VisitorTransaction as VisitorTransactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use NumberFormatter;

class VisitorRegistrantDetails extends Component
{
    public $countries, $salutations;

    public $eventCategory, $eventId, $registrantId, $finalData, $event;

    public $finalUnitPrice;

    // DELEGATE DETAILS
    public $mainVisitorId, $visitorId, $salutation, $firstName, $middleName, $lastName, $emailAddress, $mobileNumber, $nationality, $country, $city, $companyName, $jobTitle, $type, $visitorIndex, $visitorInnerIndex;

    public $transactionRemarks, $visitorCancellationStep = 1, $replaceVisitor, $visitorRefund;

    public $replaceVisitorIndex, $replaceVisitorInnerIndex, $replaceSalutation, $replaceFirstName, $replaceMiddleName, $replaceLastName, $replaceEmailAddress, $replaceMobileNumber, $replaceNationality, $replaceCountry, $replaceCity, $replaceCompanyName, $replaceJobTitle, $replaceEmailAlreadyUsedError;

    public $mapPaymentMethod;

    // MODALS
    public $showVisitorModal = false, $showAdditionalDetailsModal = false;
    public $showTransactionRemarksModal = false;
    public $showVisitorCancellationModal = false;
    public $showMarkAsPaidModal = false;

    protected $listeners = ['paymentReminderConfirmed' => 'sendEmailReminder', 'cancelRefundDelegateConfirmed' => 'cancelOrRefundVisitor', 'cancelReplaceDelegateConfirmed' => 'addReplaceVisitor', 'markAsPaidConfirmed' => 'markAsPaid'];

    public function mount($eventCategory, $eventId, $registrantId, $finalData)
    {
        $this->countries = config('app.countries');
        $this->salutations = config('app.salutations');
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();
        $this->eventCategory = $eventCategory;
        $this->eventId = $eventId;
        $this->registrantId = $registrantId;
        $this->finalData = $finalData;
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
            MainVisitors::find($this->visitorId)->fill([
                'salutation' => $this->salutation,
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName,
                'last_name' => $this->lastName,
                'email_address' => $this->emailAddress,
                'mobile_number' => $this->mobileNumber,
                'nationality' => $this->nationality,
                'country' => $this->country,
                'city' => $this->city,
                'company_name' => $this->companyName,
                'job_title' => $this->jobTitle,
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
                'country' => $this->country,
                'city' => $this->city,
                'company_name' => $this->companyName,
                'job_title' => $this->jobTitle,
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
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['country'] = $this->country;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['city'] = $this->city;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['company_name'] = $this->companyName;
        $this->finalData['allVisitors'][$this->visitorIndex][$this->visitorInnerIndex]['job_title'] = $this->jobTitle;

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
        $this->country = $this->finalData['allVisitors'][$index][$innerIndex]['country'];
        $this->city = $this->finalData['allVisitors'][$index][$innerIndex]['city'];
        $this->companyName = $this->finalData['allVisitors'][$index][$innerIndex]['company_name'];
        $this->jobTitle = $this->finalData['allVisitors'][$index][$innerIndex]['job_title'];
        $this->type = $this->finalData['allVisitors'][$index][$innerIndex]['visitorType'];
        $this->showVisitorModal = true;
    }

    public function closeEditVisitorModal()
    {
        $this->showVisitorModal = false;
        $this->resetEditModalFields();
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
        $this->country = null;
        $this->city = null;
        $this->companyName = null;
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

        MainVisitors::find($this->finalData['mainVisitorId'])->fill([
            'registration_status' => "confirmed",
            'payment_status' => $paymentStatus,
            'mode_of_payment' => $this->mapPaymentMethod,
            'paid_date_time' => Carbon::now(),
        ])->save();

        $eventFormattedData = Carbon::parse($this->event->event_start_date)->format('d') . '-' . Carbon::parse($this->event->event_end_date)->format('d M Y');
        $invoiceLink = env('APP_URL') . '/' . $this->event->category . '/' . $this->event->id . '/view-invoice/' . $this->finalData['mainVisitorId'];

        foreach ($this->finalData['allVisitors'] as $visitors) {
            foreach ($visitors as $innerVisitor) {
                if (end($visitors) == $innerVisitor) {
                    $details1 = [
                        'name' => $innerVisitor['name'],
                        'eventLink' => $this->event->link,
                        'eventName' => $this->event->name,
                        'eventDates' => $eventFormattedData,
                        'eventLocation' => $this->event->location,
                        'eventCategory' => $this->event->category,
                        'eventYear' => $this->event->year,

                        'nationality' => $innerVisitor['nationality'],
                        'country' => $innerVisitor['country'],
                        'city' => $innerVisitor['city'],
                        'amountPaid' => $this->finalData['invoiceData']['total_amount'],
                        'transactionId' => $innerVisitor['transactionId'],
                        'invoiceLink' => $invoiceLink,
                        
                        'badgeLink' => env('APP_URL')."/".$this->event->category."/".$this->event->id."/view-badge"."/".$innerVisitor['visitorType']."/".$innerVisitor['visitorId'],
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

                    Mail::to($innerVisitor['email_address'])->cc(config('app.ccEmailNotif'))->queue(new RegistrationPaid($details1));
                    Mail::to($innerVisitor['email_address'])->cc(config('app.ccEmailNotif'))->queue(new RegistrationPaymentConfirmation($details2));
                }
            }
        }

        $this->finalData['registration_status'] = "confirmed";
        $this->finalData['payment_status'] = $paymentStatus;
        $this->finalData['mode_of_payment'] = $this->mapPaymentMethod;
        $this->finalData['paid_date_time'] = Carbon::parse(Carbon::now())->format('M j, Y g:i A');

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
            $delegateDescription = "Visitor Registration Fee";

            array_push($invoiceDetails, [
                'delegateDescription' => $delegateDescription,
                'delegateNames' => [
                    $mainVisitor->first_name . " " . $mainVisitor->middle_name . " " . $mainVisitor->last_name,
                ],
                'badgeType' => null,
                'quantity' => 1,
                'totalDiscount' => 0,
                'totalNetAmount' =>  $this->checkUnitPrice(),
                'promoCodeDiscount' => 0,
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
                    $existingIndex = 0;
                    if (count($invoiceDetails) == 0) {
                        array_push($invoiceDetails, [
                            'delegateDescription' => "Visitor Registration Fee",
                            'delegateNames' => [
                                $subVisitor->first_name . " " . $subVisitor->middle_name . " " . $subVisitor->last_name,
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
                            $subVisitor->first_name . " " . $subVisitor->middle_name . " " . $subVisitor->last_name
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
            foreach ($visitors as $innerDelegate) {
                if (end($visitors) == $innerDelegate) {
                    $details = [
                        'name' => $innerDelegate['name'],
                        'eventName' => $this->event->name,
                        'eventLink' => $this->event->link,
                        'eventCategory' => $this->event->category,
                        'invoiceLink' => $invoiceLink,
                        'eventYear' => $this->event->year,
                    ];
                    Mail::to($innerDelegate['email_address'])->cc(config('app.ccEmailNotif'))->queue(new RegistrationPaymentReminder($details));
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
        $replacedVisitor = AdditionalVisitors::create([
            'main_visitor_id' => $this->finalData['allVisitors'][$this->replaceVisitorIndex][$this->replaceVisitorInnerIndex]['mainVisitorId'],
            'salutation' => $this->replaceSalutation,
            'first_name' => $this->replaceFirstName,
            'middle_name' => $this->replaceMiddleName,
            'last_name' => $this->replaceLastName,
            'email_address' => $this->replaceEmailAddress,
            'mobile_number' => $this->replaceMobileNumber,
            'nationality' => $this->replaceNationality,
            'country' => $this->replaceCountry,
            'city' => $this->replaceCity,
            'company_name' => $this->replaceCompanyName,
            'job_title' => $this->replaceJobTitle,

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
            'email_address' => $this->replaceEmailAddress,
            'mobile_number' => $this->replaceMobileNumber,
            'nationality' => $this->replaceNationality,
            'country' => $this->replaceCountry,
            'city' => $this->replaceCity,
            'company_name' => $this->replaceCompanyName,
            'job_title' => $this->replaceJobTitle,

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
        $this->replaceCountry = null;
        $this->replaceCity = null;
        $this->replaceCompanyName = null;
        $this->replaceJobTitle = null;

        $this->replaceEmailAlreadyUsedError = null;
    }
}
