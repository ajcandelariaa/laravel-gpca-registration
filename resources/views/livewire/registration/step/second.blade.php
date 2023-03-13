<div>
    {{-- COMPANY INFORMATION --}}
    <div>
        <div class="text-registrationPrimaryColor italic font-bold text-xl">
            Company Information
        </div>

        <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
            {{-- ROW 1 --}}
            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Company Name <span class="text-red-500">*</span>
                </div>
                <div>
                    @if ($delegatePassType == 'member')
                        <select required name="" id=""
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            <option value="" disabled selected hidden>Please select...</option>
                            @foreach ($companySectors as $companySector)
                                <option value="">{{ $companySector }}</option>
                            @endforeach
                            <option value="">Others</option>
                        </select>
                    @else
                        <input placeholder="Company Name" type="text" name="" id=""
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    @endif
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Company Sector <span class="text-red-500">*</span>
                </div>
                <div>
                    <select required name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        <option value="" disabled selected hidden>Please select...</option>
                        @foreach ($companySectors as $companySector)
                            <option value="">{{ $companySector }}</option>
                        @endforeach
                        <option value="">Others</option>
                    </select>
                </div>
            </div>

            {{-- ROW 2 --}}
            <div class="space-y-2 col-span-2">
                <div class="text-registrationPrimaryColor">
                    Company Address <span class="text-red-500">*</span>
                </div>
                <div>
                    <input placeholder="Please enter Complete Company Address" type="text" name=""
                        id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                </div>
            </div>

            {{-- ROW 3 --}}
            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Country <span class="text-red-500">*</span>
                </div>
                <div>
                    <select required name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        <option value="" disabled selected hidden>Please select...</option>
                        <option value="">971</option>
                        <option value="">53</option>
                        <option value="">132</option>
                        <option value="">131</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    City <span class="text-red-500">*</span>
                </div>
                <div>
                    <select required name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        <option value="" disabled selected hidden>Please select...</option>
                        <option value="">971</option>
                        <option value="">53</option>
                        <option value="">132</option>
                        <option value="">131</option>
                    </select>
                </div>
            </div>

            {{-- ROW 4 --}}
            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Landline Number <span class="italic">(optional)</span>
                </div>
                <div>
                    <input placeholder="xxxxxxx" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Mobile Number <span class="text-red-500">*</span>
                </div>
                <div>
                    <input placeholder="xxxxxxx" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                </div>
            </div>

            {{-- ROW 5 --}}
            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Promo Code
                </div>
                <div>
                    <input placeholder="Enter your promo code here" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Where did you hear about us? <span class="text-red-500">*</span>
                </div>
                <div>
                    <select required name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        <option value="" disabled selected hidden>Please select...</option>
                        <option value="">Social Media</option>
                        <option value="">Friends</option>
                        <option value="">Family</option>
                        <option value="">News</option>
                        <option value="">Others</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN DELEGATE --}}
    <div class="mt-10">
        <div class="text-registrationPrimaryColor italic font-bold text-xl">
            Main Delegate
        </div>

        <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">
            {{-- ROW 1 --}}
            <div class="space-y-2 col-span-2">
                <div class="grid grid-cols-10 gap-x-5">
                    <div class="col-span-1">
                        <div class="text-registrationPrimaryColor">
                            Salutation
                        </div>
                        <div>
                            <select required name="" id=""
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                                <option value="" disabled selected hidden>Choose...</option>
                                @foreach ($salutations as $salutation)
                                    <option value="">{{ $salutation }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-span-3">
                        <div class="text-registrationPrimaryColor">
                            First Name <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input placeholder="First Name" type="text" name="" id=""
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        </div>
                    </div>

                    <div class="col-span-3">
                        <div class="text-registrationPrimaryColor">
                            Middle Name <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input placeholder="Middle Name" type="text" name="" id=""
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        </div>
                    </div>

                    <div class="col-span-3">
                        <div class="text-registrationPrimaryColor">
                            Last Name <span class="text-red-500">*</span>
                        </div>
                        <div>
                            <input placeholder="Last Name" type="text" name="" id=""
                                class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        </div>
                    </div>
                </div>
            </div>


            {{-- ROW 2 --}}
            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Email Address <span class="text-red-500">*</span>
                </div>
                <div>
                    <input placeholder="Email Address" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Mobile Number <span class="text-red-500">*</span>
                </div>
                <div>
                    <input placeholder="xxxxxxx" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                </div>
            </div>


            {{-- ROW 3 --}}
            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Nationality <span class="text-red-500">*</span>
                </div>
                <div>
                    <input placeholder="Nationality" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-registrationPrimaryColor">
                    Job Title <span class="text-red-500">*</span>
                </div>
                <div>
                    <input placeholder="Job Title" type="text" name="" id=""
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-10">
        <div class="text-registrationPrimaryColor italic font-bold text-xl">
            Additional Delegate(s)
        </div>

        <div class="mt-5">
            <div class="grid grid-cols-4">
                <div class="col-span-1 text-registrationPrimaryColor hover:underline cursor-pointer">
                    Delegate 2
                </div>
                <div class="col-span-2 text-registrationPrimaryColor">
                    Albert Joseph Candelaria
                </div>
                <div class="col-span-1 flex gap-3">
                    <div class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Edit
                    </div>
                    <div class="cursor-pointer hover:text-red-600 text-red-500">
                        <i class="fa-solid fa-trash"></i>
                        Remove
                    </div>
                </div>
            </div>

            <hr class="my-4 w-full">

            <div class="grid grid-cols-4">
                <div class="col-span-1 text-registrationPrimaryColor hover:underline cursor-pointer">
                    Delegate 3
                </div>
                <div class="col-span-2 text-registrationPrimaryColor">
                    Wesam Issa
                </div>
                <div class="col-span-1 flex gap-3">
                    <div class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Edit
                    </div>
                    <div class="cursor-pointer hover:text-red-600 text-red-500">
                        <i class="fa-solid fa-trash"></i>
                        Remove
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-10 grid grid-addDelegateGrid grid-flow-col gap-x-10 items-center">
        <div class="col-span-1">
            <button
                class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor  rounded-md py-4 px-10">+
                Add Delegate</button>
        </div>

        <div class="col-span-1">
            <div class="text-registrationPrimaryColor italic font-bold text-xl">
                Do you wish to invite more delegates?
            </div>

            <div class="text-registrationPrimaryColor italic text-sm mt-2 w-3/5">
                If you wish to register more than 5 delegates, please contact our sales team at
                forumregistration@gpca.org.ae or call +971 4 5106666 ext. 153
            </div>
        </div>
    </div>

    {{-- <div class="form-group has-danger" wire:ignore>
        <input id="phoneTest" name="phoneTest" class="" type="tel" maxlength="15"
            wire:model="phoneTest">
        <br>
        <span id="error-msg" class="hide"></span>
        <p id="result"></p>
    </div>

    @push('scripts')
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/css/intlTelInput.css">
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/js/intlTelInput.js">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
            integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous">
        </script>
        
        <script>
            var input = document.querySelector("#phoneTest"),
                errorMap = [
                    "Invalid number",
                    "Invalid country code",
                    "Too short",
                    "Too long",
                    "Invalid number",
                ],
                result = document.querySelector("#result");

            window.addEventListener("load", function() {
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
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/js/utils.js",
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
                input.addEventListener("keyup", function() {
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
                var reset = function() {
                    $(input).removeClass("form-control is-invalid");
                    errorMsg.innerHTML = "";
                    $(errorMsg).hide();
                };
                input.addEventListener(
                    "keyup",
                    function(e) {
                        e.preventDefault();
                        var num = iti.getNumber(),
                            valid = iti.isValidNumber();
                        result.textContent = "Number: " + num + ", valid: " + valid;
                    },
                    false
                );
                input.addEventListener(
                    "focus",
                    function() {
                        result.textContent = "";
                    },
                    false
                );
                $(input).on("focusout", function(e, countryData) {
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
        </script>
    @endpush --}}
</div>
