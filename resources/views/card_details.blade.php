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
            aria-label="enter name on card" value="" tabindex="5" readonly></div>
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
        let cardNumberError = expiryMonthError = expiryYearError = securityCodeError = true;
        let securityCodeErrorEmpty = cardNameErrorEmpty = true;

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

                            document.querySelector('#card-number').style.borderColor = "green";
                            cardNumberErrMessage.classList.remove("error");

                            document.querySelector('#expiry-month').style.borderColor = "green";
                            cardMonthErrMessage.classList.remove("error");

                            document.querySelector('#expiry-year').style.borderColor = "green";
                            cardYearErrMessage.classList.remove("error");

                            if (securityCodeErrorEmpty || cardNameErrorEmpty) {
                                if (securityCodeErrorEmpty) {
                                    document.querySelector('#security-code').style.borderColor = "red";
                                    cardSecurityErrMessage.classList.add("error");
                                }

                                if (cardNameErrorEmpty) {
                                    document.querySelector('#cardholder-name').style.borderColor = "red";
                                    cardNameErrMessage.classList.add("error");
                                }
                            } else {
                                console.log("Session updated with data: " + response.session.id);

                                document.querySelector('#security-code').style.borderColor = "green";
                                cardSecurityErrMessage.classList.remove("error");

                                document.querySelector('#cardholder-name').style.borderColor = "green";
                                cardNameErrMessage.classList.remove("error");

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
                            if (securityCodeErrorEmpty) {
                                console.log("Security code required.");
                                cardSecurityErrMessage.textContent = "Security Code is required";
                                document.querySelector('#security-code').style.borderColor = "red";
                                cardSecurityErrMessage.classList.add('error');
                            } else {
                                if (response.errors.securityCode) {
                                    console.log("Security code invalid.");
                                    securityCodeError = true;
                                    securityCodeErrorEmpty = false;
                                    cardSecurityErrMessage.textContent = "Security Code is invalid";
                                    document.querySelector('#security-code').style.borderColor = "red";
                                    cardSecurityErrMessage.classList.add('error');
                                } else {
                                    securityCodeError = false;
                                    securityCodeErrorEmpty = false;
                                    cardSecurityErrMessage.textContent = "";
                                    document.querySelector('#security-code').style.borderColor = "green";
                                    cardSecurityErrMessage.classList.remove('error');
                                }
                            }

                            if (cardNameErrorEmpty) {
                                console.log("Cardholder name required.");
                                cardNameErrMessage.textContent = "Cardholder Name is required";
                                document.querySelector('#cardholder-name').style.borderColor = "red";
                                cardNameErrMessage.classList.add('error');
                            } else {
                                cardNameErrorEmpty = false;
                                cardNameErrMessage.textContent = "";
                                document.querySelector('#cardholder-name').style.borderColor = "green";
                                cardNameErrMessage.classList.remove('error');
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
                            } else {
                                cardNumberError = false;
                                cardNumberErrMessage.textContent = "";
                                document.querySelector('#card-number').style.borderColor = "green";
                                cardNumberErrMessage.classList.remove('error');
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
                            } else {
                                expiryYearError = false;
                                cardYearErrMessage.textContent = "";
                                document.querySelector('#expiry-year').style.borderColor = "green";
                                cardYearErrMessage.classList.remove('error');
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
                            } else {
                                expiryMonthError = false;
                                cardMonthErrMessage.textContent = "";
                                document.querySelector('#expiry-month').style.borderColor = "green";
                                cardMonthErrMessage.classList.remove('error');
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

        PaymentSession.onValidityChange(["card.number", "card.securityCode", "card.expiryYear", "card.expiryMonth"],
            function(selector, result) {
                if (result.isValid) {
                    document.querySelector(selector).style.borderColor = "green";
                } else {
                    document.querySelector(selector).style.borderColor = "red";
                }
            });

        PaymentSession.onEmptinessChange(["card.nameOnCard", "card.securityCode"], function(selector, result) {
            if (selector == "#cardholder-name") {
                if (result.isEmpty) {
                    cardNameErrorEmpty = true;
                    document.querySelector(selector).style.borderColor = "red";
                } else {
                    cardNameErrorEmpty = false;
                    document.querySelector(selector).style.borderColor = "green";
                }
            } else {
                if (result.isEmpty) {
                    securityCodeErrorEmpty = true;
                    console.log('sec empty');
                    document.querySelector(selector).style.borderColor = "red";
                } else {
                    securityCodeErrorEmpty = false;
                    document.querySelector(selector).style.borderColor = "green";
                }
            }
        });

        function pay() {
            PaymentSession.updateSessionFromForm('card');
            console.log('clicked');
        }
    </script>
</body>

</html>
