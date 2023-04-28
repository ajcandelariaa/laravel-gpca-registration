<div class="mt-5 bg-registrationCardBGColor p-5 rounded-lg">
    <div class="text-registrationPrimaryColor font-bold text-lg">
        Credit Card Details
    </div>

    @if ($sessionId && $cardDetails)
        <div class="mt-5 grid grid-cols-3 gap-y-3 gap-x-5 cc-form" wire:ignore>
            {{-- ROW 1 --}}
            <div class="space-y-2 col-span-3">
                <div class="text-registrationPrimaryColor">
                    Name on Card <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" id="cardholder-name"
                        class="input-field bg-registrationInputFieldsBGColor w-full py-1 px-3" title="cardholder name"
                        aria-label="enter name on card" value="" tabindex="1" placeholder="JOHN DOE" readonly>
                    <span id="card-name-error" class="text-red-600 italic text-sm mt-2 hidden"></span>
                </div>
            </div>

            {{-- ROW 2 --}}
            <div class="space-y-2 col-span-3">
                <div class="text-registrationPrimaryColor">
                    Card Number <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" id="card-number"
                        class="input-field bg-registrationInputFieldsBGColor w-full py-1 px-3" title="card number"
                        aria-label="enter your card number" value="" tabindex="2"
                        placeholder="xxxx-xxxx-xxxx-xxxx" readonly>
                    <span id="card-number-error" class="text-red-600 italic text-sm mt-2 hidden"></span>
                </div>
            </div>

            {{-- ROW 3 --}}
            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Expiration Month <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" id="expiry-month"
                        class="input-field bg-registrationInputFieldsBGColor w-full py-1 px-3" title="expiry month"
                        aria-label="two digit expiry month" value="" tabindex="3" placeholder="mm" readonly>
                    <span id="card-month-error" class="text-red-600 italic text-sm mt-2 hidden"></span>
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Expiration Year <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" id="expiry-year"
                        class="input-field bg-registrationInputFieldsBGColor w-full py-1 px-3" title="expiry year"
                        aria-label="two digit expiry year" value="" tabindex="4" placeholder="yy" readonly>
                    <span id="card-year-error" class="text-red-600 italic text-sm mt-2 hidden"></span>
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    CVC <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" id="security-code"
                        class="input-field bg-registrationInputFieldsBGColor w-full py-1 px-3" title="security code"
                        aria-label="three digit CCV security code" value="" placeholder="xxx" tabindex="4"
                        readonly>
                    <span id="card-security-error" class="text-red-600 italic text-sm mt-2 hidden"></span>
                </div>
            </div>
        </div>
    @endif
</div>
