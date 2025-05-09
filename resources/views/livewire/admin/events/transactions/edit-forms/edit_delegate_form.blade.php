<div class="fixed z-10 inset-0 overflow-y-auto">
    <form>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div>
                        <div class="text-registrationPrimaryColor italic font-bold text-xl">
                            Edit Delegate
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
                            {{-- ROW 1 --}}
                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Salutation
                                </div>
                                <div>
                                    <select wire:model.lazy="salutation"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        @foreach ($salutations as $salutationOpT)
                                            <option value="{{ $salutationOpT }}"
                                                {{ $salutation == $salutationOpT ? 'selected' : '' }}>
                                                {{ $salutationOpT }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    First Name <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <input placeholder="First Name" type="text" wire:model.lazy="firstName"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @error('firstName')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ROW 2 --}}
                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Middle Name
                                </div>
                                <div>
                                    <input placeholder="Middle Name" type="text" wire:model.lazy="middleName"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Last Name <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <input placeholder="Last Name" type="text" wire:model.lazy="lastName"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @error('lastName')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- ROW 3 --}}
                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Email Address <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <input placeholder="Email Address" type="text" wire:model.lazy="emailAddress"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @error('emailAddress')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Mobile Number <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <input placeholder="xxxxxxx" type="text" wire:model.lazy="mobileNumber"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @error('mobileNumber')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="space-y-2 col-span-2">
                                <div class="text-registrationPrimaryColor">
                                    Country <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model.lazy="country"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        @foreach ($countries as $countryChoice)
                                            <option value="{{ $countryChoice }}">
                                                {{ $countryChoice }}</option>
                                        @endforeach
                                    </select>

                                    @error('country')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ROW 4 --}}
                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Nationality <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <input placeholder="Nationality" type="text" wire:model.lazy="nationality"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @error('nationality')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Job Title <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <input placeholder="Job Title" type="text" wire:model.lazy="jobTitle"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @error('jobTitle')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- ROW 6 --}}
                            <div class="space-y-2 col-span-1">
                                <div class="text-registrationPrimaryColor">
                                    Registration type <span class="text-red-500">*</span>
                                </div>

                                @if ($promoCodeSuccess != null)
                                    <input readonly wire:model.lazy="badgeType" type="text"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none cursor-not-allowed">
                                @else
                                    <select wire:model.lazy="badgeType"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        @foreach ($registrationTypes as $registrationType)
                                            <option value="{{ $registrationType->registration_type }}"
                                                {{ $registrationType->registration_type == $badgeType ? 'selected' : '' }}>
                                                {{ $registrationType->registration_type }}</option>
                                        @endforeach
                                    </select>
                                @endif

                                @error('badgeType')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="space-y-2 col-span-1">
                                <div class="text-registrationPrimaryColor">
                                    Promo Code
                                </div>

                                <div class="flex">
                                    @if ($promoCodeSuccess != null)
                                        <input readonly type="text" wire:model.lazy="promoCode"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none cursor-not-allowed">

                                        <button wire:click.prevent="removePromoCode" wire:key="btnRemovePromoCodeEdit"
                                            type="button" class="bg-red-300 px-5 ml-2">Remove</button>
                                    @else
                                        <input placeholder="Enter your promo code here" type="text"
                                            wire:model.lazy="promoCode"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                                        <button wire:click.prevent="applyPromoCode" wire:key="btnApplyPromoCodeEdit"
                                            type="button"
                                            class="bg-registrationPrimaryColor text-white px-5 ml-2 hover:bg-registrationPrimaryColorHover">Apply</button>
                                    @endif
                                </div>

                                @error('promoCode')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @if ($promoCode != null)
                                    @if ($promoCodeFail != null)
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $promoCodeFail }}
                                        </div>
                                    @endif

                                    @if ($discountType == 'fixed')
                                        <div class="text-green-500 text-xs italic mt-1">
                                            Fixed rate applied
                                        </div>
                                    @else
                                        @if ($promoCodeDiscount != null)
                                            <div class="text-green-500 text-xs italic mt-1">
                                                {{ $promoCodeDiscount }}% discount
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>

                            @if ($event->category == 'RCW')
                                <div class="space-y-2 col-span-2">
                                    <div class="text-registrationPrimaryColor">
                                        Which RC code are you interested in?
                                    </div>
                                    <div>
                                        @foreach (['Community Awareness and Emergency Management', 'Distribution', 'Product Stewardship', 'Process Safety', 'Health & Safety', 'Security', 'Environmental Protection'] as $interestIndex => $interest)
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox" wire:model.lazy="interests"
                                                    value="{{ $interest }}" id="interest-{{ $interestIndex }}">
                                                <label
                                                    for="interest-{{ $interestIndex }}">{{ $interest }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:key="btnUpdateDelegate"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="updateDelegate">Update</button>
                    <button type="button" wire:key="btnCancelEditDelegate"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="closeEditDelegateModal">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
