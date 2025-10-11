@extends('layouts.app')

@section('header', 'Nuevo Estudiante')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        @php
            $availableUsers = $users ?? \App\Models\User::where('role', 'student')
                                                         ->doesntHave('student')
                                                         ->orderBy('name')
                                                         ->get();
        @endphp

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
                        <form action="{{ route('admin.students.store') }}" method="POST" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="user_id" class="form-label fw-semibold text-secondary">
                                    Usuario asociado <span class="text-danger">*</span>
                                </label>
                                <select name="user_id"
                                        id="user_id"
                                        class="form-select @error('user_id') is-invalid @enderror"
                                        required>
                                    <option value="" selected disabled>Seleccione un usuario con rol estudiante</option>
                                    @forelse($availableUsers as $user)
                                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
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
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="code" class="form-label fw-semibold text-secondary">
                                        Código estudiante <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="code"
                                           name="code"
                                           value="{{ old('code') }}"
                                           class="form-control @error('code') is-invalid @enderror"
                                           placeholder="Ej. 20201234"
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
                                           value="{{ old('admission_year') }}"
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
                                    Volver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save me-2"></i>
                                    Guardar estudiante
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
