<div class="fixed z-20 inset-0 overflow-y-auto">
    <form>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-10 text-center sm:block sm:p-0">

            {{-- BACKDROP --}}
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            {{-- FOR CENTERING THE CONTENT --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

            <div class="w-full inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <p
                        class="text-registrationPrimaryColor text-2xl md:text-4xl text-center font-bold font-montserrat mt-5 md:mt-10">
                        No matching records found</p>

                    <p class="mt-5">We couldn’t find any registration details with the information you provided. Please check your entry and try again.</p>
                    <ul class="list-disc ml-5 mt-2">
                        <li>If you entered your email: Ensure you’re using the same email you registered with.</li>
                        <li>If you entered your transaction ID: Double-check for any typos or missing characters.</li>
                        <li>If you entered your name: Make sure you’re using the exact name as in your registration.</li>
                    </ul>
                    <p class="mt-4">
                        If you still encounter issues, please contact us at <a href="mailto:forumregistration@gpca.org.ae" class="underline text-blue-700">forumregistration@gpca.org.ae</a> for assistance.
                    </p>
                </div>
                <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                        wire:click.prevent="tryAgainClicked">Try again</button>
                </div>
            </div>
        </div>
    </form>
</div>
