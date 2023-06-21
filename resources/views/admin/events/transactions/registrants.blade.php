@extends('admin.layouts.master')

@section('content')
    @if ($eventCategory == 'AFS')
        @livewire('spouse-registrants-list', ['eventId' => $eventId, 'eventCategory' => $eventCategory])
    @elseif ($eventCategory == 'AFV')
        @livewire('visitor-registrants-list', ['eventId' => $eventId, 'eventCategory' => $eventCategory])
    @elseif ($eventCategory == 'RCCA')
        @livewire('rcc-awards-registrants-list', ['eventId' => $eventId, 'eventCategory' => $eventCategory])
    @else
        @livewire('registrants-list', ['eventId' => $eventId, 'eventCategory' => $eventCategory])
    @endif
@endsection
