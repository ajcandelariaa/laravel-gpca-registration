<div class="fixed z-10 inset-0 overflow-y-auto">
    <form>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-10 text-center sm:block sm:p-0">

            {{-- BACKDROP --}}
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            {{-- FOR CENTERING THE CONTENT --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

            <div class="w-full inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    @if ($currentOption == 'email')
                        <div>
                            <div class="text-registrationPrimaryColor text-xl">
                                Please enter your email address: <span class="text-red-500">*</span>
                            </div>

                            <div class="mt-2">
                                <input placeholder="janedoe@gmail.com" type="text" wire:model.lazy="inputtedData"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                                @error('inputtedData')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        Email address is required
                                    </div>
                                @enderror
                            </div>
                        </div>
                    @elseif ($currentOption == 'transactionId')
                        <div>
                            <div class="text-registrationPrimaryColor text-xl">
                                Please enter your transaction Id: <span class="text-red-500">*</span>
                            </div>

                            <div class="mt-2">
                                <input placeholder="2024123456" type="text" wire:model.lazy="inputtedData"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                                @error('inputtedData')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        Transaction ID is required
                                    </div>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div>
                            <div class="text-registrationPrimaryColor text-xl">
                                Please enter your name: <span class="text-red-500">*</span>
                            </div>

                            <div class="mt-2">
                                <input placeholder="Jane Doe" type="text" wire:model.lazy="inputtedData"
                                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                                @error('inputtedData')
                                    <div class="text-red-500 text-xs italic mt-1">
                                        Name is required
                                    </div>
                                @enderror
                            </div>
                        </div>
                    @endif

                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="searchClicked">Search</button>
                    <button type="button" wire:key="btnCancelEditDelegate"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="cancelClicked">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
