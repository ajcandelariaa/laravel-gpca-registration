{{-- ROW 1 --}}
<div class="space-y-2 col-span-2 grid grid-cols-5 gap-5 items-start">
    <div class="col-span-1 mt-2">
        <div class="text-registrationPrimaryColor">
            Event Category <span class="text-red-500">*</span>
        </div>
        <div>
            <select required name="category"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                <option value="" disabled selected hidden>Please select...</option>
                @foreach ($eventCategories as $eventCategory => $code)
                    <option value="{{ $eventCategory }}" @if ($event->category == $eventCategory) selected @endif>
                        {{ $eventCategory }}</option>
                @endforeach
            </select>
            @error('category')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="col-span-2">
        <div class="text-registrationPrimaryColor">
            Event Name <span class="text-red-500">*</span>
        </div>
        <div>
            <input placeholder="14th GPCA Supply Chain" type="text" name="name" value="{{ $event->name }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            @error('name')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="col-span-2">
        <div class="text-registrationPrimaryColor">
            Event Location <span class="text-red-500">*</span>
        </div>
        <div>
            <input placeholder="Le Méridien Al Khobar, Saudi Arabia" type="text" name="location"
                value="{{ $event->location }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

            @error('location')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

{{-- ROW 2 --}}
<div class="space-y-2 col-span-2">
    <div class="text-registrationPrimaryColor">
        Event Description <span class="text-red-500">*</span>
    </div>
    <div>
        <textarea name="description" rows="3" placeholder="Type a description here..."
            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">{{ $event->description }}</textarea>

        @error('description')
            <div class="text-red-500 text-xs italic mt-1">
                {{ $message }}
            </div>
        @enderror
    </div>
</div>

{{-- ROW 3 --}}
<div class="space-y-2 col-span-2 grid grid-cols-3 gap-5 items-start">
    <div class="space-y-2">
        <div class="text-registrationPrimaryColor">
            Event Start Date <span class="text-red-500">*</span>
        </div>
        <div>
            <input type="date" name="event_start_date" placeholder="Select a date"
                value="{{ $event->event_start_date }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

            @error('event_start_date')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="space-y-2">
        <div class="text-registrationPrimaryColor">
            Event End Date <span class="text-red-500">*</span>
        </div>
        <div>
            <input type="date" name="event_end_date" placeholder="Select a date" value="{{ $event->event_end_date }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

            @error('event_end_date')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="space-y-2">
        <div class="text-registrationPrimaryColor">
            VAT <span class="text-red-500">*</span>
        </div>
        <div>
            <input type="number" name="event_vat" step="1" min="0" placeholder="0%" max="100"
                value="{{ $event->event_vat }}"
                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

            @error('event_vat')
                <div class="text-red-500 text-xs italic mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>


{{-- ROW 4 --}}
<div class="space-y-2">
    <div class="text-registrationPrimaryColor">
        Event Logo <span class="text-red-500">*</span>
    </div>

    <div class="flex gap-3 flex-col">
        <div>
            <input type="file" accept="image/*" name="logo" onchange="previewLogo(event)"
                class="w-full border-2 focus:border-registrationPrimaryColor rounded-md px-2 text-sm focus:outline-none text-gray-700">
            
            @error('logo')
                <div class="text-red-500 text-xs italic mt-2">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div>
            <img src="{{ Storage::url($event->logo) }}" alt="logo" class="h-36 object-cover" id="imgLogo">
        </div>
    </div>
</div>

<div class="space-y-2">
    <div class="text-registrationPrimaryColor">
        Event Banner <span class="text-red-500">*</span>
    </div>

    <div class="flex gap-3 flex-col">
        <div>
            <input type="file" accept="image/*" name="banner" onchange="previewBanner(event)"
                class="w-full border-2 focus:border-registrationPrimaryColor rounded-md px-2 text-sm focus:outline-none text-gray-700">
            
            @error('banner')
                <div class="text-red-500 text-xs italic mt-2">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div>
            <img src="{{ Storage::url($event->banner) }}" alt="banner" class="h-36 object-cover" id="imgBanner">
        </div>
    </div>
</div>
