<div class="shadow-lg bg-white rounded-md w-72">
    <form>
        @csrf
        <div class="p-5">
            <div class="text-registrationPrimaryColor italic text-center font-bold text-2xl mt-4">
                Add member
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
                    </select>
                </div>
            </div>

            <div class="space-y-2 mt-5">
                <div class="text-registrationPrimaryColor">
                    Company Logo:
                </div>
                <div>
                    <input type="file" accept="image/*" wire:model="logo"
                        class="border-2 focus:border-registrationPrimaryColor rounded-md w-full h-full px-2 text-sm focus:outline-none text-gray-700">
                    @error('logo')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror

                    <div class="flex justify-center mt-5">
                        <img src="{{ $logo ? $logo->temporaryUrl() : 'https://via.placeholder.com/150' }}"
                            alt="Image Preview" class="h-32 w-32 object-cover">
                    </div>
                </div>
            </div>
            <div class="text-center mt-10">
                <button wire:click.prevent="addMember()"
                    class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white font-medium py-2 px-5 rounded inline-flex items-center text-sm">
                    <span class="mr-2"><i class="fas fa-plus"></i></span>
                    <span>Add Member</span>
                </button>
            </div>
        </div>

    </form>
</div>
