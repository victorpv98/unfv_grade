<div class="vh-100 bg-dark text-white d-flex flex-column"
     style="width: 16rem; background: linear-gradient(135deg, var(--bs-dark) 0%, var(--bs-secondary) 100%) !important;">
    
    <!-- Encabezado con logo -->
    <div class="p-4 d-flex align-items-center border-bottom border-secondary">
        <img src="{{ asset('images/logo_unfv.png') }}" 
             alt="Logo UNFV" 
             class="img-fluid me-3" 
             style="max-height: 50px; width: auto;">
        <h1 class="fs-5 fw-bold mb-0 text-primary">Sistema de Notas</h1>
    </div>
    
    <!-- Navegación -->
    <div class="flex-fill overflow-auto py-4">
        <nav class="px-2">
            {{-- ===================== ADMIN ===================== --}}
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('admin.dashboard') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <svg class="me-3" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.schools.index') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('admin.schools.*') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('admin.schools.*') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <svg class="me-3" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M5 21h14"/>
                    </svg>
                    Escuelas
                </a>

                <a href="{{ route('admin.courses.index') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('admin.courses.*') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('admin.courses.*') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <svg class="me-3" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13M7.5 5C5.5 5 4 5.477 3 6.253v13C4 18.477 5.5 18 7.5 18s3.5.477 4.5 1.253C13.5 18.477 15 18 17 18s3 .477 4.5 1.253v-13C19.5 5.477 18 5 16 5s-3.5.477-4.5 1.253z"/>
                    </svg>
                    Cursos
                </a>

                <a href="{{ route('admin.teachers.index') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('admin.teachers.*') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('admin.teachers.*') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <svg class="me-3" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Docentes
                </a>

                <a href="{{ route('admin.students.index') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('admin.students.*') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('admin.students.*') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <svg class="me-3" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Estudiantes
                </a>
            @endif

            {{-- ===================== DOCENTE ===================== --}}
            @if(Auth::user()->role === 'teacher')
                <a href="{{ route('teacher.dashboard') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('teacher.dashboard') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('teacher.dashboard') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <svg class="me-3" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('teacher.my-courses') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('teacher.my-courses') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('teacher.my-courses') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <svg class="me-3" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 20h9M12 4h9M4 8h16M4 16h16M4 12h16M4 20h16"/>
                    </svg>
                    Mis Cursos
                </a>
            @endif

            {{-- ===================== ESTUDIANTE ===================== --}}
            @if(Auth::user()->role === 'student')
                <a href="{{ route('student.dashboard') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('student.dashboard') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('student.dashboard') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <svg class="me-3" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('student.my-courses') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('student.my-courses') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('student.my-courses') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <svg class="me-3" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.8 5.477 9.2 5 7.5 5S4.2 5.477 3 6.253v13C4.2 18.477 5.8 18 7.5 18s3.3.477 4.5 1.253z"/>
                    </svg>
                    Mis Cursos
                </a>

                <a href="{{ route('student.my-grades') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('student.my-grades') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('student.my-grades') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <svg class="me-3" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mis Notas
                </a>
            @endif
        </nav>
    </div>
    
    <!-- Perfil del usuario -->
    <div class="p-4 border-top" style="background-color: var(--bs-secondary);">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width: 40px; height: 40px;">
                <svg width="18" height="18" class="text-white" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
                </svg>
            </div>
            <div class="ms-3">
                <p class="small fw-semibold text-white mb-0">{{ Auth::user()->name ?? 'Usuario' }}</p>
                <p class="small text-light mb-0 opacity-75">{{ ucfirst(Auth::user()->role ?? 'invitado') }}</p>
            </div>
        </div>
    </div>
</div>