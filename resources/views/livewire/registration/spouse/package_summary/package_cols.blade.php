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

    <div class="grid grid-cols-6 gap-2 justify-end">
        <div class="col-span-2 bg-white p-4">
            <p>{{ $event->name }} – {{ $eventFormattedDate }}</p>

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

        <div class="col-span-1 bg-white p-4 flex justify-center items-end">
            <div class="mt-2">
                @if ($day_one)
                    <p>1</p>
                @endif

                @if ($day_two)
                    <p>1</p>
                @endif

                @if ($day_three)
                    <p>1</p>
                @endif

                @if ($day_four)
                    <p>1</p>
                @endif
            </div>
        </div>

        <div class="col-span-1 bg-white p-4 flex justify-center items-end">
            <div class="mt-2">
                @if ($day_one)
                    <p>$ 200.00</p>
                @endif

                @if ($day_two)
                    <p>$ 220.00</p>
                @endif

                @if ($day_three)
                    <p>$ 200.00</p>
                @endif

                @if ($day_four)
                    <p>$ 200.00</p>
                @endif
            </div>
        </div>

        <div class="col-span-1 bg-white p-4 flex justify-center items-end">
            <div class="mt-2">
                @if ($day_one)
                    <p>$ 0.00</p>
                @endif

                @if ($day_two)
                    <p>$ 0.00</p>
                @endif

                @if ($day_three)
                    <p>$ 0.00</p>
                @endif

                @if ($day_four)
                    <p>$ 0.00</p>
                @endif
            </div>
        </div>

        <div class="col-span-1 bg-white p-4 flex justify-center items-end">
            <div class="mt-2">
                @if ($day_one)
                    <p>$ 200.00</p>
                @endif

                @if ($day_two)
                    <p>$ 220.00</p>
                @endif

                @if ($day_three)
                    <p>$ 200.00</p>
                @endif

                @if ($day_four)
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
