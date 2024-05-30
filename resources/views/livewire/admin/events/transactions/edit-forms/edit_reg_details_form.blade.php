<div class="fixed z-10 inset-0 overflow-y-auto">
    <form>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div>
                        <div class="text-registrationPrimaryColor italic font-bold text-xl">
                            Registration Details
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
                            {{-- ROW 1 --}}
                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Access Type <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model.lazy="accessType"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        <option value="fullEvent">Full event</option>
                                        @if (
                                            $event->co_eb_full_member_rate != null ||
                                                $event->co_eb_member_rate != null ||
                                                $event->co_eb_nmember_rate != null ||
                                                $event->co_std_full_member_rate != null ||
                                                $event->co_std_member_rate != null ||
                                                $event->co_std_nmember_rate != null)
                                            <option value="conferenceOnly">Conference only</option>
                                        @endif
                                        @if (
                                            $event->wo_eb_full_member_rate != null ||
                                                $event->wo_eb_member_rate != null ||
                                                $event->wo_eb_nmember_rate != null ||
                                                $event->wo_std_full_member_rate != null ||
                                                $event->wo_std_member_rate != null ||
                                                $event->wo_std_nmember_rate != null)
                                            <option value="workshopOnly">Workshop only</option>
                                        @endif
                                    </select>

                                    @error('accessType')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            <div class="space-y-2">
                                <div class="text-registrationPrimaryColor">
                                    Rate Type <span class="text-red-500">*</span>
                                </div>
                                <div>
                                    <select wire:model.lazy="rateType"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        @if ($accessType == 'conferenceOnly')
                                            @if ($event->co_eb_full_member_rate != null || $event->co_eb_member_rate != null || $event->co_eb_nmember_rate != null)
                                                <option value="Early Bird">Early Bird</option>
                                            @endif

                                            @if (
                                                $event->co_std_full_member_rate != null ||
                                                    $event->co_std_member_rate != null ||
                                                    $event->co_std_nmember_rate != null)
                                                <option value="Standard">Standard</option>
                                            @endif
                                        @elseif ($accessType == 'workshopOnly')
                                            @if ($event->wo_eb_full_member_rate != null || $event->wo_eb_member_rate != null || $event->wo_eb_nmember_rate != null)
                                                <option value="Early Bird">Early Bird</option>
                                            @endif

                                            @if (
                                                $event->wo_std_full_member_rate != null ||
                                                    $event->wo_std_member_rate != null ||
                                                    $event->wo_std_nmember_rate != null)
                                                <option value="Standard">Standard</option>
                                            @endif
                                        @else
                                            @if ($event->eb_full_member_rate != null || $event->eb_member_rate != null || $event->eb_nmember_rate != null)
                                                <option value="Early Bird">Early Bird</option>
                                            @endif

                                            @if ($event->std_full_member_rate != null || $event->std_member_rate != null || $event->std_nmember_rate != null)
                                                <option value="Standard">Standard</option>
                                            @endif
                                        @endif
                                    </select>

                                    @error('rateType')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:key="btnUpdateRegistration"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="updateRegistrationDetails">Update</button>
                    <button type="button" wire:key="btnCancelEditRegistration"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="closeEditRegistrationDetailsModal">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
