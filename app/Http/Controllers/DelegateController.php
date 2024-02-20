<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdditionalDelegate;
use App\Models\AdditionalVisitor;
use App\Models\Event;
use App\Models\EventRegistrationType;
use App\Models\MainDelegate;
use App\Models\MainVisitor;
use App\Models\PrintedBadge;
use App\Models\ScannedDelegate;
use App\Models\ScannedVisitor;
use App\Models\Transaction;
use App\Models\VisitorPrintedBadge;
use App\Models\VisitorTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;

class DelegateController extends Controller
{

    // =========================================================
    //                       RENDER VIEWS
    // =========================================================

    public function manageDelegateView()
    {
        return view('admin.delegates.delegate', [
            'pageTitle' => 'Manage Delegate',
        ]);
    }


    public function eventDelegateView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists() && $eventCategory != "AFS" && $eventCategory != "RCCA") {

            if ($eventCategory == "AFV") {
                $pageTitle = "Event Visitors";
            } else {
                $pageTitle = "Event Delegates";
            }

            return view('admin.events.delegates.delegates', [
                "pageTitle" => $pageTitle,
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function delegateDetailView($eventCategory, $eventId, $delegateType, $delegateId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists() && $eventCategory != "AFS" && $eventCategory != "RCCA") {
            if ($eventCategory == "AFV") {
                $visitorType = $delegateType;
                $visitorId = $delegateId;

                $finalVisitor = array();
                $tempVisitor = array();

                if ($visitorType == "main") {
                    $tempVisitor = MainVisitor::where('id', $visitorId)->first();
                } else {
                    $tempVisitor = AdditionalVisitor::where('id', $visitorId)->first();
                }

                if ($tempVisitor != null) {
                    $eventYear = Event::where('id', $eventId)->value('year');

                    foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                        if ($eventCategory == $eventCategoryC) {
                            $eventCode = $code;
                        }
                    }

                    if ($visitorType  == "main") {
                        $tempYear = Carbon::parse($tempVisitor->registered_date_time)->format('y');
                        $transactionId = VisitorTransaction::where('event_id', $eventId)->where('visitor_id', $visitorId)->where('visitor_type', "main")->value('id');
                        $lastDigit = 1000 + intval($transactionId);

                        $finalTransactionId = $eventYear . $eventCode . $lastDigit;
                        $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;

                        $finalVisitor = [
                            'eventCategory' => $eventCategory,
                            'eventId' => $eventId,
                            'visitorType' => $visitorType,
                            'visitorId' => $visitorId,
                            'mainVisitorId' => $visitorId,

                            'salutation' => $tempVisitor->salutation,
                            'first_name' => $tempVisitor->first_name,
                            'middle_name' => $tempVisitor->middle_name,
                            'last_name' => $tempVisitor->last_name,
                            'email_address' => $tempVisitor->email_address,
                            'mobile_number' => $tempVisitor->mobile_number,
                            'nationality' => $tempVisitor->nationality,
                            'job_title' => $tempVisitor->job_title,
                            'badge_type' => $tempVisitor->badge_type,

                            'pass_type' => $tempVisitor->pass_type,
                            'company_name' => $tempVisitor->company_name,
                            'alternative_company_name' => $tempVisitor->alternative_company_name,
                            'company_sector' => $tempVisitor->company_sector,
                            'company_address' => $tempVisitor->company_address,
                            'company_country' => $tempVisitor->company_country,
                            'company_city' => $tempVisitor->company_city,
                            'company_telephone_number' => $tempVisitor->company_telephone_number,
                            'company_mobile_number' => $tempVisitor->company_mobile_number,

                            'finalTransactionId' => $finalTransactionId,
                            'invoiceNumber' => $invoiceNumber,
                        ];
                    } else {
                        $mainVisitorInfo = MainVisitor::where('id', $tempVisitor->main_visitor_id)->first();

                        $tempYear = Carbon::parse($mainVisitorInfo->registered_date_time)->format('y');

                        $transactionId = VisitorTransaction::where('event_id', $eventId)->where('visitor_id', $visitorId)->where('visitor_type', "sub")->value('id');

                        $lastDigit = 1000 + intval($transactionId);
                        $transactionId2 = VisitorTransaction::where('event_id', $eventId)->where('visitor_id', $mainVisitorInfo->id)->where('visitor_type', "main")->value('id');
                        $lastDigit2 = 1000 + intval($transactionId2);

                        $finalTransactionId = $eventYear . $eventCode . $lastDigit;
                        $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit2;

                        $finalVisitor = [
                            'visitorType' => $visitorType,
                            'visitorId' => $visitorId,
                            'mainVisitorId' => $mainVisitorInfo->id,

                            'salutation' => $tempVisitor->salutation,
                            'first_name' => $tempVisitor->first_name,
                            'middle_name' => $tempVisitor->middle_name,
                            'last_name' => $tempVisitor->last_name,
                            'email_address' => $tempVisitor->email_address,
                            'mobile_number' => $tempVisitor->mobile_number,
                            'nationality' => $tempVisitor->nationality,
                            'job_title' => $tempVisitor->job_title,
                            'badge_type' => $tempVisitor->badge_type,

                            'pass_type' => $mainVisitorInfo->pass_type,
                            'company_name' => $mainVisitorInfo->company_name,
                            'alternative_company_name' => $mainVisitorInfo->alternative_company_name,
                            'company_sector' => $mainVisitorInfo->company_sector,
                            'company_address' => $mainVisitorInfo->company_address,
                            'company_country' => $mainVisitorInfo->company_country,
                            'company_city' => $mainVisitorInfo->company_city,
                            'company_telephone_number' => $mainVisitorInfo->company_telephone_number,
                            'company_mobile_number' => $mainVisitorInfo->company_mobile_number,

                            'finalTransactionId' => $finalTransactionId,
                            'invoiceNumber' => $invoiceNumber,
                        ];
                    }

                    return view('admin.events.delegates.delegates_detail', [
                        "pageTitle" => "Event Visitor",
                        "eventCategory" => $eventCategory,
                        "eventId" => $eventId,
                        "finalVisitor" => $finalVisitor,
                    ]);
                } else {
                    abort(404, 'The URL is incorrect');
                }
            } else {
                $finalDelegate = array();
                $tempDelegate = array();

                if ($delegateType == "main") {
                    $tempDelegate = MainDelegate::where('id', $delegateId)->first();
                } else {
                    $tempDelegate = AdditionalDelegate::where('id', $delegateId)->first();
                }

                if ($tempDelegate != null) {
                    $eventYear = Event::where('id', $eventId)->value('year');

                    foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                        if ($eventCategory == $eventCategoryC) {
                            $eventCode = $code;
                        }
                    }

                    if ($delegateType  == "main") {

                        $tempYear = Carbon::parse($tempDelegate->registered_date_time)->format('y');

                        $transactionId = Transaction::where('event_id', $eventId)->where('delegate_id', $delegateId)->where('delegate_type', "main")->value('id');

                        $lastDigit = 1000 + intval($transactionId);

                        $finalTransactionId = $eventYear . $eventCode . $lastDigit;
                        $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit;

                        $finalDelegate = [
                            'eventCategory' => $eventCategory,
                            'eventId' => $eventId,
                            'delegateType' => $delegateType,
                            'delegateId' => $delegateId,
                            'mainDelegateId' => $delegateId,

                            'salutation' => $tempDelegate->salutation,
                            'first_name' => $tempDelegate->first_name,
                            'middle_name' => $tempDelegate->middle_name,
                            'last_name' => $tempDelegate->last_name,
                            'email_address' => $tempDelegate->email_address,
                            'mobile_number' => $tempDelegate->mobile_number,
                            'nationality' => $tempDelegate->nationality,
                            'job_title' => $tempDelegate->job_title,
                            'badge_type' => $tempDelegate->badge_type,
                            'country' => $tempDelegate->country,

                            'seat_number' => $tempDelegate->seat_number,

                            'pass_type' => $tempDelegate->pass_type,
                            'company_name' => $tempDelegate->company_name,
                            'alternative_company_name' => $tempDelegate->alternative_company_name,
                            'company_sector' => $tempDelegate->company_sector,
                            'company_address' => $tempDelegate->company_address,
                            'company_country' => $tempDelegate->company_country,
                            'company_city' => $tempDelegate->company_city,
                            'company_telephone_number' => $tempDelegate->company_telephone_number,
                            'company_mobile_number' => $tempDelegate->company_mobile_number,

                            'finalTransactionId' => $finalTransactionId,
                            'invoiceNumber' => $invoiceNumber,
                        ];
                    } else {
                        $mainDelegateInfo = MainDelegate::where('id', $tempDelegate->main_delegate_id)->first();

                        $tempYear = Carbon::parse($mainDelegateInfo->registered_date_time)->format('y');

                        $transactionId = Transaction::where('event_id', $eventId)->where('delegate_id', $delegateId)->where('delegate_type', "sub")->value('id');

                        $lastDigit = 1000 + intval($transactionId);
                        $transactionId2 = Transaction::where('event_id', $eventId)->where('delegate_id', $mainDelegateInfo->id)->where('delegate_type', "main")->value('id');
                        $lastDigit2 = 1000 + intval($transactionId2);

                        $finalTransactionId = $eventYear . $eventCode . $lastDigit;
                        $invoiceNumber = $eventCategory . $tempYear . "/" . $lastDigit2;

                        $finalDelegate = [
                            'delegateType' => $delegateType,
                            'delegateId' => $delegateId,
                            'mainDelegateId' => $mainDelegateInfo->id,

                            'salutation' => $tempDelegate->salutation,
                            'first_name' => $tempDelegate->first_name,
                            'middle_name' => $tempDelegate->middle_name,
                            'last_name' => $tempDelegate->last_name,
                            'email_address' => $tempDelegate->email_address,
                            'mobile_number' => $tempDelegate->mobile_number,
                            'nationality' => $tempDelegate->nationality,
                            'job_title' => $tempDelegate->job_title,
                            'badge_type' => $tempDelegate->badge_type,
                            'country' => $tempDelegate->country,
                            
                            'seat_number' => $tempDelegate->seat_number,

                            'pass_type' => $mainDelegateInfo->pass_type,
                            'company_name' => $mainDelegateInfo->company_name,
                            'alternative_company_name' => $mainDelegateInfo->alternative_company_name,
                            'company_sector' => $mainDelegateInfo->company_sector,
                            'company_address' => $mainDelegateInfo->company_address,
                            'company_country' => $mainDelegateInfo->company_country,
                            'company_city' => $mainDelegateInfo->company_city,
                            'company_telephone_number' => $mainDelegateInfo->company_telephone_number,
                            'company_mobile_number' => $mainDelegateInfo->company_mobile_number,

                            'finalTransactionId' => $finalTransactionId,
                            'invoiceNumber' => $invoiceNumber,
                        ];
                    }

                    return view('admin.events.delegates.delegates_detail', [
                        "pageTitle" => "Event Delegate",
                        "eventCategory" => $eventCategory,
                        "eventId" => $eventId,
                        "finalDelegate" => $finalDelegate,
                    ]);
                } else {
                    abort(404, 'The URL is incorrect');
                }
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function printedBadgeListView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            return view('admin.events.printed-badge.printed_badge_list', [
                "pageTitle" => "Printed badges",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function scannedDelegateListView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            if ($eventCategory == "AFV") {
                $pageTitle = "Scanned visitors";
            } else {
                $pageTitle = "Scanned delegates";
            }
            return view('admin.events.scanned-delegate.scanned_delegate_list', [
                "pageTitle" => $pageTitle,
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }


    public function eventEmailBroadcastView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            return view('admin.events.broadcast.email_broadcast', [
                "pageTitle" => "Email broadcast",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    // =========================================================
    //                       RENDER LOGICS
    // =========================================================

    public function delegateDetailScanBadge($eventCategory, $eventId, $delegateType, $delegateId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            if ($eventCategory == "AFV") {
                $tempVisitor = array();
                $visitorId = $delegateId;
                $visitorType = $delegateType;

                if ($visitorType == "main") {
                    $tempVisitor = MainVisitor::where('id', $visitorId)->first();
                } else {
                    $tempVisitor = AdditionalVisitor::where('id', $visitorId)->first();
                }

                if ($tempVisitor == null) {
                    return view('admin.events.scanned-delegate.scanned_feedback', [
                        "pageTitle" => "Scanned Visitor",
                        "eventCategory" => $eventCategory,
                        "eventId" => $eventId,
                        "delegateType" => $visitorType,
                        "delegateId" => $visitorId,
                        "status" => "Failed",
                    ]);
                } else {
                    ScannedVisitor::create([
                        'event_id' => $eventId,
                        'event_category' => $eventCategory,
                        'visitor_id' => $visitorId,
                        'visitor_type' => $visitorType,
                        'scanned_date_time' => Carbon::now(),
                    ]);

                    if ($visitorType == "main") {
                        $companyName = $tempVisitor->company_name;
                    } else {
                        $companyName = MainVisitor::where('id', $tempVisitor->main_visitor_id)->value('company_name');
                    }

                    return view('admin.events.scanned-delegate.scanned_feedback', [
                        "pageTitle" => "Scanned Visitor",
                        "eventCategory" => $eventCategory,
                        "eventId" => $eventId,
                        "delegateType" => $visitorType,
                        "delegateId" => $visitorId,
                        "status" => "Success",
                        "name" => $tempVisitor->salutation . ' ' . $tempVisitor->first_name . ' ' . $tempVisitor->middle_name . ' ' . $tempVisitor->last_name,
                        "jobTitle" => $tempVisitor->job_title,
                        "companyName" => $companyName,
                    ]);
                }
            } else {
                $tempDelegate = array();

                if ($delegateType == "main") {
                    $tempDelegate = MainDelegate::where('id', $delegateId)->first();
                } else {
                    $tempDelegate = AdditionalDelegate::where('id', $delegateId)->first();
                }

                if ($tempDelegate == null) {
                    return view('admin.events.scanned-delegate.scanned_feedback', [
                        "pageTitle" => "Scanned delegate",
                        "eventCategory" => $eventCategory,
                        "eventId" => $eventId,
                        "delegateType" => $delegateType,
                        "delegateId" => $delegateId,
                        "status" => "Failed",
                    ]);
                } else {
                    ScannedDelegate::create([
                        'event_id' => $eventId,
                        'event_category' => $eventCategory,
                        'delegate_id' => $delegateId,
                        'delegate_type' => $delegateType,
                        'scanned_date_time' => Carbon::now(),
                    ]);

                    if ($delegateType == "main") {
                        $companyName = $tempDelegate->company_name;
                    } else {
                        $companyName = MainDelegate::where('id', $tempDelegate->main_delegate_id)->value('company_name');
                    }

                    return view('admin.events.scanned-delegate.scanned_feedback', [
                        "pageTitle" => "Scanned delegate",
                        "eventCategory" => $eventCategory,
                        "eventId" => $eventId,
                        "delegateType" => $delegateType,
                        "delegateId" => $delegateId,
                        "status" => "Success",
                        "name" => $tempDelegate->salutation . ' ' . $tempDelegate->first_name . ' ' . $tempDelegate->middle_name . ' ' . $tempDelegate->last_name,
                        "jobTitle" => $tempDelegate->job_title,
                        "companyName" => $companyName,
                    ]);
                }
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function delegateDetailPrintBadge($eventCategory, $eventId, $delegateType, $delegateId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();

            $finalDelegate = array();
            $tempDelegate = array();

            if ($eventCategory == "AFV") {
                if ($delegateType == "main") {
                    $tempDelegate = MainVisitor::where('id', $delegateId)->first();
                } else {
                    $tempDelegate = AdditionalVisitor::where('id', $delegateId)->first();
                }
            } else {
                if ($delegateType == "main") {
                    $tempDelegate = MainDelegate::where('id', $delegateId)->first();
                } else {
                    $tempDelegate = AdditionalDelegate::where('id', $delegateId)->first();
                }
            }


            $frontBanner = public_path(Storage::url($event->badge_front_banner));
            $finalFrontBanner = str_replace('\/', '/', $frontBanner);

            $backtBanner = public_path(Storage::url($event->badge_back_banner));
            $finalBackBanner = str_replace('\/', '/', $backtBanner);

            if ($eventCategory == "PSW") {
                // $finalHeight = (15.2 / 2.54) * 72;
                // $finalWidth = (23.3 / 2.54) * 72;


                // $finalHeight = (12.5 / 2.54) * 72;
                // $finalWidth = (17.2 / 2.54) * 72;


                $finalHeight = (13.1 / 2.54) * 72;
                $finalWidth = (18.2 / 2.54) * 72;
            } else {
                // $finalHeight = (15.2 / 2.54) * 72;
                // $finalWidth = (22.3 / 2.54) * 72;


                // $finalHeight = (15.6 / 2.54) * 72;
                // $finalWidth = (25.8 / 2.54) * 72;


                $finalHeight = (13.1 / 2.54) * 72;
                $finalWidth = (21.0 / 2.54) * 72;
            }

            $combinedString = "gpca@reg" . ',' . $eventId . ',' . $eventCategory . ',' . $delegateId . ',' . $delegateType;
            $finalCryptString = base64_encode($combinedString);
            $scanDelegateUrl = 'gpca' . $finalCryptString;

            if ($tempDelegate != null) {
                $registrationType = EventRegistrationType::where('event_id', $eventId)->where('event_category', $eventCategory)->where('registration_type', $tempDelegate->badge_type)->first();

                if ($delegateType  == "main") {
                    if ($tempDelegate->salutation == "Dr." || $tempDelegate->salutation == "Prof.") {
                        $delegateSalutation = $tempDelegate->salutation;
                    } else {
                        $delegateSalutation = null;
                    }


                    if ($tempDelegate->alternative_company_name != null) {
                        $companyName = $tempDelegate->alternative_company_name;
                    } else {
                        $companyName = $tempDelegate->company_name;
                    }

                    $seatNumber = null;
                    if ($eventCategory == "AF") {
                        if ($tempDelegate->seat_number != null) {
                            $seatNumber = $tempDelegate->seat_number;
                        }
                    }

                    $finalDelegate = [
                        'salutation' => $delegateSalutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,
                        'companyName' => $companyName,
                        'frontBanner' =>  $finalFrontBanner,
                        'backBanner' =>  $finalBackBanner,
                        'textColor' => $event->badge_footer_link_color,
                        'bgColor' => $event->badge_footer_bg_color,
                        'link' => $event->badge_footer_link,

                        'frontText' => $registrationType->badge_footer_front_name,
                        'frontTextColor' => $registrationType->badge_footer_front_text_color,
                        'frontTextBGColor' => $registrationType->badge_footer_front_bg_color,
                        'backText' => $registrationType->badge_footer_back_name,
                        'backTextColor' => $registrationType->badge_footer_back_text_color,
                        'backTextBGColor' => $registrationType->badge_footer_back_bg_color,
                        'finalWidth' => $finalWidth,
                        'finalHeight' => $finalHeight,

                        'seatNumber' => $seatNumber,

                        'scanDelegateUrl' => $scanDelegateUrl,
                        'badgeName' => $tempDelegate->last_name . '_' . $companyName . '_BADGE',
                    ];
                } else {
                    if ($eventCategory == "AFV") {
                        $mainDelegateInfo = MainVisitor::where('id', $tempDelegate->main_visitor_id)->first();
                    } else {
                        $mainDelegateInfo = MainDelegate::where('id', $tempDelegate->main_delegate_id)->first();
                    }

                    if ($tempDelegate->salutation == "Dr." || $tempDelegate->salutation == "Prof.") {
                        $delegateSalutation = $tempDelegate->salutation;
                    } else {
                        $delegateSalutation = null;
                    }

                    if ($mainDelegateInfo->alternative_company_name != null) {
                        $companyName = $mainDelegateInfo->alternative_company_name;
                    } else {
                        $companyName = $mainDelegateInfo->company_name;
                    }

                    $seatNumber = null;
                    if ($eventCategory == "AF") {
                        if ($tempDelegate->seat_number != null) {
                            $seatNumber = $tempDelegate->seat_number;
                        }
                    }

                    $finalDelegate = [
                        'salutation' => $delegateSalutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,
                        'companyName' => $companyName,
                        'frontBanner' =>  $finalFrontBanner,
                        'backBanner' =>  $finalBackBanner,
                        'textColor' => $event->badge_footer_link_color,
                        'bgColor' => $event->badge_footer_bg_color,
                        'link' => $event->badge_footer_link,

                        'frontText' => $registrationType->badge_footer_front_name,
                        'frontTextColor' => $registrationType->badge_footer_front_text_color,
                        'frontTextBGColor' => $registrationType->badge_footer_front_bg_color,
                        'backText' => $registrationType->badge_footer_back_name,
                        'backTextColor' => $registrationType->badge_footer_back_text_color,
                        'backTextBGColor' => $registrationType->badge_footer_back_bg_color,
                        'finalWidth' => $finalWidth,
                        'finalHeight' => $finalHeight,

                        'seatNumber' => $seatNumber,

                        'scanDelegateUrl' => $scanDelegateUrl,
                        'badgeName' => $tempDelegate->last_name . '_' . $companyName . '_BADGE',
                    ];
                }

                if ($eventCategory == "PSW") {
                    $pdf = Pdf::loadView('admin.events.delegates.delegate_badgev6', $finalDelegate, [
                        'margin_top' => 0,
                        'margin_right' => 0,
                        'margin_bottom' => 0,
                        'margin_left' => 0,
                    ]);
                    $pdf->setPaper('A4', 'portrait');
                } else {
                    $pdf = Pdf::loadView('admin.events.delegates.delegate_badgev7', $finalDelegate, [
                        'margin_top' => 0,
                        'margin_right' => 0,
                        'margin_bottom' => 0,
                        'margin_left' => 0,
                    ]);
                    $pdf->setPaper(array(0, 0, $finalWidth, $finalHeight), 'custom');
                }

                // return view('admin.events.delegates.delegate_badgev4', $finalDelegate);
                return $pdf->stream($finalDelegate['badgeName'] . '.pdf',  array('Attachment' => 0));
                // return $pdf->download($finalDelegate['badgeName'] . '.pdf');
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }




    public function delegateDetailPublicPrintBadge($eventCategory, $eventId, $delegateType, $delegateId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();

            $finalDelegate = array();
            $tempDelegate = array();

            if ($eventCategory == "AFV") {
                if ($delegateType == "main") {
                    $tempDelegate = MainVisitor::where('id', $delegateId)->first();
                } else {
                    $tempDelegate = AdditionalVisitor::where('id', $delegateId)->first();
                }
            } else {
                if ($delegateType == "main") {
                    $tempDelegate = MainDelegate::where('id', $delegateId)->first();
                } else {
                    $tempDelegate = AdditionalDelegate::where('id', $delegateId)->first();
                }
            }


            $frontBanner = public_path(Storage::url($event->badge_front_banner));
            $finalFrontBanner = str_replace('\/', '/', $frontBanner);

            $backtBanner = public_path(Storage::url($event->badge_back_banner));
            $finalBackBanner = str_replace('\/', '/', $backtBanner);

            if ($eventCategory == "PSW") {
                // $finalHeight = (15.2 / 2.54) * 72;
                // $finalWidth = (23.3 / 2.54) * 72;


                // $finalHeight = (12.5 / 2.54) * 72;
                // $finalWidth = (17.2 / 2.54) * 72;


                $finalHeight = (13.1 / 2.54) * 72;
                $finalWidth = (18.2 / 2.54) * 72;
            } else {
                // $finalHeight = (15.2 / 2.54) * 72;
                // $finalWidth = (22.3 / 2.54) * 72;


                // $finalHeight = (15.6 / 2.54) * 72;
                // $finalWidth = (25.8 / 2.54) * 72;


                $finalHeight = (13.1 / 2.54) * 72;
                $finalWidth = (21.0 / 2.54) * 72;
            }

            $combinedString = "gpca@reg" . ',' . $eventId . ',' . $eventCategory . ',' . $delegateId . ',' . $delegateType;
            $finalCryptString = base64_encode($combinedString);
            $scanDelegateUrl = 'gpca' . $finalCryptString;

            if ($tempDelegate != null) {
                $registrationType = EventRegistrationType::where('event_id', $eventId)->where('event_category', $eventCategory)->where('registration_type', $tempDelegate->badge_type)->first();

                if ($delegateType  == "main") {
                    if ($tempDelegate->salutation == "Dr." || $tempDelegate->salutation == "Prof.") {
                        $delegateSalutation = $tempDelegate->salutation;
                    } else {
                        $delegateSalutation = null;
                    }


                    if ($tempDelegate->alternative_company_name != null) {
                        $companyName = $tempDelegate->alternative_company_name;
                    } else {
                        $companyName = $tempDelegate->company_name;
                    }

                    $seatNumber = null;
                    if ($eventCategory == "AF") {
                        if ($tempDelegate->seat_number != null) {
                            $seatNumber = $tempDelegate->seat_number;
                        }
                    }

                    $finalDelegate = [
                        'salutation' => $delegateSalutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,
                        'companyName' => $companyName,
                        'frontBanner' =>  $finalFrontBanner,
                        'backBanner' =>  $finalBackBanner,
                        'textColor' => $event->badge_footer_link_color,
                        'bgColor' => $event->badge_footer_bg_color,
                        'link' => $event->badge_footer_link,

                        'frontText' => $registrationType->badge_footer_front_name,
                        'frontTextColor' => $registrationType->badge_footer_front_text_color,
                        'frontTextBGColor' => $registrationType->badge_footer_front_bg_color,
                        'backText' => $registrationType->badge_footer_back_name,
                        'backTextColor' => $registrationType->badge_footer_back_text_color,
                        'backTextBGColor' => $registrationType->badge_footer_back_bg_color,
                        'finalWidth' => $finalWidth,
                        'finalHeight' => $finalHeight,

                        'seatNumber' => $seatNumber,

                        'scanDelegateUrl' => $scanDelegateUrl,
                        'badgeName' => $tempDelegate->last_name . '_' . $companyName . '_BADGE',
                    ];
                } else {
                    if ($eventCategory == "AFV") {
                        $mainDelegateInfo = MainVisitor::where('id', $tempDelegate->main_visitor_id)->first();
                    } else {
                        $mainDelegateInfo = MainDelegate::where('id', $tempDelegate->main_delegate_id)->first();
                    }

                    if ($tempDelegate->salutation == "Dr." || $tempDelegate->salutation == "Prof.") {
                        $delegateSalutation = $tempDelegate->salutation;
                    } else {
                        $delegateSalutation = null;
                    }

                    if ($mainDelegateInfo->alternative_company_name != null) {
                        $companyName = $mainDelegateInfo->alternative_company_name;
                    } else {
                        $companyName = $mainDelegateInfo->company_name;
                    }

                    $seatNumber = null;
                    if ($eventCategory == "AF") {
                        if ($tempDelegate->seat_number != null) {
                            $seatNumber = $tempDelegate->seat_number;
                        }
                    }

                    $finalDelegate = [
                        'salutation' => $delegateSalutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,
                        'companyName' => $companyName,
                        'frontBanner' =>  $finalFrontBanner,
                        'backBanner' =>  $finalBackBanner,
                        'textColor' => $event->badge_footer_link_color,
                        'bgColor' => $event->badge_footer_bg_color,
                        'link' => $event->badge_footer_link,

                        'frontText' => $registrationType->badge_footer_front_name,
                        'frontTextColor' => $registrationType->badge_footer_front_text_color,
                        'frontTextBGColor' => $registrationType->badge_footer_front_bg_color,
                        'backText' => $registrationType->badge_footer_back_name,
                        'backTextColor' => $registrationType->badge_footer_back_text_color,
                        'backTextBGColor' => $registrationType->badge_footer_back_bg_color,
                        'finalWidth' => $finalWidth,
                        'finalHeight' => $finalHeight,

                        'seatNumber' => $seatNumber,

                        'scanDelegateUrl' => $scanDelegateUrl,
                        'badgeName' => $tempDelegate->last_name . '_' . $companyName . '_BADGE',
                    ];
                }

                if ($eventCategory == "PSW") {
                    $pdf = Pdf::loadView('admin.events.delegates.delegate_badgev6', $finalDelegate, [
                        'margin_top' => 0,
                        'margin_right' => 0,
                        'margin_bottom' => 0,
                        'margin_left' => 0,
                    ]);
                    $pdf->setPaper('A4', 'portrait');
                } else {
                    $pdf = Pdf::loadView('admin.events.delegates.delegate_badgev7', $finalDelegate, [
                        'margin_top' => 0,
                        'margin_right' => 0,
                        'margin_bottom' => 0,
                        'margin_left' => 0,
                    ]);
                    $pdf->setPaper(array(0, 0, $finalWidth, $finalHeight), 'custom');
                }

                if ($eventCategory == "AFV") {
                    VisitorPrintedBadge::create([
                        'event_id' => $eventId,
                        'event_category' => $eventCategory,
                        'visitor_id' => $delegateId,
                        'visitor_type' => $delegateType,
                        'printed_date_time' => Carbon::now(),
                    ]);
                } else {
                    PrintedBadge::create([
                        'event_id' => $eventId,
                        'event_category' => $eventCategory,
                        'delegate_id' => $delegateId,
                        'delegate_type' => $delegateType,
                        'printed_date_time' => Carbon::now(),
                    ]);
                }

                // return view('admin.events.delegates.delegate_badgev4', $finalDelegate);
                // return $pdf->stream($finalDelegate['badgeName'] . '.pdf',  array('Attachment'=> 0) );
                return $pdf->download($finalDelegate['badgeName'] . '.pdf');
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }
}
