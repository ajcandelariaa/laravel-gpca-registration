<div class="container mx-auto my-10">
    @php
        use Carbon\Carbon;
    @endphp
    <div class="flex justify-between">
        <div>
            <a href="{{ route('admin.event.registrants.exportData', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                target="_blank"
                class="bg-green-600 hover:bg-green-700 text-white py-2 px-5 rounded-md text-lg text-center">Export
                Data to Excel</a>
            <button type="button" wire:click="openImportModal" wire:key="openImportModal"
                class="bg-sky-600 hover:bg-sky-700 text-white py-2 px-5 rounded-md text-lg text-center">Import
                Data</button>
        </div>
        <form>
            <div class="relative">
                <input type="text" wire:model="searchTerm"
                    class="border border-gray-300 bg-white rounded-md py-2 pl-10 pr-4 block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                    placeholder="Search...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </form>
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

        @if ($finalListOfRegistrants->isEmpty())
            <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md container mx-auto">
                There are no transactions yet.
            </div>
        @else
            @foreach ($finalListOfRegistrants as $finalListOfRegistrant)
                <div
                    class="grid grid-cols-12 pt-2 pb-2 mb-1 text-center items-center {{ $count % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                    <div class="col-span-1">{{ $count }}</div>

                    <div class="col-span-2">
                        {{ Carbon::parse($finalListOfRegistrant->registered_date_time)->format('M j, Y g:iA') }}
                    </div>

                    <div class="col-span-2">
                        {{ $finalListOfRegistrant->company_name }}
                    </div>

                    <div class="col-span-1">
                        {{ $finalListOfRegistrant->company_country }}
                    </div>

                    <div class="col-span-1">
                        {{ $finalListOfRegistrant->company_city }}
                    </div>

                    <div class="col-span-1">
                        @if ($finalListOfRegistrant->pass_type == 'member')
                            Member
                        @else
                            Non-Member
                        @endif
                    </div>

                    <div class="col-span-1">
                        {{ $finalListOfRegistrant->quantity }}
                    </div>

                    <div class="col-span-1">
                        $ {{ number_format($finalListOfRegistrant->total_amount, 2, '.', ',') }}
                    </div>

                    <div class="col-span-1">
                        @if ($finalListOfRegistrant->payment_status == 'paid')
                            Paid
                        @elseif ($finalListOfRegistrant->payment_status == 'free')
                            Free
                        @else
                            Unpaid
                        @endif
                    </div>

                    <div class="col-span-1">
                        <a href="{{ route('admin.event.registrants.detail.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $finalListOfRegistrant->id]) }}"
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
        @include('livewire.registrants.import_registrants_form')
    @endif
</div>
