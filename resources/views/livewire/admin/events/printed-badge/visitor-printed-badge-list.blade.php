<div>
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>

    <div class="mx-10 my-10">
        <form>
            <div class="relative">
                <input type="text" wire:model="searchTerm"
                    class="border border-gray-300 bg-white rounded-md py-2 pl-10 pr-4 block transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                    placeholder="Search...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </form>

        <div class="shadow-lg my-5 pt-5 bg-white rounded-md">
            <h1 class="text-center text-2xl bg-registrationPrimaryColor text-white py-4">Printed Badges</h1>

            <div class="grid grid-cols-12 gap-5 p-4 px-4 text-center items-center bg-blue-600 text-white ">
                <div class="col-span-1 break-words">No.</div>
                <div class="col-span-1 break-words">Transaction ID</div>
                <div class="col-span-1 break-words">Invoice</div>
                <div class="col-span-2 break-words">Name</div>
                <div class="col-span-1 break-words">Company</div>
                <div class="col-span-2 break-words">Email Address</div>
                <div class="col-span-1 break-words">Registration type</div>
                <div class="col-span-1 break-words">Print Count</div>
                <div class="col-span-2 break-words">Printed Date Time</div>
            </div>

            @if (empty($finalListsOfVisitors))
                <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md">
                    There are no printed badges yet.
                </div>
            @else
                @foreach ($finalListsOfVisitors as $visitorIndex => $finalListsOfVisitor)
                    <div
                        class="grid grid-cols-12 gap-5 py-2 px-4 mb-1 text-center items-center  {{ $visitorIndex % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                        <div class="col-span-1 break-words">{{ $visitorIndex + 1 }}</div>

                        <div class="col-span-1 break-words">
                            <a href="{{ route('admin.event.delegates.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'delegateType' => $finalListsOfVisitor['visitorType'], 'delegateId' => $finalListsOfVisitor['visitorId']]) }}" target="_blank" class="text-blue-700 font-semibold hover:underline">
                                {{ $finalListsOfVisitor['visitorTransactionId'] }}
                            </a>
                        </div>

                        <div class="col-span-1 break-words">
                            <a href="{{ route('admin.event.registrants.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'registrantId' => $finalListsOfVisitor['mainVisitorId']]) }}" target="_blank" class="text-blue-700 font-semibold hover:underline">
                                {{ $finalListsOfVisitor['visitorInvoiceNumber'] }}
                            </a>
                        </div>

                        <div class="col-span-2 break-words">
                            {{ $finalListsOfVisitor['visitorName'] }}
                        </div>

                        <div class="col-span-1 break-words">
                            {{ $finalListsOfVisitor['visitorCompany'] }}
                        </div>

                        <div class="col-span-2 break-words">
                            {{ $finalListsOfVisitor['visitorEmailAddress'] }}
                        </div>

                        <div class="col-span-1 break-words">
                            {{ $finalListsOfVisitor['visitorBadgeType'] }}
                        </div>

                        <div class="col-span-1 break-words">
                            {{ $finalListsOfVisitor['visitorPrintBadgeCount'] }}
                        </div>

                        <div class="col-span-2 break-words">
                            {{ $finalListsOfVisitor['visitorPrintedDateTime'] }}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
