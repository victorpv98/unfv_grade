<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UNFV - Sistema de Notas') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Carga SCSS y JS compilados por Vite --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-light">

    {{-- Topbar con colores UNFV (navbar-dark + bg-secondary) --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-semibold" href="{{ route('home') }}">
                <i class="bi bi-mortarboard me-2 text-primary"></i>{{ config('app.name', 'UNFV') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topbarNav" aria-controls="topbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="topbarNav">
                <ul class="navbar-nav me-auto"></ul>

                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>{{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuBtn">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i>Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth

                @guest
                    <a class="btn btn-primary" href="{{ route('login') }}">Ingresar</a>
                @endguest
            </div>
        </div>
    </nav>

    {{-- Header (slot $header de Breeze) --}}
    @isset($header)
        <header class="bg-white border-bottom">
            <div class="container-fluid py-3">
                <h1 class="fs-4 fw-semibold text-secondary mb-0">
                    {{ $header }}
                </h1>
            </div>
        </header>
    @endisset

    {{-- Contenido principal --}}
    <main class="container-fluid py-4">
        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>