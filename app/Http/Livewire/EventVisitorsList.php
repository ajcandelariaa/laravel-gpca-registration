<?php

namespace App\Http\Livewire;

use App\Models\MainVisitor as MainVisitors;
use App\Models\AdditionalVisitor as AdditionalVisitors;
use App\Models\Event as Events;
use App\Models\EventRegistrationType as EventRegistrationTypes;
use App\Models\VisitorPrintedBadge as VisitorPrintedBadges;
use App\Models\ScannedVisitor as ScannedVisitors;
use App\Models\VisitorTransaction as VisitorTransactions;
use Carbon\Carbon;
use Livewire\Component;

class EventVisitorsList extends Component
{
    public $event, $searchTerm;

    public $finalListsOfVisitorsTemp = array();
    public $finalListsOfVisitors = array();

    public $printVisitorType, $printVisitorId, $printArrayIndex;

    public $badgeView = false;

    public $name, $company, $jobTitle, $registrationType, $badgeViewFFText, $badgeViewFBText, $badgeViewFFBGColor, $badgeViewFBBGColor, $badgeViewFFTextColor, $badgeViewFBTextColor;

    protected $listeners = ['printBadgeConfirmed' => 'printBadge', 'broadcastEmailConfirmed' => 'sendBroadcastEmail'];

    public function mount($eventId, $eventCategory)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();

        $mainVisitors = MainVisitors::where('event_id', $eventId)->get();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        if (!$mainVisitors->isEmpty()) {
            foreach ($mainVisitors as $mainVisitor) {
                $companyName = "";
                
                if ($mainVisitor->visitor_replaced_by_id == null && (!$mainVisitor->visitor_refunded)) {
                    if ($mainVisitor->registration_status == "confirmed") {

                        $tempYear = Carbon::parse($mainVisitor->registered_date_time)->format('y');
                        $transactionId = VisitorTransactions::where('event_id', $eventId)->where('visitor_id', $mainVisitor->id)->where('visitor_type', "main")->value('id');
                        $lastDigit = 1000 + intval($transactionId);

                        $finalTransactionId = $this->event->year . $eventCode . $lastDigit;
                        $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;

                        $printedBadge = VisitorPrintedBadges::where('event_id', $eventId)->where('visitor_id', $mainVisitor->id)->where('visitor_type', "main")->first();

                        if ($printedBadge != null) {
                            $visitorPrinted = "Yes";
                        } else {
                            $visitorPrinted = "No";
                        }

                        $scannedBadge = ScannedVisitors::where('event_id', $eventId)->where('visitor_id', $mainVisitor->id)->where('visitor_type', "main")->first();

                        if ($scannedBadge != null) {
                            $visitorScanned = "Yes";
                        } else {
                            $visitorScanned = "No";
                        }

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
                            'visitorPrinted' => $visitorPrinted,
                            'visitorScanned' => $visitorScanned,
                            'visitorType' => "main",
                            'visitorCompany' => $companyName,
                            'visitorJobTitle' => $mainVisitor->job_title,
                            'visitorSalutation' => $mainVisitor->salutation,
                            'visitorFName' => $mainVisitor->first_name,
                            'visitorMName' => $mainVisitor->middle_name,
                            'visitorLName' => $mainVisitor->last_name,
                            'visitorEmailAddress' => $mainVisitor->email_address,
                            'visitorBadgeType' => $mainVisitor->badge_type,
                        ]);
                    }
                }


                $subVisitors = AdditionalVisitors::where('main_visitor_id', $mainVisitor->id)->get();

