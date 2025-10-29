@extends('layouts.app')

@section('header', 'Generar Acta del Curso')
@section('subheader', $course->code . ' · ' . $course->name)

@php
    $teacherUser = optional($teacherAssignment?->teacher)->user;
    $timezone = config('app.timezone');
@endphp

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-semibold text-secondary mb-1">
                    <i class="fa-solid fa-file-signature text-primary me-2"></i>
                    Acta de Evaluación · {{ $course->code }}
                </h4>
                <p class="text-muted small mb-0">
                    Periodo: {{ $activePeriod?->name ?? 'No definido' }} · Docente responsable: {{ $teacherUser?->name ?? 'No asignado' }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('teacher.courses.summary', $course) }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-chart-line me-2"></i>
                    Resumen de notas
                </a>
                <a href="{{ route('teacher.my-courses') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Volver a mis cursos
                </a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-file-pdf text-primary me-2"></i>
                            Generar acta PDF
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="text-muted mb-1">
                                Estado actual: <span class="badge bg-primary-subtle text-primary text-uppercase">{{ $acta->status }}</span>
                            </p>
                            @if($latestGenerated)
                                <p class="text-muted small mb-0">
                                    Última generación: {{ optional($acta->generated_at)?->timezone($timezone)->format('d/m/Y H:i') ?? '—' }}
                                    · Archivo: <a href="{{ route('teacher.courses.acta.files.download', [$course, $latestGenerated]) }}" class="text-decoration-none">descargar</a>
                                </p>
                            @else
                                <p class="text-muted small mb-0">
                                    Aún no se ha generado ningún acta para este curso.
                                </p>
                            @endif
                        </div>

                        <p class="text-muted small">
                            El PDF generado no incluirá firma digital; el docente deberá firmarlo mediante el aplicativo institucional correspondiente.
                        </p>

                        <form action="{{ route('teacher.courses.acta.generate', $course) }}"
                              method="POST"
                              class="d-flex flex-column gap-3">
                            @csrf

                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       value="1"
                                       id="confirm_generation"
                                       name="confirm_generation"
                                       required>
                                <label class="form-check-label" for="confirm_generation">
                                    Confirmo que las notas registradas son correctas y deseo generar el acta en PDF.
                                </label>
                                @error('confirm_generation')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary align-self-start">
                                <i class="fa-solid fa-file-export me-2"></i>
                                Generar acta en PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-file-upload text-primary me-2"></i>
                            Subir PDF firmado
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">
                            Si el acta fue firmada manualmente fuera del sistema, puede cargar una copia escaneada en formato PDF para archivarla y notificar al administrador.
                        </p>

                        <form action="{{ route('teacher.courses.acta.upload', $course) }}"
                              method="POST"
                              enctype="multipart/form-data"
                              class="d-flex flex-column gap-3">
                            @csrf
                            <div>
                                <label for="signed_pdf" class="form-label fw-semibold small text-uppercase text-muted">
                                    Seleccionar archivo (máx. 8MB)
                                </label>
                                <input type="file"
                                       name="signed_pdf"
                                       id="signed_pdf"
                                       class="form-control @error('signed_pdf') is-invalid @enderror"
                                       accept="application/pdf"
                                       required>
                                @error('signed_pdf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-outline-primary align-self-start">
                                <i class="fa-solid fa-cloud-arrow-up me-2"></i>
                                Subir PDF firmado
                            </button>
                        </form>

                        @if($latestUploaded)
                            <div class="alert alert-info d-flex align-items-center gap-2 mt-3 mb-0 small" role="status">
                                <i class="fa-solid fa-file-signature text-primary"></i>
                                <div>
                                    <div class="fw-semibold text-secondary">Último PDF firmado</div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="text-muted">{{ $latestUploaded->filename }}</span>
                                        <span class="text-muted">· {{ optional($latestUploaded->uploaded_at)?->timezone($timezone)->format('d/m/Y H:i') }}</span>
                                        <a href="{{ route('teacher.courses.acta.files.download', [$course, $latestUploaded]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i>
                                            Ver / Descargar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-semibold mb-0 text-secondary">
                        <i class="fa-solid fa-users text-primary me-2"></i>
                        Resumen de estudiantes
                </h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-secondary">Total: {{ $stats['total'] }}</span>
                    <span class="badge bg-success-subtle text-success">Aprobados: {{ $stats['approved'] }}</span>
                    <span class="badge bg-danger-subtle text-danger">Desaprobados: {{ $stats['disapproved'] }}</span>
                    <span class="badge bg-warning-subtle text-warning">Sustitutorio: {{ $stats['substitute'] }}</span>
                    <span class="badge bg-info-subtle text-info">Aplazado: {{ $stats['makeup'] }}</span>
                    <span class="badge bg-primary-subtle text-primary">Promedio: {{ number_format($stats['average'], 2) }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Estudiante</th>
                                <th>Promedio</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $courseStudent)
                                @php
                                    $student = $courseStudent->student;
                                    $finalGrade = $courseStudent->finalGrade;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student?->code ?? '—' }}</td>
                                    <td>{{ $student?->user?->name ?? 'Sin nombre' }}</td>
                                    <td>
                                        @if(!is_null($finalGrade?->average))
                                            <span class="badge bg-primary-subtle text-primary">{{ number_format($finalGrade->average, 2) }}</span>
                                        @else
                                            <span class="text-muted">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($finalGrade?->status)
                                            <x-final-grade.status-badge :status="$finalGrade->status" />
                                        @else
                                            <span class="text-muted">Sin registrar</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No hay estudiantes matriculados para este curso.
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
