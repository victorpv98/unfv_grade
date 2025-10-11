<div class="vh-100 bg-dark text-white d-flex flex-column"
     style="width: 16rem; background: linear-gradient(135deg, var(--bs-dark) 0%, var(--bs-secondary) 100%) !important;">
    
    <!-- LOGO -->
    <div class="p-4 d-flex align-items-center border-bottom border-secondary">
        <img src="{{ asset('images/logo_unfv.png') }}" 
             alt="Logo UNFV" 
             class="img-fluid me-2" 
             style="max-height: 50px; width: auto;">
        <h1 class="fs-5 fw-bold mb-0 text-primary">Sistema de Notas</h1>
    </div>

    <!-- MENÚ PRINCIPAL -->
    <div class="flex-fill overflow-auto py-4">
        <nav class="px-2">
            {{-- ======================================
                 ADMINISTRADOR
            ======================================= --}}
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('admin.dashboard') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <i class="fas fa-home me-3"></i>
                    Dashboard
                </a>

                <a href="{{ route('admin.schools.index') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('admin.schools.*') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('admin.schools.*') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <i class="fas fa-university me-3"></i>
                    Escuelas
                </a>

                <a href="{{ route('admin.courses.index') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('admin.courses.*') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('admin.courses.*') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <i class="fas fa-book me-3"></i>
                    Cursos
                </a>

                <a href="{{ route('admin.teachers.index') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('admin.teachers.*') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('admin.teachers.*') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <i class="fas fa-chalkboard-teacher me-3"></i>
                    Docentes
                </a>

                <a href="{{ route('admin.students.index') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('admin.students.*') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('admin.students.*') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <i class="fas fa-user-graduate me-3"></i>
                    Estudiantes
                </a>
            @endif


            {{-- ======================================
                 DOCENTE
            ======================================= --}}
            @if(Auth::user()->role === 'teacher')
                <a href="{{ route('teacher.dashboard') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('teacher.dashboard') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('teacher.dashboard') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <i class="fas fa-home me-3"></i>
                    Dashboard
                </a>

                <a href="{{ route('teacher.my-courses') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('teacher.my-courses') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('teacher.my-courses') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <i class="fas fa-book-open me-3"></i>
                    Mis Cursos
                </a>
            @endif


            {{-- ======================================
                 ESTUDIANTE
            ======================================= --}}
            @if(Auth::user()->role === 'student')
                <a href="{{ route('student.dashboard') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('student.dashboard') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('student.dashboard') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <i class="fas fa-home me-3"></i>
                    Dashboard
                </a>

                <a href="{{ route('student.my-courses') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('student.my-courses') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('student.my-courses') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <i class="fas fa-book-reader me-3"></i>
                    Mis Cursos
                </a>

                <a href="{{ route('student.my-grades') }}" 
                   class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded {{ request()->routeIs('student.my-grades') ? 'bg-primary text-white' : 'text-light' }}"
                   style="{{ request()->routeIs('student.my-grades') ? 'border-left: 3px solid var(--bs-primary);' : '' }}">
                    <i class="fas fa-star me-3"></i>
                    Mis Notas
                </a>
            @endif
        </nav>
    </div>

    <!-- PIE DE PERFIL -->
    <div class="p-4 border-top" style="background-color: var(--bs-secondary);">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 40px; height: 40px;">
                <i class="fas fa-user text-white"></i>
            </div>
            <div class="ms-3">
                <p class="small fw-semibold text-white mb-0">{{ Auth::user()->name ?? 'Usuario' }}</p>
                <p class="small text-light mb-0 opacity-75">{{ ucfirst(Auth::user()->role ?? 'invitado') }}</p>
            </div>
        </div>
    </div>
</div>