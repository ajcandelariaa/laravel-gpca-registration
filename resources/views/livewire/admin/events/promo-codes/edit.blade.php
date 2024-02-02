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
                            Edit promo code
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
                            {{-- ROW 1 --}}

                            <div class="col-span-2">
                                <div class="text-registrationPrimaryColor">
                                    Code: <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <input type="text" wire:model.lazy="editPromoCode"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @error('editPromoCode')
                                        <span class="mt-2 text-red-600 italic text-sm">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-span-2">
                                <div class="text-registrationPrimaryColor">
                                    Registration Type: <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model.lazy="editBadgeType"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        @foreach ($registrationTypes as $registrationType)
                                            <option value="{{ $registrationType->registration_type }}">
                                                {{ $registrationType->registration_type }}</option>
                                        @endforeach
                                    </select>

                                    @error('editBadgeType')
                                        <span class="mt-2 text-red-600 italic text-sm">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-span-2">
                                <div class="text-registrationPrimaryColor">
                                    Discount Type: <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model.lazy="editDiscountType"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        <option value="percentage">Percentage</option>
                                        <option value="price">Price</option>
                                        <option value="fixed">Fixed</option>
                                    </select>

                                    @error('editDiscountType')
                                        <span class="mt-2 text-red-600 italic text-sm">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            @if ($editDiscountType != null && $editDiscountType != 'fixed')
                                <div class="col-span-2">
                                    <div class="text-registrationPrimaryColor">
                                        Discount: <span class="text-red-500">*</span>
                                    </div>
                                    <div>
                                        @if ($editDiscountType == 'percentage')
                                            <input type="number" wire:model.lazy="editDiscount" step="1"
                                                min="0" placeholder="0%" max="100"
                                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        @else
                                            <input type="number" wire:model.lazy="editDiscount" step="1"
                                                min="0" placeholder="0"
                                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        @endif

                                        @error('editDiscount')
                                            <span class="mt-2 text-red-600 italic text-sm">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            @if ($editDiscountType != null && $editDiscountType == 'fixed')
                                <div class="col-span-2">
                                    <div class="text-registrationPrimaryColor">
                                        New rate: <span class="text-red-500">*</span>
                                    </div>
                                    <div>
                                        <input type="number" wire:model.lazy="editNewRate" min="0"
                                            placeholder="0"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                                        @error('editNewRate')
                                            <span class="mt-2 text-red-600 italic text-sm">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-span-2">
                                    <div class="text-registrationPrimaryColor">
                                        New rate description: <span class="text-red-500">*</span>
                                    </div>
                                    <div>
                                        <input type="text" wire:model.lazy="editNewRateDescription"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        @error('editNewRateDescription')
                                            <span class="mt-2 text-red-600 italic text-sm">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="col-span-2">
                                <div class="text-registrationPrimaryColor">
                                    Number of Codes: <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <input type="number" wire:model.lazy="editNumberOfCodes" step="1"
                                        min="1" placeholder="1" max="10000"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                                    @error('editNumberOfCodes')
                                        <span class="mt-2 text-red-600 italic text-sm">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-span-2">
                                <div class="text-registrationPrimaryColor">
                                    Code Validity: <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <input type="datetime-local" wire:model.lazy="editValidity"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                                    @error('editValidity')
                                        <span class="mt-2 text-red-600 italic text-sm">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-span-2">
                                <div class="text-registrationPrimaryColor">
                                    Description:
                                </div>
                                <div>
                                    <input type="text" wire:model.lazy="editDescription"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @error('editDescription')
                                        <span class="mt-2 text-red-600 italic text-sm">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:key="updatePromoCodeConfirmation"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="updatePromoCodeConfirmation">Update</button>
                    <button type="button" wire:key="hideEditPromoCode"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="hideEditPromoCode">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
