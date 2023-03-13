var input = document.querySelector("#phoneTest"),
    errorMap = [
        "Invalid number",
        "Invalid country code",
        "Too short",
        "Too long",
        "Invalid number",
    ],
    result = document.querySelector("#result");

window.addEventListener("load", function () {
    errorMsg = document.querySelector("#error-msg");
    var iti = window.intlTelInput(input, {
        hiddenInput: "full_number",
        nationalMode: false,
        formatOnDisplay: true,
        separateDialCode: true,
        autoHideDialCode: true,
        autoPlaceholder: "aggressive",
        initialCountry: "auto",
        placeholderNumberType: "MOBILE",
        preferredCountries: ["us", "ae"],
        initialCountry: "us",
        utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/js/utils.js",
    });
    input.addEventListener("keyup", formatIntlTelInput);
    input.addEventListener("change", formatIntlTelInput);
    function formatIntlTelInput() {
        if (typeof intlTelInputUtils !== "undefined") {
            // utils are lazy loaded, so must check
            var currentText = iti.getNumber(
                intlTelInputUtils.numberFormat.E164
            );
            if (typeof currentText === "string") {
                // sometimes the currentText is an object :)
                iti.setNumber(currentText); // will autoformat because of formatOnDisplay=true
            }
        }
    }
    input.addEventListener("keyup", function () {
        reset();
        if (input.value.trim()) {
            if (iti.isValidNumber()) {
                $(input).addClass("form-control is-valid");
            } else {
                $(input).addClass("form-control is-invalid");
                var errorCode = iti.getValidationError();
                errorMsg.innerHTML = errorMap[errorCode];
                $(errorMsg).show();
            }
        }
    });
    input.addEventListener("change", reset);
    input.addEventListener("keyup", reset);
    var reset = function () {
        $(input).removeClass("form-control is-invalid");
        errorMsg.innerHTML = "";
        $(errorMsg).hide();
    };
    input.addEventListener(
        "keyup",
        function (e) {
            e.preventDefault();
            var num = iti.getNumber(),
                valid = iti.isValidNumber();
            result.textContent = "Number: " + num + ", valid: " + valid;
        },
        false
    );
    input.addEventListener(
        "focus",
        function () {
            result.textContent = "";
        },
        false
    );
    $(input).on("focusout", function (e, countryData) {
        var intlNumber = iti.getNumber();
        console.log(intlNumber);
    });
});

function isPhoneNumberKey(evt) {
    var charCode = evt.which ? evt.which : evt.keyCode;
    if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
