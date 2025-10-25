@extends('layouts.app')

@section('title', 'Panel del Estudiante')
@section('header', 'Panel del Estudiante')
@section('subheader', 'Resumen de tu progreso académico.')

@section('content')
    <div class="container-fluid px-4">
        @include('students.partials.dashboard-stats')
    </div>
@endsection
