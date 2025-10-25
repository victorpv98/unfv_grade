@extends('layouts.app')

@section('header', 'Editar Escuela')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-building-columns text-primary me-2"></i>
                            Actualizar datos de la escuela
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.schools.update', $school) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="code" class="form-label fw-semibold text-secondary">
                                    Código <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="code"
                                       name="code"
                                       value="{{ old('code', $school->code) }}"
                                       class="form-control @error('code') is-invalid @enderror"
                                       placeholder="Ej. IEI, INF, MEC, TEL"
                                       maxlength="10"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-text">Código corto que identifique a la escuela.</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold text-secondary">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $school->name) }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Nombre completo de la escuela"
                                       maxlength="160"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-text">Ejemplo: Escuela Profesional de Ingeniería Informática.</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.schools.index') }}" class="btn btn-outline-secondary">
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
