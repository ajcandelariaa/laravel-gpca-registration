<?php

namespace App\Http\Livewire;

use App\Models\Event as Events;
use App\Models\MainSpouse as MainSpouses;
use App\Models\SpouseTransaction as SpouseTransactions;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

class SpouseRegistrantsList extends Component
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

        $mainSpouses = MainSpouses::where('event_id', $this->eventId)->orderBy('registered_date_time', 'DESC')->get();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->event->category == $eventCategoryC) {
                $this->getEventCode = $code;
            }
        }

        if ($mainSpouses->isNotEmpty()) {
            foreach ($mainSpouses as $mainSpouse) {

                // get invoice number & transaction id
                $transactionId = SpouseTransactions::where('spouse_id', $mainSpouse->id)->where('spouse_type', "main")->value('id');
                $tempYear = Carbon::parse($mainSpouse->registered_date_time)->format('y');
                $lastDigit = 1000 + intval($transactionId);
                $tempInvoiceNumber = $this->event->category . $tempYear . "/" . $lastDigit;

                // get reg status
                if ($mainSpouse->registration_status == 'confirmed') {
                    $regStatus = "Confirmed";
                } else if ($mainSpouse->registration_status == 'pending') {
                    $regStatus = "Pending";
                } else if ($mainSpouse->registration_status == 'droppedOut') {
                    $regStatus = "Dropped out";
                } else {
                    $regStatus = "Cancelled";
                }

                // get payment status
                if ($mainSpouse->payment_status == 'paid') {
                    $payStatus = "Paid";
                } else if ($mainSpouse->payment_status == 'free') {
                    $payStatus = "Free";
                } else if ($mainSpouse->payment_status == 'unpaid') {
                    $payStatus = "Unpaid";
                } else {
                    $payStatus = "Refunded";
                }

                if($mainSpouse->mode_of_payment == "bankTransfer"){
                    $paymentMethod = "Bank Transfer";
                } else {
                    $paymentMethod = "Credit Card";
                }

                $mainSpouseFullName = $mainSpouse->salutation . " " . $mainSpouse->first_name . " " . $mainSpouse->middle_name . " " . $mainSpouse->last_name;

                array_push($this->finalListOfRegistrants, [
                    'mainSpouseId' => $mainSpouse->id,
                    'invoiceNumber' => $tempInvoiceNumber,
                    'fullName' => $mainSpouseFullName,
                    'country' => $mainSpouse->country,
                    'city' => $mainSpouse->city,
                    'totalAmount' => $mainSpouse->total_amount,
                    'regDateTime' => Carbon::parse($mainSpouse->registered_date_time)->format('M j, Y g:iA'),
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
        return view('livewire.admin.events.transactions.spouse.spouse-registrants-list');
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
