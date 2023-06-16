<div class="fixed z-10 inset-0 overflow-y-auto">
    <form wire:submit.prevent="submitImportRegistrants">
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
                            Company Details
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
                            {{-- ROW 1 --}}
                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Pass Type <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model="delegatePassType"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value="fullMember">Full Member</option>
                                        <option value="member">Member</option>
                                        <option value="nonMember">Non-Member</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Company Name <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    @if ($delegatePassType == 'member')
                                        <select wire:model="companyName"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                            <option value=""></option>
                                            @foreach ($members as $member)
                                                <option value="{{ $member->name }}">{{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input placeholder="Company Name" type="text" wire:model="companyName"
                                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @endif

                                    @error('companyName')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ROW 2 --}}
                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Company Sector <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model="companySector"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        @foreach ($companySectors as $companySectorOpt)
                                            <option value="{{ $companySectorOpt }}">{{ $companySectorOpt }}</option>
                                        @endforeach
                                    </select>

                                    @error('companySector')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Company Address <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <input placeholder="Please enter Complete Company Address" type="text"
                                        wire:model="companyAddress"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @error('companyAddress')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- ROW 3 --}}
                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Country <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model="companyCountry"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country }}">{{ $country }}</option>
                                        @endforeach
                                    </select>

                                    @error('companyCountry')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    City <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <input placeholder="City" type="text" wire:model="companyCity"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                                    @error('companyCity')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- ROW 4 --}}
                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Landline Number
                                </div>
                                <div>
                                    <input placeholder="xxxxxxx" type="text" wire:model="companyLandlineNumber"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                                    @error('companyLandlineNumber')
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
                                    <input placeholder="xxxxxxx" type="text" wire:model="companyMobileNumber"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    @error('companyMobileNumber')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Assistant's email address
                                </div>
                                <div>
                                    <input placeholder="Email Address" type="text" wire:model="assistantEmailAddress"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Paid? <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model="paid"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    @error('paid')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-registrationPrimaryColor italic font-bold text-xl mt-10">
                            Import Delegates
                        </div>

                        {{-- ROW 1 --}}
                        <div class="mt-3">
                            <div class="text-registrationPrimaryColor">
                                Choose file <span class="text-red-500">*</span>
                            </div>
                            <div>
                                <input type="file" wire:model="csvFile"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                @error('csvFile')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @if ($csvFileError != null)
                                    <div class="text-red-500 text-xs italic mt-1">
                                        {{ $csvFileError }}
                                    </div>
                                @endif

                                @if (count($incompleDetails) > 0)
                                    <div class="text-red-500 text-xs italic mt-1">
                                        @foreach ($incompleDetails as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif

                                @if (count($emailYouAlreadyUsed) > 0)
                                    <div class="text-red-500 text-xs italic mt-1">
                                        @foreach ($emailYouAlreadyUsed as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif


                                @if (count($emailAlreadyExisting) > 0)
                                    <div class="text-red-500 text-xs italic mt-1">
                                        @foreach ($emailAlreadyExisting as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif


                                @if (count($promoCodeErrors) > 0)
                                    <div class="text-red-500 text-xs italic mt-1">
                                        @foreach ($promoCodeErrors as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:key="submitImportRegistrantsConfirmation"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="submitImportRegistrantsConfirmation">Update</button>
                    <button type="button" wire:key="closeImportModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="closeImportModal">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
