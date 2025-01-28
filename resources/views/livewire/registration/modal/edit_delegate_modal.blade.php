<div class="fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                {{-- SUB DELEGATE --}}
                <div>
                    <div class="text-registrationPrimaryColor italic font-bold text-xl">
                        Edit delegate
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
                        {{-- ROW 1 --}}
                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Salutation
                            </div>
                            <div>
                                <select wire:model.lazy="subSalutationEdit"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    <option value=""></option>
                                    @foreach ($salutations as $salutation)
                                        <option value="{{ $salutation }}"
                                            {{ $subSalutationEdit == $salutation ? 'selected' : '' }}>
                                            {{ $salutation }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                First name <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="First name" type="text" wire:model.lazy="subFirstNameEdit"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subFirstNameEdit')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- ROW 2 --}}
                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Middle name
                            </div>
                            <div>
                                <input placeholder="Middle name" type="text" wire:model.lazy="subMiddleNameEdit"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Last name <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="Last name" type="text" wire:model.lazy="subLastNameEdit"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subLastNameEdit')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>


                        {{-- ROW 3 --}}
                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Email address <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="Email address" type="text" wire:model.lazy="subEmailAddressEdit"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subEmailAddressEdit')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror


                                @if ($emailSubExistingError != null)
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $emailSubExistingError }}
                                    </div>
                                @endif

                                @if ($emailSubAlreadyUsedError != null)
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $emailSubAlreadyUsedError }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Mobile number <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="xxxxxxx" type="text" wire:model.lazy="subMobileNumberEdit"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subMobileNumberEdit')
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

                                <select wire:model.lazy="subNationalityEdit"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    <option value=""></option>
                                    <option value="N/A">N/A</option>
                                    @foreach ($countries as $countryChoice)
                                        <option value="{{ $countryChoice }}">
                                            {{ $countryChoice }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('subNationalityEdit')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- ROW 4 --}}
                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Country <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <select wire:model.lazy="subCountryEdit"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    <option value=""></option>
                                    @foreach ($countries as $countryChoice)
                                        <option value="{{ $countryChoice }}">
                                            {{ $countryChoice }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('subCountryEdit')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Job title <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="Job title" type="text" wire:model.lazy="subJobTitleEdit"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subJobTitleEdit')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>


                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Promo code
                            </div>

                            <div class="flex">
                                @if ($promoCodeSuccessSubEdit != null)
                                    <input readonly type="text" wire:model.lazy="subPromoCodeEdit"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none cursor-not-allowed">

                                    <button wire:click.prevent="removePromoCodeSubEdit"
                                        wire:key="btnRemovePromoCodeSubEdit" type="button"
                                        class="bg-red-300 px-5 ml-2">Remove</button>
                                @else
                                    <input placeholder="Enter your promo code here" type="text"
                                        wire:model.lazy="subPromoCodeEdit"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                                    <button wire:click.prevent="applyPromoCodeSubEdit"
                                        wire:key="btnApplyPromoCodeSubEdit" type="button"
                                        class="bg-registrationPrimaryColor text-white px-5 ml-2 hover:bg-registrationPrimaryColorHover">Apply</button>
                                @endif
                            </div>

                            @if ($subPromoCodeEdit != null)
                                @if ($promoCodeFailSubEdit != null)
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $promoCodeFailSubEdit }}
                                    </div>
                                @endif

                                @if ($promoCodeSuccessSubEdit != null)
                                    <div class="text-green-500 text-xs italic mt-1">
                                        {{ $promoCodeSuccessSubEdit }}
                                    </div>
                                @endif
                            @endif
                        </div>


                        @if ($event->category == 'RCW')
                            {{-- ROW 7 --}}
                            <div class="space-y-2 col-span-2">
                                <div class="text-registrationPrimaryColor">
                                    Which RC code are you interested in?
                                </div>
                                <div>
                                    @foreach (['Community Awareness and Emergency Management', 'Distribution', 'Product Stewardship', 'Process Safety', 'Health & Safety', 'Security', 'Environmental Protection'] as $interestIndex => $interest)
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" wire:model.lazy="subDelegateInterestsEdit"
                                                value="{{ $interest }}"
                                                id="subDelegateEditOption-{{ $interestIndex }}">
                                            <label
                                                for="subDelegateEditOption-{{ $interestIndex }}">{{ $interest }}</label>
                                        </div>
                                    @endforeach
                                    {{-- <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterestsEdit"
                                            value="Community Awareness and Emergency Management" id="subDelegateEditOpt1">
                                        <label for="subDelegateEditOpt1">Community Awareness and Emergency Management</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterestsEdit"
                                            value="Distribution" id="subDelegateEditOpt2">
                                        <label for="subDelegateEditOpt2">Distribution</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterestsEdit"
                                            value="Product Stewardship" id="subDelegateEditOpt3">
                                        <label for="subDelegateEditOpt3">Product Stewardship</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterestsEdit"
                                            value="Process Safety" id="subDelegateEditOpt4">
                                        <label for="subDelegateEditOpt4">Process Safety</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterestsEdit"
                                            value="Health & Safety" id="subDelegateEditOpt5">
                                        <label for="subDelegateEditOpt5">Health & Safety</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterestsEdit"
                                            value="Security" id="subDelegateEditOpt6">
                                        <label for="subDelegateEditOpt6">Security</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterestsEdit"
                                            value="Environmental Protection" id="subDelegateEditOpt7">
                                        <label for="subDelegateEditOpt7">Environmental Protection</label>
                                    </div> --}}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" wire:key="btnUpdateAdditionalDelegate"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                    wire:click.prevent="editAdditionalDelegate('{{ $subIdEdit }}')">Update</button>
                <button type="button" wire:key="btnCancelEditAddtionalDelegate"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    wire:click.prevent="closeEditModal">Cancel</button>
            </div>
        </div>
    </div>
</div>
