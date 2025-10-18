@extends('layouts.app')

@section('header', 'Gestión de Cursos')

@section('content')
    <div class="container-fluid px-0">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title fw-semibold mb-0 text-secondary">
                        <i class="fa-solid fa-book-open me-2 text-primary"></i>
                        Cursos
                    </h5>
                    <p class="text-muted small mb-0">
                        Listado de cursos disponibles en las escuelas de la facultad.
                        @if($activePeriod)
                            <span class="d-inline-block ms-2 badge bg-primary-subtle text-primary">
                                Periodo activo: {{ $activePeriod->name }}
                            </span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>
                    Nuevo curso
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th scope="col" style="width: 10%;">Código</th>
                                <th scope="col">Nombre</th>
                                <th scope="col" style="width: 12%;">Créditos</th>
                                <th scope="col">Escuela</th>
                                <th scope="col">Docente</th>
                                <th scope="col" class="text-center" style="width: 10%;">Alumnos</th>
                                <th scope="col" class="text-center" style="width: 18%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="small text-secondary">
                            @forelse($courses as $course)
                                @php
                                    $assignment = $course->courseTeachers->first();
                                    $teacherName = ($assignment && $assignment->teacher && $assignment->teacher->user)
                                        ? $assignment->teacher->user->name
                                        : 'No asignado';
                                    $teacherBadgeClass = $assignment ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary';
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-secondary">{{ $course->code }}</td>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->credits }}</td>
                                    <td>{{ $course->school?->name ?? 'Sin asignar' }}</td>
                                    <td>
                                        <span class="badge {{ $teacherBadgeClass }}">
                                            <i class="fa-solid fa-chalkboard-user me-1"></i>
                                            {{ $teacherName }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="fa-solid fa-users me-1"></i>
                                            {{ $course->students_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('admin.courses.edit', $course) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Asignar docente o editar curso">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.students', $course) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Ver estudiantes">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.courses.destroy', $course) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('¿Deseas eliminar este curso?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Eliminar curso">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-circle-info me-2"></i>
                                        No se encontraron cursos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($courses instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-end">
                        {{ $courses->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
