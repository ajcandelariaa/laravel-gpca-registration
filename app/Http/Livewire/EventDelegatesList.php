<?php

namespace App\Http\Livewire;

use App\Models\MainDelegate as MainDelegates;
use App\Models\Event as Events;
use App\Models\PrintedBadge as PrintedBadges;
use Carbon\Carbon;
use Livewire\Component;

class EventDelegatesList extends Component
{
    public $event, $searchTerm;

    public $finalListsOfDelegatesTemp = array();
    public $finalListsOfDelegates = array();

    public $printDelegateType, $printDelegateId, $printArrayIndex;

    public $badgeView = false;

    public $name, $company, $jobTitle, $registrationType, $badgeViewFFText, $badgeViewFBText, $badgeViewFFBGColor, $badgeViewFBBGColor, $badgeViewFFTextColor, $badgeViewFBTextColor;

    protected $listeners = ['printBadgeConfirmed' => 'printBadge', 'broadcastEmailConfirmed' => 'sendBroadcastEmail'];

    public function mount($eventId, $eventCategory)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();

        $mainDelegates = MainDelegates::with(['additionalDelegates', 'transaction', 'printedBadge', 'scannedBadge'])->where('event_id', $eventId)->get();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        if (!$mainDelegates->isEmpty()) {
            foreach ($mainDelegates as $mainDelegate) {
                $companyName = "";

                if ($mainDelegate->alternative_company_name != null) {
                    $companyName = $mainDelegate->alternative_company_name;
                } else {
                    $companyName = $mainDelegate->company_name;
                }

                if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                    if ($mainDelegate->registration_status == "confirmed") {

                        $tempYear = Carbon::parse($mainDelegate->registered_date_time)->format('y');
                        $transactionId = $mainDelegate->transaction->id;
                        $lastDigit = 1000 + intval($transactionId);

                        $finalTransactionId = $this->event->year . $eventCode . $lastDigit;
                        $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;

                        $delegatePrinted = $mainDelegate->printedBadge ? "Yes" : "No";
                        $delegateScanned = $mainDelegate->scannedBadge ? "Yes" : "No";

                        array_push($this->finalListsOfDelegatesTemp, [
                            'mainDelegateId' => $mainDelegate->id,
                            'delegateId' => $mainDelegate->id,
                            'delegateTransactionId' => $finalTransactionId,
                            'delegateInvoiceNumber' => $invoiceNumber,
                            'delegatePrinted' => $delegatePrinted,
                            'delegateScanned' => $delegateScanned,
                            'delegateType' => "main",
                            'delegateCompany' => $companyName,
                            'delegateJobTitle' => $mainDelegate->job_title,
                            'delegateSalutation' => $mainDelegate->salutation,
                            'delegateFName' => $mainDelegate->first_name,
                            'delegateMName' => $mainDelegate->middle_name,
                            'delegateLName' => $mainDelegate->last_name,
                            'delegateEmailAddress' => $mainDelegate->email_address,
                            'delegateBadgeType' => $mainDelegate->badge_type,
                        ]);
                    }
                }


                if (!$mainDelegate->additionalDelegates->isEmpty()) {
                    foreach ($mainDelegate->additionalDelegates as $subDelegate) {

                        if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {
                            if ($mainDelegate->registration_status == "confirmed") {

                                $tempYear = Carbon::parse($subDelegate->registered_date_time)->format('y');
                                $transactionId = $subDelegate->transaction->id;
                                $lastDigit = 1000 + intval($transactionId);
                                $finalTransactionId = $this->event->year . $eventCode . $lastDigit;

                                $transactionId2 = $mainDelegate->transaction->id;
                                $lastDigit2 = 1000 + intval($transactionId2);
                                $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit2;

                                $delegatePrinted = $subDelegate->printedBadge ? "Yes" : "No";
                                $delegateScanned = $subDelegate->scannedBadge ? "Yes" : "No";

                                array_push($this->finalListsOfDelegatesTemp, [
                                    'mainDelegateId' => $mainDelegate->id,
                                    'delegateId' => $subDelegate->id,
                                    'delegateTransactionId' => $finalTransactionId,
                                    'delegateInvoiceNumber' => $invoiceNumber,
                                    'delegatePrinted' => $delegatePrinted,
                                    'delegateScanned' => $delegateScanned,
                                    'delegateType' => "sub",
                                    'delegateCompany' => $companyName,
                                    'delegateJobTitle' => $subDelegate->job_title,
                                    'delegateSalutation' => $subDelegate->salutation,
                                    'delegateFName' => $subDelegate->first_name,
                                    'delegateMName' => $subDelegate->middle_name,
                                    'delegateLName' => $subDelegate->last_name,
                                    'delegateEmailAddress' => $subDelegate->email_address,
                                    'delegateBadgeType' => $subDelegate->badge_type,
                                ]);
                            }
                        }
                    }
                }
            }
        }
        $this->finalListsOfDelegates = $this->finalListsOfDelegatesTemp;
    }

    public function render()
    {
        return view('livewire.admin.events.delegates.event-delegates-list');
    }

    public function search()
    {
        if (empty($this->searchTerm)) {
            $this->finalListsOfDelegates = $this->finalListsOfDelegatesTemp;
        } else {
            $filteredData = collect($this->finalListsOfDelegatesTemp)
                ->filter(function ($item) {
                    return str_contains(strtolower($item['delegateCompany']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateSalutation']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateFName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateMName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateLName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateJobTitle']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateEmailAddress']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateTransactionId']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateInvoiceNumber']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateBadgeType']), strtolower($this->searchTerm));
                })
                ->all();

            $this->finalListsOfDelegates = $filteredData;
        }
    }

    public function sendBroadcastEmailShow()
    {
        $this->dispatchBrowserEvent('swal:broadcast-email-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);
    }

    public function sendBroadcastEmail()
    {
        $this->dispatchBrowserEvent('swal:broadcast-email-success', [
            'type' => 'success',
            'message' => 'Broadcast Email Notification Sent!',
            'text' => "",
        ]);
        dd($this->finalListsOfDelegates);
    }


    public function markAsPrint($arrayIndex){
        $delegateId = $this->finalListsOfDelegates[$arrayIndex]['delegateId'];
        $delegateType = $this->finalListsOfDelegates[$arrayIndex]['delegateType'];

        PrintedBadges::create([
            'event_id' => $this->event->id,
            'event_category' => $this->event->category,
            'delegate_id' => $delegateId,
            'delegate_type' => $delegateType,
            'printed_date_time' => Carbon::now(),
        ]);

        $this->finalListsOfDelegates[$arrayIndex]['delegatePrinted'] = "Yes";
        $this->finalListsOfDelegatesTemp[$arrayIndex]['delegatePrinted'] = "Yes";
    }









    // public function printBadgeClicked($delegateType, $delegateId, $arrayIndex)
    // {
    //     $this->printDelegateType = $delegateType;
    //     $this->printDelegateId = $delegateId;
    //     $this->printArrayIndex = $arrayIndex;

    //     $this->dispatchBrowserEvent('swal:print-badge-confirmation', [
    //         'type' => 'warning',
    //         'message' => 'Are you sure?',
    //         'text' => "",
    //     ]);
    // }

    // public function printBadge()
    // {
    //     PrintedBadges::create([
    //         'event_id' => $this->event->id,
    //         'event_category' => $this->event->category,
    //         'delegate_id' => $this->printDelegateId,
    //         'delegate_type' => $this->printDelegateType,
    //         'printed_date_time' => Carbon::now(),
    //     ]);

    //     $this->finalListsOfDelegates[$this->printArrayIndex]['delegatePrinted'] = "Yes";
    //     $this->finalListsOfDelegatesTemp[$this->printArrayIndex]['delegatePrinted'] = "Yes";

    //     $this->dispatchBrowserEvent('swal:print-badge-confirmed', [
    //         'url' => route('admin.event.delegates.detail.printBadge', ['eventCategory' => $this->event->category, 'eventId' => $this->event->id, 'delegateId' => $this->printDelegateId, 'delegateType' => $this->printDelegateType]),

    //         'type' => 'success',
    //         'message' => 'Badge Printed Successfully!',
    //         'text' => ''
    //     ]);

    //     $this->printDelegateType = null;
    //     $this->printDelegateId = null;
    //     $this->printArrayIndex = null;
    // }

    // public function previewBadge($delegateIndex)
    // {

    //     if ($this->finalListsOfDelegates[$delegateIndex]['delegateSalutation'] == "Dr." || $this->finalListsOfDelegates[$delegateIndex]['delegateSalutation'] == "Prof.") {
    //         $this->name = $this->finalListsOfDelegates[$delegateIndex]['delegateSalutation'] . ' ' . $this->finalListsOfDelegates[$delegateIndex]['delegateFName'] . ' ' . $this->finalListsOfDelegates[$delegateIndex]['delegateMName'] . ' ' . $this->finalListsOfDelegates[$delegateIndex]['delegateLName'];
    //     } else {
    //         $this->name = $this->finalListsOfDelegates[$delegateIndex]['delegateFName'] . ' ' . $this->finalListsOfDelegates[$delegateIndex]['delegateMName'] . ' ' . $this->finalListsOfDelegates[$delegateIndex]['delegateLName'];
    //     }

    //     $this->jobTitle = $this->finalListsOfDelegates[$delegateIndex]['delegateJobTitle'];
    //     $this->company = $this->finalListsOfDelegates[$delegateIndex]['delegateCompany'];
    //     $this->registrationType = $this->finalListsOfDelegates[$delegateIndex]['delegateBadgeType'];

    //     $registrationType = EventRegistrationTypes::where('event_id', $this->event->id)->where('registration_type', $this->registrationType)->first();

    //     $this->badgeViewFFText = $registrationType->badge_footer_front_name;
    //     $this->badgeViewFBText = $registrationType->badge_footer_back_name;

    //     $this->badgeViewFFBGColor = $registrationType->badge_footer_front_bg_color;
    //     $this->badgeViewFBBGColor = $registrationType->badge_footer_back_bg_color;

    //     $this->badgeViewFFTextColor = $registrationType->badge_footer_front_text_color;
    //     $this->badgeViewFBTextColor = $registrationType->badge_footer_back_text_color;

    //     $this->badgeView = true;
    // }


    // public function closePreviewBadge()
    // {
    //     $this->name = null;
    //     $this->jobTitle = null;
    //     $this->company = null;
    //     $this->registrationType = null;

    //     $this->badgeViewFFText = null;
    //     $this->badgeViewFBText = null;

    //     $this->badgeViewFFBGColor = null;
    //     $this->badgeViewFBBGColor = null;

    //     $this->badgeViewFFTextColor = null;
    //     $this->badgeViewFBTextColor = null;

    //     $this->badgeView = false;
    // }
}
