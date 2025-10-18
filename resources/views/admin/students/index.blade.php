@extends('layouts.app')

@section('header', 'Gestión de Estudiantes')

@section('content')
    <div class="container-fluid px-0">
        @include('layouts.partials.alert')

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title fw-semibold mb-0 text-secondary">
                        <i class="fa-solid fa-user-graduate text-primary me-2"></i>
                        Estudiantes registrados
                    </h5>
                    <p class="text-muted small mb-0">Consulta y administra la información general de los estudiantes.</p>
                </div>
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>
                    Nuevo estudiante
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th scope="col" style="width: 12%;">Código</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Correo</th>
                                <th scope="col" style="width: 14%;">Año ingreso</th>
                                <th scope="col" style="width: 20%;">Escuela</th>
                                <th scope="col" class="text-center" style="width: 16%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td class="fw-semibold text-secondary">{{ $student->code }}</td>
                                    <td>{{ $student->user?->name ?? 'Sin usuario' }}</td>
                                    <td>{{ $student->user?->email ?? '—' }}</td>
                                    <td>{{ $student->enrollment_year }}</td>
                                    <td>{{ $student->user?->school?->name ?? 'Sin asignar' }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('admin.students.edit', $student) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Editar estudiante">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.students.destroy', $student) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('¿Deseas eliminar este estudiante?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Eliminar estudiante">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-circle-info me-2"></i>
                                        No se registraron estudiantes todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($students instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="card-footer bg-white border-0 py-3">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
