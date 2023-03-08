@extends('admin.layouts.master')

@section('content')
    <h1>{{ $pageTitle }}</h1>

    <br>
    <h1>Add a member</h1>
    <br>
    <form action="/admin/member/add" method="POST">
        @csrf
        <input type="text" name="name"
            class="border focus:border-black rounded-md w-1/4 py-1 px-2 text-sm focus:outline-non text-gray-700"
            placeholder="Company Name">
        <input type="text" name="sector"
            class="border focus:border-black rounded-md w-1/4 py-1 px-2 text-sm focus:outline-non text-gray-700"
            placeholder="Company Sector">

        <div class="justify-self-center flex flex-row">
            <h1 class="mr-4">Logo:</h1>
            <div class="flex-row">
                <input type="file" name="logo" onchange="previewFile(this);"
                    class="border focus:border-black rounded-md w-full h-full px-2 text-sm focus:outline-non text-gray-700">
                {{-- <br><span class="mt-2 text-red-600 italic text-sm">@error('foodImage'){{ $message }}@enderror</span> --}}
            </div>
        </div>

        <input type="submit" value="Add">
    </form>



    <br>

    <h1>List of members</h1>
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
        <div class="grid grid-cols-9 pt-4 pb-2 text-center">
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
@endsection
