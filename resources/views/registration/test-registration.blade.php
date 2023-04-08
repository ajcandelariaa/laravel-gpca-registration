<html>

<head>
    <!-- INCLUDE SESSION.JS JAVASCRIPT LIBRARY -->
    {{-- <script src="https://test-gateway.mastercard.com/form/version/71/merchant/TEST900755/session.js"></script> --}}
    <script src="https://ap-gateway.mastercard.com/form/version/70/merchant/TEST900755/session.js"></script>
    {{-- <script src="https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755/session/SESSION0002472380655J09639461I1"></script> --}}
    {{-- <script src="https://test-gateway.mastercard.com/form/version/71/merchant/TEST28102022/session.js"></script> --}}
    {{-- <script src="https://ap-gateway.mastercard.com/form/version/70/merchant/TEST900755/session.js"></script> --}}

    {{-- <script src="https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST28102022/session"></script> --}}
    <!-- APPLY CLICK-JACKING STYLING AND HIDE CONTENTS OF THE PAGE -->
    <style id="antiClickjack">
        body {
            display: none !important;
        }
    </style>
</head>

<body>

    <!-- CREATE THE HTML FOR THE PAYMENT PAGE -->

    <div>Please enter your payment details:</div>
    <h3>Credit Card</h3>
    {{-- <input type="hidden" id="session-id" value="{{ session('session_id') }}"> --}}
    <div>Card Number: <input type="text" id="card-number" class="input-field" title="card number"
            aria-label="enter your card number" value="" tabindex="1" readonly></div>
    <div>Expiry Month:<input type="text" id="expiry-month" class="input-field" title="expiry month"
            aria-label="two digit expiry month" value="" tabindex="2" readonly></div>
    <div>Expiry Year:<input type="text" id="expiry-year" class="input-field" title="expiry year"
            aria-label="two digit expiry year" value="" tabindex="3" readonly></div>
    <div>Security Code:<input type="text" id="security-code" class="input-field" title="security code"
            aria-label="three digit CCV security code" value="" tabindex="4" readonly></div>
    <div>Cardholder Name:<input type="text" id="cardholder-name" class="input-field" title="cardholder name"
            aria-label="enter name on card" value="" tabindex="5" readonly></div>
    <div><button id="payButton" onclick="pay('card');">Pay Now</button></div>

    <!-- JAVASCRIPT FRAME-BREAKER CODE TO PROVIDE PROTECTION AGAINST IFRAME CLICK-JACKING -->
    <script type="text/javascript">
        if (self === top) {
            var antiClickjack = document.getElementById("antiClickjack");
            antiClickjack.parentNode.removeChild(antiClickjack);
        } else {
            top.location = self.location;
        }

        // let sessionId = document.getElementById('session-id').value;

        PaymentSession.configure({
            // session: sessionId,
            merchantId: 'TEST900755',
            apiKey: '3b41414705a08d0fa159a77316aba3b3',
            fields: {
                // ATTACH HOSTED FIELDS TO YOUR PAYMENT PAGE FOR A CREDIT CARD
                card: {
                    number: "#card-number",
                    securityCode: "#security-code",
                    expiryMonth: "#expiry-month",
                    expiryYear: "#expiry-year",
                    nameOnCard: "#cardholder-name"
                },
                "order": {
                    "amount": 100.00,
                    "currency": "USD"
                }
            },
            //SPECIFY YOUR MITIGATION OPTION HERE
            frameEmbeddingMitigation: ["javascript"],
            callbacks: {
                initialized: function(response) {
                    // HANDLE INITIALIZATION RESPONSE
                    console.log(response);
                },
                formSessionUpdate: function(response) {
                    // HANDLE RESPONSE FOR UPDATE SESSION
                    if (response.status) {
                        if ("ok" == response.status) {
                            console.log("Session updated with data: " + response.session.id);

                            //check if the security code was provided by the user
                            if (response.sourceOfFunds.provided.card.securityCode) {
                                console.log("Security code was provided.");
                            }

                            //check if the user entered a Mastercard credit card
                            if (response.sourceOfFunds.provided.card.scheme == 'MASTERCARD') {
                                console.log("The user entered a Mastercard credit card.")
                            }
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
            // // UPDATE THE SESSION WITH THE INPUT FROM HOSTED FIELDS
            // PaymentSession.updateSessionFromForm('card');

            // Step 1: Update the session with the card details entered by the customer
            var updateSuccessful = PaymentSession.updateSessionFromForm('card');

            // Step 2: Submit the updated session to the API for processing
            if (updateSuccessful) {
                console.log("updateSuccessful");
                PaymentSession.updateSession({
                    success: function(response) {
                        // Handle success response
                        console.log("success");
                    },
                    error: function(response) {
                        // Handle error response
                        console.log("error");
                    }
                });
            } else {
                // Handle update failure
                var validationErrors = PaymentSession.getFormValidationErrors();
                console.log(validationErrors);
                console.log("updateSuccessful failure");
            }

            console.log('clicked');
        }
    </script>
</body>

</html>
