@extends('admin.layouts.master')

@section('content')

    @livewire('event-registration-type', ['eventCategory' => $eventCategory, 'eventId' => $eventId])

@endsection
