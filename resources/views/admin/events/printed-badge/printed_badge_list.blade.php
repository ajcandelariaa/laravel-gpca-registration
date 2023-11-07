@extends('admin.layouts.master')

@section('content')
    @if ($eventCategory == 'AFV')
        @livewire('visitor-printed-badge-list', ['eventCategory' => $eventCategory, 'eventId' => $eventId])
    @else
        @livewire('printed-badge-list', ['eventCategory' => $eventCategory, 'eventId' => $eventId])
    @endif
@endsection
