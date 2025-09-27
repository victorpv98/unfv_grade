<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Panel de Administración</h2>
    </x-slot>

    <div class="flex">
        <aside class="w-64 bg-gray-100 p-4 min-h-[70vh]">
            <nav class="space-y-2 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-200">Dashboard</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-200">Usuarios</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-200">Cursos</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-200">Períodos</a>
            </nav>
        </aside>
        <main class="flex-1 p-6">
            {{ $slot }}
        </main>
    </div>
</x-app-layout>
