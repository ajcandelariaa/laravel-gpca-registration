{{-- PROGRESS BAR --}}
<div class="horizontal-progress">
    {{-- STEP 1 --}}
    <div class="grid grid-cols-horizontalProgressBarGrid">
        <div class="bg-registrationPrimaryColor font-bold text-white rounded-full flex items-center justify-center"
            style="height: 55px; width: 55px; font-size: 18px;">
            1
        </div>

        <div class="flex items-center justify-center">
            <div style="height: 2px;" class="w-full bg-registrationPrimaryColor"></div>
        </div>

        <div class="{{ $currentStep >= 2 ? 'bg-registrationPrimaryColor text-white' : 'text-registrationPrimaryColor bg-white border-solid border-registrationPrimaryColor border-2' }} font-bold rounded-full flex items-center justify-center"
            style="height: 55px; width: 55px; font-size: 18px;">
            2
        </div>

        <div class="flex items-center justify-center">
            <div style="height: 2px;" class="w-full bg-registrationPrimaryColor"></div>
        </div>

        <div class="{{ $currentStep >= 3 ? 'bg-registrationPrimaryColor text-white' : 'text-registrationPrimaryColor bg-white border-solid border-registrationPrimaryColor border-2' }} font-bold rounded-full flex items-center justify-center"
            style="height: 55px; width: 55px; font-size: 18px;">
            3
        </div>

        <div class="flex items-center justify-center">
            <div style="height: 2px;" class="w-full bg-registrationPrimaryColor"></div>
        </div>

        <div class="{{ $currentStep >= 4 ? 'bg-registrationPrimaryColor text-white' : 'text-registrationPrimaryColor bg-white border-solid border-registrationPrimaryColor border-2' }} font-bold rounded-full flex items-center justify-center"
            style="height: 55px; width: 55px; font-size: 18px;">
            4
        </div>

        <div class="flex items-center justify-center">
            <div style="height: 2px;" class="w-full bg-registrationPrimaryColor"></div>
        </div>

        <div class="{{ $currentStep >= 5 ? 'bg-registrationPrimaryColor text-white' : 'text-registrationPrimaryColor bg-white border-solid border-registrationPrimaryColor border-2' }} font-bold rounded-full flex items-center justify-center"
            style="height: 55px; width: 55px; font-size: 18px;">
            5
        </div>
    </div>

    
    <div class="grid grid-cols-horizontalProgressBarGrid mt-5">
        <div class="font-bold text-registrationPrimaryColor text-center flex justify-center">
            Registration Type
        </div>

        <div class="w-full"></div>

        
        <div class="font-bold text-registrationPrimaryColor text-center flex justify-center">
            Company Details
        </div>

        <div class="w-full"></div>

        
        <div class="font-bold text-registrationPrimaryColor text-center flex justify-center">
            Delegate Details
        </div>

        <div class="w-full"></div>

        
        <div class="font-bold text-registrationPrimaryColor text-center flex justify-center">
            Package Summary
        </div>

        <div class="w-full"></div>

        
        <div class="font-bold text-registrationPrimaryColor text-center flex justify-center">
            Payment Details
        </div>
    </div>
</div>