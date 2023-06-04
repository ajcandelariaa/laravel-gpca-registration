{{-- ROW 6 --}}
<div class="space-y-2 col-span-2 grid grid-cols-3 gap-5 items-start">
    <div class="text-registrationPrimaryColor font-medium text-xl pt-3 pb-2 col-span-3">
        Badge Details
    </div>
    <div class="col-span-1">
        <div class="text-registrationPrimaryColor">
            Badge Footer Link <span class="text-red-500">*</span>
        </div>
        <div>
            <input type="text" name="badge_footer_link" value="{{ old('badge_footer_link') }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('badge_footer_link')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="col-span-1">
        <div class="text-registrationPrimaryColor">
            Badge Footer Link Color <span class="text-red-500">*</span>
        </div>
        <div>
            <input type="text" name="badge_footer_link_color" value="{{ old('badge_footer_link_color') }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('badge_footer_link_color')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="col-span-1">
        <div class="text-registrationPrimaryColor">
            Badge Footer Link Background Color <span class="text-red-500">*</span>
        </div>
        <div>
            <input type="text" name="badge_footer_bg_color" value="{{ old('badge_footer_bg_color') }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('badge_footer_bg_color')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="col-span-3 grid grid-cols-2 gap-5 items-start">
        <div class="space-y-2">
            <div class="text-registrationPrimaryColor">
                Badge Front Banner <span class="text-red-500">*</span>
            </div>

            <div class="flex gap-3 flex-col">
                <div>
                    <input type="file" accept="image/*" name="badge_front_banner" onchange="previewBadgeFrontBanner(event)"
                        class="w-full border-2 focus:border-registrationPrimaryColor rounded-md px-2 text-sm focus:outline-none text-gray-700">

                    @error('badge_front_banner')
                        <div class="text-red-500 text-xs italic mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div>
                    <img src="https://via.placeholder.com/150" alt="badge_front_banner" class="h-36 object-cover" id="badgeFrontBanner">
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <div class="text-registrationPrimaryColor">
                Badge Back Banner <span class="text-red-500">*</span>
            </div>

            <div class="flex gap-3 flex-col">
                <div>
                    <input type="file" accept="image/*" name="badge_back_banner" onchange="previewBadgeBackBanner(event)"
                        class="w-full border-2 focus:border-registrationPrimaryColor rounded-md px-2 text-sm focus:outline-none text-gray-700">

                    @error('badge_back_banner')
                        <div class="text-red-500 text-xs italic mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div>
                    <img src="https://via.placeholder.com/150" alt="badge_back_banner" class="h-36 object-cover"
                        id="badgeBackBanner">
                </div>
            </div>
        </div>
    </div>
</div>
