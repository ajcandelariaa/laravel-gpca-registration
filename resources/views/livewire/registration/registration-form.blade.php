<div class="container mx-auto my-10">

    <div class="xl:float-left mx-5">
        @include('livewire.registration.progress.progress_bar_vertical')
        @include('livewire.registration.progress.progress_bar_horizontal')
    </div>

    <div class="form-container mt-10 xl:mt-0">
        <form wire:submit.prevent="submit">
            @if ($currentStep == 1)
                @include('livewire.registration.step.first')
            @elseif ($currentStep == 2)
                @include('livewire.registration.step.second')
            @elseif ($currentStep == 3)
                @include('livewire.registration.step.third')
            @elseif ($currentStep == 4)
                @include('livewire.registration.step.fourth')
            @else
                @include('livewire.registration.step.fifth')
            @endif

            <div class="mt-10 mx-5 flex justify-between gap-5">
                @if ($currentStep == 1)
                    <div></div>
                @endif
                @if ($currentStep == 2 || $currentStep == 3)
                    <button type="button" wire:key="btnDecreaseStep"
                        class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2"
                        wire:click.prevent="decreaseStep">PREVIOUS</button>
                @endif
                @if ($currentStep == 1 || $currentStep == 2 || $currentStep == 3)
                    <button type="button" wire:key="btnIncreaseStep"
                        class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2"
                        wire:click.prevent="increaseStep">NEXT</button>
                @endif
            </div>
        </form>
    </div>

    <script src="https://ap-gateway.mastercard.com/form/version/70/merchant/{{ env('MERCHANT_ID') }}/session.js"></script>

    @if ($sessionId && $cardDetails)
        <script>
            $(document).ready(function() {
                let sessionId = "{{ $sessionId }}";

                let payButton = document.getElementById('payButton');
                let processingButton = document.getElementById('processingButton');
                let registrationLloadingScreen2 = document.getElementById('registration-loading-screen-2');

                processingButton.classList.add('hidden');

                let cardNumberError = true;
                let expiryMonthError = true;
                let expiryYearError = true;
                let securityCodeError = true;
                let securityCodeErrorEmpty = true;
                let cardNameErrorEmpty = true;

                let cardNumberErrMessage = document.getElementById('card-number-error');
                let cardMonthErrMessage = document.getElementById('card-month-error');
                let cardYearErrMessage = document.getElementById('card-year-error');
                let cardSecurityErrMessage = document.getElementById('card-security-error');
                let cardNameErrMessage = document.getElementById('card-name-error');

                cardNameErrMessage.textContent = "Cardholder Name is required";
                cardSecurityErrMessage.textContent = "Security Code is required";


                PaymentSession.configure({
                    session: sessionId,
                    merchantId: "{{ env('MERCHANT_ID') }}",
                    apiKey: "{{ env('MERCHANT_AUTH_PASSWORD') }}",
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
                                    showBorderGreen('#card-number');
                                    cardNumberErrMessage.classList.remove("block");
                                    cardNumberErrMessage.classList.add("hidden");

                                    showBorderGreen('#expiry-month');
                                    cardMonthErrMessage.classList.remove("block");
                                    cardMonthErrMessage.classList.add("hidden");

                                    showBorderGreen('#expiry-year');
                                    cardYearErrMessage.classList.remove("block");
                                    cardYearErrMessage.classList.add("hidden");


                                    if (securityCodeErrorEmpty || cardNameErrorEmpty) {
                                        if (securityCodeErrorEmpty) {
                                            showBorderRed('#security-code');
                                            cardSecurityErrMessage.classList.add("block");
                                            cardSecurityErrMessage.classList.remove("hidden");
                                        }

                                        if (cardNameErrorEmpty) {
                                            showBorderRed('#cardholder-name');
                                            cardNameErrMessage.classList.add("block");
                                            cardNameErrMessage.classList.remove("hidden");
                                        }
                                        removeLoading();
                                    } else {
                                        console.log("Session updated with data: " + response.session.id);

                                        showBorderGreen('#security-code');
                                        cardSecurityErrMessage.classList.remove("block");
                                        cardSecurityErrMessage.classList.add("hidden");

                                        showBorderGreen('#cardholder-name');
                                        cardNameErrMessage.classList.remove("block");
                                        cardNameErrMessage.classList.add("hidden");

                                        window.Livewire.emit('emitInitiateAuth');
                                    }
                                } else if ("fields_in_error" == response.status) {
                                    console.log("Session update failed with field errors.");
                                    removeLoading();
                                    if (securityCodeErrorEmpty) {
                                        console.log("Security code required.");
                                        cardSecurityErrMessage.textContent = "Security Code is required";
                                        showBorderRed('#security-code');
                                        cardSecurityErrMessage.classList.add('block');
                                        cardSecurityErrMessage.classList.remove('hidden');
                                    } else {
                                        if (response.errors.securityCode) {
                                            console.log("Security code invalid.");
                                            securityCodeError = true;
                                            securityCodeErrorEmpty = false;
                                            cardSecurityErrMessage.textContent = "Security Code is invalid";
                                            showBorderRed('#security-code');
                                            cardSecurityErrMessage.classList.add('block');
                                            cardSecurityErrMessage.classList.remove('hidden');
                                        } else {
                                            securityCodeError = false;
                                            securityCodeErrorEmpty = false;
                                            cardSecurityErrMessage.textContent = "";
                                            showBorderGreen('#security-code');
                                            cardSecurityErrMessage.classList.remove('block');
                                            cardSecurityErrMessage.classList.add('hidden');
                                        }
                                    }

                                    if (cardNameErrorEmpty) {
                                        console.log("Cardholder name required.");
                                        cardNameErrMessage.textContent = "Cardholder Name is required";
                                        showBorderRed('#cardholder-name');
                                        cardNameErrMessage.classList.add('block');
                                        cardNameErrMessage.classList.remove('hidden');
                                    } else {
                                        cardNameErrorEmpty = false;
                                        cardNameErrMessage.textContent = "";
                                        showBorderGreen('#cardholder-name');
                                        cardNameErrMessage.classList.remove('block');
                                        cardNameErrMessage.classList.add('hidden');
                                    }


                                    if (response.errors.cardNumber) {
                                        console.log("Card number invalid or missing.");

                                        cardNumberError = true;

                                        if (response.errors.cardNumber == "missing") {
                                            cardNumberErrMessage.textContent = "Card Number is required";
                                        } else if (response.errors.cardNumber == "invalid") {
                                            cardNumberErrMessage.textContent = "Card Number is invalid";
                                        } else {
                                            cardNumberErrMessage.textContent =
                                                "Card Number invalid or missing";
                                        }

                                        showBorderRed('#card-number');
                                        cardNumberErrMessage.classList.add('block');
                                        cardNumberErrMessage.classList.remove('hidden');
                                    } else {
                                        cardNumberError = false;
                                        cardNumberErrMessage.textContent = "";
                                        showBorderGreen('#card-number');
                                        cardNumberErrMessage.classList.remove('block');
                                        cardNumberErrMessage.classList.add('hidden');
                                    }

                                    if (response.errors.expiryYear) {
                                        console.log("Expiry year invalid or missing.");

                                        expiryYearError = true;

                                        if (response.errors.expiryYear == "missing") {
                                            cardYearErrMessage.textContent = "Expiry Year is required";
                                        } else if (response.errors.expiryYear == "invalid") {
                                            cardYearErrMessage.textContent = "Expiry Year is invalid";
                                        } else {
                                            cardYearErrMessage.textContent =
                                                "Expiry Year invalid or missing";
                                        }

                                        showBorderRed('#expiry-year');
                                        cardYearErrMessage.classList.add('block');
                                        cardYearErrMessage.classList.remove('hidden');
                                    } else {
                                        expiryYearError = false;
                                        cardYearErrMessage.textContent = "";
                                        showBorderGreen('#expiry-year');
                                        cardYearErrMessage.classList.remove('block');
                                        cardYearErrMessage.classList.add('hidden');
                                    }


                                    if (response.errors.expiryMonth) {
                                        console.log("Expiry month invalid or missing.");

                                        expiryMonthError = true;

                                        if (response.errors.expiryMonth == "missing") {
                                            cardMonthErrMessage.textContent = "Expiry Month is required";
                                        } else if (response.errors.expiryMonth == "invalid") {
                                            cardMonthErrMessage.textContent = "Expiry Month is invalid";
                                        } else {
                                            cardMonthErrMessage.textContent =
                                                "Expiry Month invalid or missing";
                                        }
                                        showBorderRed('#expiry-month');
                                        cardMonthErrMessage.classList.add('block');
                                        cardMonthErrMessage.classList.remove('hidden');
                                    } else {
                                        expiryMonthError = false;
                                        cardMonthErrMessage.textContent = "";
                                        showBorderGreen('#expiry-month');
                                        cardMonthErrMessage.classList.remove('block');
                                        cardMonthErrMessage.classList.add('hidden');
                                    }
                                } else if ("request_timeout" == response.status) {
                                    console.log("Session update failed with request timeout: " + response
                                        .errors
                                        .message);
                                    removeLoading();
                                } else if ("system_error" == response.status) {
                                    console.log("Session update failed with system error: " + response
                                        .errors.message);
                                    removeLoading();
                                }
                            } else {
                                console.log("Session update failed: " + response);
                                removeLoading();
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

                PaymentSession.onValidityChange(["card.number", "card.securityCode", "card.expiryYear",
                        "card.expiryMonth"
                    ],
                    function(selector, result) {
                        if (result.isValid) {
                            showBorderGreen(selector);
                        } else {
                            showBorderRed(selector);
                        }
                    });

                PaymentSession.onEmptinessChange(["card.nameOnCard", "card.securityCode"], function(selector, result) {
                    if (selector == "#cardholder-name") {
                        if (result.isEmpty) {
                            cardNameErrorEmpty = true;
                            showBorderRed(selector);
                        } else {
                            cardNameErrorEmpty = false;
                            showBorderGreen(selector);
                        }
                    } else {
                        if (result.isEmpty) {
                            securityCodeErrorEmpty = true;
                            showBorderRed(selector);
                        } else {
                            securityCodeErrorEmpty = false;
                            showBorderGreen(selector);
                        }
                    }
                });


                function showBorderRed(inputSelector) {
                    if (!document.querySelector(inputSelector).classList.contains('cc-show-red-border')) {
                        document.querySelector(inputSelector).classList.add('cc-show-red-border');
                    }

                    if (document.querySelector(inputSelector).classList.contains('cc-show-green-border')) {
                        document.querySelector(inputSelector).classList.remove('cc-show-green-border');
                    }
                }

                function showBorderGreen(inputSelector) {
                    if (!document.querySelector(inputSelector).classList.contains('cc-show-green-border')) {
                        document.querySelector(inputSelector).classList.add('cc-show-green-border');
                    }

                    if (document.querySelector(inputSelector).classList.contains('cc-show-red-border')) {
                        document.querySelector(inputSelector).classList.remove('cc-show-red-border');
                    }
                }

                function showLoading() {
                    processingButton.classList.remove('hidden');
                    payButton.classList.add('hidden');
                    registrationLloadingScreen2.classList.remove('hidden');
                }

                function removeLoading() {
                    processingButton.classList.add('hidden');
                    payButton.classList.remove('hidden');
                    registrationLloadingScreen2.classList.add('hidden');
                }

                function pay() {
                    console.log('clicked');
                    showLoading();
                    PaymentSession.updateSessionFromForm('card');
                }

                $('#payButton').click(function() {
                    pay();
                });
            });
        </script>
    @endif

    {{-- <script>
        document.addEventListener('livewire:load', function() {
            Livewire.on('stepChanges', function() {
                window.scrollTo(0, 0);
            });
        });
    </script> --}}
</div>
