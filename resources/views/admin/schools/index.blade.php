@extends('layouts.app')

@section('header', 'Gestión de Escuelas')

@section('content')
    <div class="container-fluid px-0">
        {{-- Mensajes flash --}}
        @include('layouts.partials.alert')

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title fw-semibold mb-0 text-secondary">
                        <i class="fa-solid fa-building-columns me-2 text-primary"></i>
                        Escuelas registradas
                    </h5>
                    <p class="text-muted small mb-0">Administra la información de las escuelas pertenecientes a la FIEI.</p>
                </div>
                <a href="{{ route('admin.schools.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>
                    Nueva escuela
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted text-uppercase small">
                                <th scope="col" style="width: 10%;">Código</th>
                                <th scope="col">Nombre</th>
                                <th scope="col" class="text-center" style="width: 15%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schools as $school)
                                <tr>
                                    <td class="fw-semibold text-secondary">{{ $school->code }}</td>
                                    <td>{{ $school->name }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('admin.schools.edit', $school) }}"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.schools.destroy', $school) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('¿Deseas eliminar esta escuela?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-circle-info me-2"></i>
                                        No hay escuelas registradas por el momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($schools instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="card-footer bg-white border-0 py-3">
                    {{ $schools->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
