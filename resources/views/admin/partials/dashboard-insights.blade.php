{{-- Bloque con insights y acciones recomendadas para el administrador --}}
<div class="row g-4 mt-1">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary bg-opacity-10 border-0 py-3">
                <h5 class="card-title fw-semibold mb-0 text-primary">
                    <i class="fa-solid fa-chart-pie me-2"></i>
                    Visión general
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    Consolida métricas clave de las escuelas FIEI para obtener un control rápido de la matrícula,
                    asignación docente y oferta académica. Utiliza esta vista como punto de partida para tus
                    decisiones administrativas.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <span class="rounded-circle bg-primary bg-opacity-10 text-primary me-3 d-inline-flex align-items-center justify-content-center"
                                  style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </span>
                            <div>
                                <p class="fw-semibold mb-0 text-dark">Tasa de aprobación mínima</p>
                                <small class="text-muted">Objetivo: 65 % o superior por curso.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <span class="rounded-circle bg-success bg-opacity-10 text-success me-3 d-inline-flex align-items-center justify-content-center"
                                  style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-chalkboard-teacher"></i>
                            </span>
                            <div>
                                <p class="fw-semibold mb-0 text-dark">Cobertura docente</p>
                                <small class="text-muted">Verifica cursos sin asignación semanal.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <span class="rounded-circle bg-warning bg-opacity-10 text-warning me-3 d-inline-flex align-items-center justify-content-center"
                                  style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </span>
                            <div>
                                <p class="fw-semibold mb-0 text-dark">Actualizaciones recientes</p>
                                <small class="text-muted">Sincroniza datos con el último cierre académico.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <span class="rounded-circle bg-secondary bg-opacity-10 text-secondary me-3 d-inline-flex align-items-center justify-content-center"
                                  style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-layer-group"></i>
                            </span>
                            <div>
                                <p class="fw-semibold mb-0 text-dark">Integridad de prerequisitos</p>
                                <small class="text-muted">Revisa correlatividades para evitar cruces.</small>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Placeholder para gráficos futuros --}}
                <div class="border border-secondary border-opacity-25 rounded-3 p-4 text-center bg-light mt-4" style="border-style: dashed;">
                    <i class="fa-solid fa-chart-area text-muted fa-lg mb-2"></i>
                    <p class="mb-0 small text-muted">Espacio reservado para reportes visuales y comparativas históricas.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-dark bg-opacity-10 border-0 py-3">
                <h5 class="card-title fw-semibold mb-0 text-dark">
                    <i class="fa-solid fa-list-check me-2"></i>
                    Próximas acciones
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex align-items-start mb-3">
                        <span class="badge bg-primary-subtle text-primary me-3">
                            <i class="fa-solid fa-file-circle-plus"></i>
                        </span>
                        <div>
                            <p class="fw-semibold mb-1 text-dark">Registrar nuevo periodo académico</p>
                            <small class="text-muted">Configura fechas, cursos y responsables para el ciclo entrante.</small>
                        </div>
                    </li>
                    <li class="d-flex align-items-start mb-3">
                        <span class="badge bg-success-subtle text-success me-3">
                            <i class="fa-solid fa-user-plus"></i>
                        </span>
                        <div>
                            <p class="fw-semibold mb-1 text-dark">Asignar docentes pendientes</p>
                            <small class="text-muted">Completa la designación de secciones para garantizar cobertura.</small>
                        </div>
                    </li>
                    <li class="d-flex align-items-start">
                        <span class="badge bg-warning-subtle text-warning me-3">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <div>
                            <p class="fw-semibold mb-1 text-dark">Verificar respaldos de notas</p>
                            <small class="text-muted">Asegura el respaldo de actas antes del cierre del periodo.</small>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('admin.schools.index') }}" class="btn btn-primary w-100">
                    <i class="fa-solid fa-arrow-right me-2"></i>
                    Ir a la gestión académica
                </a>
            </div>
        </div>
    </div>
</div>
