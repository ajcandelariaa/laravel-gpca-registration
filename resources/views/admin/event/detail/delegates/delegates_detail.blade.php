@extends('admin.layouts.master')

@section('content')
    @livewire('delegate-details', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'finalDelegate' => $finalDelegate])
@endsection
