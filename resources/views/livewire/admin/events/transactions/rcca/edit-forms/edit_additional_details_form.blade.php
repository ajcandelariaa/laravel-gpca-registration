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
                            Additional details
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-y-3 gap-x-5">

                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Category <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model.lazy="category"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        @foreach ($awardsCategories as $awardsCategory => $sub)
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

                            @if ($category != null && $awardsCategories[$category] != null)
                                <div class="space-y-2">
                                    <div class="text-registrationPrimaryColor">
                                        Sub category <span class="text-red-500">*</span>
                                    </div>
                                    <div class="mt-2">
                                        <select wire:model.lazy="subCategory"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                            <option value=""></option>
                                            @foreach ($awardsCategories[$category] as $subCategory)
                                                <option value="{{ $subCategory }}">
                                                    {{ $subCategory }}</option>
                                            @endforeach
                                        </select>

                                        @error('subCategory')
                                            <div class="text-red-500 text-xs italic mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- ROW 1 --}}
                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Pass Type <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model.lazy="participantPassType"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value="member" {{ $participantPassType == 'member' ? 'selected' : '' }}>
                                            Member</option>

                                        @if ($category == 'Responsible Partner')
                                            <option value="nonMember"
                                                {{ $participantPassType == 'nonMember' ? 'selected' : '' }}>Non-Member
                                            </option>
                                        @endif
                                    </select>
                                    

                                    @error('participantPassType')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Company Name <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    @if ($participantPassType == 'member')
                                        <select wire:model.lazy="companyName"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                            <option value=""></option>
                                            @foreach ($members as $member)
                                                <option value="{{ $member->name }}"
                                                    {{ $member->name == $companyName ? 'selected' : '' }}>
                                                    {{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input placeholder="Company Name" type="text" wire:model.lazy="companyName"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @endif

                                    @error('companyName')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:key="btnUpdateAdditional"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="updateAdditionalDetails">Update</button>
                    <button type="button" wire:key="btnCancelEditAdditional"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="closeEditAdditionalDetailsModal">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
