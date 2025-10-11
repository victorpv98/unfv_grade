@extends('layouts.app')

@section('header', 'Nuevo Docente')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        @php
            $availableUsers = $users ?? \App\Models\User::where('role', 'teacher')
                                                         ->doesntHave('teacher')
                                                         ->orderBy('name')
                                                         ->get();
        @endphp

        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-chalkboard-teacher text-primary me-2"></i>
                            Registrar nuevo docente
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.teachers.store') }}" method="POST" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="user_id" class="form-label fw-semibold text-secondary">
                                    Usuario asociado <span class="text-danger">*</span>
                                </label>
                                <select name="user_id"
                                        id="user_id"
                                        class="form-select @error('user_id') is-invalid @enderror"
                                        required>
                                    <option value="" disabled selected>Seleccione un usuario con rol docente</option>
                                    @forelse($availableUsers as $user)
                                        <option value="{{ $user->id }}"
                                            @selected(old('user_id') == $user->id)>
                                            {{ $user->name }} &mdash; {{ $user->email }}
                                        </option>
                                    @empty
                                        <option value="" disabled>No hay usuarios disponibles</option>
                                    @endforelse
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="specialty" class="form-label fw-semibold text-secondary">
                                    Especialidad
                                </label>
                                <input type="text"
                                       id="specialty"
                                       name="specialty"
                                       value="{{ old('specialty') }}"
                                       class="form-control @error('specialty') is-invalid @enderror"
                                       placeholder="Ej. Sistemas Digitales"
                                       maxlength="100">
                                @error('specialty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-text">Describe la especialidad o línea de enseñanza del docente.</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-arrow-left me-2"></i>
                                    Volver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save me-2"></i>
                                    Guardar docente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
