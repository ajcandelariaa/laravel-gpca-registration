<div>
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>

    <div class="mx-10 my-10">
        <h1 class="text-registrationregibg-registrationPrimaryColor text-3xl font-bold">Confirmed delegates</h1>

        @if (count($confirmedDelegates) == 0)
            <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md">
                There are no confirmed delegates yet.
            </div>
        @else
            <p class="mt-5">Total confirmed delegates: {{ count($confirmedDelegates) }}</p>
            <p>Total added delegates: {{ $countAlreadyAdded }}</p>
            <p>Total not added delegates: {{ count($confirmedDelegates) - $countAlreadyAdded }}</p>

            <div class="mt-5">
                @if ((count($confirmedDelegates) - $countAlreadyAdded) == 0)
                    <button class="cursor-not-allowed bg-gray-400 py-1 px-6 rounded-md" disabled>All delegates are added</button>
                @else
                    <button class="cursor-pointer bg-registrationPrimaryColor text-white py-1 px-6 rounded-md"
                        wire:click.prevent="addRemainingDelegatesConfirmation">Add remaining delegates</button>
                @endif
            </div>

            <div class="shadow-lg my-5 bg-white rounded-md">
                <div
                    class="grid grid-cols-8 pt-2 pb-2 mt-3 text-center items-center gap-10 text-sm text-white bg-registrationPrimaryColor rounded-tl-md rounded-tr-md">
                    <div class="col-span-1">Registration ID</div>
                    <div class="col-span-1">Email</div>
                    <div class="col-span-1">First name</div>
                    <div class="col-span-1">Last name</div>
                    <div class="col-span-1">Name</div>
                    <div class="col-span-1">Job title</div>
                    <div class="col-span-1">Company</div>
                    <div class="col-span-1">Action</div>
                </div>
                @foreach ($confirmedDelegates as $index => $confirmedDelegate)
                    <div
                        class="grid grid-cols-8 gap-10 pt-2 pb-2 mb-1 text-center items-center text-sm {{ $index % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                        <div class="col-span-1 break-words">{{ $confirmedDelegate['registration_id'] }}</div>
                        <div class="col-span-1 break-words">{{ $confirmedDelegate['email'] }}</div>
                        <div class="col-span-1 break-words">{{ $confirmedDelegate['first_name'] }}</div>
                        <div class="col-span-1 break-words">{{ $confirmedDelegate['last_name'] }}</div>
                        <div class="col-span-1 break-words">{{ $confirmedDelegate['name'] }}</div>
                        <div class="col-span-1 break-words">{{ $confirmedDelegate['job_title'] }}</div>
                        <div class="col-span-1 break-words">{{ $confirmedDelegate['company_name'] }}</div>
                        <div class="col-span-1 break-words">
                            @if ($confirmedDelegate['isDelegateAlreadyAdded'])
                                <button class="cursor-not-allowed bg-gray-400 py-1 px-6 rounded-md"
                                    disabled>Added</button>
                            @else
                                <button class="cursor-pointer bg-registrationPrimaryColor text-white py-1 px-6 rounded-md"
                                    wire:click.prevent="{{ "addDelegateConfirmation($index)" }}">Add</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
