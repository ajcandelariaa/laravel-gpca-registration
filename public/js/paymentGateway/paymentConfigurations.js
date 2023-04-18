if (self === top) {
    var antiClickjack = document.getElementById("antiClickjack");
    antiClickjack.parentNode.removeChild(antiClickjack);
} else {
    top.location = self.location;
}

let sessionId = document.getElementById('session-id').value;

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
            nameOnCard: "#cardholder-name"
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
                    window.location.replace("http://127.0.0.1:8000/retrieveSession");
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