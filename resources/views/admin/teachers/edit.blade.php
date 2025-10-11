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

                        <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="specialty" class="form-label fw-semibold text-secondary">
                                    Especialidad
                                </label>
                                <input type="text"
                                       id="specialty"
                                       name="specialty"
                                       value="{{ old('specialty', $teacher->specialty) }}"
                                       class="form-control @error('specialty') is-invalid @enderror"
                                       maxlength="100"
                                       placeholder="Ej. Sistemas Digitales">
                                @error('specialty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-text">Describe brevemente el área de especialización.</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-arrow-left me-2"></i>
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save me-2"></i>
                                    Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
