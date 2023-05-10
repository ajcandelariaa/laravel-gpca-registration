@extends('admin.layouts.master')

@section('content')
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>

    <div class="container mx-auto my-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <img src="{{ Storage::url($event->logo) }}" alt="" class="h-16">
                <p class="font-bold text-3xl">{{ $event->name }}</p>
                <p
                    class="text-registrationPrimaryColor rounded-full border border-registrationPrimaryColor px-4 font-bold text-sm">
                    {{ $event->category }}</p>
            </div>
            <div>
                <a href="{{ $regFormLink }}" target="_blank"
                    class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white font-medium py-2 px-5 rounded-md inline-flex items-center text-sm">
                    <span class="mr-2"><i class="fa-solid fa-file-pen"></i></span>
                    <span>View registration form</span>
                </a>
            </div>
        </div>

        <div class="flex gap-3 items-center mt-5 text-registrationPrimaryColor">
            <i class="fa-solid fa-location-dot"></i>
            <p>{{ $event->location }}</p>
        </div>

        <div class="flex gap-3 items-center mt-2 text-registrationPrimaryColor">
            <i class="fa-solid fa-calendar-days"></i>
            <p>{{ $finalEventStartDate . ' - ' . $finalEventEndDate }}</p>
        </div>

        <div class="mt-5">
            {{ $event->description }}
        </div>

        <table class="w-1/2 mx-auto bg-registrationPrimaryColor text-white text-center mt-10" cellspacing="1"
            cellpadding="2">
            <thead>
                <tr>
                    <td class="py-4 font-bold text-lg">Pass Category</td>
                    @if ($finalEbEndDate != null)
                        <td class="py-4 font-bold text-lg">
                            <span>Early Bird Rate <br> <span class="font-normal text-base">(valid until
                                    {{ $finalEbEndDate }})</span></span>
                        </td>
                    @endif
                    <td class="py-4 font-bold text-lg">
                        <span>Standard Rate <br> <span class="font-normal text-base">(starting
                                {{ $finalStdStartDate }})</span></span>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-black">
                        <div class="bg-white py-4 font-bold ml-1">
                            Member
                        </div>
                    </td>
                    @if ($finalEbEndDate != null)
                        <td class="text-black">
                            <div class="bg-white py-4">
                                $ {{ $event->eb_member_rate }} + {{ $event->event_vat }}%
                            </div>
                        </td>
                    @endif
                    <td class="text-black">
                        <div class="bg-white py-4 mr-1">
                            $ {{ $event->std_member_rate }} + {{ $event->event_vat }}%
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-black">
                        <div class="bg-white py-4 font-bold mb-1 ml-1">
                            Non-Member
                        </div>
                    </td>
                    @if ($finalEbEndDate != null)
                        <td class="text-black">
                            <div class="bg-white py-4 mb-1">
                                $ {{ $event->eb_nmember_rate }} + {{ $event->event_vat }}%
                            </div>
                        </td>
                    @endif
                    <td class="text-black">
                        <div class="bg-white py-4 mb-1 mr-1">
                            $ {{ $event->std_nmember_rate }} + {{ $event->event_vat }}%
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>


        <div class="mt-5 pb-20 flex justify-center items-center">
            <div class="flex gap-10">
                <div style="width: 321px; height: 492px;">
                    <div class="border border-black mt-10 flex flex-col justify-between h-full">
                        <div>
                            <img src="{{ Storage::url($event->badge_front_banner) }}">
                        </div>
                        <div>
                            <p class="text-center font-bold text-lg">Full name</p>
                            <p class="text-center italic mt-3">Job title</p>
                            <p class="text-center font-bold">Company name</p>
                        </div>
                        <div>
                            <p class="text-white bg-black text-center py-4 font-bold">Delegate</p>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <p>Front</p>
                    </div>
                </div>

                <div style="width: 321px; height: 492px;">
                    <div class="border border-black mt-10 flex flex-col justify-between h-full">
                        <div>
                            <img src="{{ Storage::url($event->badge_back_banner) }}">
                        </div>
                        <div>
                            <p class="text-center font-bold text-lg">Full name</p>
                            <p class="text-center italic mt-3">Job title</p>
                            <p class="text-center font-bold">Company name</p>
                        </div>
                        <div>
                            <p class="text-center py-4 font-bold" style="color: {{ $event->badge_footer_link_color }}">
                                {{ $event->badge_footer_link }}</p>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <p>Back</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
