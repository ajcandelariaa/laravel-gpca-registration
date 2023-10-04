<div class="mx-5">
    <div class="flex items-center justify-between">
        <div class="text-registrationPrimaryColor italic font-bold text-xl">
            Spouse details 
        </div>
        <div>
            <a href="https://www.gpcaforum.com/spouse-program/" target="_blank" class="text-white bg-registrationPrimaryColorHover text-md rounded-md py-1 px-4 hover:bg-registrationPrimaryColor">Click here to view itineraries</a>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
        {{-- ROW 1 --}}
        <div class="col-span-2 space-y-2">
            <div class="text-registrationPrimaryColor">
                Select which day(s) you want to attend <span class="text-red-500">*</span> 
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model.lazy="selectedDays" value="1">
                    <label>December 4, 2023 (Monday) - <span class="font-semibold">$ 200.00</span></label>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model.lazy="selectedDays" value="2">
                    <label>December 5, 2023 (Tuesday) - <span class="font-semibold">$ 220.00</span></label>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model.lazy="selectedDays" value="3">
                    <label>December 6, 2023 (Wednesday) - <span class="font-semibold">$ 200.00</span></label>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model.lazy="selectedDays" value="4">
                    <label>December 7, 2023 (Thursday) - <span class="font-semibold">$ 200.00</span></label>
                </div>

                @error('selectedDays')
                    <div class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="col-span-2 space-y-2">
            <div class="text-registrationPrimaryColor">
                Full name of Annual GPCA Forum registered attendee? <span class="text-red-500">*</span>
            </div>
            <div>
                <input placeholder="Full name" type="text" wire:model.lazy="referenceDelegateName"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                @error('referenceDelegateName')
                    <div class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- ROW 2 --}}
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

        {{-- ROW 3 --}}
        <div class="space-y-2 col-span-2">
            <div class="grid grid-cols-3 gap-x-5">
                <div class="col-span-3 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Nationality <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <select wire:model.lazy="nationality"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            <option value=""></option>
                            @foreach ($countries as $country)
                                <option value="{{ $country }}">
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>

                        @error('nationality')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

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
            </div>
        </div>

        {{-- ROW 4 --}}
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

        {{-- ROW 5 --}}
        <div class="col-span-2 space-y-2">
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

                @error('heardWhere')
                    <div class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>
</div>
