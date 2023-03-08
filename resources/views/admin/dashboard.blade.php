@extends('admin.layouts.master')

@section('content')
    <h1>{{ $pageTitle }}</h1>
    @if (Session::has('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
    @endif
@endsection
