@extends('admin.layouts.master')

@section('content')
    @livewire('registrant-details', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'registrantId' => $registrantId, 'finalData' => $finalData])
@endsection
