<?php

namespace App\Http\Livewire;

use App\Models\MainDelegate as MainDelegates;
use Livewire\Component;

class DigitalHelper extends Component
{
    public $event, $eventCode, $confirmedDelegates = array();
    public $inputtedData, $currentOption, $currentDelegate;
    public $showInputFormModal = false;
    public $showCollectYourBadgeDetails = false;

    public function mount($event)
    {
        $this->event = $event;
        $this->eventCode = config('app.eventCategories')[$event->category];
        $this->confirmedDelegates = $this->fetchConfirmedDelgates();
    }

    public function render()
    {
        return view('livewire.digital-helper.2024.af.digital-helper');
    }

    public function optionClicked($option)
    {
        $this->currentOption = $option;
        $this->showCollectYourBadgeDetails = false;
        $this->showInputFormModal = true;
    }

    public function cancelClicked()
    {
        $this->showInputFormModal = false;
        $this->inputtedData = null;
        $this->currentOption = false;
        $this->resetValidation();
    }

    public function searchClicked()
    {
        $this->validate([
            'inputtedData' => 'required'
        ]);

        if ($this->currentOption == "email") {
            $this->currentDelegate = $this->searchViaEmail();
        } else if ($this->currentOption == "transactionId") {
            $this->currentDelegate = $this->searchViaTransactionId();
        } else {
            $this->currentDelegate = null;
        }

        $this->showInputFormModal = false;
        $this->currentOption = false;
        $this->inputtedData = null;

        if($this->currentDelegate){
            $this->showCollectYourBadgeDetails = true;
        }
    }

    public function searchViaName() {

    }

    public function searchViaTransactionId()
    {
        $currentDelegate = null;

        foreach ($this->confirmedDelegates as $delegate) {
            if (strtolower(trim($delegate['transactionId'])) == strtolower(trim($this->inputtedData))) {
                $currentDelegate = $delegate;
                break;
            }
        }

        return $currentDelegate;
    }

    public function searchViaEmail()
    {
        $currentDelegate = null;

        foreach ($this->confirmedDelegates as $delegate) {
            if (strtolower(trim($delegate['emailAddress'])) == strtolower(trim($this->inputtedData))) {
                $currentDelegate = $delegate;
                break;
            }
        }

        return $currentDelegate;
        // $finalDelegate = null;

        // $mainDelegate = MainDelegates::with(['transaction', 'printedBadges', 'scannedBadges'])->where('event_id', $this->event->id)->whereRaw('LOWER(email_address) = ?', [strtolower($this->inputtedData)])->first();

        // if ($mainDelegate) {
        //     if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
        //         if ($mainDelegate->registration_status == "confirmed") {

        //             $transactionId = $mainDelegate->transaction->id;
        //             $lastDigit = 1000 + intval($transactionId);
        //             $finalTransactionId = $this->event->year . $this->eventCode . $lastDigit;

        //             $name = $mainDelegate->salutation . ' ' . $mainDelegate->first_name . ' ' . $mainDelegate->middle_name . ' ' . $mainDelegate->last_name;
        //             $companyName = $mainDelegate->alternative_company_name ?? $mainDelegate->company_name;

        //             $isPrinted = false;
        //             $isCollected = false;
        //             $isCollectedBy = null;

        //             if ($mainDelegate->printedBadges->isNotEmpty()) {
        //                 foreach ($mainDelegate->printedBadges as $printedBadge) {
        //                     $isPrinted = true;

        //                     if ($printedBadge->collected) {
        //                         $isCollected = true;
        //                         $isCollectedBy = $printedBadge->collected_by;
        //                     }
        //                 }
        //             }
        //             $visuals = [];
        //             $howToCollectYourBadge = null;

        //             if ($isCollected) {
        //                 $howToCollectYourBadge = $isCollectedBy;
        //             } else {
        //                 $data = $this->getTextAndVisualDetails($companyName, $mainDelegate->badge_type);
        //                 $howToCollectYourBadge = $data['howToCollectYourBadge'];
        //                 $visuals = $data['imageLinks'];
        //             }


        //             $finalDelegate = [
        //                 'transactionId' => $finalTransactionId,
        //                 'name' => trim($name),
        //                 'jobTitle' => $mainDelegate->job_title,
        //                 'companyName' => $companyName,
        //                 'badgeType' => $mainDelegate->badge_type,
        //                 'emailAddress' => $mainDelegate->email_address,
        //                 'isPrinted' => $isPrinted,
        //                 'isCollected' => $isCollected,
        //                 'howToCollectYourBadge' => $howToCollectYourBadge,
        //                 'visuals' => $visuals,
        //             ];
        //         }
        //     }
        // } else {
        //     $mainDelegates = MainDelegates::with('additionalDelegates')->where('event_id', $this->event->id)->get();
        //     if ($mainDelegates->isNotEmpty()) {
        //         if (!$mainDelegate->additionalDelegates->isEmpty()) {
        //             foreach ($mainDelegate->additionalDelegates as $subDelegate) {
        //                 if (strtolower($subDelegate->email_address) == strtolower($this->inputtedData)) {
        //                     if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {
        //                         if ($mainDelegate->registration_status == "confirmed") {

        //                             $transactionId = $subDelegate->transaction->id;
        //                             $lastDigit = 1000 + intval($transactionId);
        //                             $finalTransactionId = $this->event->year . $this->eventCode . $lastDigit;

        //                             $name = $subDelegate->salutation . ' ' . $subDelegate->first_name . ' ' . $subDelegate->middle_name . ' ' . $subDelegate->last_name;
        //                             $companyName = $mainDelegate->alternative_company_name ?? $mainDelegate->company_name;

        //                             $isPrinted = false;
        //                             $isCollected = false;
        //                             $isCollectedBy = null;

        //                             if ($subDelegate->printedBadges->isNotEmpty()) {
        //                                 foreach ($subDelegate->printedBadges as $printedBadge) {
        //                                     $isPrinted = true;

        //                                     if ($printedBadge->collected) {
        //                                         $isCollected = true;
        //                                         $isCollectedBy = $printedBadge->collected_by;
        //                                     }
        //                                 }
        //                             }
        //                             $visuals = [];
        //                             $howToCollectYourBadge = null;

        //                             if ($isCollected) {
        //                                 $howToCollectYourBadge = $isCollectedBy;
        //                             } else {
        //                                 $data = $this->getTextAndVisualDetails($companyName, $subDelegate->badge_type);
        //                                 $howToCollectYourBadge = $data['howToCollectYourBadge'];
        //                                 $visuals = $data['imageLinks'];
        //                             }

        //                             $finalDelegate = [
        //                                 'transactionId' => $finalTransactionId,
        //                                 'name' => trim($name),
        //                                 'jobTitle' => $subDelegate->job_title,
        //                                 'companyName' => $companyName,
        //                                 'badgeType' => $subDelegate->badge_type,
        //                                 'emailAddress' => $subDelegate->email_address,
        //                                 'isPrinted' => $isPrinted,
        //                                 'isCollected' => $isCollected,
        //                                 'howToCollectYourBadge' => $howToCollectYourBadge,
        //                                 'visuals' => $visuals,
        //                             ];
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        // return $finalDelegate;
    }


