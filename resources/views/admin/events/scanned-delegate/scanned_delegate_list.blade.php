@extends('admin.layouts.master')

@section('content')
    @if ($eventCategory == 'AFV')
        @livewire('scanned-visitor-list', ['eventCategory' => $eventCategory, 'eventId' => $eventId])
    @else
        @livewire('scanned-delegate-list', ['eventCategory' => $eventCategory, 'eventId' => $eventId])
    @endif
@endsection
