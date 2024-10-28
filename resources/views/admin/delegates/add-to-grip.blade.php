@extends('admin.layouts.master')

@section('content')
    @livewire('add-delegates-to-grip', ['event' => $event])
@endsection