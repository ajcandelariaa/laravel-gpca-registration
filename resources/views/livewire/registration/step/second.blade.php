<div class="mx-5">
    {{-- COMPANY INFORMATION --}}
    <div class="text-registrationPrimaryColor italic font-bold text-xl">
        Company details
    </div>

    <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5 items-start">
        {{-- ROW 1 --}}
        <div class="col-span-2 sm:col-span-1 space-y-2">
            <div class="text-registrationPrimaryColor">
                Company name <span class="text-red-500">*</span>
            </div>
            <div>
                <input readonly placeholder="Company name" type="text" wire:model="companyName"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none">
            </div>
        </div>

        <div class="col-span-2 sm:col-span-1 space-y-2">
            <div class="text-registrationPrimaryColor">
                Company sector <span class="text-red-500">*</span>
            </div>
            <div>
                <select wire:model="companySector"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    <option value=""></option>
                    @foreach ($companySectors as $companySector)
                        <option value="{{ $companySector }}">{{ $companySector }}</option>
                    @endforeach
                </select>

                @error('companySector')
                    <div class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- ROW 2 --}}
        <div class="space-y-2 col-span-2">
            <div class="text-registrationPrimaryColor">
                Company address <span class="text-red-500">*</span>
            </div>
            <div>
                <input placeholder="Please enter complete company address" type="text" wire:model="companyAddress"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                @error('companyAddress')
                    <div class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- ROW 3 --}}
        <div class="col-span-2 sm:col-span-1 space-y-2">
            <div class="text-registrationPrimaryColor">
                Country <span class="text-red-500">*</span>
            </div>
            <div>
                <select wire:model="companyCountry"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    <option value=""></option>
                    @foreach ($countries as $country)
                        <option value="{{ $country }}">
                            {{ $country }}
                        </option>
                    @endforeach
                </select>

                @error('companyCountry')
                    <div class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="col-span-2 sm:col-span-1 space-y-2">
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
        <div class="col-span-2 sm:col-span-1 space-y-2">
            <div class="text-registrationPrimaryColor">
                Landline number <span class="italic">(optional)</span>
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

        <div class="col-span-2 sm:col-span-1 space-y-2">
            <div class="text-registrationPrimaryColor">
                Mobile number <span class="text-red-500">*</span>
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

        {{-- ROW 5 --}}

        <div class="col-span-2 sm:col-span-1 space-y-2">
            <div class="text-registrationPrimaryColor">
                Assistant's email address
            </div>
            <div>
                <input placeholder="Email address" type="text" wire:model="assistantEmailAddress"
                    wire:key="assistantEmailAddress"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                @error('assistantEmailAddress')
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
                <select wire:model="heardWhere"
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

        @if ($event->category == 'PC')
            <div class="col-span-2 space-y-2">
                <div class="text-registrationPrimaryColor">
                    Would you be attending the Networking Gala Dinner and Plastics Circul-A-Thon Awards 14<sup>th</sup> May 2023?
                </div>
                <div>
                    <select wire:model="pcAttendingND"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        <option value=""></option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
        @elseif ($event->category == 'SCC')
            <div class="col-span-2 space-y-2">
                <div class="text-registrationPrimaryColor">
                    Would you be attending the Networking Gala Dinner and SC Excellence Awards on 16<sup>th</sup> May 2013?
                </div>
                <div>
                    <select wire:model="sccAttendingND"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        <option value=""></option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
        @else
        @endif
    </div>
</div>
