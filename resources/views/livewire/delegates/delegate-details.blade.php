<div>
    <div>
        <img src="{{ Storage::url($eventBanner) }}" alt="" class="w-full object-cover">
    </div>
    <div class="container mx-auto my-10">
        <div class="grid grid-cols-12 gap-20 mt-10">
            <div class="col-span-12 grid grid-cols-2 items-start">
                <div>
                    <div class="flex items-center gap-5">
                        <div class="text-registrationPrimaryColor italic font-bold text-xl">
                            Personal Information
                        </div>
                        <div>
                            <button wire:click="openEditDelegateModal"
                                class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                        </div>
                    </div>
                    <div class="mt-10 grid grid-cols-delegateDetailGrid gap-y-3 gap-x-5 items-start">
                        <div class="text-registrationPrimaryColor">
                            Salutation
                        </div>
                        <div>
                            {{ $finalDelegate['salutation'] }}
                        </div>


                        <div class="text-registrationPrimaryColor">
                            First Name
                        </div>
                        <div>
                            {{ $finalDelegate['first_name'] }}
                        </div>


                        <div class="text-registrationPrimaryColor">
                            Middle Name
                        </div>
                        <div>
                            {{ $finalDelegate['middle_name'] }}
                        </div>


                        <div class="text-registrationPrimaryColor">
                            Last Name
                        </div>
                        <div>
                            {{ $finalDelegate['last_name'] }}
                        </div>


                        <div class="text-registrationPrimaryColor">
                            Email Address
                        </div>
                        <div>
                            {{ $finalDelegate['email_address'] }}
                        </div>

                        <div class="text-registrationPrimaryColor">
                            Mobile Number
                        </div>
                        <div>
                            {{ $finalDelegate['mobile_number'] }}
                        </div>



                        <div class="text-registrationPrimaryColor">
                            Nationality
                        </div>
                        <div>
                            {{ $finalDelegate['nationality'] }}
                        </div>

                        <div class="text-registrationPrimaryColor">
                            Job Title
                        </div>
                        <div>
                            {{ $finalDelegate['job_title'] }}
                        </div>



                        <div class="text-registrationPrimaryColor">
                            Registration Type
                        </div>
                        <div>
                            {{ $finalDelegate['badge_type'] }}
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-5">
                        <div class="text-registrationPrimaryColor italic font-bold text-xl">
                            Company Information
                        </div>
                        <div>
                            <button wire:click="openEditCompanyDetailsModal"
                                class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                        </div>
                    </div>
                    <div class="mt-10 grid grid-cols-delegateDetailGrid gap-y-3 gap-x-5 items-start">
                        <div class="text-registrationPrimaryColor">
                            Pass Type
                        </div>
                        <div>
                            @if ($finalDelegate['pass_type'] == 'member')
                                Member
                            @else
                                Non-Member
                            @endif
                        </div>


                        <div class="text-registrationPrimaryColor">
                            Company Name
                        </div>
                        <div>
                            {{ $finalDelegate['companyName'] }}
                        </div>


                        <div class="text-registrationPrimaryColor">
                            Company Name
                        </div>
                        <div>
                            {{ $finalDelegate['company_sector'] }}
                        </div>


                        <div class="text-registrationPrimaryColor">
                            Company Address
                        </div>
                        <div>
                            {{ $finalDelegate['company_address'] }}
                        </div>


                        <div class="text-registrationPrimaryColor">
                            Company Country
                        </div>
                        <div>
                            {{ $finalDelegate['company_country'] }}
                        </div>

                        <div class="text-registrationPrimaryColor">
                            Company City
                        </div>
                        <div>
                            {{ $finalDelegate['company_city'] }}
                        </div>



                        <div class="text-registrationPrimaryColor">
                            Company Landline Number
                        </div>
                        <div>
                            {{ $finalDelegate['company_telephone_number'] }}
                        </div>

                        <div class="text-registrationPrimaryColor">
                            Company Mobile Number
                        </div>
                        <div>
                            {{ $finalDelegate['company_mobile_number'] }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-12 mb-20">
                <div class="text-registrationPrimaryColor italic font-bold text-xl">
                    Delegate Badge
                </div>
                <div class="flex gap-10">
                    <div style="width: 321px; height: 492px;">
                        <div class="border border-black mt-10 flex flex-col justify-between h-full">
                            <div>
                                <img src="{{ Storage::url($event->badge_front_banner) }}">
                            </div>
                            <div>
                                <p class="text-center font-bold text-lg">{{ $finalDelegate['salutation'] }}
                                    {{ $finalDelegate['first_name'] }} {{ $finalDelegate['middle_name'] }}
                                    {{ $finalDelegate['last_name'] }}</p>
                                <p class="text-center italic mt-3">{{ $finalDelegate['job_title'] }}</p>
                                <p class="text-center font-bold">{{ $finalDelegate['companyName'] }}</p>
                            </div>
                            <div>
                                <p class="text-white bg-black text-center py-4 font-bold">
                                    {{ $finalDelegate['badge_type'] }}</p>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <p>Front</p>
                        </div>
                    </div>

                    <div style="width: 321px; height: 492px;">
                        <div class="border border-black mt-10 flex flex-col justify-between h-full">
                            <div>
                                <img src="{{ Storage::url($event->badge_back_banner) }}">
                            </div>
                            <div>
                                <p class="text-center font-bold text-lg">{{ $finalDelegate['salutation'] }}
                                    {{ $finalDelegate['first_name'] }} {{ $finalDelegate['middle_name'] }}
                                    {{ $finalDelegate['last_name'] }}</p>
                                <p class="text-center italic mt-3">{{ $finalDelegate['job_title'] }}</p>
                                <p class="text-center font-bold">{{ $finalDelegate['companyName'] }}</p>
                            </div>
                            <div>
                                <p class="text-center py-4 font-bold"
                                    style="color: {{ $event->badge_footer_link_color }}">
                                    {{ $event->badge_footer_link }}</p>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <p>Back</p>
                        </div>
                    </div>

                    <div>
                        <div class="mt-14 text-center">
                            <a href="{{ route('admin.event.delegates.detail.printBadge', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'delegateType' => $finalDelegate['delegateType'], 'delegateId' => $finalDelegate['delegateId']]) }}"
                                class="bg-registrationPrimaryColorHover hover:bg-registrationPrimaryColor text-white py-2 px-20 mx-auto rounded-md text-lg"
                                target="_blank">Print Badge</a>
                        </div>

                        <div class="mt-10 text-center">
                            Or Scan the below QR Code
                        </div>

                        <div class="flex mt-10 justify-center">
                            {!! QrCode::size(300)->generate(Request::url() . '/print-badge') !!}
                        </div>

                        {{-- <div class="flex mt-10 justify-center">
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->generate(Request::url().'/print-badge')) !!}" />
                        </div> --}}
                    </div>
                </div>



            </div>
        </div>
    </div>

    @if ($showDelegateModal)
        @include('livewire.delegates.edit_delegate_form')
    @endif

    @if ($showCompanyModal)
        @include('livewire.delegates.edit_company_form')
    @endif
</div>
