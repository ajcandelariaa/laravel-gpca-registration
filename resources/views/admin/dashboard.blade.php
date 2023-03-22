@extends('admin.layouts.master')

@section('content')
    {{-- @if (Session::has('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
    @endif --}}
    <img src="{{ asset('assets/images/website-under-construction.jpg') }}" alt="" class="mx-auto w-full object-cover" style="height: 85vh">
@endsection
