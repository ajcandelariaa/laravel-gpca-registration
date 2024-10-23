<div>
    <div class="w-10/12 mx-auto md:w-full md:mx-10 text-center">
        <p
            class="text-registrationPrimaryColor text-2xl md:text-4xl text-center font-bold font-montserrat mt-5 md:mt-10">
            How to collect your badge</p>

        <div class="mt-10">
            <p class="font-bold text-lg">Delegate details:</p>
            <p>Transaction ID: {{ $currentDelegate['transactionId'] }}</p>
            <p>Name: {{ $currentDelegate['name'] }}</p>
            <p>Job title: {{ $currentDelegate['jobTitle'] }}</p>
            <p>Company Name: {{ $currentDelegate['companyName'] }}</p>
            <p>Badge type: {{ $currentDelegate['badgeType'] }}</p>
            <p>Email address: {{ $currentDelegate['emailAddress'] }}</p>
        </div>

        <div class="mt-10">
            <p class="font-bold text-lg">Badge status:</p>
            <p>Printed: 
                @if ($currentDelegate['isPrinted'])
                    Yes
                @else
                    No
                @endif
            </p>
            <p>Collected: 
                @if ($currentDelegate['isCollected'])
                    Yes
                @else
                    No
                @endif
            </p>
        </div>
        

        <div class="mt-10">
            <p class="font-bold text-lg">How to collect your badge:</p>
            <p>{{ $currentDelegate['howToCollectYourBadge'] }}</p>
            <div class="mt-5 flex flex-col gap-3 items-center">
                @if (count($currentDelegate['visuals']) > 0)
                    @foreach ($currentDelegate['visuals'] as $imageLink)
                        <img src="{{ $imageLink }}" class="w-full md:w-96 block">
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
