@extends('layouts.app')

@section('header', 'Estudiantes del Curso')

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-semibold text-secondary mb-1">
                    <i class="fa-solid fa-book-open text-primary me-2"></i>
                    {{ $course->code }} · {{ $course->name }}
                </h4>
                <p class="text-muted small mb-0">
                    Escuela: {{ $course->school?->name ?? 'Sin asignar' }} · Créditos: {{ $course->credits }}
                    @if($activePeriod)
                        · Periodo activo: <span class="fw-semibold">{{ $activePeriod->name }}</span>
                    @endif
                </p>
                @if($course->prerequisites->isNotEmpty())
                    <div class="mt-2">
                        <span class="badge bg-primary text-white me-1">
                            <i class="fa-solid fa-list-check me-1"></i>
                            Pre-requisitos
                        </span>
                        @foreach($course->prerequisites as $prerequisite)
                            <span class="badge bg-secondary-subtle text-secondary">
                                {{ $prerequisite->code }} · {{ $prerequisite->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Volver a cursos
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title fw-semibold mb-0 text-secondary">
                        <i class="fa-solid fa-user-graduate text-primary me-2"></i>
                        Estudiantes matriculados
                    </h5>
                    <p class="text-muted small mb-0">
                        Se muestran las matrículas registradas
                        {{ $activePeriod ? "para el periodo {$activePeriod->name}" : 'en todos los periodos' }}.
                    </p>
                </div>
                @if($activePeriod)
                    <button type="button"
                            class="btn btn-primary @if($studentsOptions->isEmpty()) disabled @endif"
                            data-bs-toggle="modal"
                            data-bs-target="#enrollStudentModal"
                            @if($studentsOptions->isEmpty()) disabled @endif>
                        <i class="fa-solid fa-circle-plus me-2"></i>
                        Matricular estudiante
                    </button>
                @endif
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
                            @forelse($enrollments as $enrollment)
                                @php
                                    $student = $enrollment->student;
                                    $finalGrade = $enrollment->finalGrade?->average;
                                    $status = $enrollment->finalGrade?->status;
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-secondary">{{ $student->code }}</td>
                                    <td>{{ $student->user?->name ?? 'Sin nombre asignado' }}</td>
                                    <td>{{ $student->enrollment_year ?? '—' }}</td>
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
                                            <x-final-grade.status-badge :status="$status" />
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

    @if($activePeriod)
        <div class="modal fade" id="enrollStudentModal" tabindex="-1" aria-labelledby="enrollStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="enrollStudentModalLabel">
                            <i class="fa-solid fa-user-plus me-2"></i>
                            Matricular estudiante
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="enroll-student-form" action="{{ route('admin.courses.students.enroll', $course) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="student_id" class="form-label fw-semibold text-secondary">
                                    Seleccione un estudiante <span class="text-danger">*</span>
                                </label>
                                <select name="student_id"
                                        id="student_id"
                                        class="form-select"
                                        data-allow-clear="true"
                                        required
                                        @if($studentsOptions->isEmpty()) disabled @endif>
                                    <option value="" selected disabled>Buscar estudiante...</option>
                                    @foreach($studentsOptions as $studentOption)
                                        <option value="{{ $studentOption->id }}">
                                            {{ $studentOption->code }} · {{ $studentOption->user?->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($studentsOptions->isEmpty())
                                    <div class="form-text text-danger">
                                        No hay estudiantes disponibles para matricular en este periodo.
                                    </div>
                                @endif
                            </div>
                            <div id="prerequisite-feedback" class="alert d-none" role="alert"></div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" id="enroll-submit-btn" @if($studentsOptions->isEmpty()) disabled @endif>
                                <i class="fa-solid fa-save me-2"></i>
                                Confirmar matrícula
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@if($activePeriod)
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
                integrity="sha256-3gJwYp4gkER6dV2GIt1fM1XoC9e3p2Dni65Dr2y4S2M="
                crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modalElement = document.getElementById('enrollStudentModal');
                if (!modalElement) {
                    return;
                }

                const feedback = document.getElementById('prerequisite-feedback');
                const submitButton = document.getElementById('enroll-submit-btn');
                const form = document.getElementById('enroll-student-form');
                const studentSelect = $('#student_id');
                const checkUrl = @json(route('admin.courses.prerequisites.check', $course));

                const resetFeedback = () => {
                    feedback.classList.add('d-none');
                    feedback.classList.remove('alert-success', 'alert-danger');
                    feedback.textContent = '';
                    feedback.dataset.status = '';

                    if (submitButton) {
                        submitButton.disabled = true;
                    }
                };

                const showFeedback = (type, message) => {
                    feedback.classList.remove('d-none', 'alert-success', 'alert-danger');
                    feedback.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
                    feedback.textContent = message;
                    feedback.dataset.status = type;

                    if (submitButton) {
                        submitButton.disabled = type !== 'success';
                    }
                };

                studentSelect.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#enrollStudentModal'),
                    width: '100%',
                    placeholder: 'Buscar estudiante...',
                    allowClear: true,
                });

                studentSelect.on('change', async function () {
                    const studentId = $(this).val();

                    if (!studentId) {
                        resetFeedback();
                        return;
                    }

                    showFeedback('danger', 'Validando requisitos...');

                    try {
                        const response = await axios.post(checkUrl, { student_id: studentId });
                        showFeedback('success', response.data.message);
                    } catch (error) {
                        if (error.response && error.response.data) {
                            const { message, missing = [] } = error.response.data;
                            const details = missing.length
                                ? `Cursos pendientes: ${missing.join(', ')}`
                                : '';
                            showFeedback('danger', [message, details].filter(Boolean).join(' '));
                        } else {
                            showFeedback('danger', 'No se pudo validar los requisitos. Intente nuevamente.');
                        }
                    }
                });

                modalElement.addEventListener('hidden.bs.modal', () => {
                    studentSelect.val(null).trigger('change');
                    resetFeedback();
                });

                form.addEventListener('submit', (event) => {
                    if (feedback.dataset.status !== 'success') {
                        event.preventDefault();
                        showFeedback('danger', 'Confirme un estudiante válido antes de matricular.');
                    }
                });
            });
        </script>
    @endpush
@endif
