<?php

namespace App\Http\Livewire;

use App\Models\Event as Events;
use App\Models\PrintedBadge as PrintedBadges;
use App\Models\Transaction as Transactions;
use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use Carbon\Carbon;
use Livewire\Component;

class PrintedBadgeList extends Component
{
    public $event, $searchTerm;

    public $finalListsOfDelegatesTemp = array();
    public $finalListsOfDelegates = array();

    public function mount($eventCategory, $eventId)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        $printedBadges = PrintedBadges::where('event_id', $eventId)->get();

        if($printedBadges->isNotEmpty()){
            foreach ($printedBadges as $printedBadge) {
                if($printedBadge->delegate_type == "main"){

                    $mainDelegate = MainDelegates::where('id', $printedBadge->delegate_id)->first();

                    $tempYear = Carbon::parse($mainDelegate->registered_date_time)->format('y');
                    $transactionId = Transactions::where('event_id', $eventId)->where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    $finalTransactionId = $this->event->year . $eventCode . $lastDigit;
                    $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;

                    $result = $this->checkIfBadgePrintedExist($finalTransactionId, $this->finalListsOfDelegatesTemp);
                    if ($result[0]) {
                        $this->finalListsOfDelegatesTemp[$result[1]]['delegatePrintBadgeCount'] += 1;
                        $this->finalListsOfDelegatesTemp[$result[1]]['delegatePrintedDateTime'] = $printedBadge->printed_date_time;
                    } else {

                        if($mainDelegate->alternative_company_name != null){
                            $companyName = $mainDelegate->alternative_company_name;
                        } else {
                            $companyName = $mainDelegate->company_name;
                        }

                        array_push($this->finalListsOfDelegatesTemp, [
                            'mainDelegateId' => $mainDelegate->id,
                            'delegateId' => $mainDelegate->id,
                            'delegateTransactionId' => $finalTransactionId,
                            'delegateInvoiceNumber' => $invoiceNumber,
                            'delegateType' => "main",
                            'delegateCompany' => $companyName,
                            'delegateName' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                            'delegateEmailAddress' => $mainDelegate->email_address,
                            'delegateBadgeType' => $mainDelegate->badge_type,
                            'delegatePrintBadgeCount' => 1,
                            'delegatePrintedDateTime' => $printedBadge->printed_date_time,
                            'delegateBadgeIsCollected' => $printedBadge->collected,
                            'delegateBadgeCollectedBy' => $printedBadge->collected_by ?? "N/A",
                        ]);
                    }

                } else {
                    $additionalDelegate = AdditionalDelegates::where('id', $printedBadge->delegate_id)->first();

                    $tempYear = Carbon::parse($additionalDelegate->registered_date_time)->format('y');
                    $transactionId = Transactions::where('event_id', $eventId)->where('delegate_id', $additionalDelegate->id)->where('delegate_type', "sub")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    $finalTransactionId = $this->event->year . $eventCode . $lastDigit;

                    $transactionId2 = Transactions::where('event_id', $eventId)->where('delegate_id', $additionalDelegate->main_delegate_id)->where('delegate_type', "main")->value('id');
                    $lastDigit2 = 1000 + intval($transactionId2);
                    $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit2;

                    $mainDelegate = MainDelegates::where('id', $additionalDelegate->main_delegate_id)->first();

                    if($mainDelegate->alternative_company_name != null){
                        $mainDelegateCompany = $mainDelegate->alternative_company_name;
                    } else {
                        $mainDelegateCompany = $mainDelegate->company_name;
                    }

                    $result = $this->checkIfBadgePrintedExist($finalTransactionId, $this->finalListsOfDelegatesTemp);
                    if ($result[0]) {
                        $this->finalListsOfDelegatesTemp[$result[1]]['delegatePrintBadgeCount'] += 1;
                        $this->finalListsOfDelegatesTemp[$result[1]]['delegatePrintedDateTime'] = $printedBadge->printed_date_time;
                    } else {
                        array_push($this->finalListsOfDelegatesTemp, [
                            'mainDelegateId' => $additionalDelegate->main_delegate_id,
                            'delegateId' => $additionalDelegate->id,
                            'delegateTransactionId' => $finalTransactionId,
                            'delegateInvoiceNumber' => $invoiceNumber,
                            'delegateType' => "sub",
                            'delegateCompany' => $mainDelegateCompany,
                            'delegateName' => $additionalDelegate->salutation . " " . $additionalDelegate->first_name . " " . $additionalDelegate->middle_name . " " . $additionalDelegate->last_name,
                            'delegateEmailAddress' => $additionalDelegate->email_address,
                            'delegateBadgeType' => $additionalDelegate->badge_type,
                            'delegatePrintBadgeCount' => 1,
                            'delegatePrintedDateTime' => $printedBadge->printed_date_time,
                            'delegateBadgeIsCollected' => $printedBadge->collected,
                            'delegateBadgeCollectedBy' => $printedBadge->collected_by ?? "N/A",
                        ]);
                    }
                }
            }
        }
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
        return view('livewire.admin.events.printed-badge.printed-badge-list');
    }

    public function checkIfBadgePrintedExist($transactionId, $printedBadges)
    {
        $checker = 0;
        $index = null;
        foreach ($printedBadges as $printedBadgeIndex => $printedBadge) {
            if ($printedBadge['delegateTransactionId'] == $transactionId) {
                $checker++;
                $index = $printedBadgeIndex;
            }
        }

        if ($checker > 0) {
            return [true, $index];
        } else {
            return [false, $index];
        }
    }
}
