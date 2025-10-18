@extends('layouts.app')

@section('header', 'Nuevo Estudiante')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-user-graduate text-primary me-2"></i>
                            Registrar nuevo estudiante
                        </h5>
                    </div>
                    <div class="card-body">
                        @include('admin.students.form', [
                            'schools' => $schools,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
