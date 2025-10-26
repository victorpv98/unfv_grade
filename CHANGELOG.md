# Changelog

Este documento sigue la convención de [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/) y se adhiere a [SemVer](https://semver.org/lang/es/) mientras el proyecto permanezca en la serie `0.x`. Se utiliza el formato `MAJOR.MINOR.PATCH`, con `PATCH` relleno a tres dígitos para facilitar la lectura cronológica commit a commit. Cada sección resume un commit aplicado a la rama principal (ordenados del más reciente al más antiguo) con el detalle de qué se añadió, cambió o retiró según el diff observado.

## [1.12.000] - 2025-10-25 · Add Changelog and Readme
_Commit: ``_
### Added
- `CHANGELOG.md` inicial siguiendo la convención de Keep a Changelog con versiones `MAJOR.MINOR.PATCH` y vinculación directa a cada commit.
### Changed
- `README.md` reemplazado por una guía específica del proyecto con arquitectura, requisitos, flujo de desarrollo en Docker, tareas frecuentes y pasos de despliegue en plataformas basadas en contenedores.

## [1.11.000] - 2025-10-25 · Add Dockerfile
_Commit: `b09f319`_
### Added
- `.dockerignore`, `Dockerfile` y `docker-compose.yml` para contenerizar la aplicación con PHP 8.2, Apache, Node 18 y PostgreSQL listo para producción.
- `start.sh` como entrypoint: ajusta permisos, ejecuta `php artisan optimize`, espera la base de datos (PostgreSQL/MySQL), corre migraciones en bucle y permite seeders controlados por variables de entorno antes de exponer Apache.
- `.env.production.example` con variables mínimas para despliegues en contenedores.
### Changed
- `config/database.php` ahora permite personalizar `DB_SSLMODE` vía entorno, facilitando conexiones seguras en PaaS.
- Migraciones de usuarios, periodos y notas finales usan columnas `enum` nativas en lugar de constraints directos, compatibles con motores que no admiten `ALTER TABLE ... CHECK` (SQLite, etc.).
- `DatabaseSeeder` e `InformaticaCoursesSeeder` se reescribieron con transacciones, `updateOrCreate` y asociaciones idempotentes para soportar ejecuciones repetidas dentro del ciclo de CI/CD y durante el arranque del contenedor.

## [1.10.002] - 2025-10-25 · Import fontawesome-free
_Commit: `cebbad6`_
### Added
- Dependencia NPM `@fortawesome/fontawesome-free` e import directo en `resources/js/app.js` para empaquetar íconos con Vite.
### Changed
- Layouts principales (`resources/views/layouts/app.blade.php` y `guest.blade.php`) eliminan el CDN y confían en los assets generados, evitando llamadas externas en entornos cerrados.

## [1.10.001] - 2025-10-25 · Delete details
_Commit: `5e41473`_
### Changed
- Limpieza de Blade views: se retiraron comentarios redundantes, se homogenizaron secciones y se simplificaron los layouts (`layouts/app.blade.php`, `layouts/navigation.blade.php`, parciales de alertas).
- Modelos (`School`, `Student`, `Teacher`, `User`) quedaron sin comentarios superfluos para clarificar únicamente las relaciones activas.
- Estilos SASS/CSS retocados para alinearse con la nueva estructura visual del panel administrativo.
- Rutas (`routes/web.php`) reorganizadas con comentarios precisos en lugar de bloques heredados.
### Removed
- Vistas de detalle de matrículas por curso y por estudiante (`admin/enrollments/by_course.blade.php`, `admin/enrollments/by_student.blade.php`) que habían quedado obsoletas frente a los listados actuales.

## [1.10.000] - 2025-10-25 · Add bulk upload of grades
_Commit: `e740f43`_
### Added
- Búsqueda y filtros por escuela y docente en `Admin\CourseController::index`, con paginación que conserva parámetros y entrega catálogos de docentes/escuelas a la vista.
- Flujo de carga masiva para docentes: plantilla CSV descargable (`downloadTemplate`), importación (`import`) con normalización de encabezados, autodetección de delimitador y sanitización de filas vacías.
- Tarjeta informativa en `resources/views/teachers/course-grades.blade.php` con acciones para descargar/importar notas, más validaciones visuales de errores de carga.
- Utilidades privadas en `Teacher\CourseGradeController` para calcular promedios con sustitutorio/aplazado y determinar el estado final (`A`, `D`, `S`, `R`) de manera consistente.
### Changed
- Vista de cursos administrados (`admin/courses/index.blade.php`) ahora muestra filtros activos, integra contadores de estudiantes por periodo y conserva los parámetros en la paginación.
- `students/dashboard.blade.php` consolida las métricas y el listado de cursos activos con estado/nota final, mientras `students/my-courses.blade.php` se simplificó para mostrarse como tabla secundaria.

## [1.9.002] - 2025-10-24 · Fix dashboard
_Commit: `bff18c6`_
### Added
- Parcial `resources/views/students/partials/dashboard-stats.blade.php` para encapsular métricas y reutilizarlas en el panel del estudiante.
### Changed
- `DashboardController::studentDashboard` ahora consulta métricas reales: cursos activos, promedio general, aprobados/desaprobados y docentes asignados, controlando el acceso si el usuario no posee perfil de estudiante.
- `students/dashboard.blade.php` aprovecha las nuevas métricas y presenta un encabezado dinámico junto al parcial con estadísticas y cursos activos.
### Removed
- `students/dashboard-stats.blade.php` (se reemplazó por el nuevo parcial especializado).

## [1.9.001] - 2025-10-18 · Fix CourseGradeController and form
_Commit: `bf02750`_
### Added
- Componente Blade `components/final-grade/status-badge.blade.php` que estandariza el color y etiqueta de los estados finales (incluso si provienen de alias como `approved`, `sustitutorio`, etc.).
### Changed
- `Teacher\CourseGradeController::update` normaliza entradas numéricas, recalcula promedios con sustitutorio y aplazado, y establece el estado final mediante un `match` expresivo.
- `FinalGrade` y `GradeDetail` mantienen la información coherente para el resumen docente y la vista de estudiantes.
- Vistas de notas (`teachers/final-summary.blade.php`, `students/my-grades.blade.php`, `admin/courses/students.blade.php`) ahora utilizan el badge unificado.
- Migración `2025_10_18_182900_update_final_grades_status_constraint` agrega compatibilidad con SQLite cuando se recalculan constraints.

## [1.9.000] - 2025-10-18 · Fix admin views
_Commit: `e427545`_
### Added
- Formularios parciales para docentes y estudiantes (`admin/students/form.blade.php`, `admin/teachers/form.blade.php`) con listados de escuelas y estados.
- Migraciones correctivas que renombran `admission_year` a `enrollment_year` y amplían la longitud del código de estudiante, manteniendo datos existentes.
### Changed
- Controladores de admin (`StudentController`, `TeacherController`) gestionan creación/actualización de usuarios asociados con validaciones robustas, hash de contraseñas y control de transacciones.
- `DatabaseSeeder` asegura que los cambios de esquema se reflejen en datos de prueba (por ejemplo, año de ingreso actualizado).
- Vistas de formularios muestran selectores de escuelas,estado y mensajes de validación contextualizados.

## [1.8.000] - 2025-10-18 · Fix course form
_Commit: `25dfe3b`_
### Added
- Integración de `choices.js` para seleccionar múltiples prerrequisitos de forma accesible en modales y formularios (`resources/js/app.js`).
- Parciales específicos para formularios de cursos y nuevas plantillas en `resources/views/admin/courses`.
- Paginador Bootstrap 5 (`resources/views/vendor/pagination/bootstrap-5.blade.php`) personalizado para el panel.
### Changed
- `Admin\CourseController` incorpora validación sólida (códigos únicos, prerrequisitos distintos, docente obligatorio), sincroniza prerrequisitos, asigna docentes por periodo activo y expone endpoints JSON para las matriculaciones en modales.
- `vite.config.js`, SASS y layouts ajustados para compilar correctamente los assets con los nuevos estilos y scripts.
- Rutas web reorganizadas para soportar las nuevas acciones (gestión de estudiantes dentro de un curso).

## [1.7.000] - 2025-10-11 · Add index and forms
_Commit: `93d2539`_
### Added
- Parciales de métricas (`admin/partials/dashboard-metrics.blade.php`) y de insights (`dashboard-insights.blade.php`) que llenan el panel administrador con tarjetas de estadísticas.
- Formularios refinados para escuelas, cursos, estudiantes y docentes con estructura unificada y componentes reutilizables.
- Ajustes visuales (SASS variables y layout) para el tablero de estudiantes, docentes y administración.
### Changed
- `dashboard/index.blade.php` y layouts incluyen los nuevos parciales y definen slots claros para alertas/botones.
- Tablas del panel admin integran columnas de acción y estados con iconografía Font Awesome.

## [1.6.000] - 2025-10-11 · Add content to controller
_Commit: `d731a76`_
### Added
- Relaciones completas en modelos (`Course`, `CoursePrerequisite`, `CourseStudent`, `FinalGrade`, `School`, `Teacher`, etc.) incluyendo `withDefault`, scopes y casts para trabajar con métricas y reportes.
- Lógica de negocio real en controladores admin/teacher: matriculaciones, verificación de prerrequisitos, generación de vistas resumidas, dashboards por rol y cálculo de progreso del estudiante.
- Sidebar responsive con enlaces contextuales basados en rol y badges con contadores.
### Changed
- `DashboardController` distribuye la lógica entre métodos privados por rol y evita duplicar cálculos en las vistas.
- `CourseGradeController` añade vistas parciales y cálculos para resumen final, exportando datos a las vistas docentes/estudiantes.

## [1.5.000] - 2025-10-11 · Add controllers and views
_Commit: `64e3dd2`_
### Added
- Controladores de administración (cursos, escuelas, estudiantes, docentes) y vistas asociadas (CRUD completo con formularios, listados, modales y dashboards).
- Nuevos layouts (`layouts/app.blade.php`, `layouts/sidebar.blade.php`, `layouts/partials/alert.blade.php`, etc.) y componentes (alertas, tablas, sidebar, botones) para la experiencia de usuario interna.
- Dashboards específicos para admin, docentes y estudiantes con tarjetas métricas y secciones iniciales.
- Vistas de autenticación personalizadas (login) integrando la nueva imagen institucional.
### Changed
- Rutas web se reorganizaron con middlewares por rol (`checkrole`) y se añadieron prefijos/aliases (`admin.*`, `teacher.*`, `student.*`).
- Componentes Blade básicos (inputs, labels, modales, botones) ajustados para seguir la guía visual de Bootstrap 5 y Font Awesome.

## [1.4.000] - 2025-10-10 · Add models and seeder
_Commit: `56b6c71`_
### Added
- Migraciones para entidades académicas: escuelas, usuarios con rol, estudiantes, docentes, cursos, prerrequisitos, asignaciones docentes, matrículas y detalle de notas; todas con claves foráneas y restricciones.
- Modelos Eloquent (`School`, `Student`, `Teacher`, `Course`, `CourseStudent`, `CourseTeacher`, `GradeDetail`, `FinalGrade`, `Period`) con `$fillable` definidos y relaciones base.
- `DatabaseSeeder` inicial que crea escuelas, usuarios admin/docentes/estudiantes, cursos demo y detalles de notas.

## [10.3.001] - 2025-10-10 · Fix views
_Commit: `fec1f1b`_
### Added
- Recursos gráficos institucionales (`public/images/logo_unfv.png` / `.svg`) para los layouts.
- Vistas revisadas para dashboards de cada rol con tarjetas y componentes modernizados.
### Changed
- Ajustes masivos en modelos, migraciones y seeders para alinear nombres de columnas, llaves foráneas y datos sembrados con la nueva guía de estilos de las vistas.
- Middlewares `RedirectIfAuthenticated` y `RoleMiddleware` simplificados para redirigir según rol con mensajes claros.
- Archivos JS/CSS adaptados a la nueva estructura de componentes.

## [1.3.000] - 2025-09-27 · Add vite
_Commit: `b7d2c81`_
### Added
- Configuración de Vite y actualizaciones a `package.json`/`package-lock.json` para compilar assets con el nuevo layout.
- Ajustes en `app/Http/Kernel.php` y `bootstrap/app.php` para registrar middleware de Vite en desarrollo.
### Changed
- Layouts Blade apuntan a `@vite()` en lugar de Mix, y los dashboards aprovechan estilos empaquetados.

## [1.2.000] - 2025-09-27 · Add tables and views
_Commit: `ddbb9d3`_
### Added
- Scaffold completo de autenticación de Laravel Breeze (controladores, requests, tests) junto con componentes Blade (`AppLayout`, `GuestLayout`).
- Modelos adicionales (curso, periodo, estudiante, docente, relaciones intermedias) y migraciones iniciales para la estructura académica.
- Vistas del panel (admin, estudiante, docente), formularios de perfil, layouts con navegación, y componentes reutilizables.
- Seeders (`AdminUserSeeder`, `SchoolsSeeder`) y nuevos estilos SCSS que definen la apariencia general del proyecto.

## [1.1.000] - 2025-09-27 · First commit
_Commit: `29ae9c4`_
### Added
- Arranque del proyecto Laravel 10 vanilla con configuración base (`composer.json`, `vite.config.js`, rutas, controladores esqueleto, vistas de bienvenida) y estructura de pruebas.
- Archivos de configuración (`.env.example`, `.editorconfig`, `.gitignore`) y scaffolding mínimo necesario para iniciar el desarrollo.
