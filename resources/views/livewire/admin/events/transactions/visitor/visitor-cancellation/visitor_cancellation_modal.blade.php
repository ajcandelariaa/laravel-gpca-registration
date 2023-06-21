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
                    <div class="text-registrationPrimaryColor italic font-bold text-xl">
                        Visitor cancellation
                    </div>

                    @if ($visitorCancellationStep == 1)
                        <div class="mt-4">
                            <div class="text-registrationPrimaryColor">
                                Do you want to replace it with a new visitor? <span class="text-red-500">*</span>
                            </div>
                            <div class="mt-3">
                                <select wire:model.lazy="replaceVisitor"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>
                    @endif

                    @if ($visitorCancellationStep == 2)
                        @if ($replaceVisitor == 'No')
                            <div class="mt-4">
                                <div class="text-registrationPrimaryColor">
                                    Is this gonna be a refund? <span class="text-red-500">*</span>
                                </div>
                                <div class="mt-3">
                                    <select wire:model.lazy="visitorRefund"
                                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                        <option value=""></option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    @error('visitorRefund')
                                        <div class="text-red-500 text-xs italic mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        @else
                            @include('livewire.admin.events.transactions.visitor.visitor-cancellation.visitor_replace_form')
                        @endif
                    @endif
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse mt-3">
                    @if ($visitorCancellationStep == 1)
                        <button type="button" wire:key="btnVisitorCancellationNext"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                            wire:click.prevent="nextVisitorCancellation">Next</button>
                    @endif

                    @if ($visitorCancellationStep == 2)
                        <button type="button" wire:key="btnVisitorCancellationSubmit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                            wire:click.prevent="submitVisitorCancellation">Submit</button>
                        <button type="button" wire:key="btnVisitorCancellationPrev"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            wire:click.prevent="prevVisitorCancellation">Previous</button>
                    @endif
                    <button type="button" wire:key="btnCancelVisitorCancellation"
                        class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="closeVisitorCancellationModal">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
