<div class="bg-adminEventDetailNavigationBGColor">
    <div class="container mx-auto py-2 px-5">
        <div class="flex justify-between">
            <div>
                <a href="{{ route('admin.event.view') }}" class="text-registrationPrimaryColor hover:underline"><i
                        class="fa-solid fa-arrow-left text-sm"></i> Back</a>
            </div>
            <div class="text-white font-semibold flex items-center gap-10">
                <a href="{{ route('admin.event.dashboard.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                    class="{{ request()->is('admin/event/*/*/dashboard*') ? 'underline' : 'hover:underline' }} text-registrationPrimaryColor">Event
                    Dashboard</a>

                <a href="{{ route('admin.event.detail.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                    class="{{ request()->is('admin/event/*/*/detail*') ? 'underline' : 'hover:underline' }} text-registrationPrimaryColor">Event
                    Detail</a>

                @if ($eventCategory != 'AFS' && $eventCategory != 'RCCA')
                    <a href="{{ route('admin.event.registration-type.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                        class="{{ request()->is('admin/event/*/*/registration-type*') ? 'underline' : 'hover:underline' }} text-registrationPrimaryColor">Registration
                        Type</a>
                        
                    <a href="{{ route('admin.event.delegate-fees.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                        class="{{ request()->is('admin/event/*/*/delegate-fees*') ? 'underline' : 'hover:underline' }} text-registrationPrimaryColor">Delegate
                        Fees</a>

                    <a href="{{ route('admin.event.promo-codes.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                        class="{{ request()->is('admin/event/*/*/promo-code*') ? 'underline' : 'hover:underline' }} text-registrationPrimaryColor">Promo
                        Codes</a>
                @endif


                <a href="{{ route('admin.event.registrants.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                    class="{{ request()->is('admin/event/*/*/registrant*') ? 'underline' : 'hover:underline' }} text-registrationPrimaryColor">Transactions</a>

                @if ($eventCategory != 'AFS' && $eventCategory != 'RCCA')
                    <a href="{{ route('admin.event.delegates.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}"
                        class="{{ request()->is('admin/event/*/*/delegate*') ? 'underline' : 'hover:underline' }} text-registrationPrimaryColor">Delegates</a>
                @endif
            </div>
        </div>
    </div>
</div>
