@extends('layouts.app')

@section('header')
    Editar Horario
@endsection

@section('content')
<div class="card shadow border-0 rounded-3 mb-4">
    <div class="card-header bg-light py-3 d-flex flex-row align-items-center justify-content-between border-bottom">
        <h5 class="m-0 fw-semibold text-primary">
            <i class="fas fa-edit me-2"></i>Editar Horario
        </h5>
        <a href="{{ route('admin.schedules.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="course_id" class="form-label fw-medium">
                        <i class="fas fa-book text-primary me-1"></i>Curso
                    </label>
                    <select class="form-select @error('course_id') is-invalid @enderror" 
                        id="course_id" name="course_id" required>
                        <option value="">Seleccione un curso</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $schedule->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->name }} ({{ $course->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="teacher_id" class="form-label fw-medium">
                        <i class="fas fa-chalkboard-teacher text-primary me-1"></i>Profesor
                    </label>
                    <select class="form-select @error('teacher_id') is-invalid @enderror" 
                        id="teacher_id" name="teacher_id" required>
                        <option value="">Seleccione un profesor</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $schedule->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->user->name }} ({{ $teacher->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="classroom" class="form-label fw-medium">
                        <i class="fas fa-door-open text-primary me-1"></i>Aula
                    </label>
                    <input type="text" class="form-control @error('classroom') is-invalid @enderror" 
                        id="classroom" name="classroom" value="{{ old('classroom', $schedule->classroom) }}" 
                        required placeholder="Ejemplo: A-101">
                    @error('classroom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="day" class="form-label fw-medium">
                        <i class="fas fa-calendar-day text-primary me-1"></i>Día
                    </label>
                    <select class="form-select @error('day') is-invalid @enderror" 
                        id="day" name="day" required>
                        <option value="">Seleccione un día</option>
                        <option value="monday" {{ old('day', $schedule->day) == 'monday' ? 'selected' : '' }}>Lunes</option>
                        <option value="tuesday" {{ old('day', $schedule->day) == 'tuesday' ? 'selected' : '' }}>Martes</option>
                        <option value="wednesday" {{ old('day', $schedule->day) == 'wednesday' ? 'selected' : '' }}>Miércoles</option>
                        <option value="thursday" {{ old('day', $schedule->day) == 'thursday' ? 'selected' : '' }}>Jueves</option>
                        <option value="friday" {{ old('day', $schedule->day) == 'friday' ? 'selected' : '' }}>Viernes</option>
                        <option value="saturday" {{ old('day', $schedule->day) == 'saturday' ? 'selected' : '' }}>Sábado</option>
                    </select>
                    @error('day')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="start_time" class="form-label fw-medium">
                        <i class="fas fa-clock text-primary me-1"></i>Hora de inicio
                    </label>
                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                        id="start_time" name="start_time" value="{{ old('start_time', $schedule->start_time) }}" required>
                    @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="end_time" class="form-label fw-medium">
                        <i class="fas fa-clock text-primary me-1"></i>Hora de fin
                    </label>
                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                        id="end_time" name="end_time" value="{{ old('end_time', $schedule->end_time) }}" required>
                    @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="semester" class="form-label fw-medium">
                        <i class="fas fa-graduation-cap text-primary me-1"></i>Semestre
                    </label>
                    <input type="text" class="form-control @error('semester') is-invalid @enderror" 
                        id="semester" name="semester" value="{{ old('semester', $schedule->semester) }}" 
                        required placeholder="Ejemplo: 2025-I">
                    @error('semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-times me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Actualizar Horario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection