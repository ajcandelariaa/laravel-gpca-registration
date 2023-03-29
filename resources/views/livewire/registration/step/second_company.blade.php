<div>
    <div class="text-registrationPrimaryColor italic font-bold text-xl">
        Company Information
    </div>

    <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5 items-start">
        {{-- ROW 1 --}}
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
                            <option value="{{ $member->name }}" data-icon="{{ Storage::url($member->logo) }}">
                                {{ $member->name }}</option>
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

        <div class="space-y-2">
            <div class="text-registrationPrimaryColor">
                Company Sector <span class="text-red-500">*</span>
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
                Company Address <span class="text-red-500">*</span>
            </div>
            <div>
                <input placeholder="Please enter Complete Company Address" type="text" wire:model="companyAddress"
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
                        <option value="{{ $country }}">
                            {{ $country }}</option>
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
                Landline Number <span class="italic">(optional)</span>
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

        {{-- ROW 5 --}}

        <div class="space-y-2 col-span-2">
            <div class="text-registrationPrimaryColor">
                Where did you hear about us?
            </div>
            <div>
                <select wire:model="heardWhere"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    <option value=""></option>
                    <option value="Social Media">Social Media</option>
                    <option value="Friends">Friends</option>
                    <option value="Family">Family</option>
                    <option value="News">News</option>
                    <option value="Others">Others</option>
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
