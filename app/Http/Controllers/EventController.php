<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // RENDER VIEWS
    public function manageEventView()
    {
        $events = Event::all();
        return view('admin.event.events', [
            "pageTitle" => "Manage Event",
            "events" => $events,
        ]);
    }

    public function addEventView()
    {
        return view('admin.event.add.add_event', [
            "pageTitle" => "Add Event",
            "eventCategories" => config('app.eventCategories'),
        ]);
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

            $regFormLink = env('APP_URL').'/register/'.$event->year.'/'.$eventCategory.'/'.$eventId;

            return view('admin.event.detail.event_details', [
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

    public function eventDelegateFeesView($eventCategory, $eventId){
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            return view('admin.event.detail.delegate_fees', [
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
            return view('admin.event.detail.promo_codes', [
                "pageTitle" => "Event Promo Codes",
                "eventCategory" => $eventCategory,
                "eventId" => $eventId,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }

    public function eventEditView($eventCategory, $eventId)
    {
        if (Event::where('category', $eventCategory)->where('id', $eventId)->exists()) {
            $event = Event::where('category', $eventCategory)->where('id', $eventId)->first();

            return view('admin.event.edit.edit_event', [
                "pageTitle" => "Edit Event",
                "eventCategories" => config('app.eventCategories'),
                "event" => $event,
            ]);
        } else {
            abort(404, 'The URL is incorrect');
        }
    }


    // RENDER LOGICS
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
                'eb_member_rate' => 'nullable|numeric|min:0|max:99999.99',
                'eb_nmember_rate' => 'nullable|numeric|min:0|max:99999.99',

                'std_start_date' => 'required|date',
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

                'eb_member_rate.numeric' => 'Early Bird Member Rate must be a number.',
                'eb_member_rate.min' => 'Early Bird Member Rate must be at least :min.',
                'eb_member_rate.max' => 'Early Bird Member Rate may not be greater than :max.',
                'eb_nmember_rate.numeric' => 'Early Bird Non-Member Rate must be a number.',
                'eb_nmember_rate.min' => 'Early Bird Non-Member Rate must be at least :min.',
                'eb_nmember_rate.max' => 'Early Bird Non-Member Rate may not be greater than :max.',



                'std_start_date.required' => 'Standard Start Date is required',
                'std_start_date.date' => 'Standard Start Date must be a date',

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

        $currentYear = date('Y');
        $logoPath = $request->file('logo')->store('public/event/' . $currentYear . '/logos');
        $bannerPath = $request->file('banner')->store('public/event/' . $currentYear . '/banners');
        $badgeFrontBannerPath = $request->file('badge_front_banner')->store('public/event/' . $currentYear . '/badges/front');
        $badgeBackBannerPath = $request->file('badge_back_banner')->store('public/event/' . $currentYear . '/badges/back');

        
        Event::create([
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
            'eb_member_rate' => $request->eb_member_rate,
            'eb_nmember_rate' => $request->eb_nmember_rate,

            'std_start_date' => $request->std_start_date,
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
                    'eb_member_rate' => 'nullable|numeric|min:0|max:99999.99',
                    'eb_nmember_rate' => 'nullable|numeric|min:0|max:99999.99',

                    'std_start_date' => 'required|date',
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

                    'eb_member_rate.numeric' => 'Early Bird Member Rate must be a number.',
                    'eb_member_rate.min' => 'Early Bird Member Rate must be at least :min.',
                    'eb_member_rate.max' => 'Early Bird Member Rate may not be greater than :max.',
                    'eb_nmember_rate.numeric' => 'Early Bird Non-Member Rate must be a number.',
                    'eb_nmember_rate.min' => 'Early Bird Non-Member Rate must be at least :min.',
                    'eb_nmember_rate.max' => 'Early Bird Non-Member Rate may not be greater than :max.',



                    'std_start_date.required' => 'Standard Start Date is required',
                    'std_start_date.date' => 'Standard Start Date must be a date',

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
            $event->eb_member_rate = $validatedData['eb_member_rate'];
            $event->eb_nmember_rate = $validatedData['eb_nmember_rate'];

            $event->std_start_date = $validatedData['std_start_date'];
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

            return redirect()->route('admin.event.view')->with('success', 'Event updated successfully.');
        } else {
            abort(404, 'The URL is incorrect');
        }
    }
}
