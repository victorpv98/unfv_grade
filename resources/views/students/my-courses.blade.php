@extends('layouts.app')

@section('header', 'Mis Cursos')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title fw-semibold mb-0 text-secondary">
                    <i class="fa-solid fa-book-open text-primary me-2"></i>
                    Cursos matriculados
                </h5>
                <p class="text-muted small mb-0">Lista de cursos asignados durante el periodo académico actual.</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th scope="col" style="width: 12%;">Código</th>
                                <th scope="col">Nombre</th>
                                <th scope="col" style="width: 12%;">Créditos</th>
                                <th scope="col" style="width: 25%;">Periodo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses as $courseStudent)
                                @php
                                    $course = $courseStudent->course;
                                    $period = $courseStudent->period?->name;
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-secondary">{{ $course?->code ?? '—' }}</td>
                                    <td>{{ $course?->name ?? 'Curso no disponible' }}</td>
                                    <td>{{ $course?->credits ?? '—' }}</td>
                                    <td>{{ $period ?? 'Periodo no asignado' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-circle-info me-2"></i>
                                        No te encuentras matriculado en cursos actualmente.
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
