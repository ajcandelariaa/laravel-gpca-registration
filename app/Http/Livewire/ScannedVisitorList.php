<?php

namespace App\Http\Livewire;

use App\Models\Event as Events;
use App\Models\ScannedVisitor as ScannedVisitors;
use App\Models\VisitorTransaction as VisitorTransactions;
use App\Models\MainVisitor as MainVisitors;
use App\Models\AdditionalVisitor as AdditionalVisitors;
use Carbon\Carbon;
use Livewire\Component;

class ScannedVisitorList extends Component
{
    public $event, $searchTerm;

    public $finalListsOfVisitorsTemp = array();
    public $finalListsOfVisitors = array();

    public function mount($eventCategory, $eventId)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        $scannedVisitors = ScannedVisitors::where('event_id', $eventId)->get();

        if($scannedVisitors->isNotEmpty()){
            foreach ($scannedVisitors as $scannedVisitor) {
                if($scannedVisitor->visitor_type == "main"){

                    $mainVisitor = MainVisitors::where('id', $scannedVisitor->visitor_id)->first();

                    $tempYear = Carbon::parse($mainVisitor->registered_date_time)->format('y');
                    $transactionId = VisitorTransactions::where('event_id', $eventId)->where('visitor_id', $mainVisitor->id)->where('visitor_type', "main")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    $finalTransactionId = $this->event->year . $eventCode . $lastDigit;
                    $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;

                    $result = $this->checkIfBadgeScannedExist($finalTransactionId, $this->finalListsOfVisitorsTemp);
                    if ($result[0]) {
                        $this->finalListsOfVisitorsTemp[$result[1]]['visitorScannedCount'] += 1;
                        $this->finalListsOfVisitorsTemp[$result[1]]['visitorScannedDateTime'] = $scannedVisitor->scanned_date_time;
                    } else {

                        if($mainVisitor->alternative_company_name != null){
                            $companyName = $mainVisitor->alternative_company_name;
                        } else {
                            $companyName = $mainVisitor->company_name;
                        }

                        array_push($this->finalListsOfVisitorsTemp, [
                            'mainVisitorId' => $mainVisitor->id,
                            'visitorId' => $mainVisitor->id,
                            'visitorTransactionId' => $finalTransactionId,
                            'visitorInvoiceNumber' => $invoiceNumber,
                            'visitorType' => "main",
                            'visitorCompany' => $companyName,
                            'visitorName' => $mainVisitor->salutation . " " . $mainVisitor->first_name . " " . $mainVisitor->middle_name . " " . $mainVisitor->last_name,
                            'visitorEmailAddress' => $mainVisitor->email_address,
                            'visitorBadgeType' => $mainVisitor->badge_type,
                            'visitorScannedCount' => 1,
                            'visitorScannedDateTime' => $scannedVisitor->scanned_date_time,
                        ]);
                    }

                } else {
                    $additionalVisitor = AdditionalVisitors::where('id', $scannedVisitor->visitor_id)->first();

                    $tempYear = Carbon::parse($additionalVisitor->registered_date_time)->format('y');
                    $transactionId = VisitorTransactions::where('event_id', $eventId)->where('visitor_id', $additionalVisitor->id)->where('visitor_type', "sub")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    $finalTransactionId = $this->event->year . $eventCode . $lastDigit;

                    $transactionId2 = VisitorTransactions::where('event_id', $eventId)->where('visitor_id', $additionalVisitor->main_visitor_id)->where('visitor_type', "main")->value('id');
                    $lastDigit2 = 1000 + intval($transactionId2);
                    $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit2;

                    $mainVisitor = MainVisitors::where('id', $additionalVisitor->main_visitor_id)->first();

                    if($mainVisitor->alternative_company_name != null){
                        $mainVisitorCompany = $mainVisitor->alternative_company_name;
                    } else {
                        $mainVisitorCompany = $mainVisitor->company_name;
                    }

                    $result = $this->checkIfBadgeScannedExist($finalTransactionId, $this->finalListsOfVisitorsTemp);
                    if ($result[0]) {
                        $this->finalListsOfVisitorsTemp[$result[1]]['visitorScannedCount'] += 1;
                        $this->finalListsOfVisitorsTemp[$result[1]]['visitorScannedDateTime'] = $scannedVisitor->scanned_date_time;
                    } else {
                        array_push($this->finalListsOfVisitorsTemp, [
                            'mainVisitorId' => $additionalVisitor->main_visitor_id,
                            'visitorId' => $additionalVisitor->id,
                            'visitorTransactionId' => $finalTransactionId,
                            'visitorInvoiceNumber' => $invoiceNumber,
                            'visitorType' => "sub",
                            'visitorCompany' => $mainVisitorCompany,
                            'visitorName' => $additionalVisitor->salutation . " " . $additionalVisitor->first_name . " " . $additionalVisitor->middle_name . " " . $additionalVisitor->last_name,
                            'visitorEmailAddress' => $additionalVisitor->email_address,
                            'visitorBadgeType' => $additionalVisitor->badge_type,
                            'visitorScannedCount' => 1,
                            'visitorScannedDateTime' => $scannedVisitor->scanned_date_time,
                        ]);
                    }
                }
            }
        }
    }

    public function render()
    {
        if (empty($this->searchTerm)) {
            $this->finalListsOfVisitors = $this->finalListsOfVisitorsTemp;
        } else {
            $this->finalListsOfVisitors = collect($this->finalListsOfVisitorsTemp)
                ->filter(function ($item) {
                    return str_contains(strtolower($item['visitorCompany']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorEmailAddress']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorTransactionId']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorInvoiceNumber']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorBadgeType']), strtolower($this->searchTerm));
                })
                ->all();
        }
        return view('livewire.admin.events.scanned-delegate.scanned-visitor-list');
    }

    public function checkIfBadgeScannedExist($transactionId, $scannedVisitors)
    {
        $checker = 0;
        $index = null;
        foreach ($scannedVisitors as $scannedVisitorIndex => $scannedVisitor) {
            if ($scannedVisitor['visitorTransactionId'] == $transactionId) {
                $checker++;
                $index = $scannedVisitorIndex;
            }
        }

        if ($checker > 0) {
            return [true, $index];
        } else {
            return [false, $index];
        }
    }
}