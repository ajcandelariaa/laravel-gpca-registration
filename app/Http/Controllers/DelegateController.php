<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdditionalDelegate;
use App\Models\Event;
use App\Models\MainDelegate;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;


class DelegateController extends Controller
{
    // RENDER VIEWS
    public function manageDelegateView()
    {
        return view('admin.delegates.delegate', [
            'pageTitle' => 'Manage Delegate',
        ]);
    }


    public function eventDelegateView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            return view('admin.event.detail.delegates.delegates', [
                "pageTitle" => "Event Delegates",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function delegateDetailView($eventCategory, $eventId, $delegateType, $delegateId, Request $request){
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $finalDelegate = array();
            $tempDelegate = array();

            if($delegateType == "main"){
                $tempDelegate = MainDelegate::where('id', $delegateId)->first();
            } else {
                $tempDelegate = AdditionalDelegate::where('id', $delegateId)->first();
            }

            if($tempDelegate != null){
                if($delegateType  == "main"){
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
    
                return view('admin.event.detail.delegates.delegates_detail', [
                    "pageTitle" => "Event Delegates",
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

    public function delegateDetailPrintBadge($eventCategory, $eventId, $delegateType, $delegateId){
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $finalDelegate = array();
            $tempDelegate = array();

            if($delegateType == "main"){
                $tempDelegate = MainDelegate::where('id', $delegateId)->first();
            } else {
                $tempDelegate = AdditionalDelegate::where('id', $delegateId)->first();
            }

            if($tempDelegate != null){
                if($delegateType  == "main"){
                    $finalDelegate = [
                        'salutation' => $tempDelegate->salutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,
                        'companyName' => $tempDelegate->company_name,
                    ];
                } else {
                    $mainDelegateInfo = MainDelegate::where('id', $tempDelegate->main_delegate_id)->first();
                    $finalDelegate = [
                        'salutation' => $tempDelegate->salutation,
                        'first_name' => $tempDelegate->first_name,
                        'middle_name' => $tempDelegate->middle_name,
                        'last_name' => $tempDelegate->last_name,
                        'job_title' => $tempDelegate->job_title,
                        'badge_type' => $tempDelegate->badge_type,
                        'companyName' => $mainDelegateInfo->company_name,
                    ];
                }
                $pdf = Pdf::loadView('admin.event.detail.delegates.delegate_badge', $finalDelegate);
                return $pdf->stream('badge.pdf');
            } else {
                abort(404, 'The URL is incorrect');
            }
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function checkPhpInfo(){
        phpinfo();
    }
}