                if (!$subVisitors->isEmpty()) {
                    foreach ($subVisitors as $subVisitor) {

                        if ($subVisitor->visitor_replaced_by_id == null && (!$subVisitor->visitor_refunded)) {
                            if ($mainVisitor->registration_status == "confirmed") {

                                $tempYear = Carbon::parse($subVisitor->registered_date_time)->format('y');
                                $transactionId = VisitorTransactions::where('visitor_id', $subVisitor->id)->where('visitor_type', "sub")->value('id');
                                $lastDigit = 1000 + intval($transactionId);
                                $finalTransactionId = $this->event->year . $eventCode . $lastDigit;

                                $transactionId2 = VisitorTransactions::where('event_id', $eventId)->where('visitor_id', $subVisitor->main_visitor_id)->where('visitor_type', "main")->value('id');
                                $lastDigit2 = 1000 + intval($transactionId2);
                                $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit2;

                                $printedBadge = VisitorPrintedBadges::where('event_id', $eventId)->where('visitor_id', $subVisitor->id)->where('visitor_type', "sub")->first();

                                if ($printedBadge != null) {
                                    $visitorPrinted = "Yes";
                                } else {
                                    $visitorPrinted = "No";
                                }

                                $scannedBadge = ScannedVisitors::where('event_id', $eventId)->where('visitor_id', $subVisitor->id)->where('visitor_type', "sub")->first();

                                if ($scannedBadge != null) {
                                    $visitorScanned = "Yes";
                                } else {
                                    $visitorScanned = "No";
                                }

                                array_push($this->finalListsOfVisitorsTemp, [
                                    'mainVisitorId' => $mainVisitor->id,
                                    'visitorId' => $subVisitor->id,
                                    'visitorTransactionId' => $finalTransactionId,
                                    'visitorInvoiceNumber' => $invoiceNumber,
                                    'visitorPrinted' => $visitorPrinted,
                                    'visitorScanned' => $visitorPrinted,
                                    'visitorType' => "sub",
                                    'visitorCompany' => $companyName,
                                    'visitorJobTitle' => $subVisitor->job_title,
                                    'visitorSalutation' => $subVisitor->salutation,
                                    'visitorFName' => $subVisitor->first_name,
                                    'visitorMName' => $subVisitor->middle_name,
                                    'visitorLName' => $subVisitor->last_name,
                                    'visitorEmailAddress' => $subVisitor->email_address,
                                    'visitorBadgeType' => $subVisitor->badge_type,
                                ]);
                            }
                        }
                    }
                }
            }
        }
        $this->finalListsOfVisitors = $this->finalListsOfVisitorsTemp;
    }

    public function render()
    {
        return view('livewire.admin.events.visitors.event-visitors-list');
    }

    public function search(){
        if (empty($this->searchTerm)) {
            $this->finalListsOfVisitors = $this->finalListsOfVisitorsTemp;
        } else {
            $this->finalListsOfVisitors = collect($this->finalListsOfVisitorsTemp)
                ->filter(function ($item) {
                    return str_contains(strtolower($item['visitorCompany']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorSalutation']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorFName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorMName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorLName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorJobTitle']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorEmailAddress']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorTransactionId']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorInvoiceNumber']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['visitorBadgeType']), strtolower($this->searchTerm));
                })
                ->all();
        }
    }

    public function printBadgeClicked($visitorType, $visitorId, $arrayIndex)
    {
        $this->printVisitorType = $visitorType;
        $this->printVisitorId = $visitorId;
        $this->printArrayIndex = $arrayIndex;

        $this->dispatchBrowserEvent('swal:print-badge-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);
    }

    public function printBadge()
    {
        VisitorPrintedBadges::create([
            'event_id' => $this->event->id,
            'event_category' => $this->event->category,
            'visitor_id' => $this->printVisitorId,
            'visitor_type' => $this->printVisitorType,
            'printed_date_time' => Carbon::now(),
        ]);

        $this->finalListsOfVisitors[$this->printArrayIndex]['visitorPrinted'] = "Yes";
        $this->finalListsOfVisitorsTemp[$this->printArrayIndex]['visitorPrinted'] = "Yes";

        $this->dispatchBrowserEvent('swal:print-badge-confirmed', [
            'url' => route('admin.event.delegates.detail.printBadge', ['eventCategory' => $this->event->category, 'eventId' => $this->event->id, 'delegateId' => $this->printVisitorId, 'delegateType' => $this->printVisitorType]),

            'type' => 'success',
            'message' => 'Badge Printed Successfully!',
            'text' => ''
        ]);

        $this->printVisitorType = null;
        $this->printVisitorId = null;
        $this->printArrayIndex = null;
    }

    public function previewBadge($visitorIndex)
    {

        if ($this->finalListsOfVisitors[$visitorIndex]['visitorSalutation'] == "Dr." || $this->finalListsOfVisitors[$visitorIndex]['visitorSalutation'] == "Prof.") {
            $this->name = $this->finalListsOfVisitors[$visitorIndex]['visitorSalutation'] . ' ' . $this->finalListsOfVisitors[$visitorIndex]['visitorFName'] . ' ' . $this->finalListsOfVisitors[$visitorIndex]['visitorMName'] . ' ' . $this->finalListsOfVisitors[$visitorIndex]['visitorLName'];
        } else {
            $this->name = $this->finalListsOfVisitors[$visitorIndex]['visitorFName'] . ' ' . $this->finalListsOfVisitors[$visitorIndex]['visitorMName'] . ' ' . $this->finalListsOfVisitors[$visitorIndex]['visitorLName'];
        }

        $this->jobTitle = $this->finalListsOfVisitors[$visitorIndex]['visitorJobTitle'];
        $this->company = $this->finalListsOfVisitors[$visitorIndex]['visitorCompany'];
        $this->registrationType = $this->finalListsOfVisitors[$visitorIndex]['visitorBadgeType'];

        $registrationType = EventRegistrationTypes::where('event_id', $this->event->id)->where('registration_type', $this->registrationType)->first();

        $this->badgeViewFFText = $registrationType->badge_footer_front_name;
        $this->badgeViewFBText = $registrationType->badge_footer_back_name;

        $this->badgeViewFFBGColor = $registrationType->badge_footer_front_bg_color;
        $this->badgeViewFBBGColor = $registrationType->badge_footer_back_bg_color;

        $this->badgeViewFFTextColor = $registrationType->badge_footer_front_text_color;
        $this->badgeViewFBTextColor = $registrationType->badge_footer_back_text_color;

        $this->badgeView = true;
    }


    public function closePreviewBadge()
    {
        $this->name = null;
        $this->jobTitle = null;
        $this->company = null;
        $this->registrationType = null;

        $this->badgeViewFFText = null;
        $this->badgeViewFBText = null;

        $this->badgeViewFFBGColor = null;
        $this->badgeViewFBBGColor = null;

        $this->badgeViewFFTextColor = null;
        $this->badgeViewFBTextColor = null;

        $this->badgeView = false;
    }

    
    public function sendBroadcastEmailShow(){
        $this->dispatchBrowserEvent('swal:broadcast-email-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);
    }    

    public function sendBroadcastEmail(){
        $this->dispatchBrowserEvent('swal:broadcast-email-success', [
            'type' => 'success',
            'message' => 'Broadcast Email Notification Sent!',
            'text' => "",
        ]);
        dd($this->finalListsOfVisitors);
    }
}
