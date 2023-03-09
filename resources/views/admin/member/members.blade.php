@extends('admin.layouts.master')

@section('content')
    <div class="container px-5 mx-auto">
        <div class="shadow-lg my-5 py-5 bg-white rounded-md	">
            <h1 class="text-center text-2xl">Add Member</h1>
            <div class="mt-10 flex justify-center">
                <form action="/admin/member/add" method="POST" class="w-1/2">
                    @csrf
                    <div class="flex flex-col gap-5">
                        <div class="items-center grid grid-cols-2">
                            <label class="mr-5">Company Name: <span class="text-red-600">*</span></label>
                            <input type="text" name="name"
                                class="border focus:border-black rounded-md w-full h-full py-1 px-2 text-sm focus:outline-non text-gray-700">
                            @error('name')
                                <span class="mt-2 text-red-600 italic text-sm">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="items-center grid grid-cols-2">
                            <label class="mr-5">Company Sector: </label>
                            <input type="text" name="sector"
                                class="border focus:border-black rounded-md w-full h-full py-1 px-2 text-sm focus:outline-non text-gray-700">
                        </div>

                        <div class="items-center grid grid-cols-2">
                            <label class="mr-5">Company Logo: </label>
                            <div class="flex-row">
                                <input type="file" accept="image/png, image/jpg, image/jpeg" name="logo"
                                    onchange="previewFile(this);"
                                    class="border focus:border-black rounded-md w-full h-full px-2 text-sm focus:outline-non text-gray-700">
                                {{-- <br><span class="mt-2 text-red-600 italic text-sm">@error('foodImage'){{ $message }}@enderror</span> --}}
                            </div>
                        </div>

                        <div class="text-center">
                            <input type="submit" value="Add"
                                class="bg-blue-500 rounded-md text-white py-1 px-14 hover:cursor-pointer hover:bg-blue-700">
                        </div>
                    </div>
                </form>
            </div>
        </div>



        <div class="shadow-lg my-5 py-5 bg-white rounded-md	">
            <h1 class="text-center text-2xl">List of members</h1>
            <div class="grid grid-rows-1 gap-y-2">
                <div class="grid grid-cols-9 pt-4 pb-2 text-center">
                    <div class="col-span-1">No.</div>
                    <div class="col-span-2">Logo</div>
                    <div class="col-span-2">Company Name</div>
                    <div class="col-span-2">Company Sector</div>
                    <div class="col-span-2">Actions</div>
                </div>
                @php
                    $count = 1;
                @endphp
                <div class="grid grid-cols-9 pt-2 pb-2 text-center">
                    @foreach ($members as $member)
                        <div class="col-span-1">{{ $count }}</div>
                        <div class="col-span-2">Logo</div>
                        <div class="col-span-2">{{ $member['name'] }}</div>
                        <div class="col-span-2">{{ $member['sector'] }}</div>
                        <div class="col-span-2">
                            <a href="">Edit</a>
                            <a href="">Delete</a>
                        </div>
                        @php
                            $count++;
                        @endphp
                    @endforeach
                </div>
            </div>
        </div>


    </div>
@endsection
