<?php

namespace App\Http\Livewire;

use App\Models\Event as Events;
use App\Models\MainVisitor as MainVisitors;
use App\Models\VisitorTransaction as VisitorTransactions;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

class VisitorRegistrantsList extends Component
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
    public $filterByRegStatus, $filterByPayStatus, $filterByPaymentMethod;

    // ERRORS
    public $incompleDetails = array(), $emailYouAlreadyUsed = array(), $emailAlreadyExisting = array();

    public $getEventCode;

    public function mount($eventId, $eventCategory)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();
        $this->countries = config('app.countries');
        $this->eventId = $eventId;
        $this->eventCategory = $eventCategory;

        $mainVisitors = MainVisitors::where('event_id', $this->eventId)->orderBy('registered_date_time', 'DESC')->get();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->event->category == $eventCategoryC) {
                $this->getEventCode = $code;
            }
        }

        if ($mainVisitors->isNotEmpty()) {
            foreach ($mainVisitors as $mainVisitor) {

                // get invoice number & transaction id
                $transactionId = VisitorTransactions::where('visitor_id', $mainVisitor->id)->where('visitor_type', "main")->value('id');
                $tempYear = Carbon::parse($mainVisitor->registered_date_time)->format('y');
                $lastDigit = 1000 + intval($transactionId);
                $tempInvoiceNumber = $this->event->category . $tempYear . "/" . $lastDigit;

                // get reg status
                if ($mainVisitor->registration_status == 'confirmed') {
                    $regStatus = "Confirmed";
                } else if ($mainVisitor->registration_status == 'pending') {
                    $regStatus = "Pending";
                } else if ($mainVisitor->registration_status == 'droppedOut') {
                    $regStatus = "Dropped out";
                } else {
                    $regStatus = "Cancelled";
                }

                // get payment status
                if ($mainVisitor->payment_status == 'paid') {
                    $payStatus = "Paid";
                } else if ($mainVisitor->payment_status == 'free') {
                    $payStatus = "Free";
                } else if ($mainVisitor->payment_status == 'unpaid') {
                    $payStatus = "Unpaid";
                } else {
                    $payStatus = "Refunded";
                }

                if($mainVisitor->mode_of_payment == "bankTransfer"){
                    $paymentMethod = "Bank Transfer";
                } else {
                    $paymentMethod = "Credit Card";
                }

                $mainVisitorFullName = $mainVisitor->salutation . " " . $mainVisitor->first_name . " " . $mainVisitor->middle_name . " " . $mainVisitor->last_name;

                array_push($this->finalListOfRegistrants, [
                    'mainVisitorId' => $mainVisitor->id,
                    'invoiceNumber' => $tempInvoiceNumber,
                    'fullName' => $mainVisitorFullName,
                    'country' => $mainVisitor->country,
                    'company_name' => $mainVisitor->company_name,
                    'city' => $mainVisitor->city,
                    'totalAmount' => $mainVisitor->total_amount,
                    'regDateTime' => Carbon::parse($mainVisitor->registered_date_time)->format('M j, Y g:iA'),
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
        return view('livewire.admin.events.transactions.visitor.visitor-registrants-list');
    }

    public function clearFilter(){
        $this->filterByPaymentMethod = null;
        $this->filterByRegStatus = null;
        $this->filterByPayStatus = null;
        $this->filter();
    }

    public function filter()
    {
        if ($this->filterByPaymentMethod == null && $this->filterByRegStatus == null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = $this->finalListOfRegistrantsConst;
        } else if ($this->filterByPaymentMethod != null && $this->filterByRegStatus == null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['paymentMethod'] === ($this->filterByPaymentMethod));
                })->all();
        } else if ($this->filterByPaymentMethod == null && $this->filterByRegStatus != null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['regStatus'] === ($this->filterByRegStatus));
                })->all();
        } else if ($this->filterByPaymentMethod == null && $this->filterByRegStatus == null && $this->filterByPayStatus != null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['payStatus'] === ($this->filterByPayStatus));
                })->all();
        } else if ($this->filterByPaymentMethod != null && $this->filterByRegStatus != null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['paymentMethod'] === ($this->filterByPaymentMethod)) &&
                        strtolower($item['regStatus'] === ($this->filterByRegStatus));
                })->all();
        } else if ($this->filterByPaymentMethod != null && $this->filterByRegStatus == null && $this->filterByPayStatus != null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['paymentMethod'] === ($this->filterByPaymentMethod)) &&
                        strtolower($item['payStatus'] === ($this->filterByPayStatus));
                })->all();
        } else if ($this->filterByPaymentMethod == null && $this->filterByRegStatus != null && $this->filterByPayStatus != null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['regStatus'] === ($this->filterByRegStatus)) &&
                        strtolower($item['payStatus'] === ($this->filterByPayStatus));
                })->all();
        } else {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['paymentMethod'] === ($this->filterByPaymentMethod)) &&
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
                        str_contains(strtolower($item['fullName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['country']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['city']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['totalAmount']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['regDateTime']), strtolower($this->searchTerm));
                })
                ->all();
        }
    }
}
