<div>
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>

    <div class="container mx-auto my-10">
        <a href="{{ route('admin.event.registrants.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
            class="bg-red-500 hover:bg-red-400 text-white font-medium py-2 px-5 rounded inline-flex items-center text-sm">
            <span class="mr-2"><i class="fa-sharp fa-solid fa-arrow-left"></i></span>
            <span>List of Transactions</span>
        </a>

        <div class="grid grid-cols-12 gap-20 mt-10">
            <div class="col-span-5">

                <div class="mt-3 bg-registrationInputFieldsBGColor py-1 px-1">
                    <div class="flex items-center gap-3">
                        <p class="text-xl text-registrationPrimaryColor font-bold italic py-4 px-3">Additional Details
                        </p>
                        <button wire:click="openEditAdditionalDetailsModal"
                            class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>
                    </div>

                    <div class="grid grid-cols-2 bg-white py-3 px-4 gap-4">
                        <p>Days will attend:</p>
                        <ul class="font-bold">
                            @if ($finalData['invoiceData']['day_one'])
                                <li>December 4, 2023 (Monday)</li>
                            @endif

                            @if ($finalData['invoiceData']['day_two'])
                                <li>December 5, 2023 (Tuesday)</li>
                            @endif

                            @if ($finalData['invoiceData']['day_three'])
                                <li>December 6, 2023 (Wednesday)</li>
                            @endif

                            @if ($finalData['invoiceData']['day_four'])
                                <li>December 7, 2023 (Thursday)</li>
                            @endif
                        </ul>

                        <p>Full name of Annual GPCA Forum registered attendee?</p>
                        <p class="font-bold">{{ $finalData['reference_delegate_name'] }}</p>

                        <p>Where did you hear about us?</p>
                        <p class="font-bold">
                            @if ($finalData['heard_where'] != null)
                                {{ $finalData['heard_where'] }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>

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

                    <p>Last registration confirmation sent:</p>
                    <p class="font-bold">{{ $finalData['registration_confirmation_sent_datetime'] }}</p>
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
                            class="col-span-1 {{ $finalData['registration_confirmation_sent_count'] > 0 ? 'bg-gray-400' : 'bg-yellow-600 hover:bg-yellow-700' }}  text-white py-2 rounded-md text-lg text-center">Send
                            Registration Confirmation</button>
                    @endif
                </div>
            </div>

            <div class="col-span-7">
                <p
                    class="bg-registrationCardBGColor text-xl text-center text-registrationPrimaryColor font-bold italic py-4">
                    Spouse(s)</p>
                <div class="grid grid-cols-1 gap-10 mt-1">
                    @foreach ($finalData['allSpouses'] as $index => $spouses)
                        <div class="bg-registrationInputFieldsBGColor">
                            @foreach ($spouses as $innerIndex => $innerSpouse)
                                <div class="bg-white py-3 px-4 m-2">
                                    <div class="flex items-center gap-4">
                                        <span
                                            class="col-span-2 text-registrationPrimaryColor py-1 rounded-full border border-registrationPrimaryColor px-4 font-bold text-sm">
                                            @if ($innerSpouse['is_replacement'])
                                                Replacement {{ $innerIndex }}
                                            @else
                                                Spouse {{ $index + 1 }}
                                            @endif
                                        </span>
                                        <button
                                            wire:click="openEditSpouseModal({{ $index }}, {{ $innerIndex }})"
                                            class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-2 mt-3 gap-2 px-6">
                                        <p>Transaction Id:</p>
                                        <p class="font-bold">{{ $innerSpouse['transactionId'] }}</p>

                                        <p>Full Name:</p>
                                        <p class="font-bold">{{ $innerSpouse['name'] }}</p>

                                        <p>Email Address:</p>
                                        <p class="font-bold">{{ $innerSpouse['email_address'] }}</p>

                                        <p>Mobile Number:</p>
                                        <p class="font-bold">{{ $innerSpouse['mobile_number'] }}</p>

                                        <p>Nationality:</p>
                                        <p class="font-bold">{{ $innerSpouse['nationality'] }}</p>

                                        <p>Country:</p>
                                        <p class="font-bold">{{ $innerSpouse['country'] }}</p>

                                        <p>City:</p>
                                        <p class="font-bold">{{ $innerSpouse['city'] }}</p>

                                        @if ($innerSpouse['spouse_cancelled'])
                                            <p>Status: </p>

                                            @if ($innerSpouse['spouse_replaced'])
                                                <p class="font-bold">Cancelled & Replaced</p>
                                            @elseif($innerSpouse['spouse_refunded'])
                                                <p class="font-bold">Cancelled & Refunded</p>
                                            @else
                                                <p class="font-bold">Cancelled but not refunded</p>
                                            @endif

                                            <p>Cancelled date & time: </p>
                                            <p class="font-bold">{{ $innerSpouse['spouse_cancelled_datetime'] }}
                                            </p>
                                        @else
                                            <button
                                                wire:click="openSpouseCancellationModal({{ $index }}, {{ $innerIndex }})"
                                                class="bg-yellow-600 hover:bg-yellow-700 text-white py-1 rounded-md text-center w-40 mt-2">Mark
                                                as cancelled</button>
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

                        <div class="grid grid-cols-6 gap-x-2">
                            <div class="col-span-2 bg-white p-4">
                                <p>{{ $finalData['invoiceData']['eventName'] }} –
                                    {{ $finalData['invoiceData']['eventFormattedData'] }}</p>

                                <p class="mt-4">Spouse registration fee</p>

                                <ul class="mt-2 list-decimal ml-4">
                                    @if ($finalData['invoiceData']['day_one'])
                                        <li>December 4, 2023 (Monday)</li>
                                    @endif

                                    @if ($finalData['invoiceData']['day_two'])
                                        <li>December 5, 2023 (Tuesday)</li>
                                    @endif

                                    @if ($finalData['invoiceData']['day_three'])
                                        <li>December 6, 2023 (Wednesday)</li>
                                    @endif

                                    @if ($finalData['invoiceData']['day_four'])
                                        <li>December 7, 2023 (Thursday)</li>
                                    @endif
                                </ul>
                            </div>

                            <div class="col-span-1 bg-white p-4 flex justify-center items-end">
                                <div class="mt-2">
                                    @if ($finalData['invoiceData']['day_one'])
                                        <p>1</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_two'])
                                        <p>1</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_three'])
                                        <p>1</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_four'])
                                        <p>1</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-span-1 bg-white p-4 flex justify-center items-end">
                                <div class="mt-2">
                                    @if ($finalData['invoiceData']['day_one'])
                                        <p>$ 200.00</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_two'])
                                        <p>$ 220.00</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_three'])
                                        <p>$ 200.00</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_four'])
                                        <p>$ 200.00</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-span-1 bg-white p-4 flex justify-center items-end">
                                <div class="mt-2">
                                    @if ($finalData['invoiceData']['day_one'])
                                        <p>$ 0.00</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_two'])
                                        <p>$ 0.00</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_three'])
                                        <p>$ 0.00</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_four'])
                                        <p>$ 0.00</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-span-1 bg-white p-4 flex justify-center items-end">
                                <div class="mt-2">
                                    @if ($finalData['invoiceData']['day_one'])
                                        <p>$ 200.00</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_two'])
                                        <p>$ 220.00</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_three'])
                                        <p>$ 200.00</p>
                                    @endif

                                    @if ($finalData['invoiceData']['day_four'])
                                        <p>$ 200.00</p>
                                    @endif
                                </div>
                            </div>
                        </div>

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

        @if ($showSpouseModal)
            @include('livewire.admin.events.transactions.spouse.edit-forms.edit_spouse_form')
        @endif

        @if ($showAdditionalDetailsModal)
            @include('livewire.admin.events.transactions.spouse.edit-forms.edit_additional_details_form')
        @endif

        @if ($showTransactionRemarksModal)
            @include('livewire.admin.events.transactions.spouse.edit-forms.edit_remarks')
        @endif

        @if ($showSpouseCancellationModal)
            @include('livewire.admin.events.transactions.spouse.spouse-cancellation.spouse_cancellation_modal')
        @endif

        @if ($showMarkAsPaidModal)
            @include('livewire.admin.events.transactions.spouse.mark-paid.mark_as_paid_form')
        @endif
    </div>
</div>
