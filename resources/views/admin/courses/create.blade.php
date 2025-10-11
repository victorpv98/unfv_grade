@extends('layouts.app')

@section('header', 'Nuevo Curso')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-book-open text-primary me-2"></i>
                            Registrar nuevo curso
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Formulario de creación --}}
                        <form action="{{ route('admin.courses.store') }}" method="POST" novalidate>
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="code" class="form-label fw-semibold text-secondary">
                                        Código <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="code"
                                           name="code"
                                           value="{{ old('code') }}"
                                           class="form-control @error('code') is-invalid @enderror"
                                           placeholder="Ej. INF101"
                                           maxlength="10"
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="form-text">Código único del curso.</div>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label for="name" class="form-label fw-semibold text-secondary">
                                        Nombre <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           class="form-control @error('name') is-invalid @enderror"
                                           placeholder="Nombre del curso"
                                           maxlength="150"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="credits" class="form-label fw-semibold text-secondary">
                                        Créditos <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           id="credits"
                                           name="credits"
                                           value="{{ old('credits') }}"
                                           class="form-control @error('credits') is-invalid @enderror"
                                           min="1"
                                           required>
                                    @error('credits')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="school_id" class="form-label fw-semibold text-secondary">
                                        Escuela <span class="text-danger">*</span>
                                    </label>
                                    <select name="school_id"
                                            id="school_id"
                                            class="form-select @error('school_id') is-invalid @enderror"
                                            required>
                                        <option value="" selected disabled>Seleccione una escuela</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}"
                                                @selected(old('school_id') == $school->id)>
                                                {{ $school->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('school_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-arrow-left me-2"></i>
                                    Volver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save me-2"></i>
                                    Guardar curso
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
