@extends('admin.layouts.master')

@section('content')
    @livewire('scanned-delegate-list-categorized', ['eventCategory' => $eventCategory, 'eventId' => $eventId])
@endsection
