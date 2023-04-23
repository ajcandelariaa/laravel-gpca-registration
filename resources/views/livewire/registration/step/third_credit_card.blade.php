<div class="mt-5 bg-registrationCardBGColor p-5 rounded-lg" >
    <div class="text-registrationPrimaryColor font-bold text-lg">
        Credit Card Details
    </div>

    @if ($sessionId && $cardDetails)
        <div class="mt-5 grid grid-cols-3 gap-y-3 gap-x-5" wire:ignore>
            {{-- ROW 1 --}}
            <div class="space-y-2 col-span-3">
                <div class="text-registrationPrimaryColor">
                    Name on Card <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" id="cardholder-name" class="input-field" title="cardholder name"
                        aria-label="enter name on card" value="" tabindex="1" placeholder="JOHN DOE"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor"
                        readonly>
                    {{-- <input placeholder="AJ CANDELARIA" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor"> --}}
                </div>
            </div>

            {{-- ROW 2 --}}
            <div class="space-y-2 col-span-3">
                <div class="text-registrationPrimaryColor">
                    Card Number <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" id="card-number" class="input-field" title="card number"
                        aria-label="enter your card number" value="" tabindex="2"
                        placeholder="xxxx-xxxx-xxxx-xxxx"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor"
                        readonly>
                    {{-- <input placeholder="xxxx-xxxx-xxxx-xxxx" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor"> --}}
                </div>
            </div>

            {{-- ROW 3 --}}
            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Expiration Month <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" id="expiry-month" class="input-field" title="expiry month"
                        aria-label="two digit expiry month" value="" tabindex="3" placeholder="mm"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor"
                        readonly>
                    {{-- <input placeholder="mm" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor"> --}}
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Expiration Year <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" id="expiry-year" class="input-field" title="expiry year"
                        aria-label="two digit expiry year" value="" tabindex="4" placeholder="yy"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor"
                        readonly>
                    {{-- <input placeholder="yyyy" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor"> --}}
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    CVC <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" id="security-code" class="input-field" title="security code"
                        aria-label="three digit CCV security code" value="" placeholder="xxx" tabindex="4"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor"
                        readonly>
                    {{-- <input placeholder="xxx" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor"> --}}
                </div>
            </div>
        </div>


        <script>
            let sessionId = "{{ $sessionId }}";
            console.log(sessionId);
            PaymentSession.configure({
                session: sessionId,
                merchantId: 'TEST900755',
                apiKey: '3b41414705a08d0fa159a77316aba3b3',
                fields: {
                    card: {
                        number: "#card-number",
                        securityCode: "#security-code",
                        expiryMonth: "#expiry-month",
                        expiryYear: "#expiry-year",
                        nameOnCard: "#cardholder-name"
                    },
                },
                frameEmbeddingMitigation: ["javascript"],
                callbacks: {
                    initialized: function(response) {
                        console.log(response);
                    },
                    formSessionUpdate: function(response) {
                        if (response.status) {
                            if ("ok" == response.status) {
                                console.log("Session updated with data: " + response.session.id);

                                if (response.sourceOfFunds.provided.card.securityCode) {
                                    console.log("Security code was provided.");
                                }

                                if (response.sourceOfFunds.provided.card.scheme == 'MASTERCARD') {
                                    console.log("The user entered a Mastercard credit card.")
                                }
                                // window.location.replace("http://127.0.0.1:8000/initiateAuthentication");
                            } else if ("fields_in_error" == response.status) {
                                console.log("Session update failed with field errors.");
                                if (response.errors.cardNumber) {
                                    console.log("Card number invalid or missing.");
                                }
                                if (response.errors.expiryYear) {
                                    console.log("Expiry year invalid or missing.");
                                }
                                if (response.errors.expiryMonth) {
                                    console.log("Expiry month invalid or missing.");
                                }
                                if (response.errors.securityCode) {
                                    console.log("Security code invalid.");
                                }
                            } else if ("request_timeout" == response.status) {
                                console.log("Session update failed with request timeout: " + response.errors
                                    .message);
                            } else if ("system_error" == response.status) {
                                console.log("Session update failed with system error: " + response.errors.message);
                            }
                        } else {
                            console.log("Session update failed: " + response);
                        }
                    }
                },
                interaction: {
                    displayControl: {
                        formatCard: "EMBOSSED",
                        invalidFieldCharacters: "REJECT"
                    }
                }
            });

            function pay() {
                PaymentSession.updateSessionFromForm('card');
                console.log('clicked');
            }
        </script>
    @endif
</div>
