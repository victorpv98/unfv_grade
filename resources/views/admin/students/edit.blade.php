@extends('layouts.app')

@section('header', 'Editar Estudiante')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-user-graduate text-primary me-2"></i>
                            Actualizar información del estudiante
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <p class="fw-semibold text-secondary mb-1">{{ $student->user?->name }}</p>
                            <p class="text-muted mb-0">{{ $student->user?->email }}</p>
                        </div>

                        <form action="{{ route('admin.students.update', $student) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="code" class="form-label fw-semibold text-secondary">
                                        Código estudiante <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="code"
                                           name="code"
                                           value="{{ old('code', $student->code) }}"
                                           class="form-control @error('code') is-invalid @enderror"
                                           maxlength="15"
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="admission_year" class="form-label fw-semibold text-secondary">
                                        Año de ingreso <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           id="admission_year"
                                           name="admission_year"
                                           value="{{ old('admission_year', $student->admission_year) }}"
                                           class="form-control @error('admission_year') is-invalid @enderror"
                                           min="2000"
                                           max="{{ now()->year }}"
                                           required>
                                    @error('admission_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
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
