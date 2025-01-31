{{-- PROGRESS BAR --}}
<div class="horizontal-progress mx-5">
    {{-- STEP 1 --}}
    <div class="grid grid-cols-horizontalProgressBarGridRCCAwards">
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
    </div>

    
    <div class="grid grid-cols-horizontalProgressBarGridRCCAwards mt-5">
        <div class="text-sm md:text-base font-bold text-registrationPrimaryColor text-center flex justify-center">
            Submission type
        </div>

        <div class="w-full"></div>

        
        <div class="text-sm md:text-base font-bold text-registrationPrimaryColor text-center flex justify-center">
            Participant details
        </div>

        <div class="w-full"></div>

        
        <div class="text-sm md:text-base font-bold text-registrationPrimaryColor text-center flex justify-center">
            Submission summary
        </div>

        <div class="w-full"></div>

        
        <div class="text-sm md:text-base font-bold text-registrationPrimaryColor text-center flex justify-center">
            Payment details
        </div>
    </div>
</div>
