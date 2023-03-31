@extends('admin.layouts.master')

@section('content')
    <div class="container mx-auto my-10">
        @if (empty($finalListOfRegistrants))
            <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md container mx-auto">
                There are no registrants yet.
            </div>
        @else
        <div>
            
            {{-- <span data-href="{{ route('admin.event.registrants.exportData', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}" id="export"
                class="bg-green-600 hover:bg-green-700 text-white py-2 px-5 rounded-md text-lg text-center"
                onclick="exportTasks(event.target);">Export Data to Excel</span> --}}
            <a href="{{ route('admin.event.registrants.exportData', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white py-2 px-5 rounded-md text-lg text-center">Export Data to Excel</a>
            <a href="" target="_blank" class="bg-sky-600 hover:bg-sky-700 text-white py-2 px-5 rounded-md text-lg text-center">Import Data</a>
        </div>
            <div class="shadow-lg my-5 pt-5 bg-white rounded-md">
                <h1 class="text-center text-2xl">List of Transactions</h1>

                <div class="grid grid-cols-12 pt-4 pb-2 text-center items-center ">
                    <div class="col-span-1">No.</div>
                    <div class="col-span-2">Registered Date & Time</div>
                    <div class="col-span-2">Company</div>
                    <div class="col-span-1">Country</div>
                    <div class="col-span-1">City</div>
                    <div class="col-span-1">Pass Type</div>
                    <div class="col-span-1">Quantity</div>
                    <div class="col-span-1">Total Amount</div>
                    <div class="col-span-1">Status</div>
                    <div class="col-span-1">Action</div>
                </div>

                @php
                    $count = 1;
                @endphp

                @foreach ($finalListOfRegistrants as $finalListOfRegistrant)
                    <div
                        class="grid grid-cols-12 pt-2 pb-2 mb-1 text-center items-center {{ $count % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                        <div class="col-span-1">{{ $count }}</div>

                        <div class="col-span-2">
                            {{ $finalListOfRegistrant['registeredDateTime'] }}
                        </div>

                        <div class="col-span-2">
                            {{ $finalListOfRegistrant['registrantCompany'] }}
                        </div>

                        <div class="col-span-1">
                            {{ $finalListOfRegistrant['registrantCountry'] }}
                        </div>

                        <div class="col-span-1">
                            {{ $finalListOfRegistrant['registrantCity'] }}
                        </div>

                        <div class="col-span-1">
                            @if ($finalListOfRegistrant['registrantPassType'] == "member")
                                Member
                            @else
                                Non-Member
                            @endif
                        </div>

                        <div class="col-span-1">
                            {{ $finalListOfRegistrant['registrantQuantity'] }}
                        </div>

                        <div class="col-span-1">
                            $ {{ number_format($finalListOfRegistrant['registrantTotalAmount'], 2, '.', ',') }}
                        </div>

                        <div class="col-span-1">
                            @if ($finalListOfRegistrant['registrantStatus'] == "paid")
                                Paid
                            @elseif ($finalListOfRegistrant['registrantStatus'] == "free")
                                Free
                            @else 
                                Unpaid
                            @endif
                        </div>

                        <div class="col-span-1">
                            <a href="{{ route('admin.event.registrants.detail.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $finalListOfRegistrant['registrantId']]) }}"
                                class="cursor-pointer hover:text-gray-600 text-gray-500" target="_blank">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                        </div>
                        @php
                            $count++;
                        @endphp
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
