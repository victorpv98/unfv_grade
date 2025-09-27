<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center">
            <span class="badge rounded-pill bg-primary me-2">&nbsp;</span>
            <span class="fw-semibold text-secondary">Panel de Administración</span>
        </div>
    </x-slot>

    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-3 col-lg-2 bg-white border-end min-vh-100 p-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.dashboard') }}"
                       class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-people me-2"></i> Usuarios
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-journal-bookmark me-2"></i> Cursos
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-calendar3 me-2"></i> Períodos
                    </a>
                </div>
            </aside>

            <main class="col-md-9 col-lg-10 p-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary bg-opacity-10 border-0">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-activity me-2 text-primary"></i>
                            <span class="fw-semibold text-secondary">Resumen</span>
                        </div>
                    </div>
                    <div class="card-body">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    @push('styles')
    <style>
        .list-group-item-action.active {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #fff !important;
        }
        .list-group-item-action:hover {
            background-color: rgba(var(--bs-primary-rgb), .08);
        }
    </style>
    @endpush
</x-app-layout>