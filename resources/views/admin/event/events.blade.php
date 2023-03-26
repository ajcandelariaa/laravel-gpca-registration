@extends('admin.layouts.master')

@section('content')
    <div class="container mx-auto my-10">
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

        <a href="{{ route('admin.event.add.view') }}"
            class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white font-medium py-2 px-5 rounded inline-flex items-center text-sm">
            <span class="mr-2"><i class="fas fa-plus"></i></span>
            <span>Add Event</span>
        </a>

        @if ($events->isNotEmpty())
            <div class="shadow-lg my-5 pt-5 bg-white rounded-md">
                <h1 class="text-center text-2xl">List of Events</h1>

                <div class="grid grid-cols-11 pt-4 pb-2 place-items-center">
                    <div class="col-span-1">No.</div>
                    <div class="col-span-1">Category</div>
                    <div class="col-span-2">Event</div>
                    <div class="col-span-2">Location</div>
                    <div class="col-span-2">Event Date</div>
                    <div class="col-span-1">Status</div>
                    <div class="col-span-2">Actions</div>
                </div>

                @php
                    $count = 1;
                @endphp

                @foreach ($events as $event)
                    <div
                        class="grid grid-cols-11 pt-2 pb-2 mb-1 place-items-center {{ $count % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                        <div class="col-span-1">{{ $count }}</div>

                        <div class="col-span-1">
                            {{ $event->category }}
                        </div>

                        <div class="col-span-2 flex justify-start items-center gap-2 ">
                            @if ($event->logo != null)
                                <img src="{{ Storage::url($event->logo) }}" alt="logo" class="object-cover w-10">
                            @else
                                <img src="{{ asset('assets/images/logo-placeholder-image.png') }}" alt="logo"
                                    class="object-cover w-10">
                            @endif
                            <div>
                                {{ $event->name }}
                            </div>
                        </div>

                        <div class="col-span-2">
                            {{ $event->location }}
                        </div>

                        <div class="col-span-2">
                            {{ $event->event_start_date }} - {{ $event->event_end_date }}
                        </div>

                        <div class="col-span-1">
                            @if ($event->active)
                                <button
                                    class="text-gray-700 bg-green-300 hover:bg-green-500 hover:text-white py-1 px-2 text-sm rounded-md">Active</button>
                            @else
                                <button
                                    class="text-gray-700 bg-red-300 hover:bg-red-500 hover:text-white py-1 px-2 text-sm rounded-md">Inactive</button>
                            @endif
                        </div>
                        <div class="col-span-2 flex gap-4">
                            <a href="{{ route('admin.event.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id]) }}"
                                class="cursor-pointer hover:text-gray-600 text-gray-500">
                                <i class="fa-solid fa-eye"></i> View
                            </a>

                            <a href="{{ route('admin.event.edit.view', ['eventCategory' => $event->category, 'eventId' => $event->id]) }}"
                                class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            {{-- <div class="cursor-pointer hover:text-red-600 text-red-500">
                                <i class="fa-solid fa-trash"></i>
                                Delete
                            </div> --}}
                        </div>
                        @php
                            $count++;
                        @endphp
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($events->isEmpty())
        <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md container mx-auto">
            There are no events yet.
        </div>
    @endif

    </div>
@endsection
