<div class="package-summary-row bg-registrationInputFieldsBGColor p-2">

    <div class="flex flex-col gap-5">
        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Description</p>
            <div class="bg-white p-4">
                <p>{{ $event->name }} – {{ $eventFormattedStartDate }} at {{ $event->location }}</p>
                <p class="mt-5">Awards Submission fee</p>

                <ul class="mt-2 list-decimal ml-4">
                    <li>Category: {{ $category }}</li>
                </ul>
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Qty</p>
            <div class="flex flex-col gap-4">
                <div class="bg-white p-4">
                    <p>{{ $finalQuantity }}</p>
                </div>
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Unit price</p>
            <div class="flex flex-col gap-4">
                <div class="bg-white p-4">
                    <p>$ {{ number_format($finalUnitPrice, 2, '.', ',') }}</p>
                </div>
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Discount</p>
            <div class="flex flex-col gap-4">
                <div class="bg-white p-4">
                    <p>$ {{ number_format($finalDiscount, 2, '.', ',') }}</p>
                </div>
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Net amount</p>
            <div class="flex flex-col gap-4">
                <div class="bg-white p-4">
                    <p>$ {{ number_format($finalNetAmount, 2, '.', ',') }}</p>
                </div>
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
