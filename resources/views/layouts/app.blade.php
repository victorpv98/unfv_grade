<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Sistema de Notas FIEI – UNFV'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <link rel="icon" href="{{ asset('images/logo_unfv.png') }}" type="image/png">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-uz6wcfN2/0xuxMxDPZqsloJcoa45PcRVvm739Ij5PYyz1ME3RbBtu3kMKrMN1Z8mOr+UUu5v4ps55i5wVfF7oA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Estilos y scripts compilados mediante Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --unfv-primary: #ff6600;
            --unfv-secondary: #2c3e50;
            --unfv-light: #f8f9fa;
        }
        body {
            background-color: var(--unfv-light);
            font-family: 'Figtree', sans-serif;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-light">
    <div class="d-flex min-vh-100">
        {{-- Sidebar principal --}}
        @include('layouts.sidebar')

        {{-- Contenido principal --}}
        <div class="flex-grow-1 d-flex flex-column vh-100">
            @include('layouts.partials.topbar')

            <main class="flex-grow-1 overflow-auto bg-light py-4">
                <div class="container-fluid px-4">
                    @include('layouts.partials.alert')
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
