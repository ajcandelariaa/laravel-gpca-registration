<?php

namespace App\Http\Controllers;

use App\Models\AdditionalDelegate;
use App\Models\Event;
use App\Models\EventRegistrationType;
use App\Models\MainDelegate;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{

    // =========================================================
    //                       RENDER VIEWS
    // =========================================================

    public function manageEventView()
    {
        $events = Event::orderBy('event_start_date', 'desc')->get();
        $finalEvents = array();

        if ($events->isNotEmpty()) {
            foreach ($events as $event) {
                $eventFormattedDate =  Carbon::parse($event->event_start_date)->format('d M Y') . ' - ' . Carbon::parse($event->event_end_date)->format('d M Y');

                array_push($finalEvents, [
                    'eventId' => $event->id,
                    'eventLogo' => $event->logo,
                    'eventName' => $event->name,
                    'eventCategory' => $event->category,
                    'eventDate' => $eventFormattedDate,
                    'eventLocation' => $event->location,
                    'eventDescription' => $event->description,
                ]);
            }
        }

        return view('admin.events.home.events', [
            "pageTitle" => "Manage Event",
            "finalEvents" => $finalEvents,
        ]);
    }

    public function addEventView()
    {
        return view('admin.events.home.add.add_event', [
            "pageTitle" => "Add Event",
            "eventCategories" => config('app.eventCategories'),
        ]);
    }

    public function eventEditView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();

            return view('admin.events.home.edit.edit_event', [
                "pageTitle" => "Edit Event",
                "eventCategories" => config('app.eventCategories'),
                "event" => $event,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function eventDashboardView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();

            $generalData = array();
            $specificData = array();

            $totalConfirmedDelegates = 0;
            $totalDelegates = 0;
            $totalRegisteredToday = 0;
            $totalPaidToday = 0;
            $totalAmountPaidToday = 0;
            $totalAmountPaid = 0;
            $totalFullMember = 0;
            $totalMember = 0;
            $totalNonMember = 0;
            $totalBankTransfer = 0;
            $totalCreditCard = 0;
            $totalPaid = 0;
            $totalFree = 0;
            $totalUnpaid = 0;
            $totalRefunded = 0;
            $totalOnline = 0;
            $totalImported = 0;
            $totalOnsite = 0;
            $totalConfirmed = 0;
            $totalPending = 0;
            $totalDroppedOut = 0;
            $totalCancelled = 0;
            $arrayCountryTotal = array();
            $arrayCompanyTotal = array();
            $arrayRegistrationTypeTotal = array();

            $dateToday = Carbon::now();
            $noRefund = 0;

            $mainDelegates = MainDelegate::where('event_id', $eventId)->get();

            if ($mainDelegates->isNotEmpty()) {
                foreach ($mainDelegates as $mainDelegate) {

                    $delegateRegisteredDate = Carbon::parse($mainDelegate->registered_date_time);
                    if ($dateToday->isSameDay($delegateRegisteredDate)) {
                        $totalRegisteredToday++;
                    }

                    if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                        if($mainDelegate->registration_status == "confirmed"){
                            $totalConfirmedDelegates++;
                        }

                        $totalDelegates++;

                        if ($mainDelegate->pass_type == "fullMember") {
                            $totalFullMember++;
                        } else if ($mainDelegate->pass_type == "member") {
                            $totalMember++;
                        } else {
                            $totalNonMember++;
                        }

                        if ($mainDelegate->mode_of_payment == "creditCard") {
                            $totalCreditCard++;
                        } else {
                            $totalBankTransfer++;
                        }

                        if ($mainDelegate->payment_status == "paid") {
                            $delegatePaidDate = Carbon::parse($mainDelegate->paid_date_time);
                            if ($dateToday->isSameDay($delegatePaidDate)) {
                                $totalPaidToday++;
                            }

                            $noRefund++;
                            $totalPaid++;
                        } else if ($mainDelegate->payment_status == "free") {
                            $totalFree++;
                        } else if ($mainDelegate->payment_status == "unpaid") {
                            $totalUnpaid++;
                        } else {
                        }


                        if ($mainDelegate->registration_method == "online") {
                            $totalOnline++;
                        } else if ($mainDelegate->registration_method == "imported") {
                            $totalImported++;
                        } else {
                            $totalOnsite++;
                        }


                        if ($mainDelegate->registration_status == "confirmed") {
                            $totalConfirmed++;
                        } else if ($mainDelegate->registration_status == "pending") {
                            $totalPending++;
                        } else if ($mainDelegate->registration_status == "droppedOut") {
                            $totalDroppedOut++;
                        }

                        if($this->checkIfCountryExist($mainDelegate->company_country, $arrayCountryTotal)){
                            foreach ($arrayCountryTotal as $index => $country) {
                                if ($country['name'] == $mainDelegate->company_country) {
                                    $arrayCountryTotal[$index]['total'] = $country['total'] + 1;
                                }
                            }
                        } else {
                            array_push($arrayCountryTotal, [
                                'name' => $mainDelegate->company_country,
                                'total' => 1,
                            ]);
                        }


                        if (in_array($mainDelegate->company_name, $arrayCompanyTotal)) {
                            foreach ($arrayCompanyTotal as $company) {
                                if ($company['name'] == $mainDelegate->company_name) {
                                    $company['total'] += 1;
                                }
                            }
                        } else {
                            array_push($arrayCompanyTotal, [
                                'name' => $mainDelegate->company_name,
                                'total' => 1,
                            ]);
                        }


                        if (in_array($mainDelegate->badge_type, $arrayRegistrationTypeTotal)) {
                            foreach ($arrayRegistrationTypeTotal as $registrationType) {
                                if ($registrationType['name'] == $mainDelegate->badge_type) {
                                    $registrationType['total'] += 1;
                                }
                            }
                        } else {
                            array_push($arrayRegistrationTypeTotal, [
                                'name' => $mainDelegate->badge_type,
                                'total' => 1,
                            ]);
                        }
                    } else {
                        if ($mainDelegate->delegate_refunded) {
                            $totalRefunded++;
                        }

                        if ($mainDelegate->delegate_cancelled) {
                            $totalCancelled++;
                        }
                    }

                    $additionalDelegates = AdditionalDelegate::where('main_delegate_id', $mainDelegate->id)->get();
                    if ($additionalDelegates->isNotEmpty()) {
                        foreach ($additionalDelegates as $additionalDelegate) {
                            if ($additionalDelegate->delegate_replaced_by_id == null && (!$additionalDelegate->delegate_refunded)) {
                                if($mainDelegate->registration_status == "confirmed"){
                                    $totalConfirmedDelegates++;
                                }
                                
                                $totalDelegates++;


                                if ($mainDelegate->pass_type == "fullMember") {
                                    $totalFullMember++;
                                } else if ($mainDelegate->pass_type == "member") {
                                    $totalMember++;
                                } else {
                                    $totalNonMember++;
                                }

                                if ($mainDelegate->mode_of_payment == "creditCard") {
                                    $totalCreditCard++;
                                } else {
                                    $totalBankTransfer++;
                                }


                                if ($mainDelegate->payment_status == "paid") {
                                    $delegatePaidDate = Carbon::parse($mainDelegate->paid_date_time);
                                    if ($dateToday->isSameDay($delegatePaidDate)) {
                                        $totalPaidToday++;
                                    }

                                    $noRefund++;
                                    $totalPaid++;
                                } else if ($mainDelegate->payment_status == "free") {
                                    $totalFree++;
                                } else if ($mainDelegate->payment_status == "unpaid") {
                                    $totalUnpaid++;
                                } else {
                                }

                                if ($mainDelegate->registration_method == "online") {
                                    $totalOnline++;
                                } else if ($mainDelegate->registration_method == "imported") {
                                    $totalImported++;
                                } else {
                                    $totalOnsite++;
                                }


                                if ($mainDelegate->registration_status == "confirmed") {
                                    $totalConfirmed++;
                                } else if ($mainDelegate->registration_status == "pending") {
                                    $totalPending++;
                                } else if ($mainDelegate->registration_status == "droppedOut") {
                                    $totalDroppedOut++;
                                }




                                if($this->checkIfCountryExist($mainDelegate->company_country, $arrayCountryTotal)){
                                    foreach ($arrayCountryTotal as $index => $country) {
                                        if ($country['name'] == $mainDelegate->company_country) {
                                            $arrayCountryTotal[$index]['total'] = $country['total'] + 1;
                                        }
                                    }
                                } else {
                                    array_push($arrayCountryTotal, [
                                        'name' => $mainDelegate->company_country,
                                        'total' => 1,
                                    ]);
                                }


                                if (in_array($mainDelegate->company_name, $arrayCompanyTotal)) {
                                    foreach ($arrayCompanyTotal as $company) {
                                        if ($company['name'] == $mainDelegate->company_name) {
                                            $company['total'] += 1;
                                        }
                                    }
                                } else {
                                    array_push($arrayCompanyTotal, [
                                        'name' => $mainDelegate->company_name,
                                        'total' => 1,
                                    ]);
                                }


                                if (in_array($additionalDelegate->badge_type, $arrayRegistrationTypeTotal)) {
                                    foreach ($arrayRegistrationTypeTotal as $registrationType) {
                                        if ($registrationType['name'] == $additionalDelegate->badge_type) {
                                            $registrationType['total'] += 1;
                                        }
                                    }
                                } else {
                                    array_push($arrayRegistrationTypeTotal, [
                                        'name' => $additionalDelegate->badge_type,
                                        'total' => 1,
                                    ]);
                                }
                            } else {
                                if ($additionalDelegate->delegate_refunded) {
                                    $totalRefunded++;
                                }

                                if ($additionalDelegate->delegate_cancelled) {
                                    $totalCancelled++;
                                }
                            }
                        }
                    }

                    if ($noRefund > 0 && $mainDelegate->payment_status == "paid") {
                        $totalAmountPaid += $mainDelegate->total_amount;

                        $delegatePaidDate = Carbon::parse($mainDelegate->paid_date_time);
                        if ($dateToday->isSameDay($delegatePaidDate)) {
                            $totalAmountPaidToday += $mainDelegate->total_amount;
                        }
                    }
                }
            }

            return view('admin.events.dashboard.dashboard', [
                "pageTitle" => "Event Dashboard",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
                "event" => $event,
                
                'totalConfirmedDelegates' => $totalConfirmedDelegates,
                'totalDelegates' => $totalDelegates,
                'totalRegisteredToday' => $totalRegisteredToday,
                'totalPaidToday' => $totalPaidToday,
                'totalAmountPaidToday' => $totalAmountPaidToday,
                'totalAmountPaid' => $totalAmountPaid,

                'totalFullMember' => $totalFullMember,
                'totalMember' => $totalMember,
                'totalNonMember' => $totalNonMember,

                'totalPaid' => $totalPaid,
                'totalFree' => $totalFree,
                'totalUnpaid' => $totalUnpaid,
                'totalRefunded' => $totalRefunded,

                'totalConfirmed' => $totalConfirmed,
                'totalPending' => $totalPending,
                'totalDroppedOut' => $totalDroppedOut,
                'totalCancelled' => $totalCancelled,

                'totalOnline' => $totalOnline,
                'totalImported' => $totalImported,
                'totalOnsite' => $totalOnsite,

                'totalCreditCard' => $totalCreditCard,
                'totalBankTransfer' => $totalBankTransfer,

                'arrayCountryTotal' => $arrayCountryTotal,
                'arrayCompanyTotal' => $arrayCompanyTotal,
                'arrayRegistrationTypeTotal' => $arrayRegistrationTypeTotal,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function eventDetailView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();

            $today = Carbon::today();
            if ($event->eb_end_date != null && $event->eb_member_rate != null && $event->eb_nmember_rate != null) {
                if ($today->lte(Carbon::parse($event->eb_end_date))) {
                    $finalEbEndDate = Carbon::parse($event->eb_end_date)->format('d M Y');
                } else {
                    $finalEbEndDate = null;
                }
            } else {
                $finalEbEndDate = null;
            }

            $finalStdStartDate = Carbon::parse($event->std_start_date)->format('d M Y');
            $finalEventStartDate = Carbon::parse($event->event_start_date)->format('d M Y');
            $finalEventEndDate = Carbon::parse($event->event_end_date)->format('d M Y');

            $regFormLink = env('APP_URL') . '/register/' . $event->year . '/' . $eventCategory . '/' . $eventId;

            return view('admin.events.details.event_details', [
                "pageTitle" => "Event Details",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
                "event" => $event,
                "finalEbEndDate" => $finalEbEndDate,
                "finalStdStartDate" => $finalStdStartDate,
                "finalEventStartDate" => $finalEventStartDate,
                "finalEventEndDate" => $finalEventEndDate,
                "regFormLink" => $regFormLink,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function eventRegistrationType($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            return view('admin.events.registration-type.registration_type', [
                "pageTitle" => "Event Registration Type",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function eventDelegateFeesView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            return view('admin.events.delegate-fees.delegate_fees', [
                "pageTitle" => "Event Delegate Fees",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function eventPromoCodeView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            return view('admin.events.promo-codes.promo_codes', [
                "pageTitle" => "Event Promo Codes",
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

    public function addEvent(Request $request)
    {
        $request->validate(
            [
                'category' => 'required',
                'name' => 'required',
                'location' => 'required',
                'description' => 'required',
                'link' => 'required',
                'event_start_date' => 'required|date',
                'event_end_date' => 'required|date',
                'event_vat' => 'required|numeric|min:0|max:100',
                'logo' => 'required|mimes:jpeg,png,jpg,gif',
                'banner' => 'required|mimes:jpeg,png,jpg,gif',

                'eb_end_date' => 'nullable|date',
                'eb_full_member_rate' => 'nullable|numeric|min:0|max:99999.99',
                'eb_member_rate' => 'nullable|numeric|min:0|max:99999.99',
                'eb_nmember_rate' => 'nullable|numeric|min:0|max:99999.99',

                'std_start_date' => 'required|date',
                'std_full_member_rate' => 'nullable|numeric|min:0|max:99999.99',
                'std_member_rate' => 'required|numeric|min:0|max:99999.99',
                'std_nmember_rate' => 'required|numeric|min:0|max:99999.99',

                'badge_footer_link' => 'required',
                'badge_footer_link_color' => 'required',
                'badge_footer_bg_color' => 'required',
                'badge_front_banner' => 'required|mimes:jpeg,png,jpg,gif',
                'badge_back_banner' => 'required|mimes:jpeg,png,jpg,gif',
            ],
            [
                'category.required' => 'Event Category is required',
                'name.required' => 'Event Name is required',
                'location.required' => 'Event Location is required',
                'description.required' => 'Event Description is required',
                'link.required' => 'Event Link is required',
                'event_start_date.required' => 'Event Start Date is required',
                'event_start_date.date' => 'Event Start Date must be a date',
                'event_end_date.required' => 'Event End Date is required',
                'event_end_date.date' => 'Event End Date must be a date',
                'event_vat.required' => 'Vat is required',
                'event_vat.numeric' => 'Vat must be a number.',
                'event_vat.min' => 'Vat must be at least :min.',
                'event_vat.max' => 'Vat may not be greater than :max.',
                'logo.required' => 'Event Logo is required',
                'logo.mimes' => 'Event Logo must be in jpeg, png, jpg, gif format',
                'banner.required' => 'Event Banner is required',
                'banner.mimes' => 'Event Banner must be in jpeg, png, jpg, gif format',



                'eb_end_date.date' => 'Early Bird End Date must be a date',

                'eb_full_member_rate.numeric' => 'Early Bird Full Member Rate must be a number.',
                'eb_full_member_rate.min' => 'Early Bird Full Member Rate must be at least :min.',
                'eb_full_member_rate.max' => 'Early Bird Full Member Rate may not be greater than :max.',
                'eb_member_rate.numeric' => 'Early Bird Member Rate must be a number.',
                'eb_member_rate.min' => 'Early Bird Member Rate must be at least :min.',
                'eb_member_rate.max' => 'Early Bird Member Rate may not be greater than :max.',
                'eb_nmember_rate.numeric' => 'Early Bird Non-Member Rate must be a number.',
                'eb_nmember_rate.min' => 'Early Bird Non-Member Rate must be at least :min.',
                'eb_nmember_rate.max' => 'Early Bird Non-Member Rate may not be greater than :max.',



                'std_start_date.required' => 'Standard Start Date is required',
                'std_start_date.date' => 'Standard Start Date must be a date',

                'std_full_member_rate.numeric' => 'Standard Full Member Rate must be a number.',
                'std_full_member_rate.min' => 'Standard Full Member Rate must be at least :min.',
                'std_full_member_rate.max' => 'Standard Full Member Rate may not be greater than :max.',
                'std_member_rate.required' => 'Standard Member Rate is required',
                'std_member_rate.numeric' => 'Standard Member Rate must be a number.',
                'std_member_rate.min' => 'Standard Member Rate must be at least :min.',
                'std_member_rate.max' => 'Standard Member Rate may not be greater than :max.',

                'std_nmember_rate.required' => 'Standard Non-Member Rate is required',
                'std_nmember_rate.numeric' => 'Standard Non-Member Rate must be a number.',
                'std_nmember_rate.min' => 'Standard Non-Member Rate must be at least :min.',
                'std_nmember_rate.max' => 'Standard Non-Member Rate may not be greater than :max.',


                'badge_footer_link.required' => 'Badge Footer Link is required',
                'badge_footer_link_color.required' => 'Badge Footer Link Color is required',
                'badge_footer_bg_color.required' => 'Badge Footer Link Background Color is required',
                'badge_front_banner.required' => 'Badge Front Banner is required',
                'badge_front_banner.mimes' => 'Badge Front Banner must be in jpeg, png, jpg, gif format',
                'badge_back_banner.required' => 'Badge Back Banner is required',
                'badge_back_banner.mimes' => 'Badge Back Banner must be in jpeg, png, jpg, gif format',
            ]
        );

        $currentYear = strval(Carbon::parse($request->event_start_date)->year);
        $logoPath = $request->file('logo')->store('public/event/' . $currentYear . '/logos');
        $bannerPath = $request->file('banner')->store('public/event/' . $currentYear . '/banners');
        $badgeFrontBannerPath = $request->file('badge_front_banner')->store('public/event/' . $currentYear . '/badges/front');
        $badgeBackBannerPath = $request->file('badge_back_banner')->store('public/event/' . $currentYear . '/badges/back');

        $newEvent = Event::create([
            'category' => $request->category,
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'link' => $request->link,
            'event_start_date' => $request->event_start_date,
            'event_end_date' => $request->event_end_date,
            'event_vat' => $request->event_vat,
            'logo' => $logoPath,
            'banner' => $bannerPath,

            'eb_end_date' => $request->eb_end_date,
            'eb_full_member_rate' => $request->eb_full_member_rate,
            'eb_member_rate' => $request->eb_member_rate,
            'eb_nmember_rate' => $request->eb_nmember_rate,

            'std_start_date' => $request->std_start_date,
            'std_full_member_rate' => $request->std_full_member_rate,
            'std_member_rate' => $request->std_member_rate,
            'std_nmember_rate' => $request->std_nmember_rate,

            'badge_footer_link' => $request->badge_footer_link,
            'badge_footer_link_color' => $request->badge_footer_link_color,
            'badge_footer_bg_color' => $request->badge_footer_bg_color,
            'badge_front_banner' => $badgeFrontBannerPath,
            'badge_back_banner' => $badgeBackBannerPath,

            'year' => $currentYear,
            'active' => true,
        ]);

        EventRegistrationType::create([
            'event_id' => $newEvent->id,
            'event_category' => $newEvent->category,
            'registration_type' => "Delegate",
            'badge_footer_front_name' => "Delegate",
            'badge_footer_front_bg_color' => "#000000",
            'badge_footer_front_text_color' => "#ffffff",
            'badge_footer_back_name' => "Delegate",
            'badge_footer_back_bg_color' => "#000000",
            'badge_footer_back_text_color' => "#ffffff",
            'active' => true,
        ]);

        EventRegistrationType::create([
            'event_id' => $newEvent->id,
            'event_category' => $newEvent->category,
            'registration_type' => "Organizer",
            'badge_footer_front_name' => "Organizer",
            'badge_footer_front_bg_color' => "#000000",
            'badge_footer_front_text_color' => "#ffffff",
            'badge_footer_back_name' => "Organizer",
            'badge_footer_back_bg_color' => "#000000",
            'badge_footer_back_text_color' => "#ffffff",
            'active' => true,
        ]);

        return redirect()->route('admin.event.view')->with('success', 'Event added successfully.');;
    }

    public function updateEvent(Request $request, $eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $validatedData = $request->validate(
                [
                    'eventId' => 'required',
                    'category' => 'required',
                    'name' => 'required',
                    'location' => 'required',
                    'description' => 'required',
                    'link' => 'required',
                    'event_start_date' => 'required|date',
                    'event_end_date' => 'required|date',
                    'event_vat' => 'required|numeric|min:0|max:100',
                    'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                    'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif',

                    'eb_end_date' => 'nullable|date',
                    'eb_full_member_rate' => 'nullable|numeric|min:0|max:99999.99',
                    'eb_member_rate' => 'nullable|numeric|min:0|max:99999.99',
                    'eb_nmember_rate' => 'nullable|numeric|min:0|max:99999.99',

                    'std_start_date' => 'required|date',
                    'std_full_member_rate' => 'nullable|numeric|min:0|max:99999.99',
                    'std_member_rate' => 'required|numeric|min:0|max:99999.99',
                    'std_nmember_rate' => 'required|numeric|min:0|max:99999.99',

                    'badge_footer_link' => 'required',
                    'badge_footer_link_color' => 'required',
                    'badge_footer_bg_color' => 'required',
                    'badge_front_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                    'badge_back_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                ],
                [
                    'category.required' => 'Event Category is required',
                    'name.required' => 'Event Name is required',
                    'location.required' => 'Event Location is required',
                    'description.required' => 'Event Description is required',
                    'link.required' => 'Event Link is required',
                    'event_start_date.required' => 'Event Start Date is required',
                    'event_start_date.date' => 'Event Start Date must be a date',
                    'event_end_date.required' => 'Event End Date is required',
                    'event_end_date.date' => 'Event End Date must be a date',
                    'event_vat.required' => 'Vat is required',
                    'event_vat.numeric' => 'Vat must be a number.',
                    'event_vat.min' => 'Vat must be at least :min.',
                    'event_vat.max' => 'Vat may not be greater than :max.',
                    'logo.image' => 'Event Logo must be an image',
                    'logo.mimes' => 'Event Logo must be in jpeg, png, jpg, gif format',
                    'banner.image' => 'Event Banner must be an image',
                    'banner.mimes' => 'Event Banner must be in jpeg, png, jpg, gif format',



                    'eb_end_date.date' => 'Early Bird End Date must be a date',

                    'eb_full_member_rate.numeric' => 'Early Bird Full Member Rate must be a number.',
                    'eb_full_member_rate.min' => 'Early Bird Full Member Rate must be at least :min.',
                    'eb_full_member_rate.max' => 'Early Bird Full Member Rate may not be greater than :max.',
                    'eb_member_rate.numeric' => 'Early Bird Member Rate must be a number.',
                    'eb_member_rate.min' => 'Early Bird Member Rate must be at least :min.',
                    'eb_member_rate.max' => 'Early Bird Member Rate may not be greater than :max.',
                    'eb_nmember_rate.numeric' => 'Early Bird Non-Member Rate must be a number.',
                    'eb_nmember_rate.min' => 'Early Bird Non-Member Rate must be at least :min.',
                    'eb_nmember_rate.max' => 'Early Bird Non-Member Rate may not be greater than :max.',



                    'std_start_date.required' => 'Standard Start Date is required',
                    'std_start_date.date' => 'Standard Start Date must be a date',

                    'std_full_member_rate.numeric' => 'Standard Full Member Rate must be a number.',
                    'std_full_member_rate.min' => 'Standard Full Member Rate must be at least :min.',
                    'std_full_member_rate.max' => 'Standard Full Member Rate may not be greater than :max.',
                    'std_member_rate.required' => 'Standard Member Rate is required',
                    'std_member_rate.numeric' => 'Standard Member Rate must be a number.',
                    'std_member_rate.min' => 'Standard Member Rate must be at least :min.',
                    'std_member_rate.max' => 'Standard Member Rate may not be greater than :max.',

                    'std_nmember_rate.required' => 'Standard Non-Member Rate is required',
                    'std_nmember_rate.numeric' => 'Standard Non-Member Rate must be a number.',
                    'std_nmember_rate.min' => 'Standard Non-Member Rate must be at least :min.',
                    'std_nmember_rate.max' => 'Standard Non-Member Rate may not be greater than :max.',


                    'badge_footer_link.required' => 'Badge Footer Link is required',
                    'badge_footer_link_color.required' => 'Badge Footer Link Color is required',
                    'badge_footer_bg_color.required' => 'Badge Footer Link Background Color is required',
                    'badge_front_banner.image' => 'Badge Front Banner must be an image',
                    'badge_front_banner.mimes' => 'Badge Front Banner must be in jpeg, png, jpg, gif format',
                    'badge_back_banner.image' => 'Badge Back Banner must be an image',
                    'badge_back_banner.mimes' => 'Badge Back Banner must be in jpeg, png, jpg, gif format',
                ]
            );

            $event = Event::findOrFail($validatedData['eventId']);

            $event->category = $validatedData['category'];
            $event->name = $validatedData['name'];
            $event->location = $validatedData['location'];
            $event->description = $validatedData['description'];
            $event->link = $validatedData['link'];
            $event->event_start_date = $validatedData['event_start_date'];
            $event->event_end_date = $validatedData['event_end_date'];
            $event->event_vat = $validatedData['event_vat'];

            $event->eb_end_date = $validatedData['eb_end_date'];
            $event->eb_full_member_rate = $validatedData['eb_full_member_rate'];
            $event->eb_member_rate = $validatedData['eb_member_rate'];
            $event->eb_nmember_rate = $validatedData['eb_nmember_rate'];

            $event->std_start_date = $validatedData['std_start_date'];
            $event->std_full_member_rate = $validatedData['std_full_member_rate'];
            $event->std_member_rate = $validatedData['std_member_rate'];
            $event->std_nmember_rate = $validatedData['std_nmember_rate'];

            $event->badge_footer_link = $validatedData['badge_footer_link'];
            $event->badge_footer_link_color = $validatedData['badge_footer_link_color'];
            $event->badge_footer_bg_color = $validatedData['badge_footer_bg_color'];

            $currentYear = date('Y');
            if ($request->hasFile('logo')) {
                Storage::delete($event->logo);
                $logoPath = $request->file('logo')->store('public/event/' . $currentYear . '/logos');
                $event->logo = $logoPath;
            }

            if ($request->hasFile('banner')) {
                Storage::delete($event->banner);
                $bannerPath = $request->file('banner')->store('public/event/' . $currentYear . '/banners');
                $event->banner = $bannerPath;
            }

            if ($request->hasFile('badge_front_banner')) {
                Storage::delete($event->badge_front_banner);
                $badgeFrontBannerPath = $request->file('badge_front_banner')->store('public/event/' . $currentYear . '/badges/front');
                $event->badge_front_banner = $badgeFrontBannerPath;
            }

            if ($request->hasFile('badge_back_banner')) {
                Storage::delete($event->badge_back_banner);
                $badgeBackBannerPath = $request->file('badge_back_banner')->store('public/event/' . $currentYear . '/badges/back');
                $event->badge_back_banner = $badgeBackBannerPath;
            }

            $event->save();

            return redirect()->route('admin.event.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id])->with('success', 'Event updated successfully.');
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function checkIfCountryExist($companyCountry, $arrayCountries){
        $checker = 0;
        foreach($arrayCountries as $country){
            if($country['name'] == $companyCountry){
                $checker++;
            }
        }

        if($checker > 0){
            return true;
        } else {
            return false;
        }
    }
}
