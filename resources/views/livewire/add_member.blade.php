<div class="shadow-lg my-5 py-5 bg-white rounded-md	">
    <h1 class="text-center text-2xl">Add Member</h1>
    <div class="mt-10 flex justify-center">
        <form>
            @csrf
            <div class="flex flex-col gap-5">
                <div class="items-center grid grid-cols-2">
                    <label class="mr-5">Company Name: <span class="text-red-600">*</span></label>
                    <input type="text" wire:model="name"
                        class="border focus:border-black rounded-md w-full h-full py-1 px-2 text-sm focus:outline-non text-gray-700">
                    @error('name')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="items-center grid grid-cols-2">
                    <label class="mr-5">Company Sector: </label>
                    <input type="text" wire:model="sector"
                        class="border focus:border-black rounded-md w-full h-full py-1 px-2 text-sm focus:outline-non text-gray-700">
                </div>

                <div class="items-center grid grid-cols-2">
                    <label class="mr-5">Company Logo: </label>
                    <div class="flex-row">
                        <input type="file" accept="image/*" wire:model="logo"
                            class="border focus:border-black rounded-md w-full h-full px-2 text-sm focus:outline-non text-gray-700">
                        @error('logo')
                            <span class="mt-2 text-red-600 italic text-sm">
                                {{ $message }}
                            </span>
                        @enderror

                        @if ($logo)
                            Photo Preview:
                            <img src="{{ $logo->temporaryUrl() }}">
                        @endif
                    </div>
                </div>

                <div class="text-center">
                    <button wire:click.prevent="addMember()"
                        class="bg-blue-500 rounded-md text-white py-1 px-14 hover:cursor-pointer hover:bg-blue-700">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>
