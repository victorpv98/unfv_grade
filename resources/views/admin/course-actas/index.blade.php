@extends('layouts.app')

@section('header', 'Actas de Cursos')
@section('subheader', 'Seguimiento de generación y firma de actas por curso')

@php
    $timezone = config('app.timezone');
@endphp

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-semibold mb-0 text-secondary">
                    <i class="fa-solid fa-file-contract text-primary me-2"></i>
                    Listado de actas generadas
                </h5>
                <span class="badge bg-light text-secondary">Total: {{ $actas->total() }}</span>
            </div>
            <div class="card-body border-bottom">
                <form method="GET" action="{{ route('admin.courses.actas.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4 col-lg-3">
                        <label for="search" class="form-label small text-uppercase text-muted fw-semibold">Curso</label>
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ $filters['search'] }}"
                               placeholder="Buscar por código o nombre"
                               class="form-control">
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label for="status" class="form-label small text-uppercase text-muted fw-semibold">Estado</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="pending" @selected($filters['status'] === 'pending')>Pendiente</option>
                            <option value="generated" @selected($filters['status'] === 'generated')>Generado</option>
                            <option value="signed" @selected($filters['status'] === 'signed')>Firmado</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label for="period_id" class="form-label small text-uppercase text-muted fw-semibold">Periodo</label>
                        <select id="period_id" name="period_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" @selected($filters['period_id'] == $period->id)>
                                    {{ $period->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-magnifying-glass me-2"></i>
                            Filtrar
                        </button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.courses.actas.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-rotate-left me-2"></i>
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th>Curso</th>
                                <th>Periodo</th>
                                <th>Estado</th>
                                <th>Última generación</th>
                                <th>Última firma</th>
                                <th>Actualizado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($actas as $acta)
                                @php
                                    $course = $acta->course;
                                    $latestGenerated = $acta->latestGeneratedFile;
                                    $latestUploaded = $acta->latestUploadedFile;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-secondary">{{ $course?->code ?? 'N/A' }}</div>
                                        <div class="text-muted small">{{ $course?->name ?? 'Curso sin nombre' }}</div>
                                        <div class="text-muted small">{{ $course?->school?->name ?? 'Sin escuela' }}</div>
                                    </td>
                                    <td>{{ $acta->period?->name ?? 'Sin periodo' }}</td>
                                    <td>
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
                                    </td>
                                    <td>
                                        @if($latestGenerated)
                                            <a href="{{ route('admin.courses.actas.files.download', $latestGenerated) }}"
                                               class="text-decoration-none d-block">
                                                {{ $latestGenerated->filename }}
                                            </a>
                                            <small class="text-muted">
                                                {{ optional($latestGenerated->uploaded_at)?->timezone($timezone)->format('d/m/Y H:i') ?? '—' }}
                                            </small>
                                        @else
                                            <span class="text-muted small">Sin registro</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($latestUploaded)
                                            <a href="{{ route('admin.courses.actas.files.download', $latestUploaded) }}"
                                               class="text-decoration-none d-block">
                                                {{ $latestUploaded->filename }}
                                            </a>
                                            <small class="text-muted">
                                                {{ optional($latestUploaded->uploaded_at)?->timezone($timezone)->format('d/m/Y H:i') ?? '—' }}
                                            </small>
                                        @else
                                            <span class="text-muted small">Sin registro</span>
                                        @endif
                                    </td>
                                    <td>{{ $acta->updated_at->clone()->setTimezone($timezone)->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.courses.actas.show', $acta) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-arrow-right me-1"></i>
                                            Ver detalle
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        No se encontraron actas con los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($actas->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $actas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
