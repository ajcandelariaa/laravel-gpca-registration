<div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
    {{-- ROW 1 --}}
    <div class="space-y-2">
        <div class="text-registrationPrimaryColor">
            Salutation
        </div>
        <div>
            <select wire:model.lazy="replaceSalutation"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                <option value=""></option>
                @foreach ($salutations as $salutationOpT)
                    <option value="{{ $salutationOpT }}">{{ $salutationOpT }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="space-y-2">
        <div class="text-registrationPrimaryColor">
            First Name <span class="text-red-500">*</span>
        </div>
        <div>
            <input placeholder="First Name" type="text" wire:model.lazy="replaceFirstName"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('replaceFirstName')
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
            <input placeholder="Middle Name" type="text" wire:model.lazy="replaceMiddleName"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
        </div>
    </div>

    <div class="space-y-2">
        <div class="text-registrationPrimaryColor">
            Last Name <span class="text-red-500">*</span>
        </div>
        <div>
            <input placeholder="Last Name" type="text" wire:model.lazy="replaceLastName"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('replaceLastName')
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
            <input placeholder="Email Address" type="text" wire:model.lazy="replaceEmailAddress"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('replaceEmailAddress')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror

            @if ($replaceEmailAlreadyUsedError != null)
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $replaceEmailAlreadyUsedError }}
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-2">
        <div class="text-registrationPrimaryColor">
            Mobile Number <span class="text-red-500">*</span>
        </div>
        <div>
            <input placeholder="xxxxxxx" type="text" wire:model.lazy="replaceMobileNumber"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('replaceMobileNumber')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>


    {{-- ROW 4 --}}
    <div class="space-y-2">
        <div class="text-registrationPrimaryColor">
            Address <span class="text-red-500">*</span>
        </div>
        <div>
            <input placeholder="Address" type="text" wire:model.lazy="replaceAddress"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('replaceAddress')
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
            <select wire:model.lazy="replaceCountry"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                <option value=""></option>
                @foreach ($countries as $country)
                    <option value="{{ $country }}">
                        {{ $country }}
                    </option>
                @endforeach
            </select>

            @error('replaceCountry')
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
            <input placeholder="City" type="text" wire:model.lazy="replaceCity"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('replaceCity')
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
            <input placeholder="Job title" type="text" wire:model.lazy="replaceJobTitle"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('replaceJobTitle')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>
