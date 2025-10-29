@php
    $role = Auth::user()->role ?? null;

    $isActive = fn(array|string $patterns) => collect(\Illuminate\Support\Arr::wrap($patterns))
        ->some(fn($pattern) => request()->routeIs($pattern));

    $menus = [
        'admin' => [
            ['label' => 'Dashboard', 'route' => route('admin.dashboard'), 'icon' => 'fa-chart-line', 'active' => $isActive(['admin.dashboard'])],
            ['label' => 'Escuelas', 'route' => route('admin.schools.index'), 'icon' => 'fa-building-columns', 'active' => $isActive(['admin.schools.*'])],
            ['label' => 'Cursos', 'route' => route('admin.courses.index'), 'icon' => 'fa-book-open', 'active' => $isActive(['admin.courses.*']) && !request()->routeIs('admin.courses.actas.*')],
            ['label' => 'Actas', 'route' => route('admin.courses.actas.index'), 'icon' => 'fa-file-contract', 'active' => $isActive(['admin.courses.actas.*'])],
            ['label' => 'Docentes', 'route' => route('admin.teachers.index'), 'icon' => 'fa-chalkboard-teacher', 'active' => $isActive(['admin.teachers.*'])],
            ['label' => 'Estudiantes', 'route' => route('admin.students.index'), 'icon' => 'fa-user-graduate', 'active' => $isActive(['admin.students.*'])],
        ],
        'teacher' => [
            ['label' => 'Panel Docente', 'route' => route('teacher.dashboard'), 'icon' => 'fa-chalkboard', 'active' => $isActive(['teacher.dashboard'])],
            ['label' => 'Mis Cursos', 'route' => route('teacher.my-courses'), 'icon' => 'fa-list-check', 'active' => $isActive(['teacher.my-courses', 'teacher.courses.grades', 'teacher.courses.grades.update', 'teacher.courses.summary', 'teacher.courses.acta.*'])],
        ],
        'student' => [
            ['label' => 'Panel Estudiante', 'route' => route('student.dashboard'), 'icon' => 'fa-user', 'active' => $isActive(['student.dashboard'])],
            ['label' => 'Mis Cursos', 'route' => route('student.my-courses'), 'icon' => 'fa-book', 'active' => $isActive(['student.my-courses'])],
            ['label' => 'Mis Notas', 'route' => route('student.my-grades'), 'icon' => 'fa-award', 'active' => $isActive(['student.my-grades'])],
        ],
    ];

    $items = $menus[$role] ?? [];
@endphp

<aside class="sidebar d-flex flex-column flex-shrink-0 p-3 min-vh-100">
    <a href="{{ route('dashboard') }}"
       class="d-flex align-items-center mb-4 text-white text-decoration-none sidebar-brand">
        <i class="fa-solid fa-graduation-cap me-2"></i>
        <span>UNFV · Sistema de Notas</span>
    </a>

    <hr class="border-light opacity-25">

    <ul class="nav nav-pills flex-column gap-1 mb-auto">
        @forelse($items as $item)
            <li class="nav-item">
                <a href="{{ $item['route'] }}"
                   class="nav-link {{ $item['active'] ? 'active' : '' }}">
                    <i class="fa-solid {{ $item['icon'] }} me-2"></i>
                    {{ $item['label'] }}
                </a>
            </li>
        @empty
            <li class="nav-item">
                <span class="nav-link disabled text-white-50">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    Rol sin menú asignado
                </span>
            </li>
        @endforelse
    </ul>

    <hr class="border-light opacity-25">

    <div class="text-white-50 small">
        <p class="mb-1 fw-semibold text-white">{{ Auth::user()->name ?? 'Usuario' }}</p>
        <p class="mb-3 text-uppercase">{{ ucfirst($role ?? 'Invitado') }}</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="fa-solid fa-right-from-bracket"></i>
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>
