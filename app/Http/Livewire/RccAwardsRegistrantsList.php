<?php

namespace App\Http\Livewire;

use App\Models\Event as Events;
use App\Models\RccAwardsMainParticipant as RccAwardsMainParticipants;
use App\Models\RccAwardsParticipantTransaction as RccAwardsParticipantTransactions;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

class RccAwardsRegistrantsList extends Component
{
    use WithFileUploads;

    public $event;
    public $countries;

    public $finalListOfRegistrants = array(), $finalListOfRegistrantsConst = array();
    public $eventId, $eventCategory;
    public $searchTerm;

    // COMPANY INFO
    public $heardWhere;
    public $finalUnitPrice;

    // FILTERS
    public $filterByPassType, $filterByRegStatus, $filterByPayStatus;

    // ERRORS
    public $incompleDetails = array(), $emailYouAlreadyUsed = array(), $emailAlreadyExisting = array();

    public $getEventCode;

    public function mount($eventId, $eventCategory)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();
        $this->countries = config('app.countries');
        $this->eventId = $eventId;
        $this->eventCategory = $eventCategory;

        $mainParticipants = RccAwardsMainParticipants::where('event_id', $this->eventId)->orderBy('registered_date_time', 'DESC')->get();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->event->category == $eventCategoryC) {
                $this->getEventCode = $code;
            }
        }

        if ($mainParticipants->isNotEmpty()) {
            foreach ($mainParticipants as $mainParticipant) {

                // get invoice number & transaction id
                $transactionId = RccAwardsParticipantTransactions::where('participant_id', $mainParticipant->id)->where('participant_type', "main")->value('id');
                $tempYear = Carbon::parse($mainParticipant->registered_date_time)->format('y');
                $lastDigit = 1000 + intval($transactionId);
                $tempInvoiceNumber = $this->event->category . $tempYear . "/" . $lastDigit;

                // get pass type
                if ($mainParticipant->pass_type == 'member') {
                    $passType = "Member";
                } else if ($mainParticipant->pass_type == 'nonMember') {
                    $passType = "Non-Member"; 
                } else {
                    $passType = "Full Member"; 
                }
                
                // get reg status
                if ($mainParticipant->registration_status == 'confirmed') {
                    $regStatus = "Confirmed";
                } else if ($mainParticipant->registration_status == 'pending') {
                    $regStatus = "Pending";
                } else if ($mainParticipant->registration_status == 'droppedOut') {
                    $regStatus = "Dropped out";
                } else {
                    $regStatus = "Cancelled";
                }

                // get payment status
                if ($mainParticipant->payment_status == 'paid') {
                    $payStatus = "Paid";
                } else if ($mainParticipant->payment_status == 'free') {
                    $payStatus = "Free";
                } else if ($mainParticipant->payment_status == 'unpaid') {
                    $payStatus = "Unpaid";
                } else {
                    $payStatus = "Refunded";
                }

                if($mainParticipant->mode_of_payment == "bankTransfer"){
                    $paymentMethod = "Bank Transfer";
                } else {
                    $paymentMethod = "Credit Card";
                }

                array_push($this->finalListOfRegistrants, [
                    'mainParticipantId' => $mainParticipant->id,
                    'invoiceNumber' => $tempInvoiceNumber,
                    'companyName' => $mainParticipant->company_name,
                    'category' => $mainParticipant->category,
                    'passType' => $passType,

                    'totalAmount' => $mainParticipant->total_amount,
                    'regDateTime' => Carbon::parse($mainParticipant->registered_date_time)->format('M j, Y g:iA'),
                    'regStatus' => $regStatus,
                    'payStatus' => $payStatus,
                    'paymentMethod' => $paymentMethod,
                ]);
            }
            $this->finalListOfRegistrantsConst = $this->finalListOfRegistrants;
        }
    }

    public function render()
    {
        return view('livewire.admin.events.transactions.rcca.rcc-awards-registrants-list');
    }

    public function clearFilter(){
        $this->filterByPassType = null;
        $this->filterByRegStatus = null;
        $this->filterByPayStatus = null;
        $this->filter();
    }

    public function filter()
    {
        if ($this->filterByPassType == null && $this->filterByRegStatus == null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = $this->finalListOfRegistrantsConst;
        } else if ($this->filterByPassType != null && $this->filterByRegStatus == null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['passType'] === ($this->filterByPassType));
                })->all();
        } else if ($this->filterByPassType == null && $this->filterByRegStatus != null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['regStatus'] === ($this->filterByRegStatus));
                })->all();
        } else if ($this->filterByPassType == null && $this->filterByRegStatus == null && $this->filterByPayStatus != null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['payStatus'] === ($this->filterByPayStatus));
                })->all();
        } else if ($this->filterByPassType != null && $this->filterByRegStatus != null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['passType'] === ($this->filterByPassType)) &&
                        strtolower($item['regStatus'] === ($this->filterByRegStatus));
                })->all();
        } else if ($this->filterByPassType != null && $this->filterByRegStatus == null && $this->filterByPayStatus != null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['passType'] === ($this->filterByPassType)) &&
                        strtolower($item['payStatus'] === ($this->filterByPayStatus));
                })->all();
        } else if ($this->filterByPassType == null && $this->filterByRegStatus != null && $this->filterByPayStatus != null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['regStatus'] === ($this->filterByRegStatus)) &&
                        strtolower($item['payStatus'] === ($this->filterByPayStatus));
                })->all();
        } else {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['passType'] === ($this->filterByPassType)) &&
                        strtolower($item['regStatus'] === ($this->filterByRegStatus)) &&
                        strtolower($item['payStatus'] === ($this->filterByPayStatus));
                })->all();
        }
    }

    
    public function search()
    {
        if (empty($this->searchTerm)) {
            $this->finalListOfRegistrants = $this->finalListOfRegistrantsConst;
        } else {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return str_contains(strtolower($item['invoiceNumber']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['companyName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['category']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['totalAmount']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['regDateTime']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['paymentMethod']), strtolower($this->searchTerm));
                })
                ->all();
        }
    }
}
