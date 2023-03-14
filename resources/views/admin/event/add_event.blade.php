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
        <form class="add-event-form" action="{{ route('admin.event.add.post') }}" method="POST">
            @csrf
            <div class="p-5">

                <div class="text-registrationPrimaryColor font-bold text-2xl">
                    Add Event
                </div>

                <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">

                    {{-- ROW 1 --}}
                    <div class="space-y-2 col-span-2 grid grid-cols-5 gap-5 items-end">
                        <div class="col-span-1">
                            <div class="text-registrationPrimaryColor">
                                Category <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <select required name="" id=""
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    <option value="" disabled selected hidden>Please select...</option>
                                    <option value="">SCC</option>
                                    <option value="">PC</option>
                                    <option value="">ANC</option>
                                    <option value="">RIC</option>
                                    <option value="">RC</option>
                                    <option value="">AF</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-span-2">
                            <div class="text-registrationPrimaryColor">
                                Name <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="14th GPCA Supply Chain" type="text" name=""
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>

                        <div class="col-span-2">
                            <div class="text-registrationPrimaryColor">
                                Location <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="Le Méridien Al Khobar, Saudi Arabia" type="text" name=""
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>
                    </div>

                    {{-- ROW 2 --}}
                    <div class="space-y-2 col-span-2">
                        <div class="text-registrationPrimaryColor">
                            Description <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <textarea name="" id="" rows="3" placeholder="Type a description here..."
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor"></textarea>
                        </div>
                    </div>

                    {{-- ROW 3 --}}
                    <div class="space-y-2">
                        <div class="text-registrationPrimaryColor">
                            Event Start Date <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input type="date" name="" placeholder="Select a date"
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="text-registrationPrimaryColor">
                            Event End Date <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input type="date" name="" placeholder="Select a date"
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        </div>
                    </div>

                    {{-- ROW 4 --}}
                    <div class="space-y-2">
                        <div class="text-registrationPrimaryColor">
                            Event Logo <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input type="file" accept="image/*" name=""
                                class="border-2 focus:border-registrationPrimaryColor rounded-md w-full h-full px-2 text-sm focus:outline-none text-gray-700">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="text-registrationPrimaryColor">
                            Event Banner <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input type="file" accept="image/*" name=""
                                class="border-2 focus:border-registrationPrimaryColor rounded-md w-full h-full px-2 text-sm focus:outline-none text-gray-700">
                        </div>
                    </div>


                    <div class="space-y-2 col-span-2 mt-6">
                        <hr>
                    </div>


                    {{-- ROW 5 --}}
                    <div class="space-y-2 col-span-2 grid grid-cols-3 gap-5 items-end">
                        <div>
                            <div class="text-registrationPrimaryColor font-medium text-lg">
                                Early Bird Details
                            </div>
                            <div class="text-registrationPrimaryColor mt-2">
                                Early Bird End Date <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input type="date" name="" placeholder="Select a date"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>

                        <div>
                            <div class="text-registrationPrimaryColor">
                                Member Rate <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input type="number" name="" step="0.01" min="0" placeholder="0.00"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>

                        <div>
                            <div class="text-registrationPrimaryColor">
                                Non-Member Rate <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input type="number" name="" step="0.01" min="0" placeholder="0.00"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>
                    </div>


                    <div class="space-y-2 col-span-2 mt-6">
                        <hr>
                    </div>

                    {{-- ROW 6 --}}
                    <div class="space-y-2 col-span-2 grid grid-cols-3 gap-5 items-end">
                        <div>
                            <div class="text-registrationPrimaryColor font-medium text-lg">
                                Standard Details
                            </div>
                            <div class="text-registrationPrimaryColor mt-2">
                                Standrd Start Date <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input type="date" name="" placeholder="Select a date"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>

                        <div>
                            <div class="text-registrationPrimaryColor">
                                Member Rate <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input type="number" name="" step="0.01" min="0" placeholder="0.00"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>

                        <div>
                            <div class="text-registrationPrimaryColor">
                                Non-Member Rate <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input type="number" name="" step="0.01" min="0" placeholder="0.00"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>
                    </div>


                    {{-- <div class="space-y-2 col-span-2 mt-6">
                        <hr>
                    </div>

                    ROW 6
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