    public function getTextAndVisualDetails($companyName, $badgeType)
    {
        $firstLetter = strtoupper(substr($companyName, 0, 1));
        $howToCollectYourBadge = null;
        $imageLinks = [];

        $letterCounters1 = [
            'ABCD' => [
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/Madinat-Al-Ifran-Theatre-Foyer.png',
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/ABCD.png',
            ],
            'EFGHI' => [
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/Madinat-Al-Ifran-Theatre-Foyer.png',
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/EFGHI.png',
            ],
            'JKLMN' => [
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/Madinat-Al-Ifran-Theatre-Foyer.png',
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/JKLMN.png',
            ],
            'OPQ' => [
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/Madinat-Al-Ifran-Theatre-Foyer.png',
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/OPQ.png',
            ],
            'S' => [
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/Madinat-Al-Ifran-Theatre-Foyer.png',
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/S.png',
            ],
            'RT' => [
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/Madinat-Al-Ifran-Theatre-Foyer.png',
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/RT.png',
            ],
            'UVWXYZ' => [
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/Madinat-Al-Ifran-Theatre-Foyer.png',
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/UVWXYZ.png',
            ],
        ];

        $letterCounters2 = [
            'ABCDEFGHIJ' => [],
            'KLMNOPQRSTUVWXYZ' => [],
        ];

        if (strtoupper($badgeType) == "VIP" || strtoupper($badgeType) == "SPEAKER") {
            $imageLinks = [
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/Madinat-Al-Ifran-Theatre-Foyer.png',
                'https://www.gpcaforum.com/wp-content/uploads/2024/10/VIP-and-Speakers.png',
            ];
            $howToCollectYourBadge = `Please proceed to the Madinat Al Ifran Theatre Foyer and line up at counter "VIP/SPEAKERS". You can find the counter assignment in the image below:`;
        } else if (strtoupper($badgeType) == "EXHIBITOR") {
            $counterGroup = null;
            foreach ($letterCounters2 as $counterKey => $counter) {
                $counterArrayOfLetters = str_split($counterKey);
                foreach ($counterArrayOfLetters as $letter) {
                    if ($firstLetter == $letter) {
                        $counterGroup = $counterKey;
                        $imageLinks = $counter;
                        break;
                    }
                }
            }

            $howToCollectYourBadge = `Please proceed to the Exhibition Foyer and line up at counter "$counterGroup". You can find the counter assignment based on your company's first letter in the image below`;
        } else {
            $counterGroup = null;
            foreach ($letterCounters1 as $counterKey => $counter) {
                $counterArrayOfLetters = str_split($counterKey);
                foreach ($counterArrayOfLetters as $letter) {
                    if ($firstLetter == $letter) {
                        $counterGroup = $counterKey;
                        $imageLinks = $counter;
                        break;
                    }
                }
            }

            $howToCollectYourBadge = "Please proceed to the Madinat Al Ifran Theatre Foyer and line up at counter '$counterGroup'. You can find the counter assignment based on your company's first letter in the image below";
        }

        return [
            'howToCollectYourBadge' => $howToCollectYourBadge,
            'imageLinks' => $imageLinks,
        ];
    }


