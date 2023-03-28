<div class="container mx-auto my-10">
    <a href="{{ route('admin.event.registrants.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
        class="bg-red-500 hover:bg-red-400 text-white font-medium py-2 px-5 rounded inline-flex items-center text-sm">
        <span class="mr-2"><i class="fa-sharp fa-solid fa-arrow-left"></i></span>
        <span>List of Transactions</span>
    </a>

    <div class="grid grid-cols-12 gap-20 mt-10">
        <div class="col-span-5">
            <div class="grid grid-cols-2 bg-registrationCardBGColor py-3 px-4 gap-4">
                <p>Rate:</p>
                <p class="font-bold">{{ $finalData['rate_type_string'] }}</p>
            </div>

            <div class="mt-3 bg-registrationInputFieldsBGColor py-1 px-1">
                <div class="flex items-center gap-3">
                    <p class="text-xl text-registrationPrimaryColor font-bold italic py-2 px-3">Company Details</p>
                    <button wire:click="openEditCompanyDetailsModal" class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                </div>

                <div class="grid grid-cols-2 mt-3 bg-white py-3 px-4 gap-4">
                    <p>Pass Type:</p>

                    @if ($finalData['pass_type'] == 'member')
                        <p class="font-bold">Member</p>
                    @else
                        <p class="font-bold">Non-Member</p>
                    @endif

                    <p>Name:</p>
                    <p class="font-bold">{{ $finalData['company_name'] }}</p>

                    <p>Sector:</p>
                    <p class="font-bold">{{ $finalData['company_sector'] }}</p>

                    <p>Address:</p>
                    <p class="font-bold">{{ $finalData['company_address'] }}</p>

                    <p>Country:</p>
                    <p class="font-bold">{{ $finalData['company_country'] }}</p>

                    <p>City:</p>
                    <p class="font-bold">{{ $finalData['company_city'] }}</p>

                    <p>Landline Number:</p>
                    <p class="font-bold">{{ $finalData['company_telephone_number'] }}</p>

                    <p>Mobile Number:</p>
                    <p class="font-bold">{{ $finalData['company_mobile_number'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 bg-registrationCardBGColor py-3 px-4 gap-4 mt-3">
                <p>Mode of Payment:</p>
                <p class="font-bold">
                    @if ($finalData['mode_of_payment'] == "bankTransfer")
                        Bank Transfer
                    @else
                        Credit Card
                    @endif
                </p>

                <p>Registration Status:</p>
                @if ($finalData['registration_status'] == 'confirmed')
                    <p class="font-bold text-green-600">Confirmed</p>
                @elseif ($finalData['registration_status'] == 'pending')
                    <p class="font-bold text-yellow-600">Pending</p>
                @else
                    <p class="font-bold text-red-600">Cancelled</p>
                @endif

                <p>Payment Status:</p>
                <p class="font-bold">
                    @if ($finalData['payment_status'] == "paid")
                        Paid
                    @elseif ($finalData['payment_status'] == 'free')
                        Free 
                    @else
                        Unpaid
                    @endif
                </p>

                <p>Registered:</p>
                <p class="font-bold">{{ $finalData['registered_date_time'] }}</p>

                <p>Payment Date:</p>
                <p class="font-bold">{{ $finalData['paid_date_time'] }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-5">
                @if ($finalData['registration_status'] == 'confirmed')
                    <button disabled class="bg-gray-400 cursor-not-allowed text-white py-2 rounded-md text-lg text-center"
                    >Mark as Paid</button>
                @else
                    <button wire:click="markAsPaid" class="bg-green-600 hover:bg-green-700 text-white py-2 rounded-md text-lg text-center"
                    >Mark as Paid</button>
                @endif
                <button wire:click="calculateTotal" class="bg-registrationPrimaryColorHover hover:bg-registrationPrimaryColor text-white py-2 rounded-md text-lg text-center"
                    >Calculate Total</button>

                <a href="{{ route('admin.event.registrants.view.invoice', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $registrantId]) }}"
                    class="bg-orange-800 hover:bg-orange-900 text-white py-2 rounded-md text-lg text-center"
                    target="_blank">View Invoice</a>
                <a href="{{ route('admin.event.registrants.download.invoice', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $registrantId]) }}"
                    class="bg-gray-800 hover:bg-black text-white py-2 rounded-md text-lg text-center"
                    target="_blank">Download Invoice</a>
            </div>
        </div>

        <div class="col-span-7">
            <div class="bg-registrationInputFieldsBGColor py-1 px-1">
                <div class="flex items-center gap-3">
                    <p class="text-xl text-registrationPrimaryColor font-bold italic py-2 px-3">Delegate Details</p>
                    <button wire:click="openEditMainDelegateModal" class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                </div>

                <div class="grid grid-cols-2 mt-3 bg-white py-3 px-4 gap-2">
                    <p>Full Name:</p>
                    <p class="font-bold">{{ $finalData['name'] }}</p>

                    <p>Email Address:</p>
                    <p class="font-bold">{{ $finalData['email_address'] }}</p>

                    <p>Mobile Number:</p>
                    <p class="font-bold">{{ $finalData['mobile_number'] }}</p>

                    <p>Nationality:</p>
                    <p class="font-bold">{{ $finalData['nationality'] }}</p>

                    <p>Job Title:</p>
                    <p class="font-bold">{{ $finalData['job_title'] }}</p>

                    <p>Badge Type:</p>
                    <p class="font-bold">{{ $finalData['badge_type'] }}</p>

                    <p>Promo Code used:</p>
                    @if ($finalData['pcode_used'] == null)
                        <p class="font-bold">N/A</p>
                    @else
                        <p class="font-bold">{{ $finalData['pcode_used'] }} <span
                                class="text-green-500 text-sm italic ml-2">{{ $finalData['discount'] }}%
                                discount</span></p>
                    @endif
                </div>
            </div>

            {{-- Additional Delegates --}}
            @if (count($finalData['subDelegates']) > 0)
                <div class="bg-registrationInputFieldsBGColor py-1 px-1 mt-5">
                    <p class="text-xl text-registrationPrimaryColor font-bold italic py-2 px-3">Additional Delegate
                        Details</p>
                    <div class="bg-white py-3 px-4">
                        @php
                            $count = 2;
                        @endphp
                        @foreach ($finalData['subDelegates'] as $subDelegate)
                            <div class="{{ $count == 2 ? 'mt-2' : 'mt-10' }}">

                                <div class="flex items-center gap-5">
                                    <span
                                        class="col-span-2 text-registrationPrimaryColor py-1 rounded-full border border-registrationPrimaryColor px-4 font-bold text-sm">
                                        Delegate {{ $count }}</span>
                                        <button wire:click.prevent="openEditSubDelegateModal('{{ $subDelegate['subDelegateId'] }}')" class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>
                                </div>

                                <div class="grid grid-cols-2 mt-5 gap-2 px-6">
                                    <p>Full Name:</p>
                                    <p class="font-bold">{{ $subDelegate['name'] }}</p>

                                    <p>Email Address:</p>
                                    <p class="font-bold">{{ $subDelegate['email_address'] }}</p>

                                    <p>Mobile Number:</p>
                                    <p class="font-bold">{{ $subDelegate['mobile_number'] }}</p>

                                    <p>Nationality:</p>
                                    <p class="font-bold">{{ $subDelegate['nationality'] }}</p>

                                    <p>Job Title:</p>
                                    <p class="font-bold">{{ $subDelegate['job_title'] }}</p>

                                    <p>Badge Type:</p>
                                    <p class="font-bold">{{ $subDelegate['badge_type'] }}</p>

                                    <p>Promo Code used:</p>
                                    @if ($subDelegate['pcode_used'] == null)
                                        <p class="font-bold">N/A</p>
                                    @else
                                        <p class="font-bold">{{ $subDelegate['pcode_used'] }} <span
                                                class="text-green-500 text-sm italic ml-2">{{ $subDelegate['discount'] }}%
                                                discount</span></p>
                                    @endif
                                </div>
                            </div>
                            @php
                                $count++;
                            @endphp
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Invoice details --}}
            <div class="bg-registrationInputFieldsBGColor p-2 mt-5">
                <div class="grid grid-cols-6 text-center font-bold text-registrationPrimaryColor text-lg pt-2 pb-4">
                    <div class="col-span-2">
                        <p>Description</p>
                    </div>

                    <div class="col-span-1">
                        <p>Qty</p>
                    </div>

                    <div class="col-span-1">
                        <p>Unit price</p>
                    </div>

                    <div class="col-span-1">
                        <p>Discount</p>
                    </div>

                    <div class="col-span-1">
                        <p>Net amount</p>
                    </div>
                </div>

                @php
                    $count = 1;
                @endphp

                @foreach ($finalData['invoiceData']['invoiceDetails'] as $delegatInvoiceDetail)
                    <div class="grid grid-cols-6 gap-x-2">
                        <div class="col-span-2 bg-white p-4">
                            @if ($count == 1)
                                <p>{{ $finalData['invoiceData']['eventName'] }} –
                                    {{ $finalData['invoiceData']['finalEventStartDate'] . ' - ' . $finalData['invoiceData']['finalEventEndDate'] }}
                                    at {{ $finalData['invoiceData']['eventLocation'] }}</p>
                                <p class="mt-10">{{ $delegatInvoiceDetail['delegateDescription'] }}</p>
                            @else
                                <p>{{ $delegatInvoiceDetail['delegateDescription'] }}</p>
                            @endif
                            <ul class="mt-2 list-decimal ml-4">
                                @foreach ($delegatInvoiceDetail['delegateNames'] as $name)
                                    <li>{{ $name }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="col-span-1 bg-white p-4 flex justify-center items-center">
                            <p>{{ $delegatInvoiceDetail['quantity'] }}</p>
                        </div>

                        <div class="col-span-1 bg-white p-4 flex justify-center items-center">
                            <p>$ {{ number_format($finalData['invoiceData']['unit_price'], 2, '.', ',') }}</p>
                        </div>

                        <div class="col-span-1 bg-white p-4 flex justify-center items-center">
                            <p>$ {{ number_format($delegatInvoiceDetail['totalDiscount'], 2, '.', ',') }}</p>
                        </div>

                        <div class="col-span-1 bg-white p-4 flex justify-center items-center">
                            <p>$ {{ number_format($delegatInvoiceDetail['totalNetAmount'], 2, '.', ',') }}</p>
                        </div>
                    </div>

                    @php
                        $count += 1;
                    @endphp
                @endforeach

                <div class="grid grid-cols-5 gap-2 mt-2">
                    <div class="col-span-4 bg-white p-4">
                        <p>Total (before VAT)</p>
                    </div>

                    <div class="col-span-1 bg-white p-4 text-right">
                        <p>$ {{ number_format($finalData['invoiceData']['net_amount'], 2, '.', ',') }}</p>
                    </div>

                    <div class="col-span-4 bg-white p-4">
                        <p>VAT {{ $finalData['invoiceData']['eventVat'] }}%</p>
                    </div>

                    <div class="col-span-1 bg-white p-4 text-right">
                        <p>$ {{ number_format($finalData['invoiceData']['vat_price'], 2, '.', ',') }}</p>
                    </div>

                    <div class="col-span-4 bg-white p-4 font-bold">
                        <p>TOTAL</p>
                    </div>

                    <div class="col-span-1 bg-white p-4 text-right font-bold">
                        <p>$ {{ number_format($finalData['invoiceData']['total_amount'], 2, '.', ',') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($showDelegateModal)
        @include('livewire.registrants.edit_delegate_form')
    @endif
    
    @if ($showCompanyModal)
        @include('livewire.registrants.edit_company_form')
    @endif
</div>
