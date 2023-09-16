<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdditionalDelegate;
use App\Models\Event;
use App\Models\EventRegistrationType;
use App\Models\MainDelegate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists() && $eventCategory != "AFS" && $eventCategory != "AFV" && $eventCategory != "RCCA") {
            return view('admin.events.delegates.delegates', [
                "pageTitle" => "Event Delegates",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function delegateDetailView($eventCategory, $eventId, $delegateType, $delegateId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists() && $eventCategory != "AFS" && $eventCategory != "AFV" && $eventCategory != "RCCA") {
            $finalDelegate = array();
            $tempDelegate = array();

            if ($delegateType == "main") {
                $tempDelegate = MainDelegate::where('id', $delegateId)->first();
            } else {
                $tempDelegate = AdditionalDelegate::where('id', $delegateId)->first();
            }

            if ($tempDelegate != null) {
                if ($delegateType  == "main") {
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

                        'pass_type' => $tempDelegate->pass_type,
                        'companyName' => $tempDelegate->company_name,
                        'company_sector' => $tempDelegate->company_sector,
                        'company_address' => $tempDelegate->company_address,
                        'company_country' => $tempDelegate->company_country,
                        'company_city' => $tempDelegate->company_city,
                        'company_telephone_number' => $tempDelegate->company_telephone_number,
                        'company_mobile_number' => $tempDelegate->company_mobile_number,
                    ];
                } else {
                    $mainDelegateInfo = MainDelegate::where('id', $tempDelegate->main_delegate_id)->first();

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

                        'pass_type' => $mainDelegateInfo->pass_type,
                        'companyName' => $mainDelegateInfo->company_name,
                        'company_sector' => $mainDelegateInfo->company_sector,
                        'company_address' => $mainDelegateInfo->company_address,
                        'company_country' => $mainDelegateInfo->company_country,
                        'company_city' => $mainDelegateInfo->company_city,
                        'company_telephone_number' => $mainDelegateInfo->company_telephone_number,
                        'company_mobile_number' => $mainDelegateInfo->company_mobile_number,
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
            return view('admin.events.scanned-delegate.scanned_delegate_list', [
                "pageTitle" => "Scanned delegates",
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

    public function delegateDetailPrintBadge($eventCategory, $eventId, $delegateType, $delegateId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $finalDelegate = array();
            $tempDelegate = array();

            $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();

            if ($delegateType == "main") {
                $tempDelegate = MainDelegate::where('id', $delegateId)->first();
            } else {
                $tempDelegate = AdditionalDelegate::where('id', $delegateId)->first();
            }
            $frontBanner = public_path(Storage::url($event->badge_front_banner));
            $finalFrontBanner = str_replace('\/', '/', $frontBanner);

            $backtBanner = public_path(Storage::url($event->badge_back_banner));
            $finalBackBanner = str_replace('\/', '/', $backtBanner);

            // $finalWidth = (20.3 / 2.54) * 72;
            $finalHeight = (15.2 / 2.54) * 72;

            $finalWidth = (22.3 / 2.54) * 72;


            if ($tempDelegate != null) {
                $registrationType = EventRegistrationType::where('event_id', $eventId)->where('event_category', $eventCategory)->where('registration_type', $tempDelegate->badge_type)->first();

                if ($delegateType  == "main") {
                    if($tempDelegate->salutation == "Dr." || $tempDelegate->salutation == "Prof."){
                        $delegateSalutation = $tempDelegate->salutation;
                    } else {
                        $delegateSalutation = null;
                    }

                    $finalDelegate = [
                        'salutation' => $delegateSalutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,
                        'companyName' => $tempDelegate->company_name,
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
                    ];
                } else {
                    $mainDelegateInfo = MainDelegate::where('id', $tempDelegate->main_delegate_id)->first();
                    if($tempDelegate->salutation == "Dr." || $tempDelegate->salutation == "Prof."){
                        $delegateSalutation = $tempDelegate->salutation;
                    } else {
                        $delegateSalutation = null;
                    }

                    $finalDelegate = [
                        'salutation' => $delegateSalutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,
                        'companyName' => $mainDelegateInfo->company_name,
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
                    ];
                }

                $pdf = Pdf::loadView('admin.events.delegates.delegate_badgev4', $finalDelegate, [
                    'margin_top' => 0,
                    'margin_right' => 0,
                    'margin_bottom' => 0,
                    'margin_left' => 0,
                ]);

                $pdf->setPaper(array(0, 0, $finalWidth, $finalHeight), 'custom');

                return $pdf->stream('badge.pdf');
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }
}
