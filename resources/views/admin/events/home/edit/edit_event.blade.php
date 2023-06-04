@extends('admin.layouts.master')

@section('content')
    <div class="container mx-auto mt-10">
        <a href="{{ route('admin.event.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id]) }}"
            class="bg-red-500 hover:bg-red-400 text-white font-medium py-2 px-5 rounded inline-flex items-center text-sm">
            <span class="mr-2"><i class="fa-sharp fa-solid fa-arrow-left"></i></span>
            <span>Cancel</span>
        </a>
    </div>

    <div class="shadow-lg bg-white rounded-md container mx-auto mt-5 mb-10">
        <form id="edit_form" class="edit-event-form"
            action="{{ route('admin.event.edit.post', ['eventCategory' => $event->category, 'eventId' => $event->id]) }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-5">

                <div class="text-registrationPrimaryColor font-bold text-2xl">
                    Edit Event
                </div>

                <input type="hidden" name="eventId" value="{{ $event->id }}">

                <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5 items-start">

                    @include('admin.events.home.edit.event_details')

                    <div class="space-y-2 col-span-2 mt-6">
                        <hr>
                    </div>

                    @include('admin.events.home.edit.eb_details')


                    <div class="space-y-2 col-span-2 mt-6">
                        <hr>
                    </div>

                    @include('admin.events.home.edit.std_details')


                    <div class="space-y-2 col-span-2 mt-6">
                        <hr>
                    </div>

                    @include('admin.events.home.edit.badge_details')
                </div>

                <div class="text-center mt-10">
                    <button id="update_btn" type="submit"
                        class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white font-medium py-2 px-10 rounded inline-flex items-center text-sm cursor-pointer">Update</button>
                </div>
            </div>
        </form>
    </div>
@endsection
