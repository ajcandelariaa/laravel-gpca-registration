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
            Nationality <span class="text-red-500">*</span>
        </div>
        <div>
            <input placeholder="Nationality" type="text" wire:model.lazy="replaceNationality"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('replaceNationality')
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
            <input placeholder="Job Title" type="text" wire:model.lazy="replaceJobTitle"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('replaceJobTitle')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>


    {{-- ROW 6 --}}
    <div class="space-y-2 col-span-1">
        <div class="text-registrationPrimaryColor">
            Registration type <span class="text-red-500">*</span>
        </div>

        @if ($replacePromoCodeSuccess != null)
            <input readonly wire:model.lazy="replaceBadgeType" type="text"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none cursor-not-allowed">
        @else
            <select wire:model.lazy="replaceBadgeType"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                <option value=""></option>
                @foreach ($registrationTypes as $registrationType)
                    <option value="{{ $registrationType->registration_type }}">{{ $registrationType->registration_type }}</option>
                @endforeach
            </select>
        @endif

        @error('replaceBadgeType')
            <div class="text-red-500 text-xs italic mt-1">
                {{ $message }}
            </div>
        @enderror
    </div>
    <div class="space-y-2 col-span-1">
        <div class="text-registrationPrimaryColor">
            Promo Code
        </div>

        <div class="flex">
            @if ($replacePromoCodeSuccess != null)
                <input readonly type="text" wire:model.lazy="replacePromoCode"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none cursor-not-allowed">

                <button wire:click.prevent="replaceRemovePromoCode" wire:key="btnReplaceRemovePromoCodeEdit"
                    type="button" class="bg-red-300 px-5 ml-2">Remove</button>
            @else
                <input placeholder="Enter your promo code here" type="text" wire:model.lazy="replacePromoCode"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                <button wire:click.prevent="replaceApplyPromoCode" wire:key="btnReplaceApplyPromoCodeEdit"
                    type="button"
                    class="bg-registrationPrimaryColor text-white px-5 ml-2 hover:bg-registrationPrimaryColorHover">Apply</button>
            @endif
        </div>

        @if ($replacePromoCode != null)
            @if ($replacePromoCodeFail != null)
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $replacePromoCodeFail }}
                </div>
            @endif

            @if ($replacePromoCodeDiscount != null)
                <div class="text-green-500 text-xs italic mt-1">
                    {{ $replacePromoCodeDiscount }}% discount
                </div>
            @endif
        @endif
    </div>
</div>
