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
                    <div class="text-registrationPrimaryColor italic font-bold text-xl flex items-center gap-2">
                        <span>Add Delegate </span>
                        <span class="text-red-500 text-xs font-normal">Please note to add delegate only in the same
                            company!</span>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
                        {{-- ROW 1 --}}
                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Salutation
                            </div>
                            <div>
                                <select wire:model="subSalutation"
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
                                First Name <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="First Name" type="text" wire:model="subFirstName"
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
                                Middle Name
                            </div>
                            <div>
                                <input placeholder="Middle Name" type="text" wire:model="subMiddleName"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="text-registrationPrimaryColor">
                                Last Name <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="Last Name" type="text" wire:model="subLastName"
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
                                Email Address <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input placeholder="Email Address" type="text" wire:model="subEmailAddress"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subEmailAddress')
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
                                <input placeholder="xxxxxxx" type="text" wire:model="subMobileNumber"
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
                                <input placeholder="Nationality" type="text" wire:model="subNationality"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subNationality')
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
                                <input placeholder="Job Title" type="text" wire:model="subJobTitle"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('subJobTitle')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>


                        {{-- ROW 6 --}}
                        <div class="space-y-2 col-span-2">
                            <div class="text-registrationPrimaryColor">
                                Promo Code
                            </div>

                            <div class="flex">
                                @if ($promoCodeSuccessSub != null)
                                    <input readonly type="text" wire:model="subPromoCode"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none cursor-not-allowed">

                                    <button wire:click.prevent="removePromoCodeSub" wire:key="btnRemovePromoCodeSub"
                                        type="button" class="bg-red-300 px-5 ml-2">Remove</button>
                                @else
                                    <input placeholder="Enter your promo code here" type="text"
                                        wire:model="subPromoCode"
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
