@extends('admin.layouts.master')

@section('content')

    @livewire('delegate-fees', ['eventCategory' => $eventCategory, 'eventId' => $eventId])

@endsection
