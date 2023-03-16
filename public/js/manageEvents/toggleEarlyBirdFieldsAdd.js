var toggleInputs = document.getElementById("toggle_inputs");
var input1 = document.getElementById("eb_end_date");
var input2 = document.getElementById("eb_member_rate");
var input3 = document.getElementById("eb_nmember_rate");
toggleInputs.addEventListener("change", function () {
    if (toggleInputs.checked) {
        input1.readOnly = false;
        input2.readOnly = false;
        input3.readOnly = false;
        
        input1.classList.remove("cursor-not-allowed", "outline-none");
        input2.classList.remove("cursor-not-allowed", "outline-none");
        input3.classList.remove("cursor-not-allowed", "outline-none");

        input1.classList.add("outline-registrationPrimaryColor");
        input2.classList.add("outline-registrationPrimaryColor");
        input3.classList.add("outline-registrationPrimaryColor");
    } else {
        input1.readOnly = true;
        input2.readOnly = true;
        input3.readOnly = true;

        input1.value = null;
        input2.value = null;
        input3.value = null;
        
        input1.classList.add("cursor-not-allowed", "outline-none");
        input2.classList.add("cursor-not-allowed", "outline-none");
        input3.classList.add("cursor-not-allowed", "outline-none");

        input1.classList.remove("outline-registrationPrimaryColor");
        input2.classList.remove("outline-registrationPrimaryColor");
        input3.classList.remove("outline-registrationPrimaryColor");
    }
});