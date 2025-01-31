{{-- PROGRESS BAR --}}
<div class="vertical-progress">
    {{-- STEP 1 --}}
    <div class="flex items-center gap-6">
        <div class="bg-registrationPrimaryColor font-bold text-white rounded-full flex items-center justify-center"
            style="height: 55px; width: 55px; font-size: 18px;">
            1
        </div>

        <div class="text-registrationPrimaryColor font-bold text-xl">
            Submission category
        </div>
    </div>

    {{-- STEP 2 --}}
    <div style="width: 60px;" class="flex justify-center">
        <div style="width: 2px;" class="h-12 bg-registrationPrimaryColor"></div>
    </div>
    <div class="flex items-center gap-6">
        <div class="{{ $currentStep >= 2 ? 'bg-registrationPrimaryColor text-white' : 'text-registrationPrimaryColor bg-white border-solid border-registrationPrimaryColor border-2' }} font-bold rounded-full flex items-center justify-center"
            style="height: 55px; width: 55px; font-size: 18px;">
            2
        </div>

        <div class="text-registrationPrimaryColor font-bold text-xl">
            Participant details
        </div>
    </div>


    {{-- STEP 3 --}}
    <div style="width: 60px;" class="flex justify-center">
        <div style="width: 2px;" class="h-12 bg-registrationPrimaryColor"></div>
    </div>
    <div class="flex items-center gap-6">
        <div class="{{ $currentStep >= 3 ? 'bg-registrationPrimaryColor text-white' : 'text-registrationPrimaryColor bg-white border-solid border-registrationPrimaryColor border-2' }} font-bold rounded-full flex items-center justify-center"
            style="height: 55px; width: 55px; font-size: 18px;">
            3
        </div>

        <div class="text-registrationPrimaryColor font-bold text-xl">
            Submission summary
        </div>
    </div>


    {{-- STEP 4 --}}
    <div style="width: 60px;" class="flex justify-center">
        <div style="width: 2px;" class="h-12 bg-registrationPrimaryColor"></div>
    </div>
    <div class="flex items-center gap-6">
        <div class="{{ $currentStep >= 4 ? 'bg-registrationPrimaryColor text-white' : 'text-registrationPrimaryColor bg-white border-solid border-registrationPrimaryColor border-2' }} font-bold rounded-full flex items-center justify-center"
            style="height: 55px; width: 55px; font-size: 18px;">
            4
        </div>

        <div class="text-registrationPrimaryColor font-bold text-xl">
            Payment details
        </div>
    </div>
</div>
