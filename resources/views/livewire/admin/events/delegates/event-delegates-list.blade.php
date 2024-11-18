<div>
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>
    <div class="mx-10 my-10">
        <div class="flex justify-between">
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" wire:model.lazy="searchTerm" wire:keydown.enter="search"
                        class="border border-gray-300 bg-white rounded-md py-2 pl-10 pr-4 block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                        placeholder="Search...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                <div>
                    <button wire:click="search"
                        class="bg-registrationPrimaryColorHover hover:bg-registrationPrimaryColor text-white py-1 px-4 rounded-md">Search</button>
                </div>
            </div>
            
            <a href="{{ route('admin.update.logs.view', ['eventCategory' => $event->category, 'eventId' => $event->id]) }}"
            class="bg-registrationPrimaryColorHover hover:bg-registrationPrimaryColor text-white py-1 px-4 rounded-md flex items-center" target="_blank">Delegate Details Update Logs</a>
        </div>

        <div class="shadow-lg my-5 bg-white rounded-md">
            <h1 class="text-center text-2xl bg-registrationPrimaryColor text-white py-4">Confirmed Delegates
                ({{ count($finalListsOfDelegatesTemp) }})</h1>

            <div class="grid grid-cols-11 gap-5 p-4 text-center items-center bg-blue-600 text-white ">
                <div class="col-span-1 break-words">No.</div>
                <div class="col-span-1 break-words">Transaction ID</div>
                <div class="col-span-1 break-words">Invoice</div>
                <div class="col-span-2 break-words">Name</div>
                <div class="col-span-1 break-words">Job title</div>
                <div class="col-span-1 break-words">Company</div>
                <div class="col-span-1 break-words">Email Address</div>
                <div class="col-span-1 break-words">Registration Type</div>
                <div class="col-span-1 break-words">Printed</div>
                <div class="col-span-1 break-words">Scanned</div>
                {{-- <div class="col-span-1 break-words">Badge</div> --}}
            </div>

            @if (empty($finalListsOfDelegates))
                <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md">
                    There are no registrants yet.
                </div>
            @else
                @foreach ($finalListsOfDelegates as $delegateIndex => $finalListsOfDelegate)
                    <div
                        class="grid grid-cols-11 gap-5 py-2 px-4 mb-1 text-center items-center  {{ $delegateIndex % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                        <div class="col-span-1 break-words text-sm">{{ $delegateIndex + 1 }}</div>

                        <div class="col-span-1 break-words text-sm">
                            <a href="{{ route('admin.event.delegates.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'delegateType' => $finalListsOfDelegate['delegateType'], 'delegateId' => $finalListsOfDelegate['delegateId']]) }}"
                                target="_blank" class="text-blue-700 font-semibold hover:underline">
                                {{ $finalListsOfDelegate['delegateTransactionId'] }}
                            </a>
                        </div>

                        <div class="col-span-1 break-words text-sm">
                            <a href="{{ route('admin.event.registrants.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'registrantId' => $finalListsOfDelegate['mainDelegateId']]) }}"
                                target="_blank" class="text-blue-700 font-semibold hover:underline">
                                {{ $finalListsOfDelegate['delegateInvoiceNumber'] }}
                            </a>
                        </div>

                        <div class="col-span-2 break-words text-sm">
                            {{ $finalListsOfDelegate['delegateSalutation'] }}
                            {{ $finalListsOfDelegate['delegateFName'] }} {{ $finalListsOfDelegate['delegateMName'] }}
                            {{ $finalListsOfDelegate['delegateLName'] }}
                        </div>

                        <div class="col-span-1 break-words text-sm">
                            {{ $finalListsOfDelegate['delegateJobTitle'] }}
                        </div>

                        <div class="col-span-1 break-words text-sm">
                            {{ $finalListsOfDelegate['delegateCompany'] }}
                        </div>

                        <div class="col-span-1 break-words text-sm">
                            {{ $finalListsOfDelegate['delegateEmailAddress'] }}
                        </div>

                        <div class="col-span-1 break-words text-sm">
                            {{ $finalListsOfDelegate['delegateBadgeType'] }}
                        </div>

                        <div class="col-span-1 break-words text-sm font-bold">
                            @if ($finalListsOfDelegate['delegatePrinted'] == 'Yes')
                                <span class="text-green-600">
                                    {{ $finalListsOfDelegate['delegatePrinted'] }}
                                </span>
                            @else
                                <span class="text-red-600">
                                    {{ $finalListsOfDelegate['delegatePrinted'] }}
                                </span>
                            @endif
                        </div>


                        <div class="col-span-1 break-words text-sm font-bold">
                            @if ($finalListsOfDelegate['delegateScanned'] == 'Yes')
                                <span class="text-green-600">
                                    {{ $finalListsOfDelegate['delegateScanned'] }}
                                </span>
                            @else
                                <span class="text-red-600">
                                    {{ $finalListsOfDelegate['delegateScanned'] }}
                                </span>
                            @endif
                        </div>

                        {{-- <div class="col-span-1 break-words text-sm">
                            <button wire:click="previewBadge({{ $delegateIndex }})"
                                class="cursor-pointer hover:text-gray-600 text-gray-500 mr-4 text-sm" target="_blank">
                                <i class="fa-solid fa-eye"></i> Preview
                            </button>

                            <button type="button"
                                wire:click="printBadgeClicked('{{ $finalListsOfDelegate['delegateType'] }}', {{ $finalListsOfDelegate['delegateId'] }}, {{ $delegateIndex }})"
                                class="bg-green-800 hover:bg-green-900 text-white py-1 px-2 w-full rounded-md text-xs text-center mt-2">
                                Print
                            </button>
                        </div> --}}
                    </div>
                @endforeach

                {{-- @if ($badgeView)
                    @include('livewire.admin.events.delegates.view_badge_modal')
                @endif --}}
            @endif
        </div>
    </div>
</div>
