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
    <div class="flex gap-5 items-center mb-2">
        <div class="text-2xl text-registrationPrimaryColor font-semibold">Full event access</div>

        @if ($event->category == 'ANC' && $event->year == '2024')
            <a href="https://gpca.org.ae/conferences/anc/pass-inclusions-and-access/"
                class="bg-registrationPrimaryColorHover hover:bg-registrationPrimaryColor text-white font-bold py-1 px-5 rounded-lg"
                target="_blank">View pass access</a>
        @endif
    </div>
@endif
<table class="w-full bg-registrationPrimaryColor text-white text-center" cellspacing="1" cellpadding="2">
    <thead>
        <tr>
            <td class="py-4 font-bold text-lg">Pass category</td>
            @if ($finalEbEndDate != null)
                <td class="py-4 font-bold text-lg">
                    <span>Early bird rate <br> <span class="font-normal text-base">(valid until
                            {{ $finalEbEndDate }})</span></span>
                </td>
            @endif
            <td class="py-4 font-bold text-lg">
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
                    <div class="bg-white py-4 font-bold ml-1">
                        Full member
                    </div>
                </td>
                @if ($finalEbEndDate != null)
                    <td class="text-black">
                        <div class="bg-white py-4">
                            $ {{ number_format($event->eb_full_member_rate, 2, '.', ',') }}
                            {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                        </div>
                    </td>
                @endif
                <td class="text-black">
                    <div class="bg-white py-4 mr-1">
                        $ {{ number_format($event->std_full_member_rate, 2, '.', ',') }}
                        {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                    </div>
                </td>
            </tr>
        @endif
        <tr>
            <td class="text-black">
                <div class="bg-white py-4 font-bold ml-1">
                    Member
                </div>
            </td>
            @if ($finalEbEndDate != null)
                <td class="text-black">
                    <div class="bg-white py-4">
                        $ {{ number_format($event->eb_member_rate, 2, '.', ',') }}
                        {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                    </div>
                </td>
            @endif
            <td class="text-black">
                <div class="bg-white py-4 mr-1">
                    $ {{ number_format($event->std_member_rate, 2, '.', ',') }}
                    {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                </div>
            </td>
        </tr>
        <tr>
            <td class="text-black">
                <div class="bg-white py-4 font-bold mb-1 ml-1">
                    Non-member
                </div>
            </td>
            @if ($finalEbEndDate != null)
                <td class="text-black">
                    <div class="bg-white py-4 mb-1">
                        $ {{ number_format($event->eb_nmember_rate, 2, '.', ',') }}
                        {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                    </div>
                </td>
            @endif
            <td class="text-black">
                <div class="bg-white py-4 mb-1 mr-1">
                    $ {{ number_format($event->std_nmember_rate, 2, '.', ',') }}
                    {{ $event->event_vat == 0 ? '' : '+VAT (' . $event->event_vat . '%)' }}
                </div>
            </td>
        </tr>
    </tbody>
</table>
