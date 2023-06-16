<div class="mx-5">
    <div class="text-registrationPrimaryColor italic font-bold text-xl">
        Participant details
    </div>

    <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
        {{-- ROW 1 --}}
        <div class="space-y-2 col-span-2">
            <div class="grid {{ $subCategory == null ? 'grid-cols-2' : 'grid-cols-3' }} gap-x-5">
                <div class="{{ $subCategory == null ? 'col-span-2' : 'col-span-3' }} sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Category <span class="text-red-500">*</span>
                    </div>
                    <div class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        {{ $category }}
                    </div>
                </div>

                @if ($subCategory != null)
                    <div class="{{ $subCategory == null ? 'col-span-2' : 'col-span-3' }} sm:col-span-1">
                        <div class="text-registrationPrimaryColor">
                            Sub Category <span class="text-red-500">*</span>
                        </div>
                        <div
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            {{ $subCategory ?? 'N/A' }}
                        </div>
                    </div>
                @endif

                <div class="{{ $subCategory == null ? 'col-span-2' : 'col-span-3' }} sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Company name <span class="text-red-500">*</span>
                    </div>
                    <div class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        {{ $companyName }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 1 --}}
        <div class="space-y-2 col-span-2">
            <div class="grid grid-cols-11 gap-x-5">
                <div class="col-span-11 sm:col-span-2">
                    <div class="text-registrationPrimaryColor">
                        Salutation
                    </div>
                    <div>
                        <select wire:model.lazy="salutation"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            <option value=""></option>
                            @foreach ($salutations as $salutation)
                                <option value="{{ $salutation }}">{{ $salutation }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-span-11 sm:col-span-3">
                    <div class="text-registrationPrimaryColor">
                        First name <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="First name" type="text" wire:model.lazy="firstName"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('firstName')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-11 sm:col-span-3">
                    <div class="text-registrationPrimaryColor">
                        Middle name
                    </div>
                    <div>
                        <input placeholder="Middle name" type="text" wire:model.lazy="middleName"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    </div>
                </div>

                <div class="col-span-11 sm:col-span-3">
                    <div class="text-registrationPrimaryColor">
                        Last name <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="Last name" type="text" wire:model.lazy="lastName"
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
            <div class="grid grid-cols-2 gap-x-5">
                <div class="col-span-2 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Email address <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="Email address" type="text" wire:model.lazy="emailAddress"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('emailAddress')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                        @if ($emailMainExistingError != null)
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $emailMainExistingError }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Mobile number <span class="text-red-500">*</span>
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
            </div>
        </div>

        {{-- ROW 3 --}}
        <div class="col-span-2 sm:col-span-1 space-y-2">
            <div class="text-registrationPrimaryColor">
                Address <span class="text-red-500">*</span>
            </div>
            <div>
                <input placeholder="Address" type="text" wire:model.lazy="address"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                @error('address')
                    <div class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="col-span-2 sm:col-span-1 space-y-2">
            <div class="text-registrationPrimaryColor">
                Where did you hear about us?
            </div>
            <div>
                <select wire:model.lazy="heardWhere"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    <option value=""></option>
                    <option value="Email">Email</option>
                    <option value="Industry association">Industry association</option>
                    <option value="Media">Media</option>
                    <option value="Facbook">Facebook</option>
                    <option value="Twitter">Twitter</option>
                    <option value="YouTube">YouTube</option>
                    <option value="Instagram">Instagram</option>
                    <option value="LinkedIn">LinkedIn</option>
                </select>
            </div>
        </div>

        {{-- ROW 3 --}}
        <div class="space-y-2 col-span-2">
            <div class="grid grid-cols-3 gap-x-5">
                <div class="col-span-3 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Country <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <select wire:model.lazy="country"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            <option value=""></option>
                            @foreach ($countries as $country)
                                <option value="{{ $country }}">
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>

                        @error('country')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-3 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        City <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="City" type="text" wire:model.lazy="city"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('city')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-3 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Job title <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="Job title" type="text" wire:model.lazy="jobTitle"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('jobTitle')
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
            <div class="grid grid-cols-2 gap-x-5">
                <div class="col-span-2 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Entry form <span class="text-red-500">*</span> <span class="italic text-xs text-red-600 ml-2">(PDF or word format only)</span> 
                    </div>
                    <div>
                        <input type="file" wire:model.lazy="entryForm"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('entryForm')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Supporting documents <span class="italic text-xs text-red-600">(You can choose up to 4 file in PDF or word format only)</span>
                    </div>
                    <div>
                        <input type="file" wire:model.lazy="supportingDocuments" multiple
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        @if (count($supportingDocumentsError) > 0)
                            @foreach ($supportingDocumentsError as $error)
                                <div class="text-red-500 text-xs italic mt-1">
                                    {{ $error }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
