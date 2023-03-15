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
        <form class="add-event-form" action="{{ route('admin.event.add.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-5">

                <div class="text-registrationPrimaryColor font-bold text-2xl">
                    Add Event
                </div>

                <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5 items-start">

                    @include('admin.event.add.event_details')

                    <div class="space-y-2 col-span-2 mt-6"><hr></div>

                    @include('admin.event.add.eb_details')


                    <div class="space-y-2 col-span-2 mt-6"><hr></div>

                    @include('admin.event.add.std_details')

                    {{-- ROW 6
                    <div class="space-y-2 col-span-2 grid grid-cols-3 gap-5 items-end">
                        <div>
                            <div class="text-registrationPrimaryColor font-medium text-lg">
                                Delegate fees
                            </div>
                            <div class="text-registrationPrimaryColor mt-2">
                                Standrd Start Date <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input type="text" name="" placeholder="Select a date"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" id="add-more">Add More</button>
                        </div>
                    </div> --}}
                </div>

                <div class="text-center mt-10">
                    <input type="submit" value="Publish"
                        class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white font-medium py-2 px-10 rounded inline-flex items-center text-sm cursor-pointer">
                </div>
            </div>
        </form>

        {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#add-more').click(function() {
                    var html = '<div class="form-group">' +
                        '<label for="name">Name</label>' +
                        '<input type="text" class="form-control" name="name[]" id="name">' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label for="price">Price</label>' +
                        '<input type="number" class="form-control" name="price[]" id="price">' +
                        '</div>';
                    $('form').append(html);
                });
            });
        </script> --}}
    </div>
@endsection
