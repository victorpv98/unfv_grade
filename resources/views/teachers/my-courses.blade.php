@extends('layouts.app')

@section('header', 'Mis Cursos Asignados')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title fw-semibold mb-0 text-secondary">
                        <i class="fa-solid fa-list-check text-primary me-2"></i>
                        Cursos en los que dicta clases
                    </h5>
                    <p class="text-muted small mb-0">
                        Seleccione un curso para registrar notas o revisar promedios finales.
                    </p>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th scope="col" style="width: 12%;">Código</th>
                                <th scope="col">Nombre</th>
                                <th scope="col" style="width: 12%;">Créditos</th>
                                <th scope="col" style="width: 18%;">Escuela</th>
                                <th scope="col" style="width: 16%;">Estudiantes</th>
                                <th scope="col" class="text-center" style="width: 20%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses as $course)
                                <tr>
                                    @php
                                        $enrolledCount = $course->courseStudents()->count();
                                    @endphp
                                    <td class="fw-semibold text-secondary">{{ $course->code }}</td>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->credits }}</td>
                                    <td>{{ $course->school?->name ?? 'Sin escuela' }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $enrolledCount }}
                                            {{ \Illuminate\Support\Str::plural('estudiante', $enrolledCount) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('teacher.courses.grades', $course) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Registrar notas">
                                                <i class="fa-solid fa-list-check me-1"></i>
                                                Notas
                                            </a>
                                            <a href="{{ route('teacher.courses.summary', $course) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Resumen final">
                                                <i class="fa-solid fa-chart-line me-1"></i>
                                                Resumen
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-circle-info me-2"></i>
                                        Aún no tienes cursos asignados para este periodo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
