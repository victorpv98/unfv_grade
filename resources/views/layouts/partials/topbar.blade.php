<header class="bg-white shadow-sm border-bottom">
    <div class="container-fluid px-4 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-primary d-lg-none"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#appSidebar"
                        aria-controls="appSidebar"
                        aria-label="Mostrar menú">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <h1 class="h5 fw-semibold text-secondary mb-0">
                        @yield('header', 'Panel principal')
                    </h1>
                    @hasSection('subheader')
                        <p class="text-muted small mb-0">@yield('subheader')</p>
                    @endif
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle"
                        type="button"
                        id="user-menu-button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <i class="fa-solid fa-user-circle me-2 text-primary"></i>
                    <span>{{ Auth::user()->name ?? 'Usuario' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0"
                    aria-labelledby="user-menu-button">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="fa-solid fa-user me-2"></i>
                            Mi perfil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fa-solid fa-right-from-bracket me-2"></i>
                                Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
