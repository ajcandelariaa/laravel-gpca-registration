@extends('admin.layouts.master')

@section('content')

    @livewire('promo-code', ['eventCategory' => $eventCategory, 'eventId' => $eventId])

@endsection
