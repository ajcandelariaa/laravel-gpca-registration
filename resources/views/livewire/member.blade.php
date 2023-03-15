<div class="px-5">
    {{-- @if (session()->has('member_updated'))
        <div class="bg-green-300 py-2 px-4 text-center">
            {{ session()->get('member_updated') }}
        </div>
    @endif
    @if (session()->has('error_updating_member'))
        <div class="bg-red-300 py-2 px-4 text-center">
            {{ session()->get('error_updating_member') }}
        </div>
    @endif


    @if (session()->has('added'))
        <div class="bg-green-300 py-2 px-4 text-center">
            {{ session()->get('added') }}
        </div>
    @endif
    @if (session()->has('error_added'))
        <div class="bg-red-300 py-2 px-4 text-center">
            {{ session()->get('error_added') }}
        </div>
    @endif --}}

    <div class="float-left">
        @if ($updateMember)
            @include('livewire.edit_member')
        @else
            @include('livewire.add_member')
        @endif
    </div>

    <div class="shadow-lg my-5 pt-5 bg-white rounded-md" style="margin-left: 320px; ">
        {{-- @if (session()->has('updated_status'))
            <div class="bg-green-300 py-2 px-4 text-center">
                {{ session()->get('updated_status') }}
            </div>
        @endif
        @if (session()->has('error_updating_status'))
            <div class="bg-red-300 py-2 px-4 text-center">
                {{ session()->get('error_updating_status') }}
            </div>
        @endif


        @if (session()->has('member_deleted'))
            <div class="bg-green-300 py-2 px-4 text-center">
                {{ session()->get('member_deleted') }}
            </div>
        @endif
        @if (session()->has('error_deleting_member'))
            <div class="bg-red-300 py-2 px-4 text-center">
                {{ session()->get('error_deleting_member') }}
            </div>
        @endif --}}

        <h1 class="text-center text-2xl">List of members</h1>
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
        @foreach ($members as $member)
            <div
                class="grid grid-cols-11 pt-2 pb-2 mb-1 place-items-center {{ $count % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                <div class="col-span-1">{{ $count }}</div>
                <div class="col-span-3 flex justify-start items-center gap-2">
                    @if ($member->logo != null)
                        <img src="{{ Storage::url($member->logo) }}" alt="logo" class="object-cover w-10">
                    @else
                        <img src="{{ asset('assets/images/logo-placeholder-image.png') }}" alt="logo"
                            class="object-cover w-10">
                    @endif
                    <div>
                        {{ $member->name }}
                    </div>
                </div>
                <div class="col-span-4">{{ $member->sector }}</div>
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
                    <div onclick="deleteMemberScript({{ $member->id }})"
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
    </div>

    <script>
        function deleteMemberScript(memberId) {
            if (confirm("Are you sure you want to delete this member?"))
                window.livewire.emit('deleteMemberScript', memberId);
        }
    </script>
</div>
