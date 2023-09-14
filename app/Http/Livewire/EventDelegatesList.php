<?php

namespace App\Http\Livewire;

use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\Event as Events;
use App\Models\PrintedBadge as PrintedBadges;
use App\Models\Transaction as Transactions;
use Carbon\Carbon;
use Livewire\Component;

class EventDelegatesList extends Component
{
    public $event, $searchTerm;

    public $finalListsOfDelegatesTemp = array();
    public $finalListsOfDelegates = array();

    public $printDelegateType, $printDelegateId, $printArrayIndex;

    protected $listeners = ['printBadgeConfirmed' => 'printBadge'];

    public function mount($eventId, $eventCategory)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();

        $mainDelegates = MainDelegates::where('event_id', $eventId)->get();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        if (!$mainDelegates->isEmpty()) {
            foreach ($mainDelegates as $mainDelegate) {
                if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                    if ($mainDelegate->registration_status == "confirmed") {

                        $tempYear = Carbon::parse($mainDelegate->registered_date_time)->format('y');
                        $transactionId = Transactions::where('event_id', $eventId)->where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->value('id');
                        $lastDigit = 1000 + intval($transactionId);

                        $finalTransactionId = $this->event->year . $eventCode . $lastDigit;
                        $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;

                        $printedBadge = PrintedBadges::where('event_id', $eventId)->where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->first();

                        if ($printedBadge != null) {
                            $delegatePrinted = "Yes";
                        } else {
                            $delegatePrinted = "No";
                        }

                        array_push($this->finalListsOfDelegatesTemp, [
                            'mainDelegateId' => $mainDelegate->id,
                            'delegateId' => $mainDelegate->id,
                            'delegateTransactionId' => $finalTransactionId,
                            'delegateInvoiceNumber' => $invoiceNumber,
                            'delegatePrinted' => $delegatePrinted,
                            'delegateType' => "main",
                            'delegateCompany' => $mainDelegate->company_name,
                            'delegateName' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                            'delegateEmailAddress' => $mainDelegate->email_address,
                            'delegateBadgeType' => $mainDelegate->badge_type,
                        ]);
                    }
                }


                $subDelegates = AdditionalDelegates::where('main_delegate_id', $mainDelegate->id)->get();

                if (!$subDelegates->isEmpty()) {
                    foreach ($subDelegates as $subDelegate) {

                        if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {
                            if ($mainDelegate->registration_status == "confirmed") {

                                $tempYear = Carbon::parse($subDelegate->registered_date_time)->format('y');
                                $transactionId = Transactions::where('delegate_id', $subDelegate->id)->where('delegate_type', "sub")->value('id');
                                $lastDigit = 1000 + intval($transactionId);

                                $finalTransactionId = $this->event->year . $eventCode . $lastDigit;
                                $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;

                                $printedBadge = PrintedBadges::where('event_id', $eventId)->where('delegate_id', $subDelegate->id)->where('delegate_type', "sub")->first();

                                if ($printedBadge != null) {
                                    $delegatePrinted = "Yes";
                                } else {
                                    $delegatePrinted = "No";
                                }

                                array_push($this->finalListsOfDelegatesTemp, [
                                    'mainDelegateId' => $mainDelegate->id,
                                    'delegateId' => $subDelegate->id,
                                    'delegateTransactionId' => $finalTransactionId,
                                    'delegateInvoiceNumber' => $invoiceNumber,
                                    'delegatePrinted' => $delegatePrinted,
                                    'delegateType' => "sub",
                                    'delegateCompany' => $mainDelegate->company_name,
                                    'delegateName' => $subDelegate->salutation . " " . $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name,
                                    'delegateEmailAddress' => $subDelegate->email_address,
                                    'delegateBadgeType' => $subDelegate->badge_type,
                                ]);
                            }
                        }
                    }
                }
            }
        }
        // dd($this->finalListsOfDelegatesTemp);
    }

    public function render()
    {
        if (empty($this->searchTerm)) {
            $this->finalListsOfDelegates = $this->finalListsOfDelegatesTemp;
        } else {
            $this->finalListsOfDelegates = collect($this->finalListsOfDelegatesTemp)
                ->filter(function ($item) {
                    return str_contains(strtolower($item['delegateCompany']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateEmailAddress']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateTransactionId']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateInvoiceNumber']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateBadgeType']), strtolower($this->searchTerm));
                })
                ->all();
        }
        return view('livewire.admin.events.delegates.event-delegates-list');
    }


    public function printBadgeClicked($delegateType, $delegateId, $arrayIndex)
    {
        $this->printDelegateType = $delegateType;
        $this->printDelegateId = $delegateId;
        $this->printArrayIndex = $arrayIndex;

        $this->dispatchBrowserEvent('swal:print-badge-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);
    }

    public function printBadge()
    {
        PrintedBadges::create([
            'event_id' => $this->event->id,
            'event_category' => $this->event->category,
            'delegate_id' => $this->printDelegateId,
            'delegate_type' => $this->printDelegateType,
            'printed_date_time' => Carbon::now(),
        ]);

        $this->finalListsOfDelegates[$this->printArrayIndex]['delegatePrinted'] = "Yes";
        $this->finalListsOfDelegatesTemp[$this->printArrayIndex]['delegatePrinted'] = "Yes";

        $this->dispatchBrowserEvent('swal:print-badge-confirmed', [
            'url' => route('admin.event.delegates.detail.printBadge', ['eventCategory' => $this->event->category, 'eventId' => $this->event->id, 'delegateId' => $this->printDelegateId, 'delegateType' => $this->printDelegateType]),
            
            'type' => 'success',
            'message' => 'Badge Printed Successfully!',
            'text' => ''
        ]);

        $this->printDelegateType = null;
        $this->printDelegateId = null;
        $this->printArrayIndex = null;
    }
}
