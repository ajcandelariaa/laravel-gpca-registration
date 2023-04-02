@extends('admin.layouts.master')

@section('content')
    @livewire('registrants-list', ['eventId' => $eventId, 'eventCategory' => $eventCategory])
@endsection
