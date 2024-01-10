<?php

namespace App\Http\Livewire;

use App\Models\Event as Events;
use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\Transaction as Transactions;
use App\Models\MainVisitor as MainVisitors;
use App\Models\AdditionalVisitor as AdditionalVisitors;
use App\Models\EventRegistrationType as EventRegistrationTypes;
use App\Models\VisitorTransaction as VisitorTransactions;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class FastTrack extends Component
{
    public $eventBanner, $state;

    public $searchTerm = '';
    public $suggestions = array();
    public $delegatesDetails = array();
    public $delegateDetail;

    public $day, $date;

    protected $listeners = ['transactionIdLoadingDone' => 'transactionIDClickedSuccess', 'scannedSuccess' => 'scannedQRContent', 'scannerStoppedSuccess' => 'scannerStopped', 'print-success' => 'printSuccess'];

    public function mount()
    {
        $this->eventBanner = Events::where('category', 'AF')->value('banner');
        $this->state = null;
        $this->date = now()->format('F j, Y');
        $this->day = now()->format('l');
        // $this->getGLFConfirmedDelegates();
        $this->getAFConfirmedDelegates();
        $this->getAFVConfirmedVisitors();
    }

    public function render()
    {
        return view('livewire.fast-track.fast-track');
    }

    public function qrCodeScannerClicked()
    {
        $this->dispatchBrowserEvent('scanStarted');
        $this->state = "qrcode";
    }

    public function scannerStopped()
    {
        $this->state = null;
    }

    public function returnToHome()
    {
        $this->state = null;
        $this->searchTerm = null;
        $this->suggestions = array();
        $this->delegateDetail = null;
    }


    public function transactionIdClicked()
    {
        $this->dispatchBrowserEvent('add-loading-screen', [
            'redirectFunction' => 'transactionIdLoadingDone',
        ]);
    }

    public function transactionIDClickedSuccess()
    {
        $this->state = "transactionid";
        $this->dispatchBrowserEvent('remove-loading-screen');
    }

    public function updatedSearchTerm()
    {
        if ($this->searchTerm == null) {
            $this->suggestions = array();
        } else {
            $this->suggestions = collect($this->delegatesDetails)
                ->filter(function ($item) {
                    return str_contains(strtolower($item['transactionId']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['fullName']), strtolower($this->searchTerm));
                })->take(10)->all();
        }
    }

    public function selectSuggestion($suggestion)
    {
        $this->searchTerm = $suggestion;
        $this->suggestions = array();

        foreach ($this->delegatesDetails as $delegateDetails) {
            if ($delegateDetails['transactionId'] == $this->searchTerm) {
                $this->delegateDetail = $delegateDetails;
                break;
            }
        }
    }

    public function searchClicked()
    {
        $this->suggestions = array();
        $matchedTransaction = null;

        if ($this->searchTerm == null && $this->searchTerm == "") {
            $this->delegateDetail = null;
        } else {
            foreach ($this->delegatesDetails as $delegateDetails) {
                if ($delegateDetails['transactionId'] == $this->searchTerm) {
                    $matchedTransaction = $delegateDetails;
                    break;
                }
            }

            if ($matchedTransaction != null) {
                $this->delegateDetail = $matchedTransaction;
            } else {
                $this->delegateDetail = null;
            }
        }
    }

    public function printClicked()
    {
        $this->dispatchBrowserEvent('print-badge', [
            'printUrl' => $this->delegateDetail['printUrl'],
        ]);
    }

    public function printSuccess()
    {
        $this->state = "transactionid";
        $this->searchTerm = null;
        $this->suggestions = array();
        $this->delegateDetail = array();

        $this->dispatchBrowserEvent('print-badge-success', [
            'type' => 'success',
            'message' => 'Success',
            'text' => "",
        ]);
    }



    public function scannedQRContent($content)
    {
        if (strlen($content) < 4) {
            $this->dispatchBrowserEvent('remove-loading-screen');
            $this->dispatchBrowserEvent('invalid-qr', [
                'type' => 'error',
                'message' => 'Invalid QR Code',
                'text' => "Please inform one of our admin to assist you",
            ]);
            $this->state = null;
        } else {
            $firstTwoContent = substr($content, 0, 2);
            $lastTwoContent = substr($content, -2);
            $combinedContent = $lastTwoContent . $firstTwoContent;
            if ($combinedContent != "gpca") {
                $this->dispatchBrowserEvent('remove-loading-screen');
                $this->dispatchBrowserEvent('invalid-qr', [
                    'type' => 'error',
                    'message' => 'Invalid QR Code',
                    'text' => "Please inform one of our admin to assist you",
                ]);
                $this->state = null;
            } else {
                $encrypTextTextContent = substr($content, 2, -2);
                $decryptedText = Crypt::decryptString($encrypTextTextContent);
                $arrayDecryptedText = explode(",", $decryptedText);
                if (count($arrayDecryptedText) < 5) {
                    $this->dispatchBrowserEvent('remove-loading-screen');
                    $this->dispatchBrowserEvent('invalid-qr', [
                        'type' => 'error',
                        'message' => 'Invalid QR Code',
                        'text' => "Please inform one of our admin to assist you",
                    ]);
                    $this->state = null;
                } else {
                    if ($arrayDecryptedText[0] != "gpca@reg") {
                        $this->dispatchBrowserEvent('remove-loading-screen');
                        $this->dispatchBrowserEvent('invalid-qr', [
                            'type' => 'error',
                            'message' => 'Invalid QR Code',
                            'text' => "Please inform one of our admin to assist you",
                        ]);
                        $this->state = null;
                    } else {
                        $eventId = $arrayDecryptedText[1];
                        $eventCategory = $arrayDecryptedText[2];
                        $delegateId = $arrayDecryptedText[3];
                        $delegateType = $arrayDecryptedText[4];


                        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                            if ($eventCategory == $eventCategoryC) {
                                $eventCode = $code;
                            }
                        }

                        $eventYear = Events::where('id', $eventId)->value('year');

                        if ($eventCategory == "AF" || $eventCategory == "AFV") {
                            if ($eventCategory == "AF") {
                                $transactionId = Transactions::where('event_id', $eventId)->where('delegate_id', $delegateId)->where('delegate_type', $delegateType)->value('id');

                                $lastDigit = 1000 + intval($transactionId);
                                $this->searchTerm = $eventYear . $eventCode . $lastDigit;
                            } else {
                                $transactionId = VisitorTransactions::where('event_id', $eventId)->where('visitor_id', $delegateId)->where('visitor_type', $delegateType)->value('id');

                                $lastDigit = 1000 + intval($transactionId);
                                $this->searchTerm = $eventYear . $eventCode . $lastDigit;
                            }

                            $matchedTransaction = null;

                            foreach ($this->delegatesDetails as $delegateDetails) {
                                if ($delegateDetails['transactionId'] == $this->searchTerm) {
                                    $matchedTransaction = $delegateDetails;
                                    break;
                                }
                            }

                            if ($matchedTransaction != null) {
                                $this->delegateDetail = $matchedTransaction;
                                $this->state = "qrCodeScanned";
                                $this->dispatchBrowserEvent('remove-loading-screen');
                            } else {
                                $this->dispatchBrowserEvent('remove-loading-screen');
                                $this->dispatchBrowserEvent('invalid-qr', [
                                    'type' => 'error',
                                    'message' => 'Invalid QR Code',
                                    'text' => "Please inform one of our admin to assist you",
                                ]);
                                $this->searchTerm = null;
                                $this->state = null;
                            }

                        } else {
                            $this->dispatchBrowserEvent('remove-loading-screen');
                            $this->dispatchBrowserEvent('invalid-qr', [
                                'type' => 'error',
                                'message' => 'Invalid QR Code',
                                'text' => "Please inform one of our admin to assist you",
                            ]);
                            $this->state = null;
                        }
                    }
                }
            }
        }
    }




    public function getGLFConfirmedDelegates()
    {
        $eventCategory = "GLF";

        $event = Events::where('category', $eventCategory)->select('id', 'category', 'year')->first();
        $mainDelegates = MainDelegates::where('event_id', $event->id)->get();


        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        foreach ($mainDelegates as $mainDelegate) {
            $companyName = "";

            if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                if ($mainDelegate->registration_status == "confirmed") {

                    $registrationType = EventRegistrationTypes::where('event_id', $event->id)->where('event_category', $eventCategory)->where('registration_type', $mainDelegate->badge_type)->first();

                    $transactionId = Transactions::where('event_id', $event->id)->where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    $finalTransactionId = $event->year . $eventCode . $lastDigit;

                    if ($mainDelegate->alternative_company_name != null) {
                        $companyName = $mainDelegate->alternative_company_name;
                    } else {
                        $companyName = $mainDelegate->company_name;
                    }


                    if ($mainDelegate->salutation == "Dr." || $mainDelegate->salutation == "Prof.") {
                        $delegateSalutation = $mainDelegate->salutation;
                    } else {
                        $delegateSalutation = null;
                    }

                    $fullName = $delegateSalutation . ' ' . $mainDelegate->first_name . ' ' . $mainDelegate->middle_name . ' ' . $mainDelegate->last_name;

                    $printUrl = route('public-print-badge', ['eventCategory' => $event->category, 'eventId' => $event->id, 'delegateId' => $mainDelegate->id, 'delegateType' => 'main']);
                    array_push($this->delegatesDetails, [
                        'transactionId' => $finalTransactionId,
                        'fullName' => $fullName,
                        'jobTitle' => $mainDelegate->job_title,
                        'companyName' => $companyName,
                        'badgeType' => $mainDelegate->badge_type,

                        'frontText' => $registrationType->badge_footer_front_name,

                        'printUrl' => $printUrl,
                        'seatNumber' => $mainDelegate->seat_number,
                    ]);
                }
            }


            $subDelegates = AdditionalDelegates::where('main_delegate_id', $mainDelegate->id)->get();

            if (!$subDelegates->isEmpty()) {
                foreach ($subDelegates as $subDelegate) {

                    if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {
                        if ($mainDelegate->registration_status == "confirmed") {

                            $registrationType = EventRegistrationTypes::where('event_id', $event->id)->where('event_category', $eventCategory)->where('registration_type', $subDelegate->badge_type)->first();

                            $transactionId = Transactions::where('delegate_id', $subDelegate->id)->where('delegate_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $finalTransactionId = $event->year . $eventCode . $lastDigit;


                            if ($subDelegate->salutation == "Dr." || $subDelegate->salutation == "Prof.") {
                                $delegateSalutation = $subDelegate->salutation;
                            } else {
                                $delegateSalutation = null;
                            }

                            $fullName = $delegateSalutation . ' ' . $subDelegate->first_name . ' ' . $subDelegate->middle_name . ' ' . $subDelegate->last_name;


                            $printUrl = route('public-print-badge', ['eventCategory' => $event->category, 'eventId' => $event->id, 'delegateId' => $subDelegate->id, 'delegateType' => 'sub']);

                            array_push($this->delegatesDetails, [
                                'transactionId' => $finalTransactionId,
                                'fullName' => $fullName,
                                'jobTitle' => $subDelegate->job_title,
                                'companyName' => $companyName,
                                'badgeType' => $subDelegate->badge_type,

                                'frontText' => $registrationType->badge_footer_front_name,

                                'printUrl' => $printUrl,
                                'seatNumber' => $subDelegate->seat_number,
                            ]);
                        }
                    }
                }
            }
        }
    }


    public function getAFConfirmedDelegates()
    {
        $eventCategory = "AF";

        $event = Events::where('category', $eventCategory)->select('id', 'category', 'year')->first();
        $mainDelegates = MainDelegates::where('event_id', $event->id)->get();


        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        foreach ($mainDelegates as $mainDelegate) {
            $companyName = "";

            if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                if ($mainDelegate->registration_status == "confirmed") {

                    $registrationType = EventRegistrationTypes::where('event_id', $event->id)->where('event_category', $eventCategory)->where('registration_type', $mainDelegate->badge_type)->first();

                    $transactionId = Transactions::where('event_id', $event->id)->where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    $finalTransactionId = $event->year . $eventCode . $lastDigit;

                    if ($mainDelegate->alternative_company_name != null) {
                        $companyName = $mainDelegate->alternative_company_name;
                    } else {
                        $companyName = $mainDelegate->company_name;
                    }


                    if ($mainDelegate->salutation == "Dr." || $mainDelegate->salutation == "Prof.") {
                        $delegateSalutation = $mainDelegate->salutation;
                    } else {
                        $delegateSalutation = null;
                    }

                    $fullName = $delegateSalutation . ' ' . $mainDelegate->first_name . ' ' . $mainDelegate->middle_name . ' ' . $mainDelegate->last_name;

                    $printUrl = route('public-print-badge', ['eventCategory' => $event->category, 'eventId' => $event->id, 'delegateId' => $mainDelegate->id, 'delegateType' => 'main']);
                    array_push($this->delegatesDetails, [
                        'transactionId' => $finalTransactionId,
                        'fullName' => $fullName,
                        'jobTitle' => $mainDelegate->job_title,
                        'companyName' => $companyName,
                        'badgeType' => $mainDelegate->badge_type,

                        'frontText' => $registrationType->badge_footer_front_name,

                        'printUrl' => $printUrl,
                        'seatNumber' => $mainDelegate->seat_number,
                    ]);
                }
            }


            $subDelegates = AdditionalDelegates::where('main_delegate_id', $mainDelegate->id)->get();

            if (!$subDelegates->isEmpty()) {
                foreach ($subDelegates as $subDelegate) {

                    if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {
                        if ($mainDelegate->registration_status == "confirmed") {

                            $registrationType = EventRegistrationTypes::where('event_id', $event->id)->where('event_category', $eventCategory)->where('registration_type', $subDelegate->badge_type)->first();

                            $transactionId = Transactions::where('delegate_id', $subDelegate->id)->where('delegate_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $finalTransactionId = $event->year . $eventCode . $lastDigit;


                            if ($subDelegate->salutation == "Dr." || $subDelegate->salutation == "Prof.") {
                                $delegateSalutation = $subDelegate->salutation;
                            } else {
                                $delegateSalutation = null;
                            }

                            $fullName = $delegateSalutation . ' ' . $subDelegate->first_name . ' ' . $subDelegate->middle_name . ' ' . $subDelegate->last_name;


                            $printUrl = route('public-print-badge', ['eventCategory' => $event->category, 'eventId' => $event->id, 'delegateId' => $subDelegate->id, 'delegateType' => 'sub']);

                            array_push($this->delegatesDetails, [
                                'transactionId' => $finalTransactionId,
                                'fullName' => $fullName,
                                'jobTitle' => $subDelegate->job_title,
                                'companyName' => $companyName,
                                'badgeType' => $subDelegate->badge_type,

                                'frontText' => $registrationType->badge_footer_front_name,

                                'printUrl' => $printUrl,
                                'seatNumber' => $subDelegate->seat_number,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function getAFVConfirmedVisitors()
    {
        $eventCategory = "AFV";

        $event = Events::where('category', $eventCategory)->first();
        $mainVisitors = MainVisitors::where('event_id', $event->id)->get();


        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        foreach ($mainVisitors as $mainVisitor) {
            $companyName = "";

            if ($mainVisitor->visitor_replaced_by_id == null && (!$mainVisitor->visitor_refunded)) {
                if ($mainVisitor->registration_status == "confirmed") {

                    $registrationType = EventRegistrationTypes::where('event_id', $event->id)->where('event_category', $eventCategory)->where('registration_type', $mainVisitor->badge_type)->first();

                    $transactionId = VisitorTransactions::where('event_id', $event->id)->where('visitor_id', $mainVisitor->id)->where('visitor_type', "main")->value('id');
                    $lastDigit = 1000 + intval($transactionId);

                    $finalTransactionId = $event->year . $eventCode . $lastDigit;

                    if ($mainVisitor->alternative_company_name != null) {
                        $companyName = $mainVisitor->alternative_company_name;
                    } else {
                        $companyName = $mainVisitor->company_name;
                    }


                    if ($mainVisitor->salutation == "Dr." || $mainVisitor->salutation == "Prof.") {
                        $delegateSalutation = $mainVisitor->salutation;
                    } else {
                        $delegateSalutation = null;
                    }

                    $fullName = $delegateSalutation . ' ' . $mainVisitor->first_name . ' ' . $mainVisitor->middle_name . ' ' . $mainVisitor->last_name;

                    $printUrl = route('public-print-badge', ['eventCategory' => $event->category, 'eventId' => $event->id, 'delegateId' => $mainVisitor->id, 'delegateType' => 'main']);

                    array_push($this->delegatesDetails, [
                        'transactionId' => $finalTransactionId,
                        'fullName' => $fullName,
                        'jobTitle' => $mainVisitor->job_title,
                        'companyName' => $companyName,
                        'badgeType' => $mainVisitor->badge_type,

                        'frontText' => $registrationType->badge_footer_front_name,

                        'printUrl' => $printUrl,
                        'seatNumber' => null,
                    ]);
                }
            }


            $subVisitors = AdditionalVisitors::where('main_visitor_id', $mainVisitor->id)->get();

            if (!$subVisitors->isEmpty()) {
                foreach ($subVisitors as $subVisitor) {

                    if ($subVisitor->visitor_replaced_by_id == null && (!$subVisitor->visitor_refunded)) {
                        if ($mainVisitor->registration_status == "confirmed") {

                            $registrationType = EventRegistrationTypes::where('event_id', $event->id)->where('event_category', $eventCategory)->where('registration_type', $subVisitor->badge_type)->first();

                            $transactionId = VisitorTransactions::where('visitor_id', $subVisitor->id)->where('visitor_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $finalTransactionId = $event->year . $eventCode . $lastDigit;


                            if ($subVisitor->salutation == "Dr." || $subVisitor->salutation == "Prof.") {
                                $delegateSalutation = $subVisitor->salutation;
                            } else {
                                $delegateSalutation = null;
                            }

                            $fullName = $delegateSalutation . ' ' . $subVisitor->first_name . ' ' . $subVisitor->middle_name . ' ' . $subVisitor->last_name;


                            $printUrl = route('public-print-badge', ['eventCategory' => $event->category, 'eventId' => $event->id, 'delegateId' => $subVisitor->id, 'delegateType' => 'sub']);

                            array_push($this->delegatesDetails, [
                                'transactionId' => $finalTransactionId,
                                'fullName' => $fullName,
                                'jobTitle' => $subVisitor->job_title,
                                'companyName' => $companyName,
                                'badgeType' => $subVisitor->badge_type,

                                'frontText' => $registrationType->badge_footer_front_name,

                                'printUrl' => $printUrl,
                                'seatNumber' => null,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
