<div>
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>

    <div class="mx-10 my-10">
        <div class="flex justify-center">
            <div class="flex items-end gap-5">
                <div class="flex flex-col gap-1 text-center">
                    <p>Start point</p>
                    <input type="number" wire:model.lazy="startPoint"
                        class="w-44 bg-registrationInputFieldsBGColor text-md px-3 border border-registrationPrimaryColor outline-registrationPrimaryColor text-center"
                        min="1" step="1" max="{{ count($allDelegates) }}">

                    @error('startPoint')
                        <div class="text-red-500 text-xs italic mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="flex flex-col gap-1 text-center">
                    <p>End point</p>
                    <input type="number" wire:model.lazy="endPoint"
                        class="w-44 bg-registrationInputFieldsBGColor text-md px-3 border border-registrationPrimaryColor outline-registrationPrimaryColor text-center"
                        min="1" step="1" max="{{ count($allDelegates) }}">

                    @error('endPoint')
                        <div class="text-red-500 text-xs italic mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-center gap-3 mt-5">
            @if ($isHighlightingDelegates)
            <button type="button" wire:click="removeDelegatesHighlight"
                class="bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded-md text-md text-center">Remove highlight
                delegates</button>
            @else
                <button type="button" wire:click="highlightDelegates"
                    class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-md text-md text-center">Highlight
                    delegates</button>
            @endif

            <button type="button" wire:click="sendEmailBroadcastConfirmation"
                class="bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded-md text-md text-center">Send
                Email</button>
        </div>


        <table class="w-full text-center border-collapse border mt-10">
            <tr class="bg-registrationPrimaryColor text-white">
                <td class="border py-4 px-3" width="4%">No.</td>
                <td class="border py-4 px-3" width="8%">Transaction ID</td>
                <td class="border py-4 px-3" width="15%">Full Name</td>
                <td class="border py-4 px-3" width="15%">Job Title</td>
                <td class="border py-4 px-3" width="15%">Company Name</td>
                <td class="border py-4 px-3" width="15%">Email Address</td>
                <td class="border py-4 px-3" width="8%">Registration Status</td>
                <td class="border py-4 px-3" width="5%">Email Sent Count</td>
                <td class="border py-4 px-3" width="10%">Email Last Sent</td>
                <td class="border py-4 px-3" width="5%">Action</td>
            </tr>
            @foreach ($allDelegates as $delegateIndex => $delegate)
                @if ($delegate['highlight'])
                    <tr class="bg-blue-500">
                    @else
                    <tr class="{{ $delegate['emailBroadcastLastSent'] == "N/A" ? 'bg-gray-50' : 'bg-green-500' }}">
                @endif
                <td class="border py-2 px-3 text-sm">{{ $delegateIndex + 1 }}</td>
                <td class="border py-2 px-3 text-sm">{{ $delegate['transactionId'] }}</td>
                <td class="border py-2 px-3 text-sm">{{ $delegate['fullName'] }}</td>
                <td class="border py-2 px-3 text-sm">{{ $delegate['jobTitle'] }}</td>
                <td class="border py-2 px-3 text-sm">{{ $delegate['companyName'] }}</td>
                <td class="border py-2 px-3 text-sm">{{ $delegate['emailAddress'] }}</td>
                <td class="border py-2 px-3 text-sm">{{ $delegate['registrationStatus'] }}</td>
                <td class="border py-2 px-3 text-sm">{{ $delegate['emailBroadcastSentCount'] }}</td>
                <td class="border py-2 px-3 text-sm">
                    {{ $delegate['emailBroadcastLastSent'] }}
                </td>
                <td class="border py-2 px-3 text-sm">
                    <p class="cursor-pointer hover:underline text-blue-700" wire:click="sendEmailConfirmation({{ $delegateIndex }})">Send email</p>
                </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
