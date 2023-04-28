<html>

<head>
    <script src="https://ap-gateway.mastercard.com/form/version/70/merchant/TEST900755/session.js"></script>
    <style id="antiClickjack">
        body {
            display: none !important;
        }
    </style>

    <style>
        span {
            display: none;
        }

        .error {
            display: block;
        }
    </style>
</head>

<body>
    <div>Please enter your payment details:</div>
    <h3>Credit Card</h3>
    <div>Card Number: <input type="text" id="card-number" class="input-field" title="card number"
            aria-label="enter your card number" value="" tabindex="1" readonly>
    </div>
    <div>Expiry Month:<input type="text" id="expiry-month" class="input-field" title="expiry month"
            aria-label="two digit expiry month" value="" tabindex="2" readonly></div>
    <div>Expiry Year:<input type="text" id="expiry-year" class="input-field" title="expiry year"
            aria-label="two digit expiry year" value="" tabindex="3" readonly></div>
    <div>Security Code:<input type="text" id="security-code" class="input-field" title="security code"
            aria-label="three digit CCV security code" value="" tabindex="4" readonly></div>
    <div>Cardholder Name:<input type="text" id="cardholder-name" class="input-field" title="cardholder name"
            aria-label="enter name on card" value="" tabindex="5" readonly required></div>
    <div><button id="payButton" onclick="pay('card');">Pay Now</button></div>

    <span id="card-number-error"></span>
    <span id="card-month-error"></span>
    <span id="card-year-error"></span>
    <span id="card-security-error"></span>
    <span id="card-name-error"></span>


    <script>
        if (self === top) {
            var antiClickjack = document.getElementById("antiClickjack");
            antiClickjack.parentNode.removeChild(antiClickjack);
        } else {
            top.location = self.location;
        }

        let sessionId = "{{ session('sessionId') }}";
        let cardNumberError = expiryMonthError = expiryYearError = securityCodeError = cardNameError = true;

        let cardNumberErrMessage = document.getElementById('card-number-error');
        let cardMonthErrMessage = document.getElementById('card-month-error');
        let cardYearErrMessage = document.getElementById('card-year-error');
        let cardSecurityErrMessage = document.getElementById('card-security-error');
        let cardNameErrMessage = document.getElementById('card-name-error');

        cardNameErrMessage.textContent = "Cardholder Name is required";
        cardSecurityErrMessage.textContent = "Security Code is required";

        PaymentSession.configure({
            session: sessionId,
            merchantId: 'TEST900755',
            apiKey: '3b41414705a08d0fa159a77316aba3b3',
            fields: {
                // ATTACH HOSTED FIELDS TO YOUR PAYMENT PAGE FOR A CREDIT CARD
                card: {
                    number: "#card-number",
                    securityCode: "#security-code",
                    expiryMonth: "#expiry-month",
                    expiryYear: "#expiry-year",
                    nameOnCard: "#cardholder-name",
                },
            },

            //SPECIFY YOUR MITIGATION OPTION HERE
            frameEmbeddingMitigation: ["javascript"],
            callbacks: {
                initialized: function(response) {
                    // HANDLE INITIALIZATION RESPONSE
                    console.log(response);
                },
                formSessionUpdate: function(response) {
                    console.log(response);
                    // HANDLE RESPONSE FOR UPDATE SESSION
                    if (response.status) {
                        if ("ok" == response.status) {
                            if (securityCodeError || cardNameError) {
                                if (securityCodeError) {
                                    document.querySelector('#security-code').style.borderColor = "red";
                                    cardSecurityErrMessage.classList.add("error");
                                }

                                if (cardNameError) {
                                    document.querySelector('#cardholder-name').style.borderColor = "red";
                                    cardNameErrMessage.classList.add("error");
                                }
                            } else {
                                console.log("Session updated with data: " + response.session.id);

                                if (response.sourceOfFunds.provided.card.securityCode) {
                                    console.log("Security code was provided.");
                                }

                                if (response.sourceOfFunds.provided.card.scheme == 'MASTERCARD') {
                                    console.log("The user entered a Mastercard credit card.")
                                }
                                // window.location.replace("http://127.0.0.1:8000/initiateAuthentication");
                            }
                        } else if ("fields_in_error" == response.status) {
                            console.log("Session update failed with field errors.");

                            // if missing din yung details ng security code at card holder name
                            if (securityCodeError) {
                                console.log("Security code missing.");

                                securityCodeError = true;
                                cardSecurityErrMessage.textContent = "Security Code is missing";

                                document.querySelector('#security-code').style.borderColor = "red";
                                cardSecurityErrMessage.classList.add('error');
                            }

                            if (cardNameError) {
                                console.log("Cardholder name missing.");

                                cardNameError = true;
                                cardNameErrMessage.textContent = "Cardholder Name is missing";

                                document.querySelector('#cardholder-name').style.borderColor = "red";
                                cardNameErrMessage.classList.add('error');
                            }


                            if (response.errors.cardNumber) {
                                console.log("Card number invalid or missing.");

                                cardNumberError = true;

                                if (response.errors.cardNumber == "missing") {
                                    cardNumberErrMessage.textContent = "Card Number is required";
                                } else if (response.errors.cardNumber == "invalid") {
                                    cardNumberErrMessage.textContent = "Card Number is invalid";
                                } else {
                                    cardNumberErrMessage.textContent = "Card Number invalid or missing";
                                }

                                document.querySelector('#card-number').style.borderColor = "red";
                                cardNumberErrMessage.classList.add('error');
                            }
                            if (response.errors.expiryYear) {
                                console.log("Expiry year invalid or missing.");

                                expiryYearError = true;

                                if (response.errors.expiryYear == "missing") {
                                    cardYearErrMessage.textContent = "Expiry Year is required";
                                } else if (response.errors.expiryYear == "invalid") {
                                    cardYearErrMessage.textContent = "Expiry Year is invalid";
                                } else {
                                    cardYearErrMessage.textContent = "Expiry Year invalid or missing";
                                }

                                document.querySelector('#expiry-year').style.borderColor = "red";
                                cardYearErrMessage.classList.add('error');
                            }
                            if (response.errors.expiryMonth) {
                                console.log("Expiry month invalid or missing.");

                                expiryMonthError = true;

                                if (response.errors.expiryMonth == "missing") {
                                    cardMonthErrMessage.textContent = "Expiry Month is required";
                                } else if (response.errors.expiryMonth == "invalid") {
                                    cardMonthErrMessage.textContent = "Expiry Month is invalid";
                                } else {
                                    cardMonthErrMessage.textContent = "Expiry Month invalid or missing";
                                }

                                document.querySelector('#expiry-month').style.borderColor = "red";
                                cardMonthErrMessage.classList.add('error');
                            }

                            if (response.errors.securityCode) {
                                console.log("Security code invalid.");

                                securityCodeError = true;
                                cardSecurityErrMessage.textContent = "Security Code is invalid";

                                document.querySelector('#security-code').style.borderColor = "red";
                                cardSecurityErrMessage.classList.add('error');
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


        PaymentSession.onFocus(["card.number", "card.securityCode", "card.expiryYear", "card.expiryMonth",
            "card.nameOnCard"
        ], function(selector, role) {
            // removeErrorMessage();
        });

        PaymentSession.onValidityChange(["card.number", "card.securityCode", "card.expiryYear", "card.expiryMonth"],
            function(selector, result) {
                if (result.isValid) {
                    document.querySelector(selector).style.borderColor = "green";
                } else {
                    document.querySelector(selector).style.borderColor = "red";
                }
            });

        PaymentSession.onEmptinessChange(["card.nameOnCard", "card.securityCode"], function(selector, result) {
            if (result.isEmpty) {
                if (selector == "#cardholder-name") {
                    cardNameError = true;
                    cardNameErrMessage.textContent = "Cardholder Name is required";
                } else {
                    securityCodeError = true;
                    cardSecurityErrMessage.textContent = "Security Code is required";
                }
                document.querySelector(selector).style.borderColor = "red";
            } else if (!result.isEmpty) {
                if (selector == "#security-code") {
                    cardNameError = false;
                    cardNameErrMessage.textContent = "";
                } else {
                    securityCodeError = false;
                    cardSecurityErrMessage.textContent = "";
                }
                document.querySelector(selector).style.borderColor = "green";
            }
        });

        function removeErrorMessage() {
            if (cardNumberErrMessage.classList.contains("error")) {
                cardNumberErrMessage.classList.remove("error");
                cardNumberErrMessage.textContent = "";
            }

            if (cardMonthErrMessage.classList.contains("error")) {
                cardMonthErrMessage.classList.remove("error");
                cardMonthErrMessage.textContent = "";
            }

            if (cardYearErrMessage.classList.contains("error")) {
                cardYearErrMessage.classList.remove("error");
                cardYearErrMessage.textContent = "";
            }

            if (cardSecurityErrMessage.classList.contains("error")) {
                cardSecurityErrMessage.classList.remove("error");
                cardSecurityErrMessage.textContent = "";
            }

            if (cardNameErrMessage.classList.contains("error")) {
                cardNameErrMessage.classList.remove("error");
                cardNameErrMessage.textContent = "";
            }
        }

        function pay() {
            PaymentSession.updateSessionFromForm('card');
            console.log('clicked');
        }
    </script>
</body>

</html>
