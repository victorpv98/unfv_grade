@extends('layouts.app')

@section('header', 'Editar Curso')

@section('content')
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-book-open text-primary me-2"></i>
                            Actualizar información del curso
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.courses.update', $course) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="code" class="form-label fw-semibold text-secondary">
                                        Código <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="code"
                                           name="code"
                                           value="{{ old('code', $course->code) }}"
                                           class="form-control @error('code') is-invalid @enderror"
                                           placeholder="Ej. INF101"
                                           maxlength="10"
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label for="name" class="form-label fw-semibold text-secondary">
                                        Nombre <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $course->name) }}"
                                           class="form-control @error('name') is-invalid @enderror"
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
                                           value="{{ old('credits', $course->credits) }}"
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
                                        <option value="" disabled>Seleccione una escuela</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}"
                                                    @selected(old('school_id', $course->school_id) == $school->id)>
                                                {{ $school->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('school_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <label for="teacher_id" class="form-label fw-semibold text-secondary">
                                        Docente responsable <span class="text-danger">*</span>
                                    </label>
                                    <select name="teacher_id"
                                            id="teacher_id"
                                            class="form-select @error('teacher_id') is-invalid @enderror"
                                            required>
                                        <option value="" disabled>Seleccione un docente</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}"
                                                @selected(old('teacher_id', $currentTeacherId) == $teacher->id)>
                                                {{ $teacher->user?->name ?? 'Docente sin usuario' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('teacher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3 mb-lg-0">
                                        @php
                                            $currentPrerequisites = (array) old('prerequisites', $selectedPrerequisites ?? []);
                                        @endphp
                                        <label for="prerequisites" class="form-label fw-semibold">
                                            Pre-requisitos
                                        </label>
                                        <select name="prerequisites[]"
                                                id="prerequisites"
                                                class="form-select choices-multiple"
                                                multiple>
                                            @foreach ($allCourses as $availableCourse)
                                                <option value="{{ $availableCourse->id }}"
                                                    @selected(in_array($availableCourse->id, $currentPrerequisites))>
                                                    {{ $availableCourse->code }} — {{ $availableCourse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('prerequisites')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
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
