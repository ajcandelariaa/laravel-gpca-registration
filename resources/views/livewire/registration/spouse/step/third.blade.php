<div class="mx-5">
    <div>
        <div class="text-registrationPrimaryColor font-bold text-2xl">
            Payment details
        </div>

        @if ($paymentMethod == 'bankTransfer')
            <div class="mt-5 bg-registrationCardBGColor p-5 rounded-lg">
                <div class="text-registrationPrimaryColor font-bold text-lg">
                    Bank details
                </div>

                <div class="ml-5 text-black mt-2">
                    <p>In favor of: <strong>Gulf Petrochemicals & Chemicals Association</strong></p>
                    <p>Mashreq Bank</p>
                    <p>Riqa Branch, Deira, P.O. Box 5511, Dubai</p>
                    <p class="mt-5">USD Acct No. <strong>{{ $bankDetails['accountNumber'] }}</strong></p>
                    <p>IBAN No. <strong>{{ $bankDetails['ibanNumber'] }}</strong></p>
                    <p>Swift Code <strong>BOMLAEAD</strong></p>
                </div>
            </div>
        @endif

        @if ($paymentMethod == 'creditCard')
            @include('livewire.registration.spouse.step.third_credit_card')
        @endif
    </div>


    <div class="mt-10 flex justify-between gap-5">

        @if ($sessionId && $cardDetails && $paymentMethod == 'creditCard')
            <button type="button"
                class="hover:bg-registrationPrimaryColorHover font-bold bg-registrationPrimaryColor text-white w-52 rounded-md py-2"
                id="payButton">PAY</button>

            <button type="button" id="processingButton"
                class="font-bold bg-registrationPrimaryColor text-white w-52 rounded-md py-2 inline-flex items-center justify-center leading-6 ease-in-out duration-150 cursor-not-allowed"
                disabled="">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                PROCESSING...
            </button>
        @else
            <button type="submit" wire:key="btnSubmitBank"
                class="hover:bg-registrationPrimaryColorHover font-bold bg-registrationPrimaryColor text-white w-52 rounded-md py-2">SUBMIT</button>
        @endif
    </div>

    <div class="mt-10">
        <div class="text-registrationPrimaryColor font-bold text-lg">
            Terms and Conditions
        </div>

        <ul class="list-decimal ml-8 mt-5">
            <li>For any cancellation, please notify us within 7 days from the receipt of the invoice. Any
                cancellation made after 7 days shall not be accepted hence the invoice has to be settled. </li>
            <li class="mt-4">If any delegate is unable to attend, we will accept a substitute delegate at no extra
                cost. Please
                notify us in writing an email to: forumregistration@gpca.org.ae with the name, job title, email
                address and telephone number of both the registered and substitute delegate. </li>
            <li class="mt-4">Refund Policy
                <ul class="list-disc ml-4">
                    <li>If delegate/s cancelled their registration 31 days before the event, they will get a refund
                        of 75%
                        on the amount paid for the registration fee.</li>
                    <li>If delegate/s cancelled their registration less than 31 days before the event, NO refund
                        will be
                        given.</li>
                </ul>
            </li>
        </ul>
    </div>
</div>
