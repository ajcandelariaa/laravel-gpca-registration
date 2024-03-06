<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Event as Events;
use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\MainVisitor as MainVisitors;
use App\Models\AdditionalVisitor as AdditionalVisitors;
use App\Models\ScannedVisitor as ScannedVisitors;
use App\Models\ScannedDelegate as ScannedDelegates;
use Carbon\Carbon;

class ScanQr extends Component
{
    public $eventBanner;
    public $state;
    public $name, $jobTitle, $companyName, $badgeType;
    public $eventCategory, $eventId;

    protected $listeners = ['scannedSuccess' => 'scannedQRContent', 'scannerStoppedSuccess' => 'scannerStopped'];

    public function mount($eventCategory, $eventId)
    {
        $this->eventBanner = Events::where('id', $eventId)->where('category', $eventCategory)->value('banner');
        $this->eventCategory = $eventCategory;
        $this->eventId = $eventId;
    }
    public function render()
    {
        return view('livewire.admin.events.scan-qr.scan-qr');
    }

    public function qrCodeScannerClicked()
    {
        $this->dispatchBrowserEvent('scanStarted');
        $this->state = "qrCodeScanning";
    }

    public function scannerStopped()
    {
        $this->state = null;
    }


    public function returnToHome()
    {
        return redirect()->route('scan.qr');
    }


