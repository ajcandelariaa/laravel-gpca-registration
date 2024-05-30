{{-- ROW 5 --}}
<div class="space-y-2 col-span-2 grid grid-cols-4 gap-x-5 items-start">
    
    <div class="flex justify-between pt-3 pb-2 col-span-4">
        <div class="text-registrationPrimaryColor font-medium text-xl ">
            Full Event - Early Bird Details
        </div>
        <div>
            <input type="checkbox" id="toggle_inputs" class="cursor-pointer">
            <label for="toggle_inputs" class="cursor-pointer">Enable inputs</label>
        </div>
    </div>

    <div>
        <div class="text-registrationPrimaryColor">
            Early Bird End Date
        </div>
        <div>
            <input readonly id="eb_end_date" type="date" name="eb_end_date" placeholder="Select a date" value="{{ old('eb_end_date') }}"
                class="cursor-not-allowed bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none">

            @error('eb_end_date')
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
            <input readonly id="eb_full_member_rate" type="number" name="eb_full_member_rate" step="0.01" min="0" placeholder="0.00" max="99999.99"
                value="{{ old('eb_full_member_rate') }}"
                class="cursor-not-allowed bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none">

            @error('eb_full_member_rate')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div>
        <div class="text-registrationPrimaryColor">
            Member Rate
        </div>
        <div>
            <input readonly id="eb_member_rate" type="number" name="eb_member_rate" step="0.01" min="0" placeholder="0.00" max="99999.99"
                value="{{ old('eb_member_rate') }}"
                class="cursor-not-allowed bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none">

            @error('eb_member_rate')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div>
        <div class="text-registrationPrimaryColor">
            Non-Member Rate
        </div>
        <div>
            <input readonly id="eb_nmember_rate" type="number" name="eb_nmember_rate" step="0.01" min="0" placeholder="0.00"max="99999.99"
                value="{{ old('eb_nmember_rate') }}"
                class="cursor-not-allowed bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-none">

            @error('eb_nmember_rate')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>
