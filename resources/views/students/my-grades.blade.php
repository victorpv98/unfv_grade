@extends('layouts.app')

@section('header', 'Mis Calificaciones')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title fw-semibold mb-0 text-secondary">
                    <i class="fa-solid fa-list-check text-primary me-2"></i>
                    Resumen de calificaciones finales
                </h5>
                <p class="text-muted small mb-0">Consulta tus promedios finales y estado académico por curso.</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th scope="col" style="width: 12%;">Código</th>
                                <th scope="col">Curso</th>
                                <th scope="col" style="width: 20%;">Promedio final</th>
                                <th scope="col" style="width: 20%;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grades as $finalGrade)
                                @php
                                    $course = $finalGrade->courseStudent?->course;
                                    $average = $finalGrade->average;
                                    $status = $finalGrade->status;
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-secondary">{{ $course?->code ?? '—' }}</td>
                                    <td>{{ $course?->name ?? 'Curso no disponible' }}</td>
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
                                        Aún no hay calificaciones registradas para tus cursos.
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
