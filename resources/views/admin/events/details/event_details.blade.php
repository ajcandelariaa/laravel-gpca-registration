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

    <div class="container mx-auto mt-10">
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
            @if ($finalEventStartDate == $finalEventEndDate)
                <p>{{ $finalEventStartDate }}</p>
            @else
                <p>{{ $finalEventStartDate . ' - ' . $finalEventEndDate }}</p>
            @endif
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
    </div>

    <div class="w-10/12 mb-20 grid grid-cols-3 mx-auto gap-10">
        <div class="col-span-1">
            @include('admin.events.details.fe_rate')
        </div>
        <div class="col-span-1">
            @include('admin.events.details.wo_rate')
        </div>
        <div class="col-span-1">
            @include('admin.events.details.co_rate')
        </div>
    </div>
@endsection
