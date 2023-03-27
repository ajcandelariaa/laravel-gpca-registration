<div class="bg-adminEventDetailNavigationBGColor">
    <div class="container mx-auto py-2 px-5">
        <div class="flex justify-between">
            <div>
                <a href="{{ route('admin.event.view') }}" class="text-registrationPrimaryColor hover:underline"><i class="fa-solid fa-arrow-left text-sm"></i> Back</a>
            </div>
            <div class="text-white font-semibold flex items-center gap-10">
                <a href="{{ route('admin.event.detail.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                    class="{{ request()->is('admin/event/*/*/detail*') ? 'underline' : 'hover:underline' }} text-registrationPrimaryColor">Event Detail</a>
                <a href="{{ route('admin.event.promo-codes.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                    class="{{ request()->is('admin/event/*/*/promo-code*') ? 'underline' : 'hover:underline' }} text-registrationPrimaryColor">Promo Codes</a>
                <a href="{{ route('admin.event.registrants.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                    class="{{ request()->is('admin/event/*/*/registrant*') ? 'underline' : 'hover:underline' }} text-registrationPrimaryColor">Transactions</a>
                <a href="{{ route('admin.event.delegates.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                    class="{{ request()->is('admin/event/*/*/delegate*') ? 'underline' : 'hover:underline' }} text-registrationPrimaryColor">Delegates</a>
            </div>
        </div>
    </div>
</div>
