@extends('admin.layouts.master')

@section('content')
    @livewire('event-delegates-list', ['eventId' => $eventId, 'eventCategory' => $eventCategory])
@endsection
