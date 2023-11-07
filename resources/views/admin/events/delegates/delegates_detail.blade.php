@extends('admin.layouts.master')

@section('content')
    @if ($eventCategory == 'AFV')
        @livewire('visitor-details', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'finalVisitor' => $finalVisitor])
    @else
        @livewire('delegate-details', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'finalDelegate' => $finalDelegate])
    @endif
@endsection
