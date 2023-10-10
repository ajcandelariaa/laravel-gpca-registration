<div class="mx-5">
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
                                {{ $event->event_vat == 0 ? '' : '+ ' . $event->event_vat . '%' }}
                            </div>
                        </td>
                    @endif
                    <td class="text-black">
                        <div class="bg-white py-4 mr-1">
                            $ {{ number_format($event->std_full_member_rate, 2, '.', ',') }}
                            {{ $event->event_vat == 0 ? '' : '+ ' . $event->event_vat . '%' }}
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
                            {{ $event->event_vat == 0 ? '' : '+ ' . $event->event_vat . '%' }}
                        </div>
                    </td>
                @endif
                <td class="text-black">
                    <div class="bg-white py-4 mr-1">
                        $ {{ number_format($event->std_member_rate, 2, '.', ',') }}
                        {{ $event->event_vat == 0 ? '' : '+ ' . $event->event_vat . '%' }}
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
                            {{ $event->event_vat == 0 ? '' : '+ ' . $event->event_vat . '%' }}
                        </div>
                    </td>
                @endif
                <td class="text-black">
                    <div class="bg-white py-4 mb-1 mr-1">
                        $ {{ number_format($event->std_nmember_rate, 2, '.', ',') }}
                        {{ $event->event_vat == 0 ? '' : '+ ' . $event->event_vat . '%' }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="grid grid-cols-2 gap-5 mt-10">
        @if ($delegateFees->isNotEmpty())
            <div class="col-span-2 lg:col-span-1">
                <div class="bg-gray-200 py-4 px-2">
                    <h1 class="text-2xl text-registrationPrimaryColor font-bold text-center">DELEGATE FEE INCLUDES:</h1>
                    <div class="bg-white mx-1 mt-5 px-14 py-5">
                        <ul class="list-disc">
                            @foreach ($delegateFees as $delegateFee)
                                <li class="text-registrationPrimaryColor"><span
                                        class="text-black">{{ $delegateFee->description }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @if (($event->category == 'AF') && ($event->year == '2023'))
                    <p class="mt-5">If you are interested with the GPCA spouse program, please click here to <a
                            href="https://www.gpcaforum.com/spouse-program/" target="_blank"
                            class="text-blue-600 hover:underline font-semibold">learn more</a> and <a
                            href="https://www.gpcaregistration.com/register/2023/AFS/11" target="_blank"
                            class="text-blue-600 hover:underline font-semibold">register.</a></p>
                @endif
            </div>
        @endif

        <div
            class="col-span-2 {{ $delegateFees->isNotEmpty() ? 'lg:col-span-1' : 'lg:col-span-2' }} lg:col-span-1 flex flex-col gap-5">
            <div class="bg-gray-200 py-4 px-2">
                <h1 class="text-2xl text-registrationPrimaryColor font-bold text-center">DO YOU WISH TO BECOME A MEMBER?
                </h1>
                <div class="bg-white mx-1 mt-5 p-5 space-y-5">
                    <p>Do you wish to become a member and avail preferred rates and other benefit?</p>
                    <p>If <strong>YES</strong>, please contact our sales team: members@gpca.org.ae</p>
                    <p>If <strong>NO</strong>, please proceed with the registration</p>
                </div>
            </div>
            <div class="bg-gray-200 py-4 px-2">
                <h1 class="text-2xl text-registrationPrimaryColor font-bold text-center">DELEGATE PASS TYPE</h1>
                <div class="bg-white mx-1 mt-5 p-5">
                    <div class="flex flex-row justify-center items-center gap-5">
                        @if ($event->eb_full_member_rate != null || $event->std_full_member_rate != null)
                            <button wire:click.prevent="fullMemberClicked"
                                class="{{ $delegatePassType == 'fullMember' ? 'bg-registrationPrimaryColor text-white' : 'hover:bg-registrationPrimaryColor hover:text-white border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor' }} w-48 py-2 rounded-md">Full
                                member</button>
                        @endif
                        <button wire:click.prevent="memberClicked"
                            class="{{ $delegatePassType == 'member' ? 'bg-registrationPrimaryColor text-white' : 'hover:bg-registrationPrimaryColor hover:text-white border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor' }} w-48 py-2 rounded-md">Member</button>
                        <button wire:click.prevent="nonMemberClicked"
                            class="{{ $delegatePassType == 'nonMember' ? 'bg-registrationPrimaryColor text-white' : 'hover:bg-registrationPrimaryColor hover:text-white border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor' }} w-48 py-2 rounded-md">Non-member</button>
                    </div>

                    @if ($delegatePassTypeError != null)
                        <div class="text-red-500 text-sm italic mt-2 text-center">
                            {{ $delegatePassTypeError }}
                        </div>
                    @endif

                    @if ($delegatePassType != null)
                        <div class="mt-10">
                            <div class="text-registrationPrimaryColor">
                                Company name <span class="text-red-500">*</span>
                            </div>
                            <div>
                                @if ($event->eb_full_member_rate != null || $event->std_full_member_rate != null)
                                    @if ($delegatePassType == 'fullMember')
                                        <select wire:model.lazy="companyName"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                            <option value=""></option>
                                            @foreach ($members as $member)
                                                @if ($member->type == 'full')
                                                    <option value="{{ $member->name }}">
                                                        {{ $member->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    @elseif($delegatePassType == 'member')
                                        <select wire:model.lazy="companyName"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                            <option value=""></option>
                                            @foreach ($members as $member)
                                                @if ($member->type == 'associate')
                                                    <option value="{{ $member->name }}">
                                                        {{ $member->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    @else
                                        <input placeholder="Company Name" type="text" wire:model.lazy="companyName"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @endif
                                @else
                                    @if ($delegatePassType == 'member')
                                        <select wire:model.lazy="companyName"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                            <option value=""></option>
                                            @foreach ($members as $member)
                                                <option value="{{ $member->name }}">
                                                    {{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input placeholder="Company Name" type="text" wire:model.lazy="companyName"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @endif
                                @endif

                                @error('companyName')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
