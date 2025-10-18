@extends('layouts.app')

@section('title', 'Panel del Docente')

@section('header', 'Panel del Docente')

@section('subheader', 'Resumen de tus cursos, alumnos y evaluaciones.')

@section('content')
    <div class="container-fluid px-4">
        {{-- Dashboard del docente --}}
        @include('teachers.partials.dashboard-stats')
    </div>
@endsection
