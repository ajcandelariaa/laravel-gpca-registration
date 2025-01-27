<div class="mx-5">
    <table class="w-full bg-registrationPrimaryColor text-white text-center" cellspacing="1" cellpadding="2">
        <thead>
            <tr>
                <td class="py-4 font-bold text-lg">Pass category</td>
                @if ($finalEbEndDate != null)
                    <td class="py-4 font-bold text-lg">
                        <span>Early Bird Rate <br> <span class="font-normal text-base">(valid until
                                {{ $finalEbEndDate }})</span></span>
                    </td>
                @endif
                <td class="py-4 font-bold text-lg">
                    <span>Standard rate</span>
                </td>
            </tr>
        </thead>
        <tbody>
            @if ($event->eb_full_member_rate != null || $event->std_full_member_rate != null)
                <tr>
                    <td class="text-black">
                        <div class="bg-white py-4 font-bold ml-1">
                            Full Member
                        </div>
                    </td>
                    @if ($finalEbEndDate != null)
                        <td class="text-black">
                            <div class="bg-white py-4">
                                $ {{ number_format($event->eb_full_member_rate, 2, '.', ',') }}
                                {{ $event->event_vat == 0 ? '' : '+ VAT (' . $event->event_vat . '%)' }}
                            </div>
                        </td>
                    @endif
                    <td class="text-black">
                        <div class="bg-white py-4 mr-1">
                            $ {{ number_format($event->std_full_member_rate, 2, '.', ',') }}
                            {{ $event->event_vat == 0 ? '' : '+ VAT (' . $event->event_vat . '%)' }}
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
                            {{ $event->event_vat == 0 ? '' : '+ VAT (' . $event->event_vat . '%)' }}
                        </div>
                    </td>
                @endif
                <td class="text-black">
                    <div class="bg-white py-4 mr-1">
                        $ {{ number_format($event->std_member_rate, 2, '.', ',') }}
                        {{ $event->event_vat == 0 ? '' : '+ VAT (' . $event->event_vat . '%)' }}
                    </div>
                </td>
            </tr>
            <tr>
                <td class="text-black w-1/2">
                    <div class="bg-white py-4 font-bold mb-1 ml-1 ">
                        Non-Member
                    </div>
                </td>
                @if ($finalEbEndDate != null)
                    <td class="text-black">
                        <div class="bg-white py-4 mb-1">
                            $ {{ number_format($event->eb_nmember_rate, 2, '.', ',') }}
                            {{ $event->event_vat == 0 ? '' : '+ VAT (' . $event->event_vat . '%)' }}
                        </div>
                    </td>
                @endif
                <td class="text-black">
                    <div class="bg-white py-4 mb-1 mr-1">
                        $ {{ number_format($event->std_nmember_rate, 2, '.', ',') }}
                        {{ $event->event_vat == 0 ? '' : '+ VAT (' . $event->event_vat . '%)' }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="grid grid-cols-2 gap-5 mt-10">

        <div class="col-span-2 lg:col-span-1">

            <div class="bg-gray-200 py-4 px-2">
                <h1 class="text-2xl text-registrationPrimaryColor font-bold text-center">DO YOU WISH TO BECOME A MEMBER?
                </h1>
                <div class="bg-white mx-1 mt-5 p-5 space-y-5">
                    <p>Do you wish to become a member and avail preferred rates and other benefit?</p>
                    <p>If <strong>YES</strong>, please contact our sales team: members@gpca.org.ae</p>
                    <p>If <strong>NO</strong>, please proceed with the registration</p>
                </div>
            </div>

            <p class="mt-5 italic font-semibold text-red-600">Note: Only Mastercard and VISA credit card payments are accepted.</p>
        </div>

        <div class="col-span-2 lg:col-span-1 flex flex-col gap-5">

            <div class="bg-gray-200 py-4 px-2">
                <h1 class="text-2xl text-registrationPrimaryColor font-bold text-center">SELECT CATEGORY:</h1>
                <div class="bg-white mx-1 mt-5 p-5">
                    <div class="text-registrationPrimaryColor">
                        Category <span class="text-red-500">*</span>
                    </div>
                    <div class="mt-2">
                        <select wire:model.lazy="category"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            <option value=""></option>
                            @foreach ($awardsCategories as $awardsCategory)
                                <option value="{{ $awardsCategory }}">
                                    {{ $awardsCategory }}</option>
                            @endforeach
                        </select>

                        @error('category')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bg-gray-200 py-4 px-2">
                <h1 class="text-2xl text-registrationPrimaryColor font-bold text-center">PARTICIPANT PASS TYPE</h1>
                <div class="bg-white mx-1 mt-5 p-5">
                    <div class="flex flex-row justify-center items-center gap-5">
                        @if ($event->eb_full_member_rate != null || $event->std_full_member_rate != null)
                            <button wire:click.prevent="fullMemberClicked"
                                class="{{ $participantPassType == 'fullMember' ? 'bg-registrationPrimaryColor text-white' : 'hover:bg-registrationPrimaryColor hover:text-white border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor' }} w-48 py-2 rounded-md">Full
                                Member</button>
                        @endif
                        <button wire:click.prevent="memberClicked"
                            class="{{ $participantPassType == 'member' ? 'bg-registrationPrimaryColor text-white' : 'hover:bg-registrationPrimaryColor hover:text-white border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor' }} w-48 py-2 rounded-md">Member</button>

                        <button wire:click.prevent="nonMemberClicked"
                            class="{{ $participantPassType == 'nonMember' ? 'bg-registrationPrimaryColor text-white' : 'hover:bg-registrationPrimaryColor hover:text-white border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor' }} w-48 py-2 rounded-md">Non-Member</button>
                    </div>

                    @if ($participantPassTypeError != null)
                        <div class="text-red-500 text-sm italic mt-2 text-center">
                            {{ $participantPassTypeError }}
                        </div>
                    @endif

                    @if ($participantPassType != null)
                        <div class="mt-5">
                            <div class="text-registrationPrimaryColor">
                                Company name <span class="text-red-500">*</span>
                            </div>
                            <div>
                                @if ($event->eb_full_member_rate != null || $event->std_full_member_rate != null)
                                    @if ($participantPassType == 'fullMember')
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
                                    @elseif($participantPassType == 'member')
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
                                    @if ($participantPassType == 'member')
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
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
