<div class="mx-5">
    {{-- COMPANY INFORMATION --}}
    @include('livewire.registration.step.second_company')

    {{-- MAIN DELEGATE --}}
    @include('livewire.registration.step.second_main_delegate')
    @if (!empty($additionalDelegates))
        <div class="mt-10">
            <div class="text-registrationPrimaryColor italic font-bold text-xl">
                Additional Delegate(s)
            </div>

            <div class="mt-5">
                @php $count = 2; @endphp
                @foreach ($additionalDelegates as $additionalDelegate)
                    <div class="bg-registrationCardBGColor px-5 py-2 mt-5 flex justify-between rounded-md">
                        <div>
                            <div class="font-bold text-2xl flex items-center gap-2 mt-1">
                                <p>{{ $additionalDelegate['subSalutation'] }} {{ $additionalDelegate['subFirstName'] }} {{ $additionalDelegate['subMiddleName'] }} {{ $additionalDelegate['subLastName'] }}</p>
                                <p class="border-2 border-registrationPrimaryColor rounded-full text-registrationPrimaryColor py-1 px-3 text-sm">
                                    {{ $additionalDelegate['subBadgeType'] }}</p>
                            </div>
                            <p class="mt-2"> {{ $additionalDelegate['subEmailAddress'] }}, {{ $additionalDelegate['subMobileNumber'] }}, {{ $additionalDelegate['subJobTitle'] }}</p>
                            <p>Nationality: {{ $additionalDelegate['subNationality'] }}</p>
                            @if ($additionalDelegate['subPromoCode'] == null)
                                <p>Promo code used: None</p>
                            @else
                                <p>Promo code used: <span class="font-bold">{{ $additionalDelegate['subPromoCode'] }}</span> <span class="text-green-500 text-xs italic mt-1">({{ $additionalDelegate['promoCodeSuccessSub'] }})</span></p>
                            @endif
                        </div>
                        <div class="flex flex-col justify-between items-end">
                            <p class="text-registrationPrimaryColor font-bold">Delegate {{ $count }}</p>
                            <div class="flex gap-3">
                                <div wire:click.prevent="openEditModal('{{ $additionalDelegate['subDelegateId'] }}')"
                                    class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    Edit
                                </div>

                                <div wire:click.prevent="removeAdditionalDelegate('{{ $additionalDelegate['subDelegateId'] }}')"
                                class="cursor-pointer hover:text-red-600 text-red-500">
                                <i class="fa-solid fa-trash"></i>
                                Remove
                            </div>
                            </div>
                        </div>
                    </div>
                    
                    @php $count++; @endphp
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-10 flex flex-col sm:flex-row gap-10">
        <div class="col-span-1">

            @if ($showAddDelegateModal)
                @include('livewire.registration.add_delegate_modal')
            @endif

            @if ($showEditDelegateModal)
                @include('livewire.registration.edit_delegate_modal')
            @endif

            @if (
                $firstName != null &&
                    $lastName != null &&
                    $emailAddress != null &&
                    $mobileNumber != null &&
                    $nationality != null &&
                    $jobTitle != null &&
                    $badgeType != null &&
                    count($additionalDelegates) < 4)
                <button wire:click.prevent="openAddModal" type="button" wire:key="btnOpenAddModal"
                    class="cursor-pointer hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor rounded-md py-4 px-10">+
                    Add Delegate</button>
            @else
                <button disabled type="button"
                    class="cursor-not-allowed font-bold border-gray-600 border-2 bg-white text-gray-600  rounded-md py-4 px-10">+
                    Add Delegate</button>
            @endif
        </div>

        <div class="col-span-1">
            <div class="text-registrationPrimaryColor italic font-bold text-xl">
                Do you wish to invite more delegates?
            </div>

            <div class="text-registrationPrimaryColor italic text-sm mt-2 w-full sm:w-3/5">
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
