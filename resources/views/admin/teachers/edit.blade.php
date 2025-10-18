@extends('layouts.app')

@section('header', 'Editar Docente')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-chalkboard-teacher text-primary me-2"></i>
                            Actualizar información del docente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <p class="fw-semibold text-secondary mb-1">{{ $teacher->user?->name }}</p>
                            <p class="text-muted mb-0">{{ $teacher->user?->email }}</p>
                        </div>

                        @include('admin.teachers.form', [
                            'teacher' => $teacher,
                            'schools' => $schools,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