    public function scannedQRContent($content)
    {
        if (strlen($content) < 4) {
            $this->dispatchBrowserEvent('remove-loading-screen');
            $this->dispatchBrowserEvent('invalid-qr', [
                'type' => 'error',
                'message' => 'Invalid QR Code',
                'text' => "[1] Please inform our IT Team to assist you",
            ]);
            $this->state = null;
        } else {
            $firstFourContent = substr($content, 0, 4);
            if ($firstFourContent != "gpca") {
                $this->dispatchBrowserEvent('remove-loading-screen');
                $this->dispatchBrowserEvent('invalid-qr', [
                    'type' => 'error',
                    'message' => 'Invalid QR Code',
                    'text' => "[2] Please inform our IT Team admin to assist you",
                ]);
                $this->state = null;
            } else {
                $encrypTextTextContent = substr($content, 4);
                $decryptedText = base64_decode($encrypTextTextContent);
                $arrayDecryptedText = explode(",", $decryptedText);
                if (count($arrayDecryptedText) < 5) {
                    $this->dispatchBrowserEvent('remove-loading-screen');
                    $this->dispatchBrowserEvent('invalid-qr', [
                        'type' => 'error',
                        'message' => 'Invalid QR Code',
                        'text' => "[3] Please inform our IT Team admin to assist you",
                    ]);
                    $this->state = null;
                } else {
                    if ($arrayDecryptedText[0] != "gpca@scan") {
                        $this->dispatchBrowserEvent('remove-loading-screen');
                        $this->dispatchBrowserEvent('invalid-qr', [
                            'type' => 'error',
                            'message' => 'Invalid QR Code',
                            'text' => "[4] Please inform our IT Team admin to assist you",
                        ]);
                        $this->state = null;
                    } else {
                        $eventId = $arrayDecryptedText[1];
                        $eventCategory = $arrayDecryptedText[2];
                        $delegateId = $arrayDecryptedText[3];
                        $delegateType = $arrayDecryptedText[4];

                        if ($this->eventCategory == $eventCategory && $this->eventId == $eventId) {
                            if ($eventCategory == "AFV") {
                                if ($delegateType == "main") {
                                    $delegateDetails = MainVisitors::where('id', $delegateId)->first();

                                    if ($delegateDetails->alternative_company_name != null) {
                                        $companyName = $delegateDetails->alternative_company_name;
                                    } else {
                                        $companyName = $delegateDetails->company_name;
                                    }


                                    if ($delegateDetails->salutation == "Dr." || $delegateDetails->salutation == "Prof.") {
                                        $delegateSalutation = $delegateDetails->salutation;
                                    } else {
                                        $delegateSalutation = null;
                                    }

                                    $this->name = $delegateSalutation . ' ' . $delegateDetails->first_name . ' ' . $delegateDetails->middle_name . ' ' . $delegateDetails->last_name;
                                    $this->jobTitle = $delegateDetails->job_title;
                                    $this->companyName = $companyName;
                                    $this->badgeType = $delegateDetails->badge_type;
                                } else {
                                    $delegateDetails = AdditionalVisitors::where('id', $delegateId)->first();
                                    $mainVisitor = MainVisitors::where('id', $delegateDetails->main_delegate_id)->first();

                                    if ($mainVisitor->alternative_company_name != null) {
                                        $companyName = $mainVisitor->alternative_company_name;
                                    } else {
                                        $companyName = $mainVisitor->company_name;
                                    }


                                    if ($delegateDetails->salutation == "Dr." || $delegateDetails->salutation == "Prof.") {
                                        $delegateSalutation = $delegateDetails->salutation;
                                    } else {
                                        $delegateSalutation = null;
                                    }

                                    $this->name = $delegateSalutation . ' ' . $delegateDetails->first_name . ' ' . $delegateDetails->middle_name . ' ' . $delegateDetails->last_name;
                                    $this->jobTitle = $delegateDetails->job_title;
                                    $this->companyName = $companyName;
                                    $this->badgeType = $delegateDetails->badge_type;
                                }

                                ScannedVisitors::create([
                                    'event_id' => $eventId,
                                    'event_category' => $eventCategory,
                                    'visitor_id' => $delegateId,
                                    'visitor_type' => $delegateType,
                                    'scanned_date_time' => Carbon::now(),
                                ]);
                            } else {
                                if ($delegateType == "main") {
                                    $delegateDetails = MainDelegates::where('id', $delegateId)->first();

                                    if ($delegateDetails->alternative_company_name != null) {
                                        $companyName = $delegateDetails->alternative_company_name;
                                    } else {
                                        $companyName = $delegateDetails->company_name;
                                    }


                                    if ($delegateDetails->salutation == "Dr." || $delegateDetails->salutation == "Prof.") {
                                        $delegateSalutation = $delegateDetails->salutation;
                                    } else {
                                        $delegateSalutation = null;
                                    }

                                    $this->name = $delegateSalutation . ' ' . $delegateDetails->first_name . ' ' . $delegateDetails->middle_name . ' ' . $delegateDetails->last_name;
                                    $this->jobTitle = $delegateDetails->job_title;
                                    $this->companyName = $companyName;
                                    $this->badgeType = $delegateDetails->badge_type;
                                } else {
                                    $delegateDetails = AdditionalDelegates::where('id', $delegateId)->first();
                                    $mainDelegate = MainDelegates::where('id', $delegateDetails->main_delegate_id)->first();



                                    if ($mainDelegate->alternative_company_name != null) {
                                        $companyName = $mainDelegate->alternative_company_name;
                                    } else {
                                        $companyName = $mainDelegate->company_name;
                                    }


                                    if ($delegateDetails->salutation == "Dr." || $delegateDetails->salutation == "Prof.") {
                                        $delegateSalutation = $delegateDetails->salutation;
                                    } else {
                                        $delegateSalutation = null;
                                    }

                                    $this->name = $delegateSalutation . ' ' . $delegateDetails->first_name . ' ' . $delegateDetails->middle_name . ' ' . $delegateDetails->last_name;
                                    $this->jobTitle = $delegateDetails->job_title;
                                    $this->companyName = $companyName;
                                    $this->badgeType = $delegateDetails->badge_type;
                                }

                                ScannedDelegates::create([
                                    'event_id' => $eventId,
                                    'event_category' => $eventCategory,
                                    'delegate_id' => $delegateId,
                                    'delegate_type' => $delegateType,
                                    'scanned_date_time' => Carbon::now(),
                                ]);
                            }

                            $this->dispatchBrowserEvent('scan-qr-success', [
                                'type' => 'success',
                                'message' => 'Success',
                                'text' => "",
                            ]);

                            $this->state = "showDelegateDetails";
                        } else {
                            $this->dispatchBrowserEvent('remove-loading-screen');
                            $this->dispatchBrowserEvent('invalid-qr', [
                                'type' => 'error',
                                'message' => 'Invalid QR Code',
                                'text' => "[5] Please inform our IT Team admin to assist you",
                            ]);
                            $this->state = null;
                        }
                    }
                }
            }
        }
    }
}
