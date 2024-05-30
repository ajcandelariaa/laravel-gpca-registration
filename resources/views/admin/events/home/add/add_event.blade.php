@extends('admin.layouts.master')

@section('content')
    <div class="container mx-auto mt-10">
        <a href="{{ route('admin.event.view') }}"
            class="bg-red-500 hover:bg-red-400 text-white font-medium py-2 px-5 rounded inline-flex items-center text-sm">
            <span class="mr-2"><i class="fa-sharp fa-solid fa-arrow-left"></i></span>
            <span>Cancel</span>
        </a>
    </div>

    <div class="shadow-lg bg-white rounded-md container mx-auto mt-5 mb-10">
        <form id="add_form" class="add-event-form" action="{{ route('admin.event.add.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-5">

                <div class="text-registrationPrimaryColor font-bold text-2xl">
                    Add Event
                </div>

                <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5 items-start">

                    @include('admin.events.home.add.event_details')

                    <div class="space-y-2 col-span-2 mt-6"><hr></div>

                    @include('admin.events.home.add.eb_details')

                    @include('admin.events.home.add.std_details')

                    <div class="space-y-2 col-span-2 mt-6">
                        <hr>
                    </div>

                    @include('admin.events.home.add.wo_eb_details')

                    @include('admin.events.home.add.wo_std_details')

                    <div class="space-y-2 col-span-2 mt-6">
                        <hr>
                    </div>

                    @include('admin.events.home.add.co_eb_details')

                    @include('admin.events.home.add.co_std_details')

                    <div class="space-y-2 col-span-2 mt-6">
                        <hr>
                    </div>

                    @include('admin.events.home.add.badge_details')
                </div>

                <div class="text-center mt-10">
                    <button id="add_btn" type="submit" 
                        class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white font-medium py-2 px-10 rounded inline-flex items-center text-sm cursor-pointer">Publish</button>
                </div>
            </div>
        </form>
    </div>
@endsection
