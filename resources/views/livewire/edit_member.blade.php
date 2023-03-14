<div class="shadow-lg bg-white rounded-md w-72">
    <form>
        @csrf
        <input type="hidden" wire:model="memberId">
        <div class="p-5">
            <div class="text-registrationPrimaryColor italic text-center font-bold text-2xl mt-4">
                Edit member
            </div>

            <div class="space-y-2 mt-10">
                <div class="text-registrationPrimaryColor">
                    Company Name: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" wire:model="name"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    @error('name')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-5">
                <div class="text-registrationPrimaryColor">
                    Company Sector:
                </div>
                <div>
                    <select required wire:model="sector"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        <option value=""></option>
                        @foreach ($companySectors as $companySector)
                            <option value="{{ $companySector }}">{{ $companySector }}</option>
                        @endforeach
                        <option value="Others">Others</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2 mt-5">
                <div class="text-registrationPrimaryColor">
                    Company Logo:
                </div>
                <div>
                    <input type="file" accept="image/*" wire:model="logo"
                        class="border focus:border-black rounded-md w-full h-full px-2 text-sm focus:outline-non text-gray-700">
                    @error('logo')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror

                    <div class="flex justify-center mt-5">
                        @if ($oldImage && !$logo)
                            <img src="{{ Storage::url($oldImage) }}" alt="Image Preview" class="h-32 w-32 object-cover">
                        @elseif (($oldImage && $logo) || ((!$oldImage) && $logo))
                            <img src="{{ $logo->temporaryUrl() }}" alt="Image Preview" class="h-32 w-32 object-cover">
                        @else
                            <img src="https://via.placeholder.com/150" alt="Image Preview" class="h-32 w-32 object-cover">
                        @endif


                        {{-- <img src="{{ ($logo ? $logo->temporaryUrl() : $oldImage) ? Storage::url($oldImage) : 'https://via.placeholder.com/150' }} "
                            alt="Image Preview" class="h-32 w-32 object-cover"> --}}
                    </div>
                </div>
            </div>

            <div class="text-center mt-10 flex gap-4">
                <button wire:click.prevent="hideEditMember()"
                    class="bg-red-500 rounded-md text-white py-1 w-full hover:cursor-pointer hover:bg-red-700">Cancel</button>
                <button wire:click.prevent="updateMember()"
                    class="bg-registrationPrimaryColor rounded-md text-white py-1 w-full hover:cursor-pointer hover:bg-registrationPrimaryColorHover">Update</button>
            </div>
        </div>
    </form>
</div>
