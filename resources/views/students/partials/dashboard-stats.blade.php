@php
    use Carbon\Carbon;

    $averageGrade = isset($averageGrade) && is_numeric($averageGrade) ? (float) $averageGrade : 0;
    $coursesCount = isset($coursesCount) ? (int) $coursesCount : 0;
    $approvedCount = isset($approvedCount) ? (int) $approvedCount : 0;
    $failedCount = isset($failedCount) ? (int) $failedCount : 0;
    $activeCourses = $activeCourses instanceof \Illuminate\Support\Collection
        ? $activeCourses
        : collect($activeCourses ?? []);
    $lastAccess = $lastAccess ?? null;

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
        ['label' => 'Cursos matriculados', 'value' => $coursesCount, 'hint' => 'Total de cursos inscritos', 'icon' => 'fa-book-open', 'color' => 'primary'],
        ['label' => 'Promedio general', 'value' => number_format($averageGrade, 2), 'hint' => 'Promedio ponderado acumulado', 'icon' => 'fa-chart-line', 'color' => 'info'],
        ['label' => 'Cursos aprobados', 'value' => $approvedCount, 'hint' => 'Total de cursos aprobados', 'icon' => 'fa-circle-check', 'color' => 'success'],
        ['label' => 'Cursos desaprobados', 'value' => $failedCount, 'hint' => 'Total de cursos desaprobados', 'icon' => 'fa-circle-xmark', 'color' => 'danger'],
    ];

    $statusLabels = [
        'A' => ['Aprobado', 'success'],
        'D' => ['Desaprobado', 'danger'],
        'S' => ['Sustitutorio', 'info'],
        'R' => ['Aplazado', 'warning'],
    ];
@endphp

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h4 mb-1 text-secondary d-flex align-items-center">
            <i class="fa-solid fa-user-graduate text-primary me-2"></i>
            Panel del Estudiante
        </h1>
        <span class="text-muted small">Último acceso: {{ $lastAccessLabel }}</span>
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
                            {{ $card['value'] }}
                        </h3>
                        <p class="text-muted small mb-0">{{ $card['hint'] }}</p>
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
        Cursos actuales
    </div>
    <div class="card-body p-0">
        @if ($activeCourses->isEmpty())
            <div class="p-4">
                <div class="alert alert-info mb-0">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    No tienes cursos activos actualmente.
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0">
                    <thead class="bg-light text-muted text-uppercase small">
                        <tr>
                            <th>Código</th>
                            <th>Curso</th>
                            <th>Docente</th>
                            <th class="text-center">Créditos</th>
                            <th class="text-center">Promedio</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activeCourses as $course)
                            @php
                                $statusKey = $course->status ?? null;
                                [$label, $color] = $statusLabels[$statusKey] ?? ['Sin registro', 'secondary'];
                            @endphp
                            <tr>
                                <td>{{ $course->code }}</td>
                                <td class="fw-semibold text-secondary">{{ $course->name }}</td>
                                <td>{{ $course->teacher_name ?? 'Sin asignar' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary px-3 py-2">{{ $course->credits }}</span>
                                </td>
                                <td class="text-center">
                                    @if (is_numeric($course->average))
                                        <span class="badge bg-primary-subtle text-primary">{{ number_format($course->average, 2) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">{{ $label }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
