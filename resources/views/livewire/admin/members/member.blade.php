<div class="px-5">
    <div class="float-left">
        @if ($updateMember)
            @include('livewire.admin.members.edit_member')
        @else
            @include('livewire.admin.members.add_member')
        @endif
    </div>

    <div class="shadow-lg my-5 pt-5 bg-white rounded-md" style="margin-left: 320px; ">
        <div class="flex gap-5 items-center">
            <div>
                <a href="{{ route('admin.member.export.data') }}" target="_blank"
                    class="bg-green-600 hover:bg-green-700 text-white py-2 px-5 rounded-md text-lg text-center">Export
                    Data to Excel</a>
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

        <h1 class="text-center text-2xl bg-registrationPrimaryColor text-white py-4 mt-5">Members</h1>
        <div class="grid grid-cols-12 gap-5 p-4 px-4 text-center items-center bg-blue-600 text-white ">
            <div class="col-span-1 break-words">No.</div>
            <div class="col-span-1 break-words">Type</div>
            <div class="col-span-3 break-words">Company Name</div>
            <div class="col-span-4 break-words">Company Sector</div>
            <div class="col-span-1 break-words">Status</div>
            <div class="col-span-2 break-words">Actions</div>
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
                    class="grid grid-cols-12 gap-5 py-2 px-4 mb-1 items-center text-center {{ $count % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                    <div class="col-span-1 break-words">{{ $count }}</div>
                    <div class="col-span-1 break-words">
                        @if ($member->type == "full")
                            Full
                        @else 
                            Associate
                        @endif
                    </div>
                    <div class="col-span-3 break-words flex items-center justify-center gap-2">
                        @if ($member->logo != null)
                            <img src="{{ Storage::url($member->logo) }}" alt="logo" class="object-cover w-10">
                        @endif
                        <div>
                            {{ $member->name }}
                        </div>
                    </div>
                    <div class="col-span-4 break-words">
                        @if ($member->sector == null)
                            N/A
                        @else
                            {{ $member->sector }}
                        @endif
                    </div>
                    <div class="col-span-1 break-words">
                        @if ($member->active)
                            <button wire:click="updateStatus({{ $member->id }}, {{ $member->active }})"
                                class="text-gray-700 bg-green-300 hover:bg-green-500 hover:text-white py-1 px-2 text-sm rounded-md">Active</button>
                        @else
                            <button wire:click="updateStatus({{ $member->id }}, {{ $member->active }})"
                                class="text-gray-700 bg-red-300 hover:bg-red-500 hover:text-white py-1 px-2 text-sm rounded-md">Inactive</button>
                        @endif
                    </div>
                    <div class="col-span-2 break-words">
                        <button wire:click="showEditMember({{ $member->id }})"
                            class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                            <i class="fa-solid fa-pen-to-square"></i>
                            Edit
                        </button>
                        <button wire:click="deleteMemberConfirmation({{ $member->id }})"
                            class="cursor-pointer hover:text-red-600 text-red-500">
                            <i class="fa-solid fa-trash"></i>
                            Delete
                        </button>
                    </div>
                    @php
                        $count++;
                    @endphp
                </div>
            @endforeach
        @endif
    </div>
    
    @if ($showImportModal)
        @include('livewire.admin.members.import_member_form')
    @endif
</div>
