<div class="fixed inset-0 z-10 flex items-center justify-center">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="bg-white rounded py-4 px-10 shadow-lg z-20" style="height: 600px;">
        <div class="bg-gray-50 flex sm:flex-row-reverse">
            <button type="button" wire:click="closeSampleBadge" wire:key="closeSampleBadge" class="text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i> Close
            </button>
        </div>

        <div class="flex gap-10">
            <div style="width: 321px; height: 492px;">
                <div class="border border-black mt-5 flex flex-col justify-between h-full">
                    <div>
                        <img src="{{ Storage::url($event->badge_front_banner) }}">
                    </div>
                    <div>
                        <p class="text-center font-bold text-lg">Aj Candelaria</p>
                        <p class="text-center italic mt-3">IT Coordinator</p>
                        <p class="text-center font-bold">GPCA</p>
                    </div>
                    <div>
                        <p class="text-center py-4 font-bold"
                            style="color: {{ $badgeViewFFTextColor }}; background-color: {{ $badgeViewFFBGColor }}">
                            {{ $badgeViewFFText }}</p>
                    </div>
                </div>

                <div class="text-center mt-2">
                    <p>Front</p>
                </div>
            </div>

            <div style="width: 321px; height: 492px;">
                <div class="border border-black mt-5 flex flex-col justify-between h-full">
                    <div>
                        <img src="{{ Storage::url($event->badge_front_banner) }}">
                    </div>
                    <div>
                        <p class="text-center font-bold text-lg">Aj Candelaria</p>
                        <p class="text-center italic mt-3">IT Coordinator</p>
                        <p class="text-center font-bold">GPCA</p>
                    </div>
                    <div>
                        <p class="text-center py-4 font-bold"
                            style="color: {{ $badgeViewFBTextColor }}; background-color: {{ $badgeViewFBBGColor }}">
                            {{ $badgeViewFFText }}</p>
                    </div>
                </div>

                <div class="text-center mt-2">
                    <p>Back</p>
                </div>
            </div>
        </div>

    </div>
</div>
