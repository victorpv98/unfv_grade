{{-- Estadísticas generales del panel administrativo --}}
@php
    $statCards = [
        [
            'label' => 'Escuelas',
            'value' => $schoolsCount ?? $schools ?? $faculties ?? 0,
            'hint' => 'Total de escuelas activas en la facultad',
            'icon' => 'fa-building-columns',
            'color' => 'primary',
        ],
        [
            'label' => 'Cursos',
            'value' => $coursesCount ?? $courses ?? 0,
            'hint' => 'Cursos registrados en los periodos vigentes',
            'icon' => 'fa-book-open',
            'color' => 'success',
        ],
        [
            'label' => 'Docentes',
            'value' => $teachersCount ?? $teachers ?? 0,
            'hint' => 'Docentes con acceso al registro de notas',
            'icon' => 'fa-chalkboard-teacher',
            'color' => 'info',
        ],
        [
            'label' => 'Estudiantes',
            'value' => $studentsCount ?? $students ?? 0,
            'hint' => 'Estudiantes matriculados en el sistema',
            'icon' => 'fa-user-graduate',
            'color' => 'warning',
        ],
    ];
@endphp

<div class="row g-4">
    @foreach ($statCards as $card)
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <span class="text-uppercase text-muted fw-semibold small d-block mb-1">
                            {{ $card['label'] }}
                        </span>
                        <h3 class="fw-bolder text-{{ $card['color'] }} mb-2">
                            {{ number_format((int) $card['value']) }}
                        </h3>
                        <p class="text-muted small mb-0">
                            {{ $card['hint'] }}
                        </p>
                    </div>
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }} flex-shrink-0"
                          style="width: 52px; height: 52px;"
                          aria-hidden="true">
                        <i class="fa-solid {{ $card['icon'] }} fa-lg"></i>
                    </span>
                </div>
            </div>
        </div>
    @endforeach
</div>
