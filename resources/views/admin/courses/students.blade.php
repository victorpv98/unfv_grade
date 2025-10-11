@extends('layouts.app')

@section('header', 'Estudiantes del Curso')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-semibold text-secondary mb-1">
                    <i class="fa-solid fa-book-open text-primary me-2"></i>
                    {{ $course->code }} · {{ $course->name }}
                </h4>
                <p class="text-muted small mb-0">
                    Escuela: {{ $course->school?->name ?? 'Sin asignar' }} · Créditos: {{ $course->credits }}
                </p>
            </div>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Volver a cursos
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title fw-semibold mb-0 text-secondary">
                    <i class="fa-solid fa-user-graduate text-primary me-2"></i>
                    Estudiantes matriculados
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th scope="col" style="width: 12%;">Código</th>
                                <th scope="col">Estudiante</th>
                                <th scope="col" style="width: 18%;">Año de ingreso</th>
                                <th scope="col" style="width: 18%;">Promedio final</th>
                                <th scope="col" style="width: 18%;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php
                                    $enrollment = $student->courseStudents()
                                                          ->with(['finalGrade'])
                                                          ->where('course_id', $course->id)
                                                          ->first();
                                    $finalGrade = $enrollment?->finalGrade?->average;
                                    $status = $enrollment?->finalGrade?->status;
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-secondary">{{ $student->code }}</td>
                                    <td>{{ $student->user?->name ?? 'Sin nombre asignado' }}</td>
                                    <td>{{ $student->admission_year ?? '—' }}</td>
                                    <td>
                                        @if(!is_null($finalGrade))
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ number_format($finalGrade, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($status)
                                            <span class="badge {{ $status === 'Aprobado' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                {{ $status }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin registrar</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-circle-info me-2"></i>
                                        No hay estudiantes matriculados en este curso.
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
