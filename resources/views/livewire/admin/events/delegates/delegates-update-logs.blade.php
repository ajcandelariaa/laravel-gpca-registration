<div>
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>
    <div class="mx-10 my-10">
        <div class="shadow-lg my-5 bg-white rounded-md">
            <div class="grid grid-cols-11 gap-5 p-4 text-center items-center bg-blue-600 text-white ">
                <div class="col-span-1 break-words">No.</div>
                <div class="col-span-1 break-words">Transaction ID</div>
                <div class="col-span-1 break-words">PC Name</div>
                <div class="col-span-1 break-words">PC Number</div>
                <div class="col-span-6 break-words">Description</div>
                <div class="col-span-1 break-words">Update Datetime</div>
            </div>

            @if (empty($delegateLogs))
                <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md">
                    There are no logs yet.
                </div>
            @else
                @foreach ($delegateLogs as $delegateLogIndex => $delegateLog)
                    <div
                        class="grid grid-cols-11 gap-5 py-2 px-4 mb-1 text-center items-center  {{ $delegateLogIndex % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                        <div class="col-span-1 break-words text-sm">{{ $delegateLogIndex + 1 }}</div>

                        <div class="col-span-1 break-words text-sm">
                            <a href="{{ route('admin.event.delegates.detail.view', ['eventCategory' => $event->category, 'eventId' => $event->id, 'delegateType' => $delegateLog['delegateType'], 'delegateId' => $delegateLog['delegateId']]) }}"
                                target="_blank" class="text-blue-700 font-semibold hover:underline">
                                {{ $delegateLog['delegateTransactionId'] }}
                            </a>
                        </div>

                        <div class="col-span-1 break-words text-sm">
                            {{ $delegateLog['pcName'] }}
                        </div>

                        <div class="col-span-1 break-words text-sm">
                            {{ $delegateLog['pcNumber'] }}
                        </div>

                        <div class="col-span-6 break-words text-sm">
                            {{ $delegateLog['description'] }}
                        </div>

                        <div class="col-span-1 break-words text-sm">
                            {{ $delegateLog['updateDateTime'] }}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>