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
                            Edit {{ $editRegTypeShowPC }} registration types
                        </div>

                        <div class="text-registrationPrimaryColor mt-5">
                            Add Registration Type: <span class="text-red-500">*</span>
                        </div>
                        <div class="grid grid-cols-10 gap-x-5 items-center">
                            <div class="col-span-8">
                                <select wire:model.lazy="addRegType"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    <option value=""></option>
                                    @foreach ($registrationTypes as $registrationType)
                                        <option value="{{ $registrationType->registration_type }}">
                                            {{ $registrationType->registration_type }}</option>
                                    @endforeach
                                </select>

                                @error('addRegType')
                                    <span class="mt-2 text-red-600 italic text-sm">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-span-2">
                                <button wire:click.prevent="addRegistrationType" class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white font-medium py-2 px-5 rounded-md inline-flex items-center text-sm">
                                    <span class="mr-2"><i class="fas fa-plus"></i></span>
                                    <span>Add</span>
                                </button>
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-10 gap-5 py-2 px-4 text-center items-center bg-registrationPrimaryColor text-white mt-5">
                            <div class="col-span-8 break-words">Registration Types</div>
                            <div class="col-span-2 break-words">Action</div>
                        </div>

                        @if (empty($editPromoCodeRegistrationTypesArr))
                            <div class="bg-red-400 text-white text-center py-2 text-sm mt-1 rounded-md">
                                There are no additional registration types yet.
                            </div>
                        @else
                            <div
                                class="grid grid-cols-10 gap-x-5 py-1 px-4 items-center bg-gray-200 text-black text-sm">
                                @foreach ($editPromoCodeRegistrationTypesArr as $regTypeIndex => $regType)
                                    <div class="col-span-8 break-words text-left">{{ $regTypeIndex + 1 }}.
                                        {{ $regType['badgeType'] }}</div>
                                    <div wire:click="deleteRegistrationType({{ $regType['id'] }})"
                                        class="cursor-pointer hover:text-red-600 text-red-500 col-span-2 break-words text-center">
                                        <i class="fa-solid fa-trash"></i>
                                        Delete
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:key="cancelEditRegistrationTypes"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="cancelEditRegistrationTypes">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
