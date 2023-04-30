<div class="mx-5">
    <div wire:loading>
        @include('livewire.loading.loading_screen')
    </div>

    <div>
        <div class="text-registrationPrimaryColor font-bold text-2xl">
            Payment Details
        </div>

        @if ($paymentMethod == 'bankTransfer')
            <div class="mt-5 bg-registrationCardBGColor p-5 rounded-lg">
                <div class="text-registrationPrimaryColor font-bold text-lg">
                    Bank Details
                </div>

                <div class="ml-5 text-black mt-2">
                    <p>In favour of: Gulf Petrochemicals & Chemicals Association Mashreq Bank Riqqa Branch, Deira, P.O.
                        Box
                        5511, Dubai, UAE</p>
                    <p class="mt-5">USD Acct No. <strong>{{ $bankDetails['accountNumber'] }}</strong></p>
                    <p>IBAN No. <strong>{{ $bankDetails['ibanNumber'] }}</strong></p>
                    <p>Swift Code <strong>BOMLAEAD</strong></p>
                </div>
            </div>
        @endif

        @if ($paymentMethod == 'creditCard')
            @include('livewire.registration.step.fifth_credit_card')
        @endif
    </div>


    <div class="mt-10 flex justify-between gap-5">
        @if ($sessionId && $cardDetails && $paymentMethod == 'creditCard')
            <button type="button"
                class="hover:bg-registrationPrimaryColorHover font-bold bg-registrationPrimaryColor text-white w-52 rounded-md py-2"
                id="payButton">PAY</button>
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
