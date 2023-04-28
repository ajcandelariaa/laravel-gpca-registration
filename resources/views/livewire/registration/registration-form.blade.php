<div class="container mx-auto my-10">

    <div class="xl:float-left mx-5">
        @include('livewire.registration.progress_bar_vertical')
        @include('livewire.registration.progress_bar_horizontal')
    </div>

    <div class="form-container mt-10 xl:mt-0">
        <form wire:submit.prevent="submit">
            @if ($currentStep == 1)
                @include('livewire.registration.step.first')
            @elseif ($currentStep == 2)
                @include('livewire.registration.step.second')
            @elseif ($currentStep == 3)
                @include('livewire.registration.step.third')
            @else
                @include('livewire.registration.step.fourth')
            @endif

            <div class="w-full mt-20 mx-5 flex justify-between gap-5">
                @if ($currentStep == 1)
                    <div></div>
                @endif
                @if ($currentStep == 2 || $currentStep == 3)
                    <button type="button" wire:key="btnDecreaseStep"
                        class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2"
                        wire:click.prevent="decreaseStep">PREVIOUS</button>
                @endif
                @if ($currentStep == 1 || $currentStep == 2)
                    <button type="button" wire:key="btnIncreaseStep"
                        class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2"
                        wire:click.prevent="increaseStep">NEXT</button>
                @endif
                @if ($currentStep == 3)
                    <button type="button" wire:key="btnIncreaseStep"
                        class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2"
                        wire:click.prevent="increaseStep">REGISTER</button>
                @endif
                @if ($currentStep == 4)
                    @if ($sessionId && $cardDetails && $paymentMethod == "creditCard")
                        <button type="button"
                            class="hover:bg-registrationPrimaryColorHover font-bold bg-registrationPrimaryColor text-white w-52 rounded-md py-2"
                            id="payButton">PAY</button>
                    @else
                        <button type="submit" wire:key="btnSubmitBank"
                            class="hover:bg-registrationPrimaryColorHover font-bold bg-registrationPrimaryColor text-white w-52 rounded-md py-2">PAY</button>
                    @endif
                @endif
            </div>
        </form>
    </div>

    <script src="https://ap-gateway.mastercard.com/form/version/70/merchant/{{ env('MERCHANT_ID') }}/session.js"></script>

    @if ($sessionId && $cardDetails)
        <script>
            $(document).ready(function() {
                let sessionId = "{{ $sessionId }}";
                console.log(sessionId);
                console.log("Test")
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
                                    console.log("Session updated with data: " + response.session.id);

                                    if (response.sourceOfFunds.provided.card.securityCode) {
                                        console.log("Security code was provided.");
                                    }

                                    if (response.sourceOfFunds.provided.card.scheme == 'MASTERCARD') {
                                        console.log("The user entered a Mastercard credit card.")
                                    }
                                    window.Livewire.emit('emitInitiateAuth');
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
                                    console.log("Session update failed with request timeout: " + response
                                        .errors
                                        .message);
                                } else if ("system_error" == response.status) {
                                    console.log("Session update failed with system error: " + response
                                        .errors.message);
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
                    console.log('clicked');
                    PaymentSession.updateSessionFromForm('card');
                }

                $('#payButton').click(function() {
                    pay();
                });
            });
        </script>
    @endif

    <script>
        document.addEventListener('livewire:load', function() {
            Livewire.on('stepChanges', function() {
                window.scrollTo(0, 0);
            });
        });
    </script>
</div>
