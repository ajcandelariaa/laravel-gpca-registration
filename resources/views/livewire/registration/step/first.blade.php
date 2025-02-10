<div class="mx-5">
    @if ($event->category != 'GLF' && $event->category != 'DFCLW1')
        @include('livewire.registration.step.rates_table.fe_rates')

        @if (
            $event->co_eb_full_member_rate != null ||
                $event->co_eb_member_rate != null ||
                $event->co_eb_nmember_rate != null ||
                $event->co_std_full_member_rate != null ||
                $event->co_std_member_rate != null ||
                $event->co_std_nmember_rate != null)
            <div class="mt-8"></div>
            @include('livewire.registration.step.rates_table.co_rates')
        @endif

        @if (
            $event->wo_eb_full_member_rate != null ||
                $event->wo_eb_member_rate != null ||
                $event->wo_eb_nmember_rate != null ||
                $event->wo_std_full_member_rate != null ||
                $event->wo_std_member_rate != null ||
                $event->wo_std_nmember_rate != null)
            <div class="mt-8"></div>
            @include('livewire.registration.step.rates_table.wo_rates')
        @endif
    @endif


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
                {{-- @if ($event->category == 'AF' && $event->year == '2023')
                    <p class="mt-5">If you are interested with the GPCA spouse program, please click here to <a
                            href="https://www.gpcaforum.com/spouse-program/" target="_blank"
                            class="text-blue-600 hover:underline font-semibold">learn more</a> and <a
                            href="https://www.gpcaregistration.com/register/2023/AFS/11" target="_blank"
                            class="text-blue-600 hover:underline font-semibold">register.</a></p>
                @endif --}}

                <div class="mt-5">
                    <input type="checkbox" wire:model.lazy="termsCondition" id="terms-condition">
                    <label for="terms-condition">I agree to the <a href="https://www.gpca.org.ae/terms-and-conditions/"
                            target="_blank">Terms and Conditions</a> and <a href="https://www.gpca.org.ae/privacy-policy/"
                            target="_blank">Privacy Policy</a>.</label>
                </div>

                <p class="mt-5">For inquiries or to speak with a member of our team, please contact <strong>Faheem
                        Chowdhury</strong>, <em>Head of Events</em>, at <a
                        href="mailto:faheem@gpca.org.ae">faheem@gpca.org.ae</a> or call +971 4 451 0666 ext. 122.</p>
            </div>
        @endif


        <div
            class="col-span-2 {{ $delegateFees->isNotEmpty() ? 'lg:col-span-1' : 'lg:col-span-2' }} lg:col-span-1 flex flex-col gap-5">

            @if ($event->category != 'GLF' && $event->category != 'DFCLW1')
                <div class="bg-gray-200 py-4 px-2">
                    <h1 class="text-2xl text-registrationPrimaryColor font-bold text-center">DO YOU WISH TO BECOME A
                        MEMBER?
                    </h1>
                    <div class="bg-white mx-1 mt-5 p-5 space-y-5">
                        <p>Do you wish to become a member and avail preferred rates and other benefit?</p>
                        <p>If <strong>YES</strong>, please contact our sales team: members@gpca.org.ae</p>
                        <p>If <strong>NO</strong>, please proceed with the registration</p>
                    </div>
                </div>
            @endif
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
                        </div>

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
                            <div class="mt-5">
                                <div class="text-registrationPrimaryColor">
                                    Access type <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model.lazy="accessType"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value="fullEvent">Full event access</option>
                                        @if (
                                            $event->co_eb_full_member_rate != null ||
                                                $event->co_eb_member_rate != null ||
                                                $event->co_eb_nmember_rate != null ||
                                                $event->co_std_full_member_rate != null ||
                                                $event->co_std_member_rate != null ||
                                                $event->co_std_nmember_rate != null)
                                            <option value="conferenceOnly">Conference only</option>
                                        @endif
                                        @if (
                                            $event->wo_eb_full_member_rate != null ||
                                                $event->wo_eb_member_rate != null ||
                                                $event->wo_eb_nmember_rate != null ||
                                                $event->wo_std_full_member_rate != null ||
                                                $event->wo_std_member_rate != null ||
                                                $event->wo_std_nmember_rate != null)
                                            <option value="workshopOnly">Workshop only</option>
                                        @endif
                                    </select>


                                    @error('accessType')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if ($delegateFees->isEmpty())
        <div class="col-span-2 mt-5">
            <input type="checkbox" wire:model.lazy="termsCondition" id="terms-condition">
            <label for="terms-condition">I agree to the <a href="https://www.gpca.org.ae/terms-and-conditions/"
                    target="_blank">Terms and Conditions</a> and <a href="https://www.gpca.org.ae/privacy-policy/"
                    target="_blank">Privacy Policy</a>.</label>
        </div>

        <p class="col-span-2 mt-5">For inquiries or to speak with a member of our team, please contact <strong>Faheem
                Chowdhury</strong>, <em>Head of Events</em>, at <a
                href="mailto:faheem@gpca.org.ae">faheem@gpca.org.ae</a> or call +971 4 451 0666 ext. 122.</p>
    @endif
</div>
