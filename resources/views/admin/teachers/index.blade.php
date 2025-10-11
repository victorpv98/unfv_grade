@extends('layouts.app')

@section('header', 'Gestión de Docentes')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title fw-semibold mb-0 text-secondary">
                        <i class="fa-solid fa-chalkboard-teacher text-primary me-2"></i>
                        Docentes registrados
                    </h5>
                    <p class="text-muted small mb-0">Controla la asignación de docentes a los cursos de la facultad.</p>
                </div>
                <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>
                    Nuevo docente
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Correo</th>
                                <th scope="col" style="width: 18%;">Especialidad</th>
                                <th scope="col" style="width: 20%;">Escuela</th>
                                <th scope="col" class="text-center" style="width: 16%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td class="fw-semibold text-secondary">{{ $teacher->user?->name ?? 'Sin asignar' }}</td>
                                    <td>{{ $teacher->user?->email ?? '—' }}</td>
                                    <td>{{ $teacher->specialty ?? 'Sin definir' }}</td>
                                    <td>{{ $teacher->user?->school?->name ?? 'Sin escuela asociada' }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('admin.teachers.edit', $teacher) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Editar docente">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.teachers.destroy', $teacher) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('¿Deseas eliminar este docente?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Eliminar docente">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-circle-info me-2"></i>
                                        No se han registrado docentes aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($teachers instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="card-footer bg-white border-0 py-3">
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
