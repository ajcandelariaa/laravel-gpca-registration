<div class="px-5">
    <div class="float-left">
        @if ($updateMember)
            @include('livewire.members.edit_member')
        @else
            @include('livewire.members.add_member')
        @endif
    </div>

    <div class="shadow-lg my-5 pt-5 bg-white rounded-md" style="margin-left: 320px; ">
        <div class="flex gap-5 items-center">
            <div>
                {{-- <a href="{{ route('admin.event.registrants.exportData', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                    target="_blank"
                    class="bg-green-600 hover:bg-green-700 text-white py-2 px-5 rounded-md text-lg text-center">Export
                    Data to Excel</a> --}}
                <button type="button" wire:click="openImportModal" wire:key="openImportModal"
                    class="bg-sky-600 hover:bg-sky-700 text-white py-2 px-5 rounded-md text-lg text-center">Import
                    Members</button>
            </div>
            <form>
                <div class="relative">
                    <input type="text" wire:model="searchTerm"
                        class="border border-gray-300 bg-white rounded-md py-2 pl-10 pr-4 block transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                        placeholder="Search...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </form>
        </div>

        <h1 class="text-center text-2xl mt-4">List of members</h1>
        <div class="grid grid-cols-11 pt-4 pb-2 place-items-center">
            <div class="col-span-1">No.</div>
            <div class="col-span-3">Company Name</div>
            <div class="col-span-4">Company Sector</div>
            <div class="col-span-1">Status</div>
            <div class="col-span-2">Actions</div>
        </div>
        @php
            $count = 1;
        @endphp
        @if ($members->isEmpty())
            <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md">
                There are no members yet.
            </div>
        @else
            @foreach ($members as $member)
                <div
                    class="grid grid-cols-11 pt-2 pb-2 mb-1 place-items-center {{ $count % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                    <div class="col-span-1">{{ $count }}</div>
                    <div class="col-span-3 flex justify-start items-center gap-2">
                        @if ($member->logo != null)
                            <img src="{{ Storage::url($member->logo) }}" alt="logo" class="object-cover w-10">
                        @endif
                        <div>
                            {{ $member->name }}
                        </div>
                    </div>
                    <div class="col-span-4">
                        @if ($member->sector == null)
                            N/A
                        @else
                            {{ $member->sector }}
                        @endif
                    </div>
                    <div class="col-span-1">
                        @if ($member->active)
                            <button wire:click="updateStatus({{ $member->id }}, {{ $member->active }})"
                                class="text-gray-700 bg-green-300 hover:bg-green-500 hover:text-white py-1 px-2 text-sm rounded-md">Active</button>
                        @else
                            <button wire:click="updateStatus({{ $member->id }}, {{ $member->active }})"
                                class="text-gray-700 bg-red-300 hover:bg-red-500 hover:text-white py-1 px-2 text-sm rounded-md">Inactive</button>
                        @endif
                    </div>
                    <div class="col-span-2 flex gap-4">
                        <div wire:click="showEditMember({{ $member->id }})"
                            class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                            <i class="fa-solid fa-pen-to-square"></i>
                            Edit
                        </div>
                        <div wire:click="deleteMemberConfirmation({{ $member->id }})"
                            class="cursor-pointer hover:text-red-600 text-red-500">
                            <i class="fa-solid fa-trash"></i>
                            Delete
                        </div>
                    </div>
                    @php
                        $count++;
                    @endphp
                </div>
            @endforeach
        @endif
    </div>
    
    @if ($showImportModal)
        @include('livewire.members.import_member_form')
    @endif
</div>
