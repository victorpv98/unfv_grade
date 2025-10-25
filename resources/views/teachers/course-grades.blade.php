@extends('layouts.app')

@section('header', 'Registrar Notas')

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
                    Escuela: {{ $course->school?->name ?? 'Sin escuela definida' }} · Créditos: {{ $course->credits }}
                </p>
            </div>
            <div class="d-inline-flex gap-2">
                <a href="{{ route('teacher.courses.summary', $course) }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-chart-line me-2"></i>
                    Resumen final
                </a>
                <a href="{{ route('teacher.my-courses') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Volver
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <h6 class="fw-semibold text-secondary mb-1">
                        <i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i>
                        Carga masiva de notas
                    </h6>
                    <p class="text-muted small mb-0">
                        Descarga la plantilla CSV (compatible con Excel), actualiza las notas y vuelve a subir el archivo.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('teacher.courses.grades.template', $course) }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-file-arrow-down me-2"></i>
                        Descargar plantilla
                    </a>
                    <form action="{{ route('teacher.courses.grades.import', $course) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="d-flex align-items-center gap-2 flex-wrap">
                        @csrf
                        <input type="file"
                               name="grades_file"
                               class="form-control form-control-sm"
                               accept=".csv,text/csv"
                               required>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-upload me-2"></i>
                            Importar notas
                        </button>
                        @error('grades_file')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </form>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title fw-semibold mb-0 text-secondary">
                    <i class="fa-solid fa-list-check text-primary me-2"></i>
                    Registro de notas por estudiante
                </h5>
            </div>
            <div class="card-body p-0">
                <form action="{{ route('teacher.courses.grades.update', $course) }}" method="POST">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th scope="col">Estudiante</th>
                                    <th scope="col" class="text-center">P1</th>
                                    <th scope="col" class="text-center">P2</th>
                                    <th scope="col" class="text-center">P3</th>
                                    <th scope="col" class="text-center">P4</th>
                                    <th scope="col" class="text-center">Parcial</th>
                                    <th scope="col" class="text-center">Final</th>
                                    <th scope="col" class="text-center">Sustitutorio</th>
                                    <th scope="col" class="text-center">Aplazado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $courseStudent)
                                    @php
                                        $student = $courseStudent->student;
                                        $detail = $courseStudent->gradeDetail;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-secondary">
                                                {{ $student->user?->name ?? 'Sin nombre' }}
                                            </div>
                                            <small class="text-muted">
                                                Código: {{ $student->code }} · Año {{ $student->enrollment_year ?? '—' }}
                                            </small>
                                        </td>
                                        @foreach (['practice1','practice2','practice3','practice4','midterm','final','substitute','makeup'] as $field)
                                            @php
                                                $label = [
                                                    'practice1' => 'P1',
                                                    'practice2' => 'P2',
                                                    'practice3' => 'P3',
                                                    'practice4' => 'P4',
                                                    'midterm' => 'Parcial',
                                                    'final' => 'Final',
                                                    'substitute' => 'Sustitutorio',
                                                    'makeup' => 'Aplazado',
                                                ][$field];
                                                $value = old("grades.{$student->id}.{$field}", $detail?->$field);
                                            @endphp
                                            <td class="text-center">
                                                <input type="number"
                                                       name="grades[{{ $student->id }}][{{ $field }}]"
                                                       class="form-control form-control-sm text-center"
                                                       placeholder="{{ $label }}"
                                                       value="{{ $value }}"
                                                       min="0"
                                                       max="20"
                                                       step="0.01"
                                                       aria-label="{{ $label }}">
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="fa-solid fa-circle-info me-2"></i>
                                            No hay estudiantes matriculados en este curso.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($students->count())
                        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-2"></i>
                                Guardar notas
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
