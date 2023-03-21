<div class="mt-10">
    <div class="text-registrationPrimaryColor italic font-bold text-xl">
        Delegate details
    </div>

    <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
        {{-- ROW 1 --}}
        <div class="space-y-2 col-span-2">
            <div class="grid grid-cols-10 gap-x-5">
                <div class="col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Salutation
                    </div>
                    <div>
                        <select wire:model="salutation"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            <option value=""></option>
                            @foreach ($salutations as $salutation)
                                <option value="{{ $salutation }}">{{ $salutation }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-span-3">
                    <div class="text-registrationPrimaryColor">
                        First Name <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="First Name" type="text" wire:model="firstName"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('firstName')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-3">
                    <div class="text-registrationPrimaryColor">
                        Middle Name
                    </div>
                    <div>
                        <input placeholder="Middle Name" type="text" wire:model="middleName"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    </div>
                </div>

                <div class="col-span-3">
                    <div class="text-registrationPrimaryColor">
                        Last Name <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="Last Name" type="text" wire:model="lastName"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('lastName')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>


        {{-- ROW 2 --}}
        <div class="space-y-2 col-span-2">
            <div class="grid grid-cols-3 gap-x-5">
                <div class="col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Email Address <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="Email Address" type="text" wire:model="emailAddress"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('emailAddress')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Mobile Number <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="xxxxxxx" type="text" wire:model="mobileNumber"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('mobileNumber')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Nationality <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="Nationality" type="text" wire:model="nationality"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('nationality')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>


        {{-- ROW 3 --}}
        <div class="space-y-2 col-span-2">
            <div class="grid grid-cols-3 gap-x-5">

                <div class="col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Job Title <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="Job Title" type="text" wire:model="jobTitle"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('jobTitle')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Registration type <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <select wire:model="badgeType"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            <option value=""></option>
                            @foreach ($badgeTypes as $badgeType)
                                <option value="{{ $badgeType }}">
                                    {{ $badgeType }}</option>
                            @endforeach
                        </select>

                        @error('badgeType')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Promo Code
                    </div>
                    <div class="flex">

                        @if ($promoCodeSuccessMain != null)
                            <input readonly type="text" wire:model="promoCode"
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none cursor-not-allowed">

                            <button wire:click.prevent="removePromoCodeMain" wire:key="btnRemovePromoCodeMain" type="button"
                                class="bg-red-300 px-5 ml-2">Remove</button>
                        @else
                            <input placeholder="Enter your promo code here" type="text" wire:model="promoCode"
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                            <button wire:click.prevent="applyPromoCodeMain" wire:key="btnApplyPromoCodeMain"
                                type="button"
                                class="bg-registrationPrimaryColor text-white px-5 ml-2 hover:bg-registrationPrimaryColorHover">Apply</button>
                        @endif
                    </div>

                    @if ($promoCodeFailMain != null)
                        <div class="text-red-500 text-xs italic mt-1">
                            {{ $promoCodeFailMain }}
                        </div>
                    @endif

                    @if ($promoCodeSuccessMain != null)
                        <div class="text-green-500 text-xs italic mt-1">
                            {{ $promoCodeSuccessMain }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
