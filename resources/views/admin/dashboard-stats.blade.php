@extends('layouts.app')

@section('header', 'Panel Administrativo')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        @include('admin.partials.dashboard-metrics')
    </div>
@endsection
