<div class="mx-20 my-10">
    <div class="flex gap-10 justify-center items-center">
        @foreach ($choices as $day => $dayData)
            <button wire:click.prevent="selectDay('{{ $day }}')"
                class="bg-registrationPrimaryColorHover hover:bg-registrationPrimaryColor text-white rounded-lg text-xl py-1 px-10">{{ $day }}</button>
        @endforeach
    </div>
    @if ($currentDay != null)
        <hr class="my-5">
        <div class="flex gap-10 justify-center items-center">
            @foreach ($choices[$currentDay] as $dayCategory => $dayCategoryTimings)
                <button wire:click.prevent="selectDayCategory('{{ $dayCategory }}')"
                    class="bg-registrationSecondaryColorHover hover:bg-registrationSecondaryColor text-white rounded-lg text-xl py-1 px-10">{{ $dayCategory }}
                    <br>
                    {{ $dayCategoryTimings['start_time'] }} - {{ $dayCategoryTimings['end_time'] }}</button>
                <br><br>
            @endforeach
        </div>

        @if ($currentDayCategory != null)
            <hr class="my-5">
            @if (empty($currentListOfDelegates))
                <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md">
                    There are no scanned delegates yet.
                </div>
            @else
                <div class="shadow-lg my-5 pt-5 bg-white rounded-md">
                    <h1 class="text-center text-2xl bg-registrationPrimaryColor text-white py-4">Scanned delegates</h1>
                    <div class="grid grid-cols-9 gap-5 p-4 px-4 text-center items-center bg-blue-600 text-white ">
                        <div class="col-span-1 break-words">No.</div>
                        <div class="col-span-1 break-words">Transaction ID</div>
                        <div class="col-span-1 break-words">Invoice</div>
                        <div class="col-span-2 break-words">Name</div>
                        <div class="col-span-1 break-words">Company</div>
                        <div class="col-span-2 break-words">Email Address</div>
                        <div class="col-span-1 break-words">Registration type</div>
                    </div>
                    @foreach ($currentListOfDelegates as $delegateIndex => $currentListOfDelegate)
                        <div
                            class="grid grid-cols-9 gap-5 py-2 px-4 mb-1 text-center items-center  {{ $delegateIndex % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                            <div class="col-span-1 break-words">{{ $delegateIndex + 1 }}</div>

                            <div class="col-span-1 break-words">
                                <a href="{{ route('admin.event.delegates.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'delegateType' => $currentListOfDelegate['delegateType'], 'delegateId' => $currentListOfDelegate['delegateId']]) }}"
                                    target="_blank" class="text-blue-700 font-semibold hover:underline">
                                    {{ $currentListOfDelegate['delegateTransactionId'] }}
                                </a>
                            </div>

                            <div class="col-span-1 break-words">
                                <a href="{{ route('admin.event.registrants.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'registrantId' => $currentListOfDelegate['mainDelegateId']]) }}"
                                    target="_blank" class="text-blue-700 font-semibold hover:underline">
                                    {{ $currentListOfDelegate['delegateInvoiceNumber'] }}
                                </a>
                            </div>

                            <div class="col-span-2 break-words">
                                {{ $currentListOfDelegate['delegateName'] }}
                            </div>

                            <div class="col-span-1 break-words">
                                {{ $currentListOfDelegate['delegateCompany'] }}
                            </div>

                            <div class="col-span-2 break-words">
                                {{ $currentListOfDelegate['delegateEmailAddress'] }}
                            </div>

                            <div class="col-span-1 break-words">
                                {{ $currentListOfDelegate['delegateBadgeType'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    @endif
</div>
