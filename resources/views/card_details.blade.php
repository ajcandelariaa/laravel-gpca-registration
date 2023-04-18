<html>
<head>
    <script src="https://ap-gateway.mastercard.com/form/version/70/merchant/TEST900755/session.js"></script>
    <style id="antiClickjack">
        body {
            display: none !important;
        }
    </style>
</head>

<body>
    <div>Please enter your payment details:</div>
    <h3>Credit Card</h3>
    <input type="hidden" id="session-id" value="{{ session('sessionId') }}">
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

    <script src="{{ asset('js/paymentGateway/paymentConfigurations.js') }}"></script>
</body>

</html>
