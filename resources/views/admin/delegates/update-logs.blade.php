@extends('admin.layouts.master')

@section('content')
    @livewire('delegates-update-logs', ['eventCategory' => $eventCategory, 'eventId' => $eventId])
@endsection