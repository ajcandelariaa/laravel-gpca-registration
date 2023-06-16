<div class="package-summary-row bg-registrationInputFieldsBGColor p-2">

    <div class="flex flex-col gap-5">
        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Description</p>
            <div class="bg-white p-4">
                @foreach ($spouseInvoiceDetails as $index => $spouseInvoiceDetail)
                    @if ($index == 0)
                        <p>{{ $event->name }} – {{ $eventFormattedDate }} at
                            {{ $event->location }}</p>
                        <p class="mt-10">{{ $spouseInvoiceDetail['spouseDescription'] }}</p>
                    @else
                        <p class="mt-5">{{ $spouseInvoiceDetail['spouseDescription'] }}</p>
                    @endif

                    <ul class="mt-2 list-decimal ml-4">
                        @foreach ($spouseInvoiceDetail['spouseNames'] as $name)
                            <li>{{ $name }}</li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Qty</p>
            <div class="flex flex-col gap-4">
                @foreach ($spouseInvoiceDetails as $spouseInvoiceDetail)
                    <div class="bg-white p-4">
                        <p>{{ $spouseInvoiceDetail['quantity'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Unit price</p>
            <div class="flex flex-col gap-4">
                @foreach ($spouseInvoiceDetails as $spouseInvoiceDetail)
                    <div class="bg-white p-4">
                        <p>$ {{ number_format($finalUnitPrice, 2, '.', ',') }}</p>
                    </div>
                @endforeach
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Discount</p>
            <div class="flex flex-col gap-4">
                @foreach ($spouseInvoiceDetails as $spouseInvoiceDetail)
                    <div class="bg-white p-4">
                        <p>$ {{ number_format($spouseInvoiceDetail['totalDiscount'], 2, '.', ',') }}</p>
                    </div>
                @endforeach
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Net amount</p>
            <div class="flex flex-col gap-4">
                @foreach ($spouseInvoiceDetails as $spouseInvoiceDetail)
                    <div class="bg-white p-4">
                        <p>$ {{ number_format($spouseInvoiceDetail['totalNetAmount'], 2, '.', ',') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-5">
        <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Total (before VAT)</p>
        <div class="col-span-1 bg-white p-4">
            <p>$ {{ number_format($finalNetAmount, 2, '.', ',') }}</p>
        </div>


        <p class="font-bold text-registrationPrimaryColor text-lg pb-2 mt-4">VAT {{ $event->event_vat }}%</p>
        <div class="col-span-1 bg-white p-4">
            <p>$ {{ number_format($finalVat, 2, '.', ',') }}</p>
        </div>

        <p class="font-bold text-registrationPrimaryColor text-lg pb-2 mt-4">TOTAL</p>
        <div class="col-span-1 bg-white p-4 font-bold">
            <p>$ {{ number_format($finalTotal, 2, '.', ',') }}</p>
        </div>
    </div>
</div>
