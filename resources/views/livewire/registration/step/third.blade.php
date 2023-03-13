<div>
    <div>
        <div class="text-registrationPrimaryColor font-bold text-2xl">
            Package Summary
        </div>

        <div class="italic mt-5">
            By registering your details, you understand that your personal data will be handled according to <a
                href="" class="text-registrationPrimaryColor underline ">GPCA Privacy Policy</a>
        </div>
    </div>

    <div class="mt-5">
        <div class="grid grid-cols-addDelegateGrid gap-y-2">
            <div class="text-registrationPrimaryColor">
                Invoice to be sent to:
            </div>

            <div>
                Albert Joseph M. Candelaria
            </div>

            <div class="text-registrationPrimaryColor">
                Email Address:
            </div>

            <div>
                ajcandelaria@gmail.com
            </div>

            <div class="text-registrationPrimaryColor col-span-2">
                Payment method:
            </div>
        </div>

        <div class="mt-5 flex gap-5">
            <button wire:click="btClicked()"
                class="{{ $paymentMethod == 'bankTransfer' ? 'bg-registrationSecondaryColor text-white' : 'hover:bg-registrationSecondaryColor hover:text-white border-registrationSecondaryColor border-2 bg-white text-registrationSecondaryColor' }} font-bold w-52 rounded-md py-5 ">
                <i class="fa-solid fa-building-columns mr-2"></i> Bank Transfer</button>
            <button wire:click="ccClicked()"
                class="{{ $paymentMethod == 'creditCard' ? 'bg-registrationSecondaryColor text-white' : 'hover:bg-registrationSecondaryColor hover:text-white border-registrationSecondaryColor border-2 bg-white text-registrationSecondaryColor' }} font-bold w-52 rounded-md py-5 ">
                <i class="fa-solid fa-credit-card mr-2"></i> Credit Card</button>
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
                    <p class="mt-5">USD Acct No. <strong>0190-00-05007-7</strong></p>
                    <p>IBAN No. <strong>AE360330000019000050077</strong></p>
                    <p>Swift Code <strong>BOMLAEAD</strong></p>
                </div>
            </div>
        @endif

        @if ($paymentMethod == 'creditCard')
            <div class="mt-5 bg-registrationCardBGColor p-5 rounded-lg">
                <div class="text-registrationPrimaryColor font-bold text-lg">
                    Credit Card Details
                </div>

                <div class="mt-5 grid grid-cols-3 gap-y-3 gap-x-5">

                    {{-- ROW 1 --}}
                    <div class="space-y-2 col-span-3">
                        <div class="text-registrationPrimaryColor">
                            Name on Card <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input placeholder="AJ CANDELARIA" type="text" name="" id=""
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        </div>
                    </div>

                    {{-- ROW 2 --}}
                    <div class="space-y-2 col-span-3">
                        <div class="text-registrationPrimaryColor">
                            Card Number <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input placeholder="xxxx-xxxx-xxxx-xxxx" type="text" name="" id=""
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        </div>
                    </div>

                    {{-- ROW 3 --}}
                    <div class="space-y-2">
                        <div class="text-registrationPrimaryColor">
                            Expiration Month <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input placeholder="mm" type="text" name="" id=""
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="text-registrationPrimaryColor">
                            Expiration Year <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input placeholder="yyyy" type="text" name="" id=""
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="text-registrationPrimaryColor">
                            CVC <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input placeholder="xxx" type="text" name="" id=""
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="mt-5">
        <div class="bg-registrationInputFieldsBGColor p-2">
            <div class="grid grid-cols-5 text-center font-bold text-registrationPrimaryColor text-lg pt-2 pb-4">
                <div class="col-span-2">
                    <p>Description</p>
                </div>

                <div class="col-span-1">
                    <p>Qty</p>
                </div>

                <div class="col-span-1">
                    <p>Unit price</p>
                </div>

                <div class="col-span-1">
                    <p>Net amount</p>
                </div>
            </div>

            <div class="grid grid-cols-5 gap-2">
                <div class="col-span-2 bg-white p-4">
                    <p>15th Annual GPCA Forum – February 10-11, 2021 at Madinat Jumeirah Hotel, Dubai, UAE</p>
                    <p class="mt-5">Delegate Registration Fee – EB Member Rate </p>
                    <ul class="mt-2 list-decimal ml-4">
                        <li>Aj Candelaria</li>
                        <li>Wesam Issa</li>
                    </ul>
                </div>

                <div class="col-span-1 bg-white p-4 flex justify-center items-center">
                    <p>3</p>
                </div>

                <div class="col-span-1 bg-white p-4 flex justify-center items-center">
                    <p>$ 1,500.00</p>
                </div>

                <div class="col-span-1 bg-white p-4 flex justify-center items-center">
                    <p>$ 4,500.00</p>
                </div>
            </div>

            <div class="grid grid-cols-5 gap-2 mt-2">
                <div class="col-span-4 bg-white p-4">
                    <p>Total (before VAT)</p>
                </div>

                <div class="col-span-1 bg-white p-4 text-right">
                    <p>$ 4,500.00</p>
                </div>

                <div class="col-span-4 bg-white p-4">
                    <p>VAT @5%</p>
                </div>

                <div class="col-span-1 bg-white p-4 text-right">
                    <p>$ 225.00</p>
                </div>

                <div class="col-span-4 bg-white p-4 font-bold">
                    <p>TOTAL</p>
                </div>

                <div class="col-span-1 bg-white p-4 text-right font-bold">
                    <p>$ 4,725.00</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
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
