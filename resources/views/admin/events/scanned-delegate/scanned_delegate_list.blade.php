@extends('admin.layouts.master')

@section('content')

    @livewire('scanned-delegate-list', ['eventCategory' => $eventCategory, 'eventId' => $eventId])

@endsection