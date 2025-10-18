@extends('layouts.app')

@section('header', 'Panel Administrativo')

@section('content')
    <div class="container-fluid px-0">
        {{-- Alertas globales --}}
        @include('layouts.partials.alert')

        {{-- Tarjetas principales con los totales del sistema --}}
        @include('admin.partials.dashboard-metrics')
    </div>
@endsection
