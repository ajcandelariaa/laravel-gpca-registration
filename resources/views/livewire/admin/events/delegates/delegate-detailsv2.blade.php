<div>
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>
    <div class="container mx-auto my-10">
        <div class="grid grid-cols-delegateDetailGrid gap-14">
            <div class="mt-5">
                <div class="relative">
                    <img src="{{ asset('assets/images/delegate-image-placeholder.jpg') }}" class="w-80 h-80">
                </div>

                <div class="mt-10">
                    <button type="button"
                        wire:click="printBadgeClicked('{{ $finalDelegate['delegateType'] }}', {{ $finalDelegate['delegateId'] }})"
                        class="bg-blue-800 hover:bg-blue-900 text-white py-2 px-2 rounded-md  text-center w-full">
                        Print Badge
                    </button>

                    <div class="text-center my-2">
                        <p>or scan the below QR to print</p>
                    </div>

                    <div class="mt-2 flex justify-center">
                        {!! QrCode::size(300)->generate($printBadgeDelegateUrl) !!}
                    </div>
                </div>
            </div>

            <div class="mt-10">
                <div class="flex justify-between">
                    <div>
                        <p class="text-primaryColor font-bold text-3xl">{{ $finalDelegate['salutation'] }}
                            {{ $finalDelegate['first_name'] }} {{ $finalDelegate['middle_name'] }}
                            {{ $finalDelegate['last_name'] }}</p>
                        <p class="mt-2 italic text-lg">{{ $finalDelegate['job_title'] }}</p>
                        <p class="font-bold text-lg">{{ $finalDelegate['company_name'] }}</p>
                    </div>
                    <div>
                        <p class="text-center">Scan here</p>
                        <div>
                            {!! QrCode::size(150)->generate($scanDelegateUrl) !!}
                        </div>
                    </div>
                </div>

                <hr class="my-6">

                <div class="grid grid-cols-delegateDetailGrid2 gap-y-2 items-center">
                    <p class="font-bold">Other Company name:</p>
                    <p>
                        @if ($finalDelegate['alternative_company_name'] != null)
                            {{ $finalDelegate['alternative_company_name'] }}
                        @else
                            N/A
                        @endif
                    </p>

                    <p class="font-bold">Pass type:</p>
                    <p>
                        @if ($finalDelegate['pass_type'] == 'fullMember')
                            Full Member
                        @elseif ($finalDelegate['pass_type'] == 'member')
                            Member
                        @else
                            Non-Member
                        @endif
                    </p>

                    <p class="font-bold">Email address:</p>
                    <p>{{ $finalDelegate['email_address'] }}</p>

                    <p class="font-bold">Mobile number:</p>
                    <p>{{ $finalDelegate['mobile_number'] }}</p>

                    <p class="font-bold">Country:</p>
                    <p>
                        @if ($finalDelegate['country'] == "" || $finalDelegate['country'] == null)
                            N/A
                        @else
                            {{ $finalDelegate['country'] }}
                        @endif
                    </p>

                    <p class="font-bold">Nationality:</p>
                    <p>{{ $finalDelegate['nationality'] }}</p>

                    <p class="font-bold">Registration type:</p>
                    <p>{{ $finalDelegate['badge_type'] }}</p>
                </div>

                <hr class="my-6">

                <div class="grid grid-cols-delegateDetailGrid2 gap-y-2 items-center">

                    <p class="font-bold">Company sector:</p>
                    <p>{{ $finalDelegate['company_sector'] }}</p>

                    <p class="font-bold">Company address:</p>
                    <p>{{ $finalDelegate['company_address'] }}, {{ $finalDelegate['company_country'] }},
                        {{ $finalDelegate['company_city'] }}</p>
                </div>

                <hr class="my-6">

                <div class="grid grid-cols-delegateDetailGrid2 gap-y-2 items-center">

                    <p class="font-bold">Is Collected:</p>
                    <p>{{ $finalDelegate['isCollected'] ? 'Yes' : 'No' }}</p>

                    <p class="font-bold">Collected by:</p>
                    <p>{{ $finalDelegate['collectedBy'] }}</p>

                    <p class="font-bold">Collected Date & Time:</p>
                    <p>{{ $finalDelegate['collectedDateTime'] }}</p>
                </div>

                <hr class="my-6">

                <div class="grid grid-cols-delegateDetailGrid2 gap-y-2 items-center">

                    <p class="font-bold">Access type:</p>
                    <p>{{ $finalDelegate['access_type'] }}</p>

                    <p class="font-bold">Transaction ID:</p>
                    <p>{{ $finalDelegate['finalTransactionId'] }}</p>

                    <p class="font-bold">Invoice number:</p>

                    <span>
                        <a href="{{ route('admin.event.registrants.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'registrantId' => $finalDelegate['mainDelegateId']]) }}"
                            target="_blank"
                            class="text-blue-700 font-semibold hover:underline">{{ $finalDelegate['invoiceNumber'] }}</a>
                    </span>

                    <p class="font-bold">Seat Number:</p>
                    @if ($showEditSeatNumber)
                        <div class="flex gap-3 items-start">
                            <input type="text" wire:model="seatNumber"
                                class="bg-registrationInputFieldsBGColor text-md px-3 border border-registrationPrimaryColor outline-registrationPrimaryColor'">

                            <button wire:click="updateSeatNumber"
                                class="cursor-pointer hover:text-green-600 text-green-500">
                                Save
                            </button>

                            <button wire:click="closeEditSeatNumber"
                                class="cursor-pointer hover:text-red-600 text-red-500">
                                Cancel
                            </button>
                        </div>
                    @else
                        <div class="flex gap-3">
                            <p>
                                @if ($finalDelegate['seat_number'] == '' || $finalDelegate['seat_number'] == null)
                                    N/A
                                @else
                                    {{ $finalDelegate['seat_number'] }}
                                @endif
                            </p>
                            <button wire:click="openEditSeatNumber"
                                class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                                <i class="fa-solid fa-pen-to-square"></i>
                                Edit
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-14 items-start">
            <div class="col-span-1 grid grid-cols-2 gap-5" style="height: 492px;">
                <div>
                    <div class="border border-black mt-10 flex flex-col justify-between h-full">
                        <div>
                            <img src="{{ Storage::url($event->badge_front_banner) }}">
                        </div>
                        <div>
                            <p class="text-center text-lg">
                                @if ($finalDelegate['salutation'] == 'Dr.' || $finalDelegate['salutation'] == 'Prof.')
                                    {{ $finalDelegate['salutation'] }} {{ $finalDelegate['first_name'] }}
                                    {{ $finalDelegate['middle_name'] }}
                                    {{ $finalDelegate['last_name'] }}
                                @else
                                    {{ $finalDelegate['first_name'] }} {{ $finalDelegate['middle_name'] }}
                                    {{ $finalDelegate['last_name'] }}
                                @endif
                            </p>
                            <p class="text-center">{{ $finalDelegate['job_title'] }}</p>
                            <p class="text-center font-bold">{{ $finalDelegate['company_name'] }}</p>
                        </div>
                        <div>
                            <p class="text-center py-4 font-bold uppercase"
                                style="color: {{ $badgeViewFFTextColor }}; background-color: {{ $badgeViewFFBGColor }}">
                                {{ $badgeViewFFText }}</p>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <p>Front</p>
                    </div>
                </div>

                <div>
                    <div class="border border-black mt-10 flex flex-col justify-between h-full">
                        <div>
                            <img src="{{ Storage::url($event->badge_front_banner) }}">
                        </div>
                        <div>
                            <p class="text-center text-lg">
                                @if ($finalDelegate['salutation'] == 'Dr.' || $finalDelegate['salutation'] == 'Prof.')
                                    {{ $finalDelegate['salutation'] }} {{ $finalDelegate['first_name'] }}
                                    {{ $finalDelegate['middle_name'] }}
                                    {{ $finalDelegate['last_name'] }}
                                @else
                                    {{ $finalDelegate['first_name'] }} {{ $finalDelegate['middle_name'] }}
                                    {{ $finalDelegate['last_name'] }}
                                @endif
                            </p>
                            <p class="text-center">{{ $finalDelegate['job_title'] }}</p>
                            <p class="text-center font-bold">{{ $finalDelegate['company_name'] }}</p>
                        </div>
                        <div>
                            <p class="text-center py-4 font-bold uppercase"
                                style="color: {{ $badgeViewFBTextColor }}; background-color: {{ $badgeViewFBBGColor }}">
                                {{ $badgeViewFBText }}</p>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <p>Back</p>
                    </div>
                </div>
            </div>

            <div class="shadow-lg my-5 pt-5 bg-white rounded-md col-span-1">
                <div
                    class="grid grid-cols-2 gap-5 py-2 px-4 text-center items-center bg-registrationPrimaryColor text-white ">
                    <div class="col-span-1 break-words">No.</div>
                    <div class="col-span-1 break-words">Printed Badge Date Time</div>
                </div>

                @if ($printedBadges->isEmpty())
                    <div class="bg-red-400 text-white text-center py-2 mt-1 rounded-md">
                        There are no printed badges yet.
                    </div>
                @else
                    @foreach ($printedBadges as $printedBadgesIndex => $printedBadges)
                        <div class="grid grid-cols-2 gap-5 py-2 px-4 text-center items-center bg-gray-300">
                            <div class="col-span-1 break-words">{{ $printedBadgesIndex + 1 }}</div>
                            <div class="col-span-1 break-words"> {{ $printedBadges['printed_date_time'] }} </div>
                        </div>
                    @endforeach
                @endif

                <div
                    class="grid grid-cols-2 gap-5 py-2 px-4 text-center items-center bg-registrationPrimaryColor text-white mt-10 ">
                    <div class="col-span-1 break-words">No.</div>
                    <div class="col-span-1 break-words">Scanned Delegate Date Time</div>
                </div>

                @if ($scannedBadges->isEmpty())
                    <div class="bg-red-400 text-white text-center py-2 mt-1 rounded-md">
                        There are no scanned badges yet.
                    </div>
                @else
                    @foreach ($scannedBadges as $scannedBadgesIndex => $scannedBadges)
                        <div class="grid grid-cols-2 gap-5 py-2 px-4 text-center items-center bg-gray-300">
                            <div class="col-span-1 break-words">{{ $scannedBadgesIndex + 1 }}</div>
                            <div class="col-span-1 break-words"> {{ $scannedBadges['scanned_date_time'] }} </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="my-10">&nbsp;</div>

        <div
            class="grid grid-cols-12 gap-5 py-2 px-4 text-center items-center bg-registrationPrimaryColor text-white mt-10 ">
            <div class="col-span-1 break-words">No.</div>
            <div class="col-span-1 break-words">PC Name</div>
            <div class="col-span-1 break-words">PC Number</div>
            <div class="col-span-7 break-words">Description</div>
            <div class="col-span-2 break-words">Update Datetime</div>
        </div>

        @if ($updateLogs->isEmpty())
                    <div class="bg-red-400 text-white text-center py-2 mt-1 rounded-md">
                        There are no update logs yet.
                    </div>
                @else
                    @foreach ($updateLogs as $updateLogIndex => $updateLog)
                        <div class="grid grid-cols-12 gap-5 py-2 px-4 text-center items-center bg-gray-300">
                            <div class="col-span-1 break-words">{{ $updateLogIndex + 1 }}</div>
                            <div class="col-span-1 break-words"> {{ $updateLog['updated_by_name'] }} </div>
                            <div class="col-span-1 break-words"> {{ $updateLog['updated_by_pc_number'] }} </div>
                            <div class="col-span-7 break-words"> {{ $updateLog['description'] }} </div>
                            <div class="col-span-2 break-words"> {{ $updateLog['updated_date_time'] }} </div>
                        </div>
                    @endforeach
                @endif

        <div class="my-10">&nbsp;</div>
    </div>

</div>
