{{-- ROW 6 --}}
<div class="space-y-2 col-span-2 grid grid-cols-4 gap-x-5 items-start">
    <div class="text-registrationPrimaryColor font-medium text-xl pt-3 pb-2 col-span-4">
        Full Event - Standard Details
    </div>
    <div>
        <div class="text-registrationPrimaryColor">
            Standrd Start Date <span class="text-red-500">*</span>
        </div>
        <div>
            <input type="date" name="std_start_date" placeholder="Select a date" value="{{ old('std_start_date') }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

            @error('std_start_date')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div>
        <div class="text-registrationPrimaryColor">
            Full Member Rate
        </div>
        <div>
            <input type="number" name="std_full_member_rate" step="0.01" min="0" placeholder="0.00" max="99999.99"
                value="{{ old('std_full_member_rate') }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

            @error('std_full_member_rate')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div>
        <div class="text-registrationPrimaryColor">
            Member Rate <span class="text-red-500">*</span>
        </div>
        <div>
            <input type="number" name="std_member_rate" step="0.01" min="0" placeholder="0.00" max="99999.99"
                value="{{ old('std_member_rate') }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

            @error('std_member_rate')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div>
        <div class="text-registrationPrimaryColor">
            Non-Member Rate <span class="text-red-500">*</span>
        </div>
        <div>
            <input type="number" name="std_nmember_rate" step="0.01" min="0" placeholder="0.00"
                max="99999.99" value="{{ old('std_nmember_rate') }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

            @error('std_nmember_rate')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>
