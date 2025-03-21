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
                    <div
                        class="text-registrationPrimaryColor italic font-bold text-xl flex md:flex-row flex-col md:items-center gap-2">
                        <span style="width: 200px; text-align: left">Add delegate </span>
                        <span class="text-red-500 text-xs font-normal">Note: Additional delegates should be from the
                            same company as the primary delegate</span>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
                        {{-- ROW 1 --}}
                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Salutation
                            </div>
                            <div>
                                <select wire:model.lazy="subSalutation"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    <option value=""></option>
                                    @foreach ($salutations as $salutation)
                                        <option value="{{ $salutation }}">{{ $salutation }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                First name <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="First name" type="text" wire:model.lazy="subFirstName"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subFirstName')
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
                                <input placeholder="Middle name" type="text" wire:model.lazy="subMiddleName"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Last name <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="Last name" type="text" wire:model.lazy="subLastName"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subLastName')
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
                                <input placeholder="Email address" type="text" wire:model.lazy="subEmailAddress"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subEmailAddress')
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
                                <input placeholder="xxxxxxx" type="text" wire:model.lazy="subMobileNumber"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subMobileNumber')
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
                                <select wire:model.lazy="subNationality"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    <option value=""></option>
                                    <option value="N/A">N/A</option>
                                    @foreach ($countries as $countryChoice)
                                        <option value="{{ $countryChoice }}">
                                            {{ $countryChoice }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('subNationality')
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
                                <select wire:model.lazy="subCountry"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    <option value=""></option>
                                    @foreach ($countries as $countryChoice)
                                        <option value="{{ $countryChoice }}">
                                            {{ $countryChoice }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('subCountry')
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
                                <input placeholder="Job title" type="text" wire:model.lazy="subJobTitle"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subJobTitle')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>


                        {{-- ROW 6 --}}
                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Promo code
                            </div>

                            <div class="flex">
                                @if ($promoCodeSuccessSub != null)
                                    <input readonly type="text" wire:model.lazy="subPromoCode"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none cursor-not-allowed">

                                    <button wire:click.prevent="removePromoCodeSub" wire:key="btnRemovePromoCodeSub"
                                        type="button" class="bg-red-300 px-5 ml-2">Remove</button>
                                @else
                                    <input placeholder="Enter your promo code here" type="text"
                                        wire:model.lazy="subPromoCode"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                                    <button wire:click.prevent="applyPromoCodeSub" wire:key="btnApplyPromoCodeSub"
                                        type="button"
                                        class="bg-registrationPrimaryColor text-white px-5 ml-2 hover:bg-registrationPrimaryColorHover">Apply</button>
                                @endif
                            </div>

                            @if ($promoCodeFailSub != null)
                                <div class="text-red-500 text-xs italic mt-1">
                                    {{ $promoCodeFailSub }}
                                </div>
                            @endif

                            @if ($promoCodeSuccessSub != null)
                                <div class="text-green-500 text-xs italic mt-1">
                                    {{ $promoCodeSuccessSub }}
                                </div>
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
                                            <input type="checkbox" wire:model.lazy="subDelegateInterests"
                                                value="{{ $interest }}"
                                                id="subDelegateOption-{{ $interestIndex }}">
                                            <label
                                                for="subDelegateOption-{{ $interestIndex }}">{{ $interest }}</label>
                                        </div>
                                    @endforeach

                                    {{-- <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterests"
                                            value="Community Awareness and Emergency Management" id="subDelegateOpt1">
                                        <label for="subDelegateOpt1">Community Awareness and Emergency Management</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterests"
                                            value="Distribution" id="subDelegateOpt2">
                                        <label for="subDelegateOpt2">Distribution</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterests"
                                            value="Product Stewardship" id="subDelegateOpt3">
                                        <label for="subDelegateOpt3">Product Stewardship</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterests"
                                            value="Process Safety" id="subDelegateOpt4">
                                        <label for="subDelegateOpt4">Process Safety</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterests"
                                            value="Health & Safety" id="subDelegateOpt5">
                                        <label for="subDelegateOpt5">Health & Safety</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterests"
                                            value="Security" id="subDelegateOpt6">
                                        <label for="subDelegateOpt6">Security</label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.lazy="subDelegateInterests"
                                            value="Environmental Protection" id="subDelegateOpt7">
                                        <label for="subDelegateOpt7">Environmental Protection</label>
                                    </div> --}}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" wire:key="btnSaveAdditionalDelegate"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                    wire:click.prevent="saveAdditionalDelegate">Save</button>
                <button type="button" wire:key="btnCancelAddtionalDelegate"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    wire:click.prevent="closeAddModal">Cancel</button>
            </div>
        </div>
    </div>
</div>
