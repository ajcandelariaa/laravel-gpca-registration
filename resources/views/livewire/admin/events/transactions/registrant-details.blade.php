<div>
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>

    <div class="container mx-auto my-10">
        {{-- <div wire:loading>
            @include('helpers.loading_screen')
        </div> --}}


        <a href="{{ route('admin.event.registrants.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
            class="bg-red-500 hover:bg-red-400 text-white font-medium py-2 px-5 rounded inline-flex items-center text-sm">
            <span class="mr-2"><i class="fa-sharp fa-solid fa-arrow-left"></i></span>
            <span>List of Transactions</span>
        </a>

        <div class="grid grid-cols-12 gap-20 mt-10">
            <div class="col-span-5">
                {{-- <div class="bg-registrationCardBGColor py-3 px-4 gap-4">
                    <p class="font-bold text-center">{{ $finalData['rate_type_string'] }}</p>
                </div> --}}

                <div class="mt-3 bg-registrationInputFieldsBGColor py-1 px-1">
                    <div class="flex items-center gap-3">
                        <p class="text-xl text-registrationPrimaryColor font-bold italic py-4 px-3">Registration Details
                        </p>
                        <button wire:click="openEditRegistrationDetailsModal"
                            class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>
                    </div>

                    <div class="grid grid-cols-2 bg-white py-3 px-4 gap-4">
                        <p>Access Type:</p>

                        @if ($finalData['access_type'] == 'conferenceOnly')
                            <p class="font-bold">Conference only</p>
                        @elseif ($finalData['access_type'] == 'workshopOnly')
                            <p class="font-bold">Workshop only</p>
                        @else
                            <p class="font-bold">Full event</p>
                        @endif

                        <p>Rate Type:</p>
                        <p class="font-bold">{{ $finalData['rate_type'] }}</p>
                    </div>
                </div>

                <div class="mt-3 bg-registrationInputFieldsBGColor py-1 px-1">
                    <div class="flex items-center gap-3">
                        <p class="text-xl text-registrationPrimaryColor font-bold italic py-4 px-3">Company Details</p>
                        <button wire:click="openEditCompanyDetailsModal"
                            class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>
                    </div>

                    <div class="grid grid-cols-2 bg-white py-3 px-4 gap-4">
                        <p>Pass Type:</p>

                        @if ($finalData['pass_type'] == 'fullMember')
                            <p class="font-bold">Full Member</p>
                        @elseif ($finalData['pass_type'] == 'member')
                            <p class="font-bold">Member</p>
                        @else
                            <p class="font-bold">Non-Member</p>
                        @endif

                        <p>Name:</p>
                        <p class="font-bold">{{ $finalData['company_name'] }}</p>

                        <p>Alternative Name:</p>
                        <p class="font-bold">
                            @if ($finalData['alternative_company_name'] != null)
                                {{ $finalData['alternative_company_name'] }}
                            @else
                                N/A
                            @endif
                        </p>

                        <p>Sector:</p>
                        <p class="font-bold">{{ $finalData['company_sector'] }}</p>

                        <p>Address:</p>
                        <p class="font-bold">{{ $finalData['company_address'] }}</p>

                        <p>Country:</p>
                        <p class="font-bold">{{ $finalData['company_country'] }}</p>

                        <p>City:</p>
                        <p class="font-bold">{{ $finalData['company_city'] }}</p>

                        <p>Landline Number:</p>
                        <p class="font-bold">
                            {{ $finalData['company_telephone_number'] ? $finalData['company_telephone_number'] : 'N/A' }}
                        </p>

                        <p>Mobile Number:</p>
                        <p class="font-bold">{{ $finalData['company_mobile_number'] }}</p>

                        <p>Assistant's email address:</p>
                        <p class="font-bold">
                            @if ($finalData['assistant_email_address'] != null)
                                {{ $finalData['assistant_email_address'] }}
                            @else
                                N/A
                            @endif
                        </p>

                        <p>Where did you hear about us?</p>
                        <p class="font-bold">
                            @if ($finalData['heard_where'] != null)
                                {{ $finalData['heard_where'] }}
                            @else
                                N/A
                            @endif
                        </p>

                        @if ($eventCategory == 'PC' && $event->year == '2023')
                            <p> Would you be attending the Networking Gala Dinner and Plastics Circul-A-Thon Awards
                                14<sup>th</sup> May 2023?</p>
                            <p class="font-bold"> {{ $finalData['pc_attending_nd'] }} </p>
                        @endif

                        @if ($eventCategory == 'SCC' && $event->year == '2023')
                            <p>Would you be attending the Networking Gala Dinner and SC Excellence Awards on
                                16<sup>th</sup> May 2013?</p>
                            <p class="font-bold"> {{ $finalData['scc_attending_nd'] }} </p>
                        @endif

                        @if ($eventCategory == 'PSC' && $event->year == '2024')
                            <p>Workshop interested in:</p>
                            @if ($finalData['optional_interests'] != null)
                                <p class="font-bold"> {{ $finalData['optional_interests'] }} </p>
                            @else
                                <p class="font-bold">N/A</p>
                            @endif
                        @endif

                        @if ($eventCategory == 'AF' && $event->year == '2024')
                            <p>Would like to receive WhatsApp notifications?</p>
                            <p class="font-bold">
                                @if ($finalData['receive_whatsapp_notifications'])
                                    Yes
                                @else
                                    No
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                @if ($eventCategory == 'AF' && $event->year == '2024')
                    <div
                        class="bg-registrationCardBGColor text-lg text-center text-registrationPrimaryColor font-bold py-2 mt-3">
                        Attending to:
                    </div>
                    <div class="font-semibold mt-2 bg-registrationInputFieldsBGColor py-2">
                        @if (
                            $finalData['attending_plenary'] ||
                                $finalData['attending_sustainability'] ||
                                $finalData['attending_solxchange'] ||
                                $finalData['attending_yf'] ||
                                $finalData['attending_welcome_dinner'] ||
                                $finalData['attending_gala_dinner']
                        )
                            <ul class="list-disc ml-10">
                                @if ($finalData['attending_plenary'])
                                    <li>18<sup>th</sup> Annual GPCA Forum Main Plenary | 2-5 December 2024</li>
                                @endif

                                @if ($finalData['attending_sustainability'])
                                    <li>GPCA Sustainability Pavilion | 3-5 December 2024</li>
                                @endif

                                @if ($finalData['attending_solxchange'])
                                    <li>Solutions XChange | 3-5 December 2024</li>
                                @endif

                                @if ($finalData['attending_yf'])
                                    <li>GPCA Youth Forum | 3-5 December 2024</li>
                                @endif

                                @if ($finalData['attending_welcome_dinner'])
                                    <li>Welcome Dinner Sponsored by OQ | 3 December 2024</li>
                                @endif

                                @if ($finalData['attending_gala_dinner'])
                                    <li>Gala Dinner Sponsored by Sabic | 4 December 2024</li>
                                @endif
                            </ul>
                        @else
                            N/A
                        @endif
                    </div>
                @endif

                @if ($eventCategory == 'AF' && $event->year == '2023')
                    <div
                        class="bg-registrationCardBGColor text-lg text-center text-registrationPrimaryColor font-bold py-2 mt-3">
                        Attending to:
                    </div>
                    <div class="font-semibold mt-2 bg-registrationInputFieldsBGColor py-2">
                        @if (
                            $finalData['attending_plenary'] ||
                                $finalData['attending_symposium'] ||
                                $finalData['attending_solxchange'] ||
                                $finalData['attending_yf'] ||
                                $finalData['attending_welcome_dinner'] ||
                                $finalData['attending_gala_dinner']
                        )
                            <ul class="list-disc ml-10">
                                @if ($finalData['attending_plenary'])
                                    <li>17<sup>th</sup> Annual GPCA Forum Main Plenary | 4-6 December 2023</li>
                                @endif

                                @if ($finalData['attending_symposium'])
                                    <li>GPCA Symposium | 4-5 December 2023</li>
                                @endif

                                @if ($finalData['attending_solxchange'])
                                    <li>Solutions XChange | 4-5 December 2023</li>
                                @endif

                                @if ($finalData['attending_yf'])
                                    <li>GPCA Youth Forum | 4-6 December 2023</li>
                                @endif

                                {{-- @if ($finalData['attending_networking_dinner'])
                                    <li>Networking Dinner | 4 December 2023</li>
                                @endif --}}

                                @if ($finalData['attending_welcome_dinner'])
                                    <li>Welcome Dinner Sponsored by QatarEnergy | 4 December 2023</li>
                                @endif

                                @if ($finalData['attending_gala_dinner'])
                                    <li>Gala Dinner Sponsored by SABIC | 5 December 2023</li>
                                @endif
                            </ul>
                        @else
                            N/A
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-2 bg-registrationCardBGColor py-3 px-4 gap-4 mt-3">
                    <p>Invoice number:</p>
                    <p class="font-bold">
                        {{ $finalData['invoiceNumber'] }}
                    </p>

                    <p>Mode of Payment:</p>
                    <p class="font-bold">
                        @if ($finalData['mode_of_payment'] == 'bankTransfer')
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
                    @elseif ($finalData['registration_status'] == 'cancelled')
                        <p class="font-bold text-red-600">Cancelled</p>
                    @else
                        <p class="font-bold text-red-600">Dropped Out</p>
                    @endif

                    <p>Payment Status:</p>
                    <p class="font-bold">
                        @if ($finalData['payment_status'] == 'paid')
                            Paid
                        @elseif ($finalData['payment_status'] == 'free')
                            Free
                        @elseif ($finalData['payment_status'] == 'unpaid')
                            Unpaid
                        @else
                            Refunded
                        @endif
                    </p>

                    <p>Registration Method:</p>
                    <p class="font-bold">
                        @if ($finalData['registration_method'] == 'online')
                            Online
                        @elseif ($finalData['registration_method'] == 'imported')
                            Imported
                        @else
                            Onsite
                        @endif
                    </p>

                    <p>Registered date & time:</p>
                    <p class="font-bold">{{ $finalData['registered_date_time'] }}</p>

                    <p>Payment date & time:</p>
                    <p class="font-bold">{{ $finalData['paid_date_time'] }}</p>
                </div>


                <div class="bg-registrationCardBGColor py-3 px-4 gap-4 mt-3">
                    <div class="flex items-center gap-3">
                        <p class="font-bold">Remarks</p>
                        <button wire:click="openEditTransactionRemarksModal"
                            class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>
                    </div>
                    <p class="mt-2">
                        {{ $finalData['transaction_remarks'] ? $finalData['transaction_remarks'] : 'N/A' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 mt-5">
                    <a href="{{ route('admin.event.registrants.view.invoice', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $registrantId]) }}"
                        class="bg-orange-800 hover:bg-orange-900 text-white py-2 rounded-md text-lg text-center"
                        target="_blank">View Invoice</a>

                    @if (
                        $finalData['payment_status'] != 'paid' &&
                            $finalData['payment_status'] != 'refunded' &&
                            $finalData['paid_date_time'] == 'N/A')
                        <button wire:click="openMarkAsPaidModal"
                            class="bg-green-600 hover:bg-green-700 text-white py-2 rounded-md text-lg text-center">Mark
                            as
                            Paid</button>
                    @else
                        <button disabled
                            class="bg-gray-400 cursor-not-allowed text-white py-2 rounded-md text-lg text-center">Mark
                            as
                            Paid</button>
                    @endif

                    @if (
                        $finalData['payment_status'] != 'paid' &&
                            $finalData['payment_status'] != 'refunded' &&
                            $finalData['paid_date_time'] == 'N/A')
                        <button wire:click="sendEmailReminderConfirmation"
                            class="col-span-1 bg-sky-600 hover:bg-sky-700 text-white py-2 rounded-md text-lg text-center">Send
                            Payment Reminder</button>
                    @else
                        <button disabled
                            class="col-span-1 bg-gray-400 cursor-not-allowed text-white py-2 rounded-md text-lg text-center">Send
                            Payment Reminder</button>
                    @endif

                    @if ($finalData['registration_status'] == 'confirmed' || $finalData['registration_status'] == 'pending')
                        <button wire:click="sendEmailRegistrationConfirmationConfirmation"
                            class="col-span-1 bg-yellow-600 hover:bg-yellow-700 text-white py-2 rounded-md text-lg text-center">Send
                            Registration Confirmation to All</button>
                    @endif
                </div>
            </div>

            <div class="col-span-7">
                <p
                    class="bg-registrationCardBGColor text-xl text-center text-registrationPrimaryColor font-bold italic py-4">
                    Delegate(s)</p>
                <div class="grid grid-cols-1 gap-10 mt-1">
                    @foreach ($finalData['allDelegates'] as $index => $delegates)
                        <div class="bg-registrationInputFieldsBGColor">
                            @foreach ($delegates as $innerIndex => $innerDelegate)
                                <div class="bg-white py-3 px-4 m-2">
                                    <div class="flex items-center gap-4">
                                        <span
                                            class="col-span-2 text-registrationPrimaryColor py-1 rounded-full border border-registrationPrimaryColor px-4 font-bold text-sm">
                                            @if ($innerDelegate['is_replacement'])
                                                Replacement {{ $innerIndex }}
                                            @else
                                                Delegate {{ $index + 1 }}
                                            @endif
                                        </span>
                                        <button
                                            wire:click="openEditDelegateModal({{ $index }}, {{ $innerIndex }})"
                                            class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-2 mt-3 gap-2 px-6">
                                        <p>Transaction Id:</p>
                                        <p class="font-bold">{{ $innerDelegate['transactionId'] }}</p>

                                        <p>Full Name:</p>
                                        <p class="font-bold">{{ $innerDelegate['name'] }}</p>

                                        <p>Email Address:</p>
                                        <p class="font-bold">{{ $innerDelegate['email_address'] }}</p>

                                        <p>Mobile Number:</p>
                                        <p class="font-bold">{{ $innerDelegate['mobile_number'] }}</p>

                                        <p>Country:</p>
                                        <p class="font-bold">
                                            @if ($innerDelegate['country'] == null || $innerDelegate['country'] == '')
                                                N/A
                                            @else
                                                {{ $innerDelegate['country'] }}
                                            @endif
                                        </p>

                                        <p>Nationality:</p>
                                        <p class="font-bold">{{ $innerDelegate['nationality'] }}</p>

                                        <p>Job Title:</p>
                                        <p class="font-bold">{{ $innerDelegate['job_title'] }}</p>

                                        <p>Badge Type:</p>
                                        <p class="font-bold">{{ $innerDelegate['badge_type'] }}</p>

                                        <p>Promo Code used:</p>
                                        @if ($innerDelegate['pcode_used'] == null)
                                            <p class="font-bold">N/A</p>
                                        @else
                                            @if ($innerDelegate['discount_type'] == 'percentage')
                                                <p class="font-bold">{{ $innerDelegate['pcode_used'] }}
                                                    <span
                                                        class="text-green-500 text-sm italic ml-2">{{ $innerDelegate['discount'] }}%
                                                        discount
                                                    </span>
                                                </p>
                                            @elseif ($innerDelegate['discount_type'] == 'price')
                                                <p class="font-bold">{{ $innerDelegate['pcode_used'] }}
                                                    <span
                                                        class="text-green-500 text-sm italic ml-2">${{ $innerDelegate['discount'] }}
                                                        discount
                                                    </span>
                                                </p>
                                            @else
                                                <p class="font-bold">{{ $innerDelegate['pcode_used'] }}
                                                    <span class="text-green-500 text-sm italic ml-2">Fixed rate
                                                        applied</span>
                                                </p>
                                            @endif
                                        @endif

                                        @if ($event->category == 'RCW')
                                            <p>Interests:</p>
                                            <p>
                                                @if (count($innerDelegate['interests']) > 0)
                                                    @foreach ($innerDelegate['interests'] as $innerDelegateInterestIndex => $innerDelegateInterest)
                                                        @if ($innerDelegateInterestIndex == count($innerDelegate['interests']) - 1)
                                                            {{ $innerDelegateInterest }}
                                                        @else
                                                            {{ $innerDelegateInterest }},
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </p>
                                        @endif

                                        <p>Registration confirmation count sent:</p>
                                        <p class="font-bold">
                                            {{ $innerDelegate['registration_confirmation_sent_count'] }}</p>

                                        <p>Last registration confirmation sent:</p>
                                        <p class="font-bold">
                                            {{ $innerDelegate['registration_confirmation_sent_datetime'] }}</p>

                                        @if ($innerDelegate['delegate_cancelled'])
                                            <p>Status: </p>

                                            @if ($innerDelegate['delegate_replaced'])
                                                <p class="font-bold">Cancelled & Replaced</p>
                                            @elseif($innerDelegate['delegate_refunded'])
                                                <p class="font-bold">Cancelled & Refunded</p>
                                            @else
                                                <p class="font-bold">Cancelled but not refunded</p>
                                            @endif

                                            <p>Cancelled date & time: </p>
                                            <p class="font-bold">{{ $innerDelegate['delegate_cancelled_datetime'] }}
                                            </p>
                                        @else
                                            <div class="flex gap-5 col-span-2">
                                                <button
                                                    wire:click="sendEmailRegistrationConfirmationConfirmationSingle({{ $index }}, {{ $innerIndex }})"
                                                    class="{{ $innerDelegate['registration_confirmation_sent_count'] > 0 ? 'bg-gray-400' : 'bg-yellow-600 hover:bg-yellow-700' }} text-white py-1 rounded-md text-center px-14 mt-2">Send
                                                    registration confirmation</button>

                                                <button
                                                    wire:click="openDelegateCancellationModal({{ $index }}, {{ $innerIndex }})"
                                                    class="bg-red-600 hover:bg-red-700 text-white py-1 rounded-md text-center px-14 mt-2">Mark
                                                    as cancelled</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>


                @if ($finalData['finalQuantity'] > 0)

                    {{-- Invoice details --}}
                    <div class="bg-registrationInputFieldsBGColor p-2 mt-5">
                        <div
                            class="grid grid-cols-6 text-center font-bold text-registrationPrimaryColor text-lg pt-2 pb-4">
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
                                        <p>{{ $finalData['invoiceData']['invoiceDescription'] }}</p>
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
                                    <p>$ {{ number_format($delegatInvoiceDetail['totalUnitPrice'], 2, '.', ',') }}</p>
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
                @endif
            </div>
        </div>

        @if ($showDelegateModal)
            @include('livewire.admin.events.transactions.edit-forms.edit_delegate_form')
        @endif

        @if ($showCompanyModal)
            @include('livewire.admin.events.transactions.edit-forms.edit_company_form')
        @endif

        @if ($showRegistrationDetailsModal)
            @include('livewire.admin.events.transactions.edit-forms.edit_reg_details_form')
        @endif

        @if ($showTransactionRemarksModal)
            @include('livewire.admin.events.transactions.edit-forms.edit_remarks')
        @endif

        @if ($showDelegateCancellationModal)
            @include('livewire.admin.events.transactions.delegate-cancellation.delegate_cancellation_modal')
        @endif

        @if ($showMarkAsPaidModal)
            @include('livewire.admin.events.transactions.mark-paid.mark_as_paid_form')
        @endif
    </div>
</div>
