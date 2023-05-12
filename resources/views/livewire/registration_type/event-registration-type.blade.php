<div>
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>
    <div class="px-5">
        <div class="float-left">
            @if ($updateRegistrationType)
                @include('livewire.registration_type.edit')
            @else
                @include('livewire.registration_type.add')
            @endif
        </div>


        @if ($registrationTypes->isNotEmpty())
            <div class="shadow-lg my-5 pt-5 bg-white rounded-md" style="margin-left: 320px; ">
                <h1 class="text-center text-2xl text-white bg-registrationPrimaryColor py-2">Registration Types</h1>
                <div class="grid grid-cols-9 pt-4 pb-2 place-items-center text-center">
                    <div class="col-span-1">Registration type</div>
                    <div class="col-span-1">Badge footer <br> front name</div>
                    <div class="col-span-1">Badge footer <br> front bg color</div>
                    <div class="col-span-1">Badge footer <br> front text color</div>
                    <div class="col-span-1">Badge footer <br> back name</div>
                    <div class="col-span-1">Badge footer <br> back bg color</div>
                    <div class="col-span-1">Badge footer <br> back text color</div>
                    <div class="col-span-1">Status</div>
                    <div class="col-span-1">Action</div>
                </div>

                @php
                    $count = 1;
                @endphp

                @foreach ($registrationTypes as $registrationType)
                    <div
                        class="grid grid-cols-9 pt-2 pb-2 mb-1 place-items-center text-center {{ $count % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                        <div class="col-span-1">{{ $registrationType->registration_type }}</div>
                        <div class="col-span-1">{{ $registrationType->badge_footer_front_name }}</div>
                        <div class="col-span-1">{{ $registrationType->badge_footer_front_bg_color }}</div>
                        <div class="col-span-1">{{ $registrationType->badge_footer_front_text_color }}</div>
                        <div class="col-span-1">{{ $registrationType->badge_footer_back_name }}</div>
                        <div class="col-span-1">{{ $registrationType->badge_footer_back_bg_color }}</div>
                        <div class="col-span-1">{{ $registrationType->badge_footer_back_text_color }}</div>
                        <div class="col-span-1">
                            @if ($registrationType->active)
                                <button wire:click="updateStatus({{ $registrationType->id }}, {{ $registrationType->active }})"
                                    class="text-gray-700 bg-green-300 hover:bg-green-500 hover:text-white py-1 px-2 text-sm rounded-md">Active</button>
                            @else
                                <button wire:click="updateStatus({{ $registrationType->id }}, {{ $registrationType->active }})"
                                    class="text-gray-700 bg-red-300 hover:bg-red-500 hover:text-white py-1 px-2 text-sm rounded-md">Inactive</button>
                            @endif
                        </div>
                        <div class="col-span-1 flex flex-col text-left gap-1">
                            <div wire:click="showSampleBadge({{ $registrationType->id }})"
                                class="cursor-pointer hover:text-gray-600 text-gray-500">
                                <i class="fa-solid fa-eye"></i>
                                View
                            </div>
                            <div wire:click="showEditRegistrationType({{ $registrationType->id }})"
                                class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                                <i class="fa-solid fa-pen-to-square"></i>
                                Edit
                            </div>
                        </div>
                        @php
                            $count++;
                        @endphp
                    </div>
                @endforeach
            </div>

            @if ($badgeView)
                @include('livewire.registration_type.view_badge_modal')
            @endif
        @endif

        @if ($registrationTypes->isEmpty())
            <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md" style="margin-left: 320px; ">
                There are no registration types yet.
            </div>
        @endif
    </div>
</div>