    public function fetchConfirmedDelgates()
    {
        $allDelegates = array();
        $mainDelegates = MainDelegates::with(['additionalDelegates', 'transaction', 'printedBadges', 'scannedBadges'])->where('event_id', $this->event->id)->get();
        if ($mainDelegates->isNotEmpty()) {
            foreach ($mainDelegates as $mainDelegate) {

                $companyName = $mainDelegate->alternative_company_name ?? $mainDelegate->company_name;

                if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                    if ($mainDelegate->registration_status == "confirmed") {

                        $transactionId = $mainDelegate->transaction->id;
                        $lastDigit = 1000 + intval($transactionId);
                        $finalTransactionId = $this->event->year . $this->eventCode . $lastDigit;

                        $name = $mainDelegate->salutation . ' ' . $mainDelegate->first_name . ' ' . $mainDelegate->middle_name . ' ' . $mainDelegate->last_name;

                        $isPrinted = false;
                        $isCollected = false;
                        $isCollectedBy = null;

                        if ($mainDelegate->printedBadges->isNotEmpty()) {
                            foreach ($mainDelegate->printedBadges as $printedBadge) {
                                $isPrinted = true;

                                if ($printedBadge->collected) {
                                    $isCollected = true;
                                    $isCollectedBy = $printedBadge->collected_by;
                                }
                            }
                        }

                        $visuals = [];
                        $howToCollectYourBadge = null;

                        if ($isCollected) {
                            $howToCollectYourBadge = $isCollectedBy;
                        } else {
                            $data = $this->getTextAndVisualDetails($companyName, $mainDelegate->badge_type);
                            $howToCollectYourBadge = $data['howToCollectYourBadge'];
                            $visuals = $data['imageLinks'];
                        }

                        $finalDelegate = [
                            'transactionId' => $finalTransactionId,
                            'name' => trim($name),
                            'jobTitle' => $mainDelegate->job_title,
                            'companyName' => $companyName,
                            'badgeType' => $mainDelegate->badge_type,
                            'emailAddress' => $mainDelegate->email_address,
                            'isPrinted' => $isPrinted,
                            'isCollected' => $isCollected,
                            'howToCollectYourBadge' => $howToCollectYourBadge,
                            'visuals' => $visuals,
                        ];

                        array_push($allDelegates, $finalDelegate);
                    }
                }

                if (!$mainDelegate->additionalDelegates->isEmpty()) {
                    foreach ($mainDelegate->additionalDelegates as $subDelegate) {

                        if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {
                            if ($mainDelegate->registration_status == "confirmed") {

                                $transactionId = $subDelegate->transaction->id;
                                $lastDigit = 1000 + intval($transactionId);
                                $finalTransactionId = $this->event->year . $this->eventCode . $lastDigit;

                                $name = $subDelegate->salutation . ' ' . $subDelegate->first_name . ' ' . $subDelegate->middle_name . ' ' . $subDelegate->last_name;

                                $isPrinted = false;
                                $isCollected = false;
                                $isCollectedBy = null;

                                if ($subDelegate->printedBadges->isNotEmpty()) {
                                    foreach ($subDelegate->printedBadges as $printedBadge) {
                                        $isPrinted = true;

                                        if ($printedBadge->collected) {
                                            $isCollected = true;
                                            $isCollectedBy = $printedBadge->collected_by;
                                        }
                                    }
                                }
                                $visuals = [];
                                $howToCollectYourBadge = null;

                                if ($isCollectedBy != null) {
                                    $howToCollectYourBadge = $isCollectedBy;
                                } else {
                                    $data = $this->getTextAndVisualDetails($companyName, $subDelegate->badge_type);
                                    $howToCollectYourBadge = $data['howToCollectYourBadge'];
                                    $visuals = $data['imageLinks'];
                                }


                                $finalDelegate = [
                                    'transactionId' => $finalTransactionId,
                                    'name' => trim($name),
                                    'jobTitle' => $subDelegate->job_title,
                                    'companyName' => $companyName,
                                    'badgeType' => $subDelegate->badge_type,
                                    'emailAddress' => $subDelegate->email_address,
                                    'isPrinted' => $isPrinted,
                                    'isCollected' => $isCollected,
                                    'howToCollectYourBadge' => $howToCollectYourBadge,
                                    'visuals' => $visuals,
                                ];

                                array_push($allDelegates, $finalDelegate);
                            }
                        }
                    }
                }
            }
        }

        usort($allDelegates, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $allDelegates;
    }
}