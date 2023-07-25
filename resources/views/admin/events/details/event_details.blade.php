@extends('admin.layouts.master')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded fixed top-4 left-1/2 transform -translate-x-1/2 w-96"
            role="alert">
            <div class="flex justify-between items-center">
                <div>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

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
            <div class="flex items-center gap-4">
                <div>
                    <form
                        action="{{ route('admin.event.update.status.post', ['eventCategory' => $event->category, 'eventId' => $event->id, 'eventStatus' => $event->active]) }}"
                        method="POST">
                        @csrf

                        @if ($event->active)
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-10 rounded inline-flex items-center text-sm cursor-pointer">Disable</button>
                        @else
                            <button type="submit"
                                class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-10 rounded inline-flex items-center text-sm cursor-pointer">Enable</button>
                        @endif
                    </form>
                </div>

                <div>
                    <a href="{{ route('admin.event.edit.view', ['eventCategory' => $event->category, 'eventId' => $event->id]) }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-5 rounded-md inline-flex items-center text-sm">
                        <span class="mr-2"><i class="fa-solid fa-file-pen"></i></span>
                        <span>Edit Event</span>
                    </a>
                </div>
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
            <a href="{{ $regFormLink }}" target="_blank"
                class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white font-medium py-2 px-5 rounded-md inline-flex items-center text-sm">
                <span class="mr-2"><i class="fa-solid fa-file-pen"></i></span>
                <span>View public registration form</span>
            </a>

            <a href="#"
                class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white font-medium py-2 px-5 rounded-md inline-flex items-center text-sm">
                <span class="mr-2"><i class="fa-solid fa-file-pen"></i></span>
                <span>View onsite registration form</span>
            </a>
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
                            Full Member
                        </div>
                    </td>
                    @if ($finalEbEndDate != null)
                        <td class="text-black">
                            <div class="bg-white py-4">
                                $ {{ $event->eb_full_member_rate ? $event->eb_full_member_rate : '0.00' }} +
                                {{ $event->event_vat }}%
                            </div>
                        </td>
                    @endif
                    <td class="text-black">
                        <div class="bg-white py-4 mr-1">
                            $ {{ $event->std_full_member_rate ? $event->std_full_member_rate : '0.00' }} +
                            {{ $event->event_vat }}%
                        </div>
                    </td>
                </tr>
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
