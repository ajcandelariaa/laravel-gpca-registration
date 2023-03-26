@extends('admin.layouts.master')

@section('content')
    <div class="container mx-auto my-10">
        @if (empty($finalListsOfDelegates))
            <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md container mx-auto">
                There are no registrants yet.
            </div>
        @else
            <div class="shadow-lg my-5 pt-5 bg-white rounded-md">
                <h1 class="text-center text-2xl">List of Delegates</h1>

                <div class="grid grid-cols-12 pt-4 pb-2 place-items-center">
                    <div class="col-span-1">No.</div>
                    <div class="col-span-1">Event</div>
                    <div class="col-span-2">Company Name</div>
                    <div class="col-span-1">Job Title</div>
                    <div class="col-span-2">Name</div>
                    <div class="col-span-2">Email Address</div>
                    <div class="col-span-1">Registration Type</div>
                    <div class="col-span-2">Actions</div>
                </div>

                @php
                    $count = 1;
                @endphp

                @foreach ($finalListsOfDelegates as $finalListsOfDelegate)
                    <div
                        class="grid grid-cols-12 pt-2 pb-2 mb-1 place-items-center {{ $count % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                        <div class="col-span-1">{{ $count }}</div>

                        <div class="col-span-1">
                            {{ $finalListsOfDelegate['delegateEventCategory'] }}
                        </div>

                        <div class="col-span-2">
                            {{ $finalListsOfDelegate['delegateCompany'] }}
                        </div>

                        <div class="col-span-1">
                            {{ $finalListsOfDelegate['delegateJobTitle'] }}
                        </div>

                        <div class="col-span-2">
                            {{ $finalListsOfDelegate['delegateName'] }}
                        </div>

                        <div class="col-span-2">
                            {{ $finalListsOfDelegate['delegateEmailAddress'] }}
                        </div>

                        <div class="col-span-1">
                            {{ $finalListsOfDelegate['delegateBadgeType'] }}
                        </div>

                        <div class="col-span-2">
                            <a href="{{ route('admin.event.delegates.detail.view', ['eventCategory' => $finalListsOfDelegate['eventCategory'], 'eventId' => $finalListsOfDelegate['eventId'], 'delegateType' => $finalListsOfDelegate['delegateType'], 'delegateId' => $finalListsOfDelegate['delegateId']]) }}"
                                class="cursor-pointer hover:text-gray-600 text-gray-500 mr-2">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.event.delegates.detail.printBadge', ['eventCategory' => $finalListsOfDelegate['eventCategory'], 'eventId' => $finalListsOfDelegate['eventId'], 'delegateType' => $finalListsOfDelegate['delegateType'], 'delegateId' => $finalListsOfDelegate['delegateId']]) }}"
                                class="bg-green-800 hover:bg-green-900 text-white py-1 px-2 rounded-md text-xs text-center" target="_blank">
                                Print Badge
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
