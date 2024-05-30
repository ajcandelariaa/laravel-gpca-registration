<div class="text-center mt-10 text-3xl text-registrationPrimaryColor font-bold uppercase">
    Full Event Rates
</div>
<table class="bg-registrationPrimaryColor text-white text-center mt-4" cellspacing="1" cellpadding="2" width="100%">
    <thead>
        <tr>
            <td class="py-4 font-bold text-lg"><br>Pass Category<br></td>
            <td class="py-4 font-bold text-lg">
                @if ($finalEbEndDate != null)
                    <span>Early Bird Rate <br> <span class="font-normal text-base">(valid until
                            {{ $finalEbEndDate }})</span></span>
                @else
                    <br>Early Bird Rate <br>
                @endif
            </td>
            <td class="py-4 font-bold text-lg">
                @if ($finalStdStartDate != null)
                    <span>Standard Rate <br> <span class="font-normal text-base">(starting
                            {{ $finalStdStartDate }})</span></span>
                @else
                    <br> Standard Rate <br>
                @endif
            </td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-black">
                <div class="bg-white py-4 font-bold ml-1">
                    Full Member
                </div>
            </td>
            <td class="text-black">
                <div class="{{ $event->eb_full_member_rate ? 'bg-white' : 'bg-gray-400' }} py-4">
                    $ {{ $event->eb_full_member_rate ? $event->eb_full_member_rate : '0.00' }} +
                    {{ $event->event_vat }}%
                </div>
            </td>
            <td class="text-black">
                <div class="{{ $event->std_full_member_rate ? 'bg-white' : 'bg-gray-400' }} py-4 mr-1">
                    $ {{ $event->std_full_member_rate ? $event->std_full_member_rate : '0.00' }} +
                    {{ $event->event_vat }}%
                </div>
            </td>
        </tr>
        <tr>
            <td class="text-black">
                <div class="bg-white py-4 font-bold ml-1">
                    Member
                </div>
            </td>
            <td class="text-black">
                <div class="{{ $event->eb_member_rate ? 'bg-white' : 'bg-gray-400' }} py-4">
                    $ {{ $event->eb_member_rate ? $event->eb_member_rate : '0.00' }} +
                    {{ $event->event_vat }}%
                </div>
            </td>
            <td class="text-black">
                <div class="{{ $event->std_member_rate ? 'bg-white' : 'bg-gray-400' }} py-4 mr-1">
                    $ {{ $event->std_member_rate ? $event->std_member_rate : '0.00' }} +
                    {{ $event->event_vat }}%
                </div>
            </td>
        </tr>
        <tr>
            <td class="text-black">
                <div class="bg-white py-4 font-bold mb-1 ml-1">
                    Non-Member
                </div>
            </td>
            <td class="text-black">
                <div class="{{ $event->eb_nmember_rate ? 'bg-white' : 'bg-gray-400' }} py-4 mb-1">
                    $ {{ $event->eb_nmember_rate ? $event->eb_nmember_rate : '0.00' }} +
                    {{ $event->event_vat }}%
                </div>
            </td>
            <td class="text-black">
                <div class="{{ $event->std_nmember_rate ? 'bg-white' : 'bg-gray-400' }} py-4 mb-1 mr-1">
                    $ {{ $event->std_nmember_rate ? $event->std_nmember_rate : '0.00' }} +
                    {{ $event->event_vat }}%
                </div>
            </td>
        </tr>
    </tbody>
</table>
