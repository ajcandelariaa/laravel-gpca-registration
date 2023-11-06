<div>
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>
    <div class="mx-10 my-10">
        <div class="flex justify-between">
            <div>
                <a href="{{ route('admin.event.registrants.exportData', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                    target="_blank"
                    class="bg-green-600 hover:bg-green-700 text-white py-2 px-5 rounded-md text-lg text-center">Export
                    Data to Excel</a>
                <button type="button" wire:click="openImportModal" wire:key="openImportModal"
                    class="bg-sky-600 hover:bg-sky-700 text-white py-2 px-5 rounded-md text-lg text-center">Import
                    Transactions</button>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" wire:model.lazy="searchTerm"
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
        </div>

        <div class="mt-5 flex flex-row gap-6">
            <div>
                <label>Pass type: </label>
                <select wire:model.lazy="filterByPassType" class="border border-gray-300 bg-white rounded-md py-1">
                    <option value=""></option>
                    <option value="Full Member">Full Member</option>
                    <option value="Member">Member</option>
                    <option value="Non-Member">Non-Member</option>
                </select>
            </div>

            <div>
                <label>Registration status: </label>
                <select wire:model.lazy="filterByRegStatus" class="border border-gray-300 bg-white rounded-md py-1">
                    <option value=""></option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Pending">Pending</option>
                    <option value="Dropped out">Dropped out</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <div>
                <label>Payment status:</label>
                <select wire:model.lazy="filterByPayStatus" class="border border-gray-300 bg-white rounded-md py-1">
                    <option value=""></option>
                    <option value="Free">Free</option>
                    <option value="Paid">Paid</option>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Refunded">Refunded</option>
                </select>
            </div>

            <div>
                <button wire:click="filter" class="bg-registrationPrimaryColorHover hover:bg-registrationPrimaryColor text-white py-1 px-4 rounded-md">Filter</button>
                @if ($filterByPassType != null || $filterByRegStatus != null || $filterByPayStatus != null)
                    <button wire:click="clearFilter" class="bg-red-700 hover:bg-red-800 text-white py-1 px-4 rounded-md">Clear Filter</button>
                @endif
            </div>
        </div>

        <div class="shadow-lg my-5 pt-5 bg-white rounded-md">
            <h1 class="text-center text-2xl bg-registrationPrimaryColor text-white py-4">Transactions</h1>

            <div class="grid grid-cols-12 gap-5 p-4 text-center items-center bg-blue-600 text-white ">
                <div class="col-span-1 break-words">Invoice Number</div>
                <div class="col-span-1 break-words">Company</div>
                <div class="col-span-1 break-words">Country</div>
                <div class="col-span-1 break-words">City</div>
                <div class="col-span-1 break-words">Pass Type</div>
                <div class="col-span-1 break-words">Quantity</div>
                <div class="col-span-1 break-words">Total Amount</div>
                <div class="col-span-1 break-words">Registered Date & Time</div>
                <div class="col-span-1 break-words">Registration Status</div>
                <div class="col-span-1 break-words">Payment Status</div>
                <div class="col-span-1 break-words">Payment Method</div>
                <div class="col-span-1 break-words">Action</div>
            </div>

            @php
                $count = 1;
            @endphp

            @if (count($finalListOfRegistrants) == 0)
                <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md">
                    There are no transactions yet.
                </div>
            @else
                @foreach ($finalListOfRegistrants as $finalListOfRegistrant)
                    <div
                        class="grid grid-cols-12 gap-5 px-4 py-2 mb-1 text-center items-center text-sm {{ $count % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                        <div class="col-span-1 break-words">{{ $finalListOfRegistrant['invoiceNumber'] }}</div>
                        <div class="col-span-1 break-words">{{ $finalListOfRegistrant['companyName'] }}</div>
                        <div class="col-span-1 break-words">{{ $finalListOfRegistrant['country'] }}</div>
                        <div class="col-span-1 break-words">{{ $finalListOfRegistrant['city'] }}</div>
                        <div class="col-span-1 break-words">{{ $finalListOfRegistrant['passType'] }}</div>
                        <div class="col-span-1 break-words">{{ $finalListOfRegistrant['quantity'] }}</div>
                        <div class="col-span-1 break-words">$
                            {{ number_format($finalListOfRegistrant['totalAmount'], 2, '.', ',') }}</div>
                        <div class="col-span-1 break-words">{{ $finalListOfRegistrant['regDateTime'] }}</div>
                        <div class="col-span-1 break-words">{{ $finalListOfRegistrant['regStatus'] }}</div>
                        <div class="col-span-1 break-words">{{ $finalListOfRegistrant['payStatus'] }}</div>
                        <div class="col-span-1 break-words">{{ $finalListOfRegistrant['paymentMethod'] }}</div>
                        <div class="col-span-1 break-words">
                            <a href="{{ route('admin.event.registrants.detail.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $finalListOfRegistrant['mainVisitorId']]) }}"
                                class="cursor-pointer hover:text-gray-600 text-gray-500" target="_blank">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                        </div>
                        @php
                            $count++;
                        @endphp
                    </div>
                @endforeach
            @endif
        </div>
        @if ($showImportModal)
            @include('livewire.admin.events.transactions.import-form.import_registrants_form')
        @endif
    </div>
</div>
