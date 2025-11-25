@extends('admin.layouts.master')

@section('content')
    @livewire('email-broadcast', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'badgeCategory' => $badgeCategory])
@endsection
