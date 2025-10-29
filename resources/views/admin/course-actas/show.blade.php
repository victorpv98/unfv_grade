@extends('layouts.app')

@section('header', 'Detalle del Acta')
@section('subheader', $acta->course?->code . ' · ' . $acta->course?->name)

@php
    $course = $acta->course;
    $latestGenerated = $acta->latestGeneratedFile;
    $latestUploaded = $acta->latestUploadedFile;
    $defaultPreview = $latestUploaded ?? $latestGenerated;
    $timezone = config('app.timezone');
@endphp

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-semibold text-secondary mb-1">
                    <i class="fa-solid fa-file-contract text-primary me-2"></i>
                    Acta · {{ $course?->code ?? 'Curso' }}
                </h4>
                <p class="text-muted small mb-0">
                    Periodo: {{ $acta->period?->name ?? 'Sin periodo' }} · Última actualización: {{ $acta->updated_at->clone()->setTimezone($timezone)->format('d/m/Y H:i') }}
                </p>
            </div>
            <a href="{{ route('admin.courses.actas.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Volver al listado
            </a>
        </div>

        <div class="row g-3">
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-circle-info text-primary me-2"></i>
                            Información general
                        </h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted small text-uppercase">Curso</dt>
                            <dd class="col-7">{{ $course?->code }} · {{ $course?->name }}</dd>

                            <dt class="col-5 text-muted small text-uppercase">Escuela</dt>
                            <dd class="col-7">{{ $course?->school?->name ?? 'Sin escuela' }}</dd>

                            <dt class="col-5 text-muted small text-uppercase">Créditos</dt>
                            <dd class="col-7">{{ $course?->credits ?? '—' }}</dd>

                            <dt class="col-5 text-muted small text-uppercase">Estado</dt>
                            <dd class="col-7">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-warning-subtle text-warning',
                                        'generated' => 'bg-primary-subtle text-primary',
                                        'signed' => 'bg-success-subtle text-success',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pendiente',
                                        'generated' => 'Generado',
                                        'signed' => 'Firmado',
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$acta->status] ?? 'bg-light text-secondary' }}">
                                    {{ $statusLabels[$acta->status] ?? ucfirst($acta->status) }}
                                </span>
                            </dd>

                            <dt class="col-5 text-muted small text-uppercase">Generada el</dt>
                            <dd class="col-7">{{ optional($acta->generated_at)?->timezone($timezone)->format('d/m/Y H:i') ?? '—' }}</dd>

                            <dt class="col-5 text-muted small text-uppercase">Generada por</dt>
                            <dd class="col-7">{{ $acta->generatedBy?->name ?? 'Sistema' }}</dd>

                            <dt class="col-5 text-muted small text-uppercase">Estado actualizado por</dt>
                            <dd class="col-7">{{ $acta->lastStatusChangedBy?->name ?? 'Sistema' }}</dd>
                        </dl>

                        @if($signaturePreview)
                            <div class="mt-3">
                                <p class="text-muted small text-uppercase fw-semibold mb-2">Vista previa de la firma</p>
                                <img src="{{ $signaturePreview['data_url'] }}" alt="Firma docente" class="img-fluid border rounded p-2 bg-white">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                {{-- <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-eye text-primary me-2"></i>
                            Vista previa del documento
                        </h5>
                        <div class="d-flex gap-2">
                            @if($latestGenerated)
                                <a href="{{ route('admin.courses.actas.files.download', $latestGenerated) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-file-arrow-down me-1"></i>
                                    PDF generado
                                </a>
                            @endif
                            @if($latestUploaded)
                                <a href="{{ route('admin.courses.actas.files.download', $latestUploaded) }}" class="btn btn-sm btn-outline-success">
                                    <i class="fa-solid fa-file-signature me-1"></i>
                                    PDF firmado
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($defaultPreview)
                            <iframe id="acta-preview-frame"
                                    src="{{ route('admin.courses.actas.files.preview', $defaultPreview) }}"
                                    style="width: 100%; min-height: 480px;"
                                    class="border rounded"></iframe>
                        @else
                            <p class="text-muted text-center mb-0 py-5">No hay documentos para previsualizar.</p>
                        @endif
                    </div>
                </div> --}}

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-semibold mb-0 text-secondary">
                            <i class="fa-solid fa-users text-primary me-2"></i>
                            Estudiantes y resultados
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
                                            <td colspan="5" class="text-center py-4 text-muted">Sin estudiantes registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
