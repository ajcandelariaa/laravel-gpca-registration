@extends('admin.layouts.master')

@section('content')

    @livewire('printed-badge-list', ['eventCategory' => $eventCategory, 'eventId' => $eventId])

@endsection