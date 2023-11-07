@extends('admin.layouts.master')

@section('content')
    @if ($eventCategory == 'AFV')
        @livewire('event-visitors-list', ['eventId' => $eventId, 'eventCategory' => $eventCategory])
    @else
        @livewire('event-delegates-list', ['eventId' => $eventId, 'eventCategory' => $eventCategory])
    @endif
@endsection
