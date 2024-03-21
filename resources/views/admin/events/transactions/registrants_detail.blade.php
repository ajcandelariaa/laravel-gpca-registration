@extends('admin.layouts.master')

@section('content')
    @if ($eventCategory == 'AFS')
        @livewire('spouse-registrant-details', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $registrantId, 'finalData' => $finalData])
    @elseif ($eventCategory == 'AFV')
        @livewire('visitor-registrant-details', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $registrantId, 'finalData' => $finalData])
    @elseif ($eventCategory == 'RCCA')
        @livewire('rcc-awards-registrant-details', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $registrantId, 'finalData' => $finalData])
    @elseif ($eventCategory == 'SCEA')
        @livewire('awards-registrant-details', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $registrantId, 'finalData' => $finalData])
    @else
        @livewire('registrant-details', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $registrantId, 'finalData' => $finalData])
    @endif
@endsection
