@extends('admin.layouts.master')

@section('content')
    <div class="container mx-auto my-10">
        <a href="{{ route('admin.event.registrants.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
            class="bg-red-500 hover:bg-red-400 text-white font-medium py-2 px-5 rounded inline-flex items-center text-sm">
            <span class="mr-2"><i class="fa-sharp fa-solid fa-arrow-left"></i></span>
            <span>List of Registrants</span>
        </a>

        <div class="grid grid-cols-12 gap-20 mt-10">
            <div class="col-span-5">
                <div class="grid grid-cols-2 bg-registrationCardBGColor py-3 px-4 gap-4">
                    <p>Rate:</p>
                    <p class="font-bold">{{ $finalData['rate_type_string'] }}</p>
                </div>

                <div class="mt-3 bg-registrationInputFieldsBGColor py-1 px-1">
                    <p class="text-xl text-registrationPrimaryColor font-bold italic py-2 px-3">Company Details</p>

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
                    <p class="font-bold">{{ $finalData['mode_of_payment'] }}</p>

                    <p>Registration Status:</p>
                    @if ($finalData['registration_status'] == 'confirmed')
                        <p class="font-bold text-green-600">Confirmed</p>
                    @elseif ($finalData['registration_status'] == 'pending')
                        <p class="font-bold text-yellow-600">Pending</p>
                    @else
                        <p class="font-bold text-red-600">Cancelled</p>
                    @endif

                    <p>Payment Status:</p>
                    <p class="font-bold">{{ $finalData['payment_status'] }}</p>

                    <p>Registered:</p>
                    <p class="font-bold">{{ $finalData['registered_date_time'] }}</p>

                    <p>Payment Date:</p>
                    <p class="font-bold">{{ $finalData['paid_date_time'] }}</p>
                </div>
            </div>

            <div class="col-span-7">
                <div class="bg-registrationInputFieldsBGColor py-1 px-1">
                    <p class="text-xl text-registrationPrimaryColor font-bold italic py-2 px-3">Delegate Details</p>

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
                                <div class="{{ $count == 2 ? "mt-2" : "mt-10"}}">
                                    <span class="col-span-2 text-registrationPrimaryColor py-1 rounded-full border border-registrationPrimaryColor px-4 font-bold text-sm">
                                        Delegate {{ $count }}</span>
                                        
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

                    @foreach ($finalData['invoiceDetails'] as $delegatInvoiceDetail)
                        <div class="grid grid-cols-6 gap-x-2">
                            <div class="col-span-2 bg-white p-4">
                                @if ($count == 1)
                                    <p>{{ $finalData['event']->name }} –
                                        {{ $finalData['finalEventStartDate'] . ' - ' . $finalData['finalEventEndDate'] }}
                                        at {{ $finalData['event']->location }}</p>
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
                                <p>$ {{ number_format($finalData['unit_price'], 2, '.', ',') }}</p>
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
                            <p>$ {{ number_format($finalData['net_amount'], 2, '.', ',') }}</p>
                        </div>

                        <div class="col-span-4 bg-white p-4">
                            <p>VAT {{ $finalData['event']->event_vat }}%</p>
                        </div>

                        <div class="col-span-1 bg-white p-4 text-right">
                            <p>$ {{ number_format($finalData['vat_price'], 2, '.', ',') }}</p>
                        </div>

                        <div class="col-span-4 bg-white p-4 font-bold">
                            <p>TOTAL</p>
                        </div>

                        <div class="col-span-1 bg-white p-4 text-right font-bold">
                            <p>$ {{ number_format($finalData['total_amount'], 2, '.', ',') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection