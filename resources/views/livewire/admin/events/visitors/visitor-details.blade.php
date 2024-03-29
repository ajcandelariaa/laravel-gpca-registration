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
                        wire:click="printBadgeClicked('{{ $finalVisitor['visitorType'] }}', {{ $finalVisitor['visitorId'] }})"
                        class="bg-blue-800 hover:bg-blue-900 text-white py-2 px-2 rounded-md  text-center w-full">
                        Print Badge
                    </button>

                    <div class="text-center my-2">
                        <p>or scan the below QR to print</p>
                    </div>

                    <div class="mt-2 flex justify-center">
                        {!! QrCode::size(300)->generate($printBadgeVisitorUrl) !!}
                    </div>
                </div>
            </div>

            <div class="mt-10">
                <div class="flex justify-between">
                    <div>
                        <p class="text-primaryColor font-bold text-3xl">{{ $finalVisitor['salutation'] }}
                            {{ $finalVisitor['first_name'] }} {{ $finalVisitor['middle_name'] }}
                            {{ $finalVisitor['last_name'] }}</p>
                        <p class="mt-2 italic text-lg">{{ $finalVisitor['job_title'] }}</p>
                        <p class="font-bold text-lg">{{ $finalVisitor['company_name'] }}</p>
                    </div>
                    <div>
                        <p class="text-center">Scan here</p>
                        <div>
                            {!! QrCode::size(150)->generate($scanVisitorUrl) !!}
                        </div>
                    </div>
                </div>

                <hr class="my-6">

                <div class="grid grid-cols-delegateDetailGrid2 gap-y-2 items-center">
                    <p class="font-bold">Other Company name:</p>
                    <p>
                        @if ($finalVisitor['alternative_company_name'] != null)
                            {{ $finalVisitor['alternative_company_name'] }}
                        @else
                            N/A
                        @endif
                    </p>

                    <p class="font-bold">Pass type:</p>
                    <p>
                        @if ($finalVisitor['pass_type'] == 'fullMember')
                            Full Member
                        @elseif ($finalVisitor['pass_type'] == 'member')
                            Member
                        @else
                            Non-Member
                        @endif
                    </p>

                    <p class="font-bold">Email address:</p>
                    <p>{{ $finalVisitor['email_address'] }}</p>

                    <p class="font-bold">Mobile number:</p>
                    <p>{{ $finalVisitor['mobile_number'] }}</p>

                    <p class="font-bold">Nationality:</p>
                    <p>{{ $finalVisitor['nationality'] }}</p>

                    <p class="font-bold">Registration type:</p>
                    <p>{{ $finalVisitor['badge_type'] }}</p>
                </div>

                <hr class="my-6">

                <div class="grid grid-cols-delegateDetailGrid2 gap-y-2 items-center">

                    <p class="font-bold">Company sector:</p>
                    <p>{{ $finalVisitor['company_sector'] }}</p>

                    <p class="font-bold">Company address:</p>
                    <p>{{ $finalVisitor['company_address'] }}, {{ $finalVisitor['company_country'] }},
                        {{ $finalVisitor['company_city'] }}</p>
                </div>

                <hr class="my-6">

                <div class="grid grid-cols-delegateDetailGrid2 gap-y-2 items-center">

                    <p class="font-bold">Transaction ID:</p>
                    <p>{{ $finalVisitor['finalTransactionId'] }}</p>

                    <p class="font-bold">Invoice number:</p>

                    <span>
                        <a href="{{ route('admin.event.registrants.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'registrantId' => $finalVisitor['mainVisitorId']]) }}"
                            target="_blank"
                            class="text-blue-700 font-semibold hover:underline">{{ $finalVisitor['invoiceNumber'] }}</a>
                    </span>
                    
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
                                @if ($finalVisitor['salutation'] == 'Dr.' || $finalVisitor['salutation'] == 'Prof.')
                                    {{ $finalVisitor['salutation'] }} {{ $finalVisitor['first_name'] }} {{ $finalVisitor['middle_name'] }}
                                    {{ $finalVisitor['last_name'] }}
                                @else
                                    {{ $finalVisitor['first_name'] }} {{ $finalVisitor['middle_name'] }}
                                    {{ $finalVisitor['last_name'] }}
                                @endif
                            </p>
                            <p class="text-center">{{ $finalVisitor['job_title'] }}</p>
                            <p class="text-center font-bold">{{ $finalVisitor['company_name'] }}</p>
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
                                @if ($finalVisitor['salutation'] == 'Dr.' || $finalVisitor['salutation'] == 'Prof.')
                                    {{ $finalVisitor['salutation'] }} {{ $finalVisitor['first_name'] }} {{ $finalVisitor['middle_name'] }}
                                    {{ $finalVisitor['last_name'] }}
                                @else
                                    {{ $finalVisitor['first_name'] }} {{ $finalVisitor['middle_name'] }}
                                    {{ $finalVisitor['last_name'] }}
                                @endif
                            </p>
                            <p class="text-center">{{ $finalVisitor['job_title'] }}</p>
                            <p class="text-center font-bold">{{ $finalVisitor['company_name'] }}</p>
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
                <div class="grid grid-cols-2 gap-5 py-2 px-4 text-center items-center bg-registrationPrimaryColor text-white ">
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

                <div class="grid grid-cols-2 gap-5 py-2 px-4 text-center items-center bg-registrationPrimaryColor text-white mt-10 ">
                    <div class="col-span-1 break-words">No.</div>
                    <div class="col-span-1 break-words">Scanned Visitor Date Time</div>
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
    </div>
</div>
