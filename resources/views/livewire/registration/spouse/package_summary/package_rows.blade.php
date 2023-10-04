<div class="package-summary-row bg-registrationInputFieldsBGColor p-2">

    <div class="flex flex-col gap-5">
        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Description</p>
            <div class="bg-white p-4">
                <p>{{ $event->name }} – {{ $eventFormattedDate }}</p>

                <p class="mt-4">Spouse registration fee</p>

                <ul class="mt-2 list-decimal ml-4">
                    @if ($day_one)
                        <li>December 4, 2023 (Monday)</li>
                    @endif

                    @if ($day_two)
                        <li>December 5, 2023 (Tuesday)</li>
                    @endif

                    @if ($day_three)
                        <li>December 6, 2023 (Wednesday)</li>
                    @endif

                    @if ($day_four)
                        <li>December 7, 2023 (Thursday)</li>
                    @endif
                </ul>
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Qty</p>
            <div class="flex flex-col gap-4">
                @if ($day_one)
                    <div class="bg-white p-4">1</div>
                @endif

                @if ($day_two)
                    <div class="bg-white p-4">1</div>
                @endif

                @if ($day_three)
                    <div class="bg-white p-4">1</div>
                @endif

                @if ($day_four)
                    <div class="bg-white p-4">1</div>
                @endif
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Unit price</p>
            <div class="flex flex-col gap-4">
                @if ($day_one)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif

                @if ($day_two)
                    <div class="bg-white p-4">$ 220.00</div>
                @endif

                @if ($day_three)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif

                @if ($day_four)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Discount</p>
            <div class="flex flex-col gap-4">
                @if ($day_one)
                    <div class="bg-white p-4">$ 0.00</div>
                @endif

                @if ($day_two)
                    <div class="bg-white p-4">$ 0.00</div>
                @endif

                @if ($day_three)
                    <div class="bg-white p-4">$ 0.00</div>
                @endif

                @if ($day_four)
                    <div class="bg-white p-4">$ 0.00</div>
                @endif
            </div>
        </div>


        <div>
            <p class="font-bold text-registrationPrimaryColor text-lg pb-2">Net amount</p>
            <div class="flex flex-col gap-4">
                @if ($day_one)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif

                @if ($day_two)
                    <div class="bg-white p-4">$ 220.00</div>
                @endif

                @if ($day_three)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif

                @if ($day_four)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif
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
