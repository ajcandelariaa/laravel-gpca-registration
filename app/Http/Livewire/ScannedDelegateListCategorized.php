<?php

namespace App\Http\Livewire;

use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\Event as Events;
use App\Models\ScannedDelegate as ScannedDelegates;
use App\Models\Transaction as Transactions;
use Illuminate\Support\Carbon;
use Livewire\Component;

class ScannedDelegateListCategorized extends Component
{
    public $event;
    public $currentDay, $currentDayCategory, $currentDate, $currentStartTime, $currentEndTime;
    public $finalListScannedDelegates = array();
    public $currentListOfDelegates = array();
    public $choices = array();

    public function mount($eventCategory, $eventId)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();
        $this->currentDay = null;
        $this->currentDayCategory = null;
        $this->currentDate = null;
        $this->currentStartTime = null;
        $this->currentEndTime = null;
        $this->choices = config('app.scanTimings.2024.ANC');

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        $scannedDelegates = ScannedDelegates::where('event_id', $eventId)->get();

        if ($scannedDelegates->isNotEmpty()) {
            foreach ($scannedDelegates as $scannedDelegate) {
                if ($scannedDelegate->delegate_type == "main") {

                    $mainDelegate = MainDelegates::where('id', $scannedDelegate->delegate_id)->first();

                    $tempYear = Carbon::parse($mainDelegate->registered_date_time)->format('y');
                    $transactionId = Transactions::where('event_id', $eventId)->where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    $finalTransactionId = $this->event->year . $eventCode . $lastDigit;
                    $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;

                    if ($mainDelegate->alternative_company_name != null) {
                        $companyName = $mainDelegate->alternative_company_name;
                    } else {
                        $companyName = $mainDelegate->company_name;
                    }

                    $carbonDateTime = Carbon::parse($scannedDelegate->scanned_date_time);

                    array_push($this->finalListScannedDelegates, [
                        'mainDelegateId' => $mainDelegate->id,
                        'delegateId' => $mainDelegate->id,
                        'delegateTransactionId' => $finalTransactionId,
                        'delegateInvoiceNumber' => $invoiceNumber,
                        'delegateType' => "main",
                        'delegateCompany' => $companyName,
                        'delegateName' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                        'delegateEmailAddress' => $mainDelegate->email_address,
                        'delegateBadgeType' => $mainDelegate->badge_type,
                        'delegateScannedDate' => $carbonDateTime->toDateString(),
                        'delegateScannedTime' => $carbonDateTime->toTimeString(),
                        'location' => $scannedDelegate->scanner_location ?? 'N/A',
                    ]);
                } else {
                    $additionalDelegate = AdditionalDelegates::where('id', $scannedDelegate->delegate_id)->first();

                    $tempYear = Carbon::parse($additionalDelegate->registered_date_time)->format('y');
                    $transactionId = Transactions::where('event_id', $eventId)->where('delegate_id', $additionalDelegate->id)->where('delegate_type', "sub")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    $finalTransactionId = $this->event->year . $eventCode . $lastDigit;

                    $transactionId2 = Transactions::where('event_id', $eventId)->where('delegate_id', $additionalDelegate->main_delegate_id)->where('delegate_type', "main")->value('id');
                    $lastDigit2 = 1000 + intval($transactionId2);
                    $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit2;

                    $mainDelegate = MainDelegates::where('id', $additionalDelegate->main_delegate_id)->first();

                    if ($mainDelegate->alternative_company_name != null) {
                        $mainDelegateCompany = $mainDelegate->alternative_company_name;
                    } else {
                        $mainDelegateCompany = $mainDelegate->company_name;
                    }

                    $carbonDateTime = Carbon::parse($scannedDelegate->scanned_date_time);

                    array_push($this->finalListScannedDelegates, [
                        'mainDelegateId' => $additionalDelegate->main_delegate_id,
                        'delegateId' => $additionalDelegate->id,
                        'delegateTransactionId' => $finalTransactionId,
                        'delegateInvoiceNumber' => $invoiceNumber,
                        'delegateType' => "sub",
                        'delegateCompany' => $mainDelegateCompany,
                        'delegateName' => $additionalDelegate->salutation . " " . $additionalDelegate->first_name . " " . $additionalDelegate->middle_name . " " . $additionalDelegate->last_name,
                        'delegateEmailAddress' => $additionalDelegate->email_address,
                        'delegateBadgeType' => $additionalDelegate->badge_type,
                        'delegateScannedCount' => 1,
                        'delegateScannedDate' => $carbonDateTime->toDateString(),
                        'delegateScannedTime' => $carbonDateTime->toTimeString(),
                        'location' => $scannedDelegate->scanner_location ?? 'N/A',
                    ]);
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.events.scanned-delegate.categorized.scanned-delegate-list-categorized');
    }

    public function selectDay($selectedDay)
    {
        if ($this->currentDay == $selectedDay) {
            $this->currentDay = null;
        } else {
            $this->currentDay = $selectedDay;
        }
    }

    public function selectDayCategory($selectedDayCategory)
    {
        if ($this->currentDayCategory == $selectedDayCategory) {
            $this->currentDayCategory = null;
        } else {
            $this->currentDayCategory = $selectedDayCategory;
            $this->currentDate = $this->choices[$this->currentDay][$this->currentDayCategory]['date'];
            $this->currentStartTime = $this->choices[$this->currentDay][$this->currentDayCategory]['start_time'];
            $this->currentEndTime = $this->choices[$this->currentDay][$this->currentDayCategory]['end_time'];

            $delegateArrayTemp = array();

            $startTime = Carbon::parse($this->currentStartTime)->format('H:i:s');
            $endTime = Carbon::parse($this->currentEndTime)->format('H:i:s');

            foreach ($this->finalListScannedDelegates as $finalListScannedDelegate) {
                if ($this->currentDate == $finalListScannedDelegate['delegateScannedDate']) {
                    $delegateScannedTime = Carbon::parse($finalListScannedDelegate['delegateScannedTime'])->format('H:i:s');
                    if ($delegateScannedTime >= $startTime && $delegateScannedTime < $endTime) {
                        array_push($delegateArrayTemp, $finalListScannedDelegate);
                    }
                }
            }

            $this->currentListOfDelegates = $delegateArrayTemp;
        }
    }
}
