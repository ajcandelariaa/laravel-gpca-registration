<div class="package-summary-col bg-registrationInputFieldsBGColor p-2">
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

    @foreach ($visitorInvoiceDetails as $visitorInvoiceDetail)
        <div class="grid grid-cols-6 gap-2">
            <div class="col-span-2 bg-white p-4">
                @if ($count == 1)
                    <p>{{ $event->name }} – {{ $eventFormattedDate }} at
                        {{ $event->location }}</p>
                    <p class="mt-10">{{ $visitorInvoiceDetail['visitorDescription'] }}</p>
                @else
                    <p>{{ $visitorInvoiceDetail['visitorDescription'] }}</p>
                @endif
                <ul class="mt-2 list-decimal ml-4">
                    @foreach ($visitorInvoiceDetail['visitorNames'] as $name)
                        <li>{{ $name }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="col-span-1 bg-white p-4 flex justify-center items-center">
                <p>{{ $visitorInvoiceDetail['quantity'] }}</p>
            </div>

            <div class="col-span-1 bg-white p-4 flex justify-center items-center">
                <p>$ {{ number_format($visitorInvoiceDetail['totalUnitPrice'], 2, '.', ',') }}</p>
            </div>

            <div class="col-span-1 bg-white p-4 flex justify-center items-center">
                <p>$ {{ number_format($visitorInvoiceDetail['totalDiscount'], 2, '.', ',') }}</p>
            </div>

            <div class="col-span-1 bg-white p-4 flex justify-center items-center">
                <p>$ {{ number_format($visitorInvoiceDetail['totalNetAmount'], 2, '.', ',') }}</p>
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