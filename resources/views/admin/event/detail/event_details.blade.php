@extends('admin.layouts.master')

@section('content')
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>

    <div class="container mx-auto my-10">
        <div class="flex items-center gap-4">
            <img src="{{ Storage::url($event->logo) }}" alt="" class="h-16">
            <p class="font-bold text-3xl">{{ $event->name }}</p>
            <p
                class="text-registrationPrimaryColor rounded-full border border-registrationPrimaryColor px-4 font-bold text-sm">
                {{ $event->category }}</p>
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

        <table class="w-1/2 mx-auto bg-registrationPrimaryColor text-white text-center mt-10" cellspacing="1" cellpadding="2">
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
    </div>
@endsection
