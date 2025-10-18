@php
    use Carbon\Carbon;

    $coursesCount = $coursesCount ?? 0;
    $studentsCount = $studentsCount ?? 0;
    $gradesCount = $gradesCount ?? 0;
    $activeCoursesCount = $activeCoursesCount ?? 0;
    $lastAccess = $lastAccess ?? null;

    $activeCourses = $activeCourses instanceof \Illuminate\Support\Collection
        ? $activeCourses
        : collect($activeCourses ?? []);

    $lastAccessLabel = 'Sin registro';
    if ($lastAccess instanceof \DateTimeInterface) {
        $lastAccessLabel = Carbon::instance($lastAccess)->locale('es')->translatedFormat('D, d M Y - H:i');
    } elseif (is_string($lastAccess) && $lastAccess !== '') {
        try {
            $lastAccessLabel = Carbon::parse($lastAccess)->locale('es')->translatedFormat('D, d M Y - H:i');
        } catch (\Throwable $e) {
            $lastAccessLabel = $lastAccess;
        }
    }

    $statCards = [
        [
            'label' => 'Cursos asignados',
            'value' => $coursesCount,
            'hint' => 'Total de cursos a tu cargo',
            'icon' => 'fa-book',
            'color' => 'primary',
        ],
        [
            'label' => 'Estudiantes',
            'value' => $studentsCount,
            'hint' => 'Total de estudiantes en tus cursos',
            'icon' => 'fa-users',
            'color' => 'success',
        ],
        [
            'label' => 'Promedios registrados',
            'value' => $gradesCount,
            'hint' => 'Notas registradas en tus cursos',
            'icon' => 'fa-clipboard-check',
            'color' => 'info',
        ],
        [
            'label' => 'Cursos activos',
            'value' => $activeCoursesCount,
            'hint' => 'Cursos activos este periodo',
            'icon' => 'fa-chalkboard',
            'color' => 'warning',
        ],
    ];
@endphp

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h4 mb-1 text-secondary d-flex align-items-center">
            <i class="fa-solid fa-chalkboard-user text-primary me-2"></i>
            Panel del Docente
        </h1>
        <span class="text-muted small">
            Último acceso: {{ $lastAccessLabel }}
        </span>
    </div>
    <span class="text-muted small">
        Actualizado: {{ now()->locale('es')->translatedFormat('d M Y, H:i') }}
    </span>
</div>

<div class="row g-4 mb-4">
    @foreach ($statCards as $card)
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-semibold small d-block mb-1">
                            {{ $card['label'] }}
                        </span>
                        <h3 class="fw-bolder text-{{ $card['color'] }}">
                            {{ number_format((int) $card['value']) }}
                        </h3>
                        <p class="text-muted small mb-0">
                            {{ $card['hint'] }}
                        </p>
                    </div>
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }}" style="width: 52px; height: 52px;">
                        <i class="fa-solid {{ $card['icon'] }} fa-lg"></i>
                    </span>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white fw-semibold text-secondary">
        Cursos activos
    </div>
    <div class="card-body p-0">
        @if ($activeCourses->isEmpty())
            <div class="p-4">
                <div class="alert alert-info mb-0">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    No tienes cursos asignados actualmente.
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0">
                    <thead class="bg-light text-muted text-uppercase small">
                        <tr>
                            <th>Código</th>
                            <th>Curso</th>
                            <th class="text-center">Créditos</th>
                            <th class="text-center">Estudiantes</th>
                            <th class="text-center">Promedio</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activeCourses as $course)
                            @php
                                $id = data_get($course, 'id');
                                $code = data_get($course, 'code', '—');
                                $name = data_get($course, 'name', 'Curso sin nombre');
                                $credits = data_get($course, 'credits', '—');
                                $students = data_get($course, 'students_count', 0);
                                $average = data_get($course, 'average');
                            @endphp
                            <tr>
                                <td class="fw-semibold text-secondary">{{ $code }}</td>
                                <td class="text-secondary fw-semibold">{{ $name }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary px-3 py-2">{{ $credits }}</span>
                                </td>
                                <td class="text-center fw-semibold text-secondary">{{ $students }}</td>
                                <td class="text-center">
                                    @if(is_numeric($average))
                                        <span class="badge bg-primary-subtle text-primary">{{ number_format($average, 2) }}</span>
                                    @else
                                        <span class="text-muted">Sin registro</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($id)
                                        <a href="{{ route('teacher.courses.grades', $id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
