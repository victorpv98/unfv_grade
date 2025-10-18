@extends('layouts.app')

@section('header', 'Resumen Final de Notas')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-semibold text-secondary mb-1">
                    <i class="fa-solid fa-chart-line text-primary me-2"></i>
                    {{ $course->code }} · {{ $course->name }}
                </h4>
                <p class="text-muted small mb-0">
                    Escuela: {{ $course->school?->name ?? 'Sin escuela definida' }} · Créditos: {{ $course->credits }}
                </p>
            </div>
            <a href="{{ route('teacher.my-courses') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Volver a mis cursos
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title fw-semibold mb-0 text-secondary">
                    <i class="fa-solid fa-graduation-cap text-primary me-2"></i>
                    Promedios finales por estudiante
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th scope="col" style="width: 16%;">Código</th>
                                <th scope="col">Estudiante</th>
                                <th scope="col" style="width: 18%;">Promedio</th>
                                <th scope="col" style="width: 18%;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grades as $finalGrade)
                                @php
                                    $student = $finalGrade->courseStudent?->student;
                                    $average = $finalGrade->average;
                                    $status = $finalGrade->status;
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-secondary">{{ $student?->code ?? '—' }}</td>
                                    <td>{{ $student?->user?->name ?? 'Sin nombre' }}</td>
                                    <td>
                                        @if(!is_null($average))
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ number_format($average, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($status)
                                            <x-final-grade.status-badge :status="$status" />
                                        @else
                                            <span class="text-muted">Sin registrar</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-circle-info me-2"></i>
                                        No se encontraron promedios registrados para este curso.
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
