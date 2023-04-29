<div class="mx-5">
    <div>
        <div class="text-registrationPrimaryColor font-bold text-2xl">
            Package Summary
        </div>

        <div class="italic mt-5">
            By registering your details, you understand that your personal data will be handled according to <a
                href="" class="text-registrationPrimaryColor underline ">GPCA Privacy Policy</a>
        </div>
    </div>

    <div class="mt-5">
        <div class="grid grid-cols-addDelegateGrid gap-y-2">
            <div class="text-registrationPrimaryColor">
                Invoice to be sent to:
            </div>

            <div>
                {{ $salutation . ' ' . $firstName . ' ' . $middleName . ' ' . $lastName }}
            </div>

            <div class="text-registrationPrimaryColor">
                Email Address:
            </div>

            <div>
                {{ $emailAddress }}
            </div>

            <div class="text-registrationPrimaryColor col-span-2">
                Payment method:
            </div>
        </div>

        <div class="mt-5 flex gap-5">
            <button wire:click.prevent="btClicked" type="button"
                class="{{ $paymentMethod == 'bankTransfer' ? 'bg-registrationSecondaryColor text-white' : 'hover:bg-registrationSecondaryColor hover:text-white border-registrationSecondaryColor border-2 bg-white text-registrationSecondaryColor' }} font-bold w-52 rounded-md py-5 ">
                <i class="fa-solid fa-building-columns mr-2"></i> Bank Transfer</button>

            @if ($finalTotal == 0)
                <button type="button"
                    class="border-gray-400 border-2 text-gray-400 bg-white font-bold w-52 rounded-md py-5 cursor-not-allowed"
                    disabled>
                    <i class="fa-solid fa-credit-card mr-2"></i> Credit Card</button>
            @else
                <button wire:click.prevent="ccClicked" type="button"
                    class="{{ $paymentMethod == 'creditCard' ? 'bg-registrationSecondaryColor text-white' : 'hover:bg-registrationSecondaryColor hover:text-white border-registrationSecondaryColor border-2 bg-white text-registrationSecondaryColor' }} font-bold w-52 rounded-md py-5 ">
                    <i class="fa-solid fa-credit-card mr-2"></i> Credit Card</button>
            @endif
        </div>

        @if ($paymentMethodError != null)
            <div class="text-red-500 text-xs italic mt-2">
                {{ $paymentMethodError }}
            </div>
        @endif
    </div>

    <div class="mt-5">
        <div class="bg-registrationInputFieldsBGColor p-2">
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

            @foreach ($delegatInvoiceDetails as $delegatInvoiceDetail)
                <div class="grid grid-cols-6 gap-2">
                    <div class="col-span-2 bg-white p-4">
                        @if ($count == 1)
                            <p>{{ $event->name }} – {{ $finalEventStartDate . ' - ' . $finalEventEndDate }} at
                                {{ $event->location }}</p>
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
                        <p>$ {{ number_format($finalUnitPrice, 2, '.', ',') }}</p>
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
                    <p>$ {{ number_format($finalNetAmount, 2, '.', ',') }}</p>
                </div>

                <div class="col-span-4 bg-white p-4">
                    <p>VAT {{ $event->event_vat }}%</p>
                </div>

                <div class="col-span-1 bg-white p-4 text-right">
                    <p>$ {{ number_format($finalVat, 2, '.', ',') }}</p>
                </div>

                <div class="col-span-4 bg-white p-4 font-bold">
                    <p>TOTAL</p>
                </div>

                <div class="col-span-1 bg-white p-4 text-right font-bold">
                    <p>$ {{ number_format($finalTotal, 2, '.', ',') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
