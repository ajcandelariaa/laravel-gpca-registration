@if (
    $event->co_eb_full_member_rate != null ||
        $event->co_eb_member_rate != null ||
        $event->co_eb_nmember_rate != null ||
        $event->co_std_full_member_rate != null ||
        $event->co_std_member_rate != null ||
        $event->co_std_nmember_rate != null ||
        $event->wo_eb_full_member_rate != null ||
        $event->wo_eb_member_rate != null ||
        $event->wo_eb_nmember_rate != null ||
        $event->wo_std_full_member_rate != null ||
        $event->wo_std_member_rate != null ||
        $event->wo_std_nmember_rate != null)
    <div class="mb-4">
        <div class="text-2xl text-registrationPrimaryColor font-semibold">Full Event Access Pass</div>

        @if ($event->category == 'ANC' && $event->year == '2024')
            <p class="text-lg font-semibold mt-2">Delegate pass includes:</p>
            <div class="grid grid-cols-2 gap-5 mt-2">
                <ul class="list-disc col-span-1 ml-5">
                    <li>Access to Operational Excellence Workshops on 10 September</li>
                    <li>A site visit to Estidamah Center on 10 September</li>
                    <li>Access to conference sessions from 11-12 September</li>
                    <li>Access to exhibition halls</li>
                </ul>
                <ul class="list-disc col-span-1 ml-5">
                    <li>Access to Luncheons and Networking breaks from 10-12 September</li>
                    <li>Access to the Gala dinner on 11 September at the Maiz Restaurant, Bujairi Terrace, Diriyah</li>
                    <li>Delegate bag and Stationery</li>
                    <li>Access to event networking app</li>
                </ul>
            </div>
        @endif

        @if ($event->category == 'PSC' && $event->year == '2024')
            <p class="text-lg font-semibold mt-2">Delegate pass includes:</p>
            <div class="grid grid-cols-2 gap-5 mt-2">
                <ul class="list-disc col-span-1 ml-5">
                    <li>3 full-day workshops on 7<sup>th</sup> October</li>
                    <li>Conference sessions on 8<sup>th</sup>-10<sup>th</sup> October</li>
                    <li>Workshop pass features</li>
                    <li>Exhibition halls</li>
                </ul>
                <ul class="list-disc col-span-1 ml-5">
                    <li>Networking breaks</li>
                    <li>Networking reception on 7<sup>th</sup> October</li>
                    <li>Gala dinner on 8<sup>th</sup> October</li>
                </ul>
            </div>
        @endif

        @if ($event->category == 'SCC' && $event->year == '2025')
            <p class="text-lg font-semibold mt-2">Delegate pass includes:</p>
            <div class="grid grid-cols-2 gap-5 mt-2">
                <ul class="list-disc col-span-1 ml-5">
                    <li>Access to Gulf SQAS Workshop on 26 May</li>
                    <li>Access to conference sessions on 27-28 May</li>
                    <li>Access to exhibition halls</li>
                </ul>

                <ul class="list-disc col-span-1 ml-5">
                    <li>Networking breaks from 26-28 May</li>
                    <li>Gala dinner on 27 May</li>
                    <li>Supply Chain Excellence Awards on 27 May</li>
                </ul>
            </div>
        @endif
    </div>
@endif
<table class="w-full bg-registrationPrimaryColor text-white text-center" cellspacing="1" cellpadding="2">
    <thead>
        <tr>
            <td class="py-2 font-bold text-lg">Pass category</td>
            @if ($finalEbEndDate != null)
                <td class="py-2 font-bold text-lg">
                    <span>Early bird rate <br> <span class="font-normal text-base">(valid until
                            {{ $finalEbEndDate }})</span></span>
                </td>
            @endif
            <td class="py-2 font-bold text-lg">
                @if ($finalEbEndDate != null)
                    <span>Standard rate <br> <span class="font-normal text-base">(starting
                            {{ $finalStdStartDate }})</span></span>
                @else
                    <span>Standard rate</span>
                @endif
            </td>
        </tr>
    </thead>
    <tbody>
        @if ($event->eb_full_member_rate != null || $event->std_full_member_rate != null)
            <tr>
                <td class="text-black">
                    <div class="bg-white py-2 font-bold ml-1">
                        Full member
                    </div>
                </td>
                @if ($finalEbEndDate != null)
                    <td class="text-black">
                        <div class="bg-white py-2">
                            $ {{ number_format($event->eb_full_member_rate, 2, '.', ',') }}
                            {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                        </div>
                    </td>
                @endif
                <td class="text-black">
                    <div class="bg-white py-2 mr-1">
                        $ {{ number_format($event->std_full_member_rate, 2, '.', ',') }}
                        {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                    </div>
                </td>
            </tr>
        @endif
        <tr>
            <td class="text-black">
                <div class="bg-white py-2 font-bold ml-1">
                    Member
                </div>
            </td>
            @if ($finalEbEndDate != null)
                <td class="text-black">
                    <div class="bg-white py-2">
                        $ {{ number_format($event->eb_member_rate, 2, '.', ',') }}
                        {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                    </div>
                </td>
            @endif
            <td class="text-black">
                <div class="bg-white py-2 mr-1">
                    $ {{ number_format($event->std_member_rate, 2, '.', ',') }}
                    {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                </div>
            </td>
        </tr>
        <tr>
            <td class="text-black">
                <div class="bg-white py-2 font-bold mb-1 ml-1">
                    Non-member
                </div>
            </td>
            @if ($finalEbEndDate != null)
                <td class="text-black">
                    <div class="bg-white py-2 mb-1">
                        $ {{ number_format($event->eb_nmember_rate, 2, '.', ',') }}
                        {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                    </div>
                </td>
            @endif
            <td class="text-black">
                <div class="bg-white py-2 mb-1 mr-1">
                    $ {{ number_format($event->std_nmember_rate, 2, '.', ',') }}
                    {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                </div>
            </td>
        </tr>
    </tbody>
</table>
