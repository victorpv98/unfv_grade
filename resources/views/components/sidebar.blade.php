@php
    $role = Auth::user()->role ?? null;
    $userName = Auth::user()->name ?? 'Usuario';

    $isActive = static function (array|string $patterns): bool {
        foreach (\Illuminate\Support\Arr::wrap($patterns) as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    };

    $menu = [
        'admin' => [
            [
                'heading' => 'Panel administrativo',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => route('admin.dashboard'),
                        'icon' => 'fa-chart-line',
                        'active' => $isActive('admin.dashboard'),
                    ],
                ],
            ],
            [
                'heading' => 'Gestión académica',
                'items' => [
                    [
                        'label' => 'Escuelas',
                        'route' => route('admin.schools.index'),
                        'icon' => 'fa-building-columns',
                        'active' => $isActive('admin.schools.*'),
                    ],
                    [
                        'label' => 'Cursos',
                        'route' => route('admin.courses.index'),
                        'icon' => 'fa-book-open',
                        'active' => $isActive('admin.courses.*'),
                    ],
                    [
                        'label' => 'Docentes',
                        'route' => route('admin.teachers.index'),
                        'icon' => 'fa-chalkboard-teacher',
                        'active' => $isActive('admin.teachers.*'),
                    ],
                    [
                        'label' => 'Estudiantes',
                        'route' => route('admin.students.index'),
                        'icon' => 'fa-user-graduate',
                        'active' => $isActive('admin.students.*'),
                    ],
                ],
            ],
        ],
        'teacher' => [
            [
                'heading' => 'Panel docente',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => route('teacher.dashboard'),
                        'icon' => 'fa-chart-line',
                        'active' => $isActive('teacher.dashboard'),
                    ],
                ],
            ],
            [
                'heading' => 'Gestión de cursos',
                'items' => [
                    [
                        'label' => 'Mis Cursos',
                        'route' => route('teacher.my-courses'),
                        'icon' => 'fa-list-check',
                        'active' => $isActive(['teacher.my-courses']),
                    ],
                    [
                        'label' => 'Registrar Notas',
                        'route' => route('teacher.my-courses') . '#registro-notas',
                        'icon' => 'fa-clipboard-list',
                        'active' => $isActive([
                            'teacher.courses.grades',
                            'teacher.courses.grades.update',
                        ]),
                    ],
                    [
                        'label' => 'Resumen Final',
                        'route' => route('teacher.my-courses') . '#resumen-final',
                        'icon' => 'fa-chart-column',
                        'active' => $isActive('teacher.courses.summary'),
                    ],
                ],
            ],
        ],
        'student' => [
            [
                'heading' => 'Panel estudiantil',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => route('student.dashboard'),
                        'icon' => 'fa-chart-line',
                        'active' => $isActive('student.dashboard'),
                    ],
                ],
            ],
            [
                'heading' => 'Mi progreso',
                'items' => [
                    [
                        'label' => 'Mis Cursos',
                        'route' => route('student.my-courses'),
                        'icon' => 'fa-list-check',
                        'active' => $isActive('student.my-courses'),
                    ],
                    [
                        'label' => 'Mis Notas',
                        'route' => route('student.my-grades'),
                        'icon' => 'fa-clipboard-list',
                        'active' => $isActive('student.my-grades'),
                    ],
                ],
            ],
        ],
    ];

    $roleSections = $menu[$role] ?? [];
@endphp

