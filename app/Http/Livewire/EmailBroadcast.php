<?php

namespace App\Http\Livewire;

use App\Mail\EmailBroadcast as MailEmailBroadcast;
use App\Models\Event as Events;
use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\Transaction as Transactions;
use App\Models\MainVisitor as MainVisitors;
use App\Models\AdditionalVisitor as AdditionalVisitors;
use App\Models\VisitorTransaction as VisitorTransactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class EmailBroadcast extends Component
{
    public $event, $allDelegates;

    public $startPoint, $endPoint;
    public $isHighlightingDelegates = false;
    public $sendOneEmailArrayIndex = null;

    public $badgeCategory;

    protected $listeners = ['broadcastEmailConfirmed' => 'sendEmailBroadcast'];

    public function mount($eventCategory, $eventId, $badgeCategory)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();

        // if ($eventCategory == "AFV") {
        //     $this->allDelegates = $this->getAllVisitors();
        // } else {
        //     $this->allDelegates = $this->getAllDelegates();
        // }
        $this->badgeCategory = $badgeCategory;
        $this->allDelegates = $this->getAllDelegates($this->badgeCategory);
    }

    public function render()
    {
        return view('livewire.admin.events.broadcast.email-broadcast');
    }

    public function highlightDelegates()
    {
        $this->validate([
            'startPoint' => 'required|min:1|max:' . count($this->allDelegates),
            'endPoint' => 'required|min:1|max:' . count($this->allDelegates),
        ]);

        for ($i = $this->startPoint - 1; $i < $this->endPoint; $i++) {
            $this->allDelegates[$i]['highlight'] = true;
        }

        $this->isHighlightingDelegates = true;
    }

    public function removeDelegatesHighlight()
    {
        $this->isHighlightingDelegates = false;

        for ($i = $this->startPoint - 1; $i < $this->endPoint; $i++) {
            $this->allDelegates[$i]['highlight'] = false;
        }
    }

    public function sendEmailBroadcastConfirmation()
    {
        $this->validate([
            'startPoint' => 'required|min:1|max:' . count($this->allDelegates),
            'endPoint' => 'required|min:1|max:' . count($this->allDelegates),
        ]);

        $this->dispatchBrowserEvent('swal:broadcast-email-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "This will send to number " . $this->startPoint . ' - ' . $this->endPoint . ' delegates',
        ]);
    }


    public function sendEmailBroadcast()
    {
        for ($i = $this->startPoint - 1; $i < $this->endPoint; $i++) {
            // SEND MAIL HERE AND UPDATE DATABASE
            $details = [
                'eventYear' => $this->event->year,
                'eventCategory' => $this->event->category,
                'registrationStatus' => $this->allDelegates[$i]['registrationStatus'],
                'fullName' => $this->allDelegates[$i]['fullName'],
                'jobTitle' => $this->allDelegates[$i]['jobTitle'],
                'companyName' => $this->allDelegates[$i]['companyName'],
                'companyName' => $this->allDelegates[$i]['companyName'],
                'badgeType' => $this->allDelegates[$i]['badgeType'],
                'transactionId' => $this->allDelegates[$i]['transactionId'],
                'qrCodeForPrint' => $this->allDelegates[$i]['qrCodeForPrint'],
                'emailAddress' => $this->allDelegates[$i]['emailAddress'],
                'badgeCategory' => $this->badgeCategory,
            ];

            try {
                Mail::to($details['emailAddress'])->send(new MailEmailBroadcast($details));
            } catch (\Exception $e) {
                Mail::to(config('app.ccEmailNotif.error'))->send(new MailEmailBroadcast($details));
            }

            // if($this->event->category == "AFV"){
            //     $visitorId = $this->allDelegates[$i]['delegateId'];
            //     $visitorType = $this->allDelegates[$i]['delegateType'];

            //     if($visitorType == "main"){
            //         MainVisitors::find($visitorId)->fill([
            //             'email_broadcast_sent_count' => $this->allDelegates[$i]['emailBroadcastSentCount'] + 1,
            //             'email_broadcast_sent_datetime' => Carbon::now(),
            //         ])->save();
            //     } else {
            //         AdditionalVisitors::find($visitorId)->fill([
            //             'email_broadcast_sent_count' => $this->allDelegates[$i]['emailBroadcastSentCount'] + 1,
            //             'email_broadcast_sent_datetime' => Carbon::now(),
            //         ])->save();
            //     }
            // } else {
            //     $delegateId = $this->allDelegates[$i]['delegateId'];
            //     $delegateType = $this->allDelegates[$i]['delegateType'];

            //     if($delegateType == "main"){
            //         MainDelegates::find($delegateId)->fill([
            //             'email_broadcast_sent_count' => $this->allDelegates[$i]['emailBroadcastSentCount'] + 1,
            //             'email_broadcast_sent_datetime' => Carbon::now(),
            //         ])->save();
            //     } else {
            //         AdditionalDelegates::find($delegateId)->fill([
            //             'email_broadcast_sent_count' => $this->allDelegates[$i]['emailBroadcastSentCount'] + 1,
            //             'email_broadcast_sent_datetime' => Carbon::now(),
            //         ])->save();
            //     }
            // }


            $delegateId = $this->allDelegates[$i]['delegateId'];
            $delegateType = $this->allDelegates[$i]['delegateType'];

            if ($delegateType == "main") {
                MainDelegates::find($delegateId)->fill([
                    'email_broadcast_sent_count' => $this->allDelegates[$i]['emailBroadcastSentCount'] + 1,
                    'email_broadcast_sent_datetime' => Carbon::now(),
                ])->save();
            } else {
                AdditionalDelegates::find($delegateId)->fill([
                    'email_broadcast_sent_count' => $this->allDelegates[$i]['emailBroadcastSentCount'] + 1,
                    'email_broadcast_sent_datetime' => Carbon::now(),
                ])->save();
            }

            $this->allDelegates[$i]['highlight'] = false;
            $this->allDelegates[$i]['emailBroadcastLastSent'] = Carbon::now()->format('M j, Y g:iA');
            $this->allDelegates[$i]['emailBroadcastSentCount'] = $this->allDelegates[$i]['emailBroadcastSentCount'] + 1;
        }

        $this->dispatchBrowserEvent('swal:broadcast-email-success', [
            'type' => 'success',
            'message' => 'Email sent successfully!',
            'text' => "",
        ]);

        $this->isHighlightingDelegates = false;
        $this->startPoint = null;
        $this->endPoint = null;
    }


    public function sendEmailConfirmation($arrayIndex)
    {
        $this->sendOneEmailArrayIndex = $arrayIndex;

        $details = [
            'eventYear' => $this->event->year,
            'eventCategory' => $this->event->category,
            'registrationStatus' => $this->allDelegates[$this->sendOneEmailArrayIndex]['registrationStatus'],
            'fullName' => $this->allDelegates[$this->sendOneEmailArrayIndex]['fullName'],
            'companyName' => $this->allDelegates[$this->sendOneEmailArrayIndex]['companyName'],
            'jobTitle' => $this->allDelegates[$this->sendOneEmailArrayIndex]['jobTitle'],
            'transactionId' => $this->allDelegates[$this->sendOneEmailArrayIndex]['transactionId'],
            'badgeType' => $this->allDelegates[$this->sendOneEmailArrayIndex]['badgeType'],
            'qrCodeForPrint' => $this->allDelegates[$this->sendOneEmailArrayIndex]['qrCodeForPrint'],
            'emailAddress' => $this->allDelegates[$this->sendOneEmailArrayIndex]['emailAddress'],
            'badgeCategory' => $this->badgeCategory,
        ];

        try {
            Mail::to($details['emailAddress'])->send(new MailEmailBroadcast($details));
        } catch (\Exception $e) {
            Mail::to(config('app.ccEmailNotif.error'))->send(new MailEmailBroadcast($details));
        }

        if ($this->event->category == "AFV") {
            $visitorId = $this->allDelegates[$this->sendOneEmailArrayIndex]['delegateId'];
            $visitorType = $this->allDelegates[$this->sendOneEmailArrayIndex]['delegateType'];

            if ($visitorType == "main") {
                MainVisitors::find($visitorId)->fill([
                    'email_broadcast_sent_count' => $this->allDelegates[$this->sendOneEmailArrayIndex]['emailBroadcastSentCount'] + 1,
                    'email_broadcast_sent_datetime' => Carbon::now(),
                ])->save();
            } else {
                AdditionalVisitors::find($visitorId)->fill([
                    'email_broadcast_sent_count' => $this->allDelegates[$this->sendOneEmailArrayIndex]['emailBroadcastSentCount'] + 1,
                    'email_broadcast_sent_datetime' => Carbon::now(),
                ])->save();
            }
        } else {
            $delegateId = $this->allDelegates[$this->sendOneEmailArrayIndex]['delegateId'];
            $delegateType = $this->allDelegates[$this->sendOneEmailArrayIndex]['delegateType'];

            if ($delegateType == "main") {
                MainDelegates::find($delegateId)->fill([
                    'email_broadcast_sent_count' => $this->allDelegates[$this->sendOneEmailArrayIndex]['emailBroadcastSentCount'] + 1,
                    'email_broadcast_sent_datetime' => Carbon::now(),
                ])->save();
            } else {
                AdditionalDelegates::find($delegateId)->fill([
                    'email_broadcast_sent_count' => $this->allDelegates[$this->sendOneEmailArrayIndex]['emailBroadcastSentCount'] + 1,
                    'email_broadcast_sent_datetime' => Carbon::now(),
                ])->save();
            }
        }

        // SEND MAIL HERE AND UPDATE DATABASE
        $this->allDelegates[$this->sendOneEmailArrayIndex]['emailBroadcastLastSent'] = Carbon::now()->format('M j, Y g:iA');
        $this->allDelegates[$this->sendOneEmailArrayIndex]['emailBroadcastSentCount'] = $this->allDelegates[$this->sendOneEmailArrayIndex]['emailBroadcastSentCount'] + 1;

        $this->dispatchBrowserEvent('swal:broadcast-email-success', [
            'type' => 'success',
            'message' => "Email sent successfully!",
            'text' => "",
        ]);
    }












    public function getAllDelegates($badgeCategory)
    {
        $tempArray = array();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->event->category == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        $sendToRegistrationStatus = ['confirmed', 'pending'];

        if ($badgeCategory == "youth-forum" || $badgeCategory == "youth-council") {
            $sendToRegistrationStatus = ['confirmed'];
        }

        $mainDelegates = MainDelegates::where('event_id', $this->event->id)->whereIn('registration_status', $sendToRegistrationStatus)->get();

        foreach ($mainDelegates as $mainDelegate) {
            if ($mainDelegate->alternative_company_name != null) {
                $companyName = $mainDelegate->alternative_company_name;
            } else {
                $companyName = $mainDelegate->company_name;
            }


            if (!$mainDelegate->delegate_cancelled) {
                $fullNameMain = $mainDelegate->first_name . ' ' . $mainDelegate->middle_name . ' ' . $mainDelegate->last_name;

                $transactionId = Transactions::where('event_id', $this->event->id)->where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->value('id');
                $lastDigit = 1000 + intval($transactionId);
                $finalTransactionId = $this->event->year . $eventCode . $lastDigit;


                $combinedStringPrint = "gpca@reg" . ',' . $this->event->id . ',' . $this->event->category . ',' . $mainDelegate->id . ',' . 'main';
                $finalCryptStringPrint = Crypt::encryptString($combinedStringPrint);
                $qrCodeForPrint = 'ca' . $finalCryptStringPrint . 'gp';

                if ($mainDelegate->email_broadcast_sent_datetime != null) {
                    $lastEmailSent = Carbon::parse($mainDelegate->email_broadcast_sent_datetime)->format('M j, Y g:iA');
                } else {
                    $lastEmailSent = 'N/A';
                }

                if ($badgeCategory == "youth-forum" && ($mainDelegate->badge_type == "Youth Forum")) {
                    array_push($tempArray, [
                        'delegateId' => $mainDelegate->id,
                        'delegateType' => "main",
                        'fullName' => $fullNameMain,
                        'companyName' => $companyName,
                        'jobTitle' => $mainDelegate->job_title,
                        'emailAddress' => $mainDelegate->email_address,
                        'transactionId' => $finalTransactionId,
                        'badgeType' => $mainDelegate->badge_type,
                        'qrCodeForPrint' => $qrCodeForPrint,
                        'emailBroadcastSentCount' => $mainDelegate->email_broadcast_sent_count,
                        'emailBroadcastLastSent' => $lastEmailSent,
                        'registrationStatus' => $mainDelegate->registration_status,
                        'highlight' => false,
                    ]);
                }

                if ($badgeCategory == "youth-council" && ($mainDelegate->badge_type == "Youth Council")) {
                    array_push($tempArray, [
                        'delegateId' => $mainDelegate->id,
                        'delegateType' => "main",
                        'fullName' => $fullNameMain,
                        'companyName' => $companyName,
                        'jobTitle' => $mainDelegate->job_title,
                        'emailAddress' => $mainDelegate->email_address,
                        'transactionId' => $finalTransactionId,
                        'badgeType' => $mainDelegate->badge_type,
                        'qrCodeForPrint' => $qrCodeForPrint,
                        'emailBroadcastSentCount' => $mainDelegate->email_broadcast_sent_count,
                        'emailBroadcastLastSent' => $lastEmailSent,
                        'registrationStatus' => $mainDelegate->registration_status,
                        'highlight' => false,
                    ]);
                }

                if ($badgeCategory == "all") {
                    array_push($tempArray, [
                        'delegateId' => $mainDelegate->id,
                        'delegateType' => "main",
                        'fullName' => $fullNameMain,
                        'companyName' => $companyName,
                        'jobTitle' => $mainDelegate->job_title,
                        'emailAddress' => $mainDelegate->email_address,
                        'transactionId' => $finalTransactionId,
                        'badgeType' => $mainDelegate->badge_type,
                        'qrCodeForPrint' => $qrCodeForPrint,
                        'emailBroadcastSentCount' => $mainDelegate->email_broadcast_sent_count,
                        'emailBroadcastLastSent' => $lastEmailSent,
                        'registrationStatus' => $mainDelegate->registration_status,
                        'highlight' => false,
                    ]);
                }

                $additionalDelegates = AdditionalDelegates::where('main_delegate_id', $mainDelegate->id)->get();

                if ($additionalDelegates->isNotEmpty()) {
                    foreach ($additionalDelegates as $additionalDelegate) {
                        if (!$additionalDelegate->delegate_cancelled) {
                            $fullNameSub = $additionalDelegate->first_name . ' ' . $additionalDelegate->middle_name . ' ' . $additionalDelegate->last_name;

                            $transactionId = Transactions::where('event_id', $this->event->id)->where('delegate_id', $additionalDelegate->id)->where('delegate_type', "sub")->value('id');
                            $lastDigit = 1000 + intval($transactionId);
                            $finalTransactionId = $this->event->year . $eventCode . $lastDigit;


                            $combinedStringPrint = "gpca@reg" . ',' . $this->event->id . ',' . $this->event->category . ',' . $additionalDelegate->id . ',' . 'sub';
                            $finalCryptStringPrint = Crypt::encryptString($combinedStringPrint);
                            $qrCodeForPrint = 'ca' . $finalCryptStringPrint . 'gp';

                            if ($additionalDelegate->email_broadcast_sent_datetime != null) {
                                $lastEmailSent = Carbon::parse($additionalDelegate->email_broadcast_sent_datetime)->format('M j, Y g:iA');
                            } else {
                                $lastEmailSent = 'N/A';
                            }

                            if ($badgeCategory == "youth-forum" && ($additionalDelegate->badge_type == "Youth Forum")) {
                                array_push($tempArray, [
                                    'delegateId' => $additionalDelegate->id,
                                    'delegateType' => "sub",
                                    'fullName' => $fullNameSub,
                                    'companyName' => $companyName,
                                    'jobTitle' => $additionalDelegate->job_title,
                                    'emailAddress' => $additionalDelegate->email_address,
                                    'transactionId' => $finalTransactionId,
                                    'badgeType' => $additionalDelegate->badge_type,
                                    'qrCodeForPrint' => $qrCodeForPrint,
                                    'emailBroadcastSentCount' => $additionalDelegate->email_broadcast_sent_count,
                                    'emailBroadcastLastSent' => $lastEmailSent,
                                    'registrationStatus' => $mainDelegate->registration_status,
                                    'highlight' => false,
                                ]);
                            }

                            if ($badgeCategory == "youth-council" && ($additionalDelegate->badge_type == "Youth Council")) {
                                array_push($tempArray, [
                                    'delegateId' => $additionalDelegate->id,
                                    'delegateType' => "sub",
                                    'fullName' => $fullNameSub,
                                    'companyName' => $companyName,
                                    'jobTitle' => $additionalDelegate->job_title,
                                    'emailAddress' => $additionalDelegate->email_address,
                                    'transactionId' => $finalTransactionId,
                                    'badgeType' => $additionalDelegate->badge_type,
                                    'qrCodeForPrint' => $qrCodeForPrint,
                                    'emailBroadcastSentCount' => $additionalDelegate->email_broadcast_sent_count,
                                    'emailBroadcastLastSent' => $lastEmailSent,
                                    'registrationStatus' => $mainDelegate->registration_status,
                                    'highlight' => false,
                                ]);
                            }

                            if ($badgeCategory == "all") {
                                array_push($tempArray, [
                                    'delegateId' => $additionalDelegate->id,
                                    'delegateType' => "sub",
                                    'fullName' => $fullNameSub,
                                    'companyName' => $companyName,
                                    'jobTitle' => $additionalDelegate->job_title,
                                    'emailAddress' => $additionalDelegate->email_address,
                                    'transactionId' => $finalTransactionId,
                                    'badgeType' => $additionalDelegate->badge_type,
                                    'qrCodeForPrint' => $qrCodeForPrint,
                                    'emailBroadcastSentCount' => $additionalDelegate->email_broadcast_sent_count,
                                    'emailBroadcastLastSent' => $lastEmailSent,
                                    'registrationStatus' => $mainDelegate->registration_status,
                                    'highlight' => false,
                                ]);
                            }
                        }
                    }
                }
            }
        }
        return $tempArray;
    }







    // public function getAllVisitors()
    // {
    //     $tempArray = array();

    //     foreach (config('app.eventCategories') as $eventCategoryC => $code) {
    //         if ($this->event->category == $eventCategoryC) {
    //             $eventCode = $code;
    //         }
    //     }

    //     $mainVisitors = MainVisitors::where('event_id', $this->event->id)->whereIn('registration_status', ['confirmed', 'pending'])->get();

    //     foreach ($mainVisitors as $mainVisitor) {
    //         if ($mainVisitor->alternative_company_name != null) {
    //             $companyName = $mainVisitor->alternative_company_name;
    //         } else {
    //             $companyName = $mainVisitor->company_name;
    //         }


    //         if (!$mainVisitor->visitor_cancelled) {
    //             $fullNameMain = $mainVisitor->first_name . ' ' . $mainVisitor->middle_name . ' ' . $mainVisitor->last_name;

    //             $transactionId = VisitorTransactions::where('event_id', $this->event->id)->where('visitor_id', $mainVisitor->id)->where('visitor_type', "main")->value('id');
    //             $lastDigit = 1000 + intval($transactionId);
    //             $finalTransactionId = $this->event->year . $eventCode . $lastDigit;


    //             $combinedStringPrint = "gpca@reg" . ',' . $this->event->id . ',' . $this->event->category . ',' . $mainVisitor->id . ',' . 'main';
    //             $finalCryptStringPrint = Crypt::encryptString($combinedStringPrint);
    //             $qrCodeForPrint = 'ca' . $finalCryptStringPrint . 'gp';

    //             if ($mainVisitor->email_broadcast_sent_datetime != null) {
    //                 $lastEmailSent = Carbon::parse($mainVisitor->email_broadcast_sent_datetime)->format('M j, Y g:iA');
    //             } else {
    //                 $lastEmailSent = 'N/A';
    //             }

    //             array_push($tempArray, [
    //                 'delegateId' => $mainVisitor->id,
    //                 'delegateType' => "main",
    //                 'fullName' => $fullNameMain,
    //                 'companyName' => $companyName,
    //                 'jobTitle' => $mainVisitor->job_title,
    //                 'emailAddress' => $mainVisitor->email_address,
    //                 'transactionId' => $finalTransactionId,
    //                 'qrCodeForPrint' => $qrCodeForPrint,
    //                 'emailBroadcastSentCount' => $mainVisitor->email_broadcast_sent_count,
    //                 'emailBroadcastLastSent' => $lastEmailSent,
    //                 'registrationStatus' => $mainVisitor->registration_status,
    //                 'highlight' => false,
    //             ]);

    //             $additionalVisitors = AdditionalVisitors::where('main_visitor_id', $mainVisitor->id)->get();

    //             if ($additionalVisitors->isNotEmpty()) {
    //                 foreach ($additionalVisitors as $additionalVisitor) {
    //                     if (!$additionalVisitor->visitor_cancelled) {
    //                         $fullNameSub = $additionalVisitor->first_name . ' ' . $additionalVisitor->middle_name . ' ' . $additionalVisitor->last_name;

    //                         $transactionId = VisitorTransactions::where('event_id', $this->event->id)->where('visitor_id', $additionalVisitor->id)->where('visitor_type', "sub")->value('id');
    //                         $lastDigit = 1000 + intval($transactionId);
    //                         $finalTransactionId = $this->event->year . $eventCode . $lastDigit;


    //                         $combinedStringPrint = "gpca@reg" . ',' . $this->event->id . ',' . $this->event->category . ',' . $additionalVisitor->id . ',' . 'sub';
    //                         $finalCryptStringPrint = Crypt::encryptString($combinedStringPrint);
    //                         $qrCodeForPrint = 'ca' . $finalCryptStringPrint . 'gp';

    //                         if ($additionalVisitor->email_broadcast_sent_datetime != null) {
    //                             $lastEmailSent = Carbon::parse($additionalVisitor->email_broadcast_sent_datetime)->format('M j, Y g:iA');
    //                         } else {
    //                             $lastEmailSent = 'N/A';
    //                         }

    //                         array_push($tempArray, [
    //                             'delegateId' => $additionalVisitor->id,
    //                             'delegateType' => "sub",
    //                             'fullName' => $fullNameSub,
    //                             'companyName' => $companyName,
    //                             'jobTitle' => $additionalVisitor->job_title,
    //                             'emailAddress' => $additionalVisitor->email_address,
    //                             'transactionId' => $finalTransactionId,
    //                             'qrCodeForPrint' => $qrCodeForPrint,
    //                             'emailBroadcastSentCount' => $additionalVisitor->email_broadcast_sent_count,
    //                             'emailBroadcastLastSent' => $lastEmailSent,
    //                             'registrationStatus' => $mainVisitor->registration_status,
    //                             'highlight' => false,
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return $tempArray;
    // }
}