@once
    @push('styles')
        <style>
            :root {
                --sidebar-width: 268px;
            }

            .app-sidebar {
                width: var(--sidebar-width);
                background-color: #ffffff;
                border-right: 1px solid rgba(44, 62, 80, 0.08);
                box-shadow: 0 0 24px rgba(44, 62, 80, 0.06);
            }

            .app-sidebar .offcanvas-header {
                background-color: #f3f4f6;
            }

            .app-sidebar .brand-title {
                color: #ff6600;
                letter-spacing: 0.05em;
            }

            .app-sidebar .menu-heading {
                font-size: 0.72rem;
                text-transform: uppercase;
                letter-spacing: 0.14em;
                color: #9aa6b2;
                margin-bottom: 0.85rem;
            }

            .app-sidebar .sidebar-link {
                color: #4f5d75;
                border-radius: 0.85rem;
                padding: 0.65rem 0.75rem;
                transition: all 0.2s ease-in-out;
                position: relative;
            }

            .app-sidebar .sidebar-link .sidebar-icon {
                width: 1.75rem;
                display: inline-flex;
                justify-content: center;
                color: #909fb7;
                transition: color 0.2s ease-in-out;
            }

            .app-sidebar .sidebar-link:hover {
                background-color: #f8f9fa;
                color: #ff6600;
            }

            .app-sidebar .sidebar-link:hover .sidebar-icon {
                color: #ff6600;
            }

            .app-sidebar .sidebar-link.active {
                background-color: #fff3e0;
                color: #ff6600;
                font-weight: 600;
                box-shadow: inset 3px 0 0 #ff6600;
            }

            .app-sidebar .sidebar-link.active .sidebar-icon {
                color: #ff6600;
            }

            .app-sidebar .sidebar-footer {
                background-color: #f8f9fa;
                border-top: 1px solid rgba(44, 62, 80, 0.08);
            }

            @media (max-width: 991.98px) {
                .app-sidebar {
                    width: min(86vw, 280px);
                }
            }
        </style>
    @endpush
@endonce

<aside id="appSidebar"
       class="app-sidebar offcanvas-lg offcanvas-start d-flex flex-column flex-shrink-0 min-vh-100"
       tabindex="-1"
       data-bs-scroll="true"
       aria-labelledby="appSidebarLabel">

    <div class="offcanvas-header align-items-center px-4 py-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div class="border rounded-circle d-inline-flex align-items-center justify-content-center"
                 style="width: 48px; height: 48px; border-color: rgba(44,62,80,0.12)!important;">
                <i class="fa-solid fa-graduation-cap text-primary fs-5"></i>
            </div>
            <div>
                <h1 id="appSidebarLabel" class="h6 fw-bold mb-1 brand-title">Sistema de Notas</h1>
                <p class="mb-0 small text-muted">FIEI · UNFV</p>
            </div>
        </div>
        <button type="button"
                class="btn btn-outline-secondary btn-sm d-lg-none"
                data-bs-dismiss="offcanvas"
                aria-label="Cerrar menú">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="offcanvas-body p-0 d-flex flex-column">
        <div class="flex-grow-1 overflow-auto px-4 py-4">
            <p class="text-uppercase small fw-semibold text-muted mb-3">Menú principal</p>

            @forelse ($roleSections as $section)
                <div class="mb-4">
                    <p class="menu-heading">{{ $section['heading'] }}</p>
                    <ul class="nav flex-column gap-2">
                        @foreach ($section['items'] as $item)
                            <li class="nav-item">
                                <a href="{{ $item['route'] }}"
                                   class="nav-link sidebar-link d-flex align-items-center gap-3 {{ $item['active'] ? 'active' : '' }}"
                                   aria-current="{{ $item['active'] ? 'page' : 'false' }}">
                                    <span class="sidebar-icon">
                                        <i class="fa-solid {{ $item['icon'] }} fa-sm"></i>
                                    </span>
                                    <span class="flex-grow-1">{{ $item['label'] }}</span>
                                    @if($item['active'])
                                        <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-10">
                                            Activo
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <div class="alert alert-light border text-muted" role="alert">
                    No hay secciones disponibles para tu rol.
                </div>
            @endforelse
        </div>

        <div class="sidebar-footer px-4 py-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center"
                     style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <p class="mb-0 fw-semibold text-secondary">{{ $userName }}</p>
                    <span class="small text-muted">{{ ucfirst($role ?? 'Invitado') }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="d-grid">
                @csrf
                <button type="submit" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </div>
</aside>
