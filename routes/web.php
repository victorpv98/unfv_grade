<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    DashboardController,
    Admin\SchoolController,
    Admin\CourseController,
    Admin\TeacherController,
    Admin\StudentController,
    Teacher\CourseGradeController,
    Student\GradeViewController,
};

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => Auth::check()
    ? redirect()->route('dashboard')
    : redirect()->route('login')
)->name('home');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (solo usuarios autenticados)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Redirección general al dashboard según rol
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRADOR
    |--------------------------------------------------------------------------
    */
    Route::middleware('checkrole:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])
                ->name('dashboard');

            // Gestión de escuelas
            Route::resource('schools', SchoolController::class)->names('schools');

            // Gestión de cursos
            Route::resource('courses', CourseController::class)->names('courses');
            // Ver estudiantes matriculados en un curso
            Route::get('/courses/{course}/students', [CourseController::class, 'students'])
                ->name('courses.students');
            Route::post('/courses/{course}/students', [CourseController::class, 'enrollStudent'])
                ->name('courses.students.enroll');
            Route::post('/courses/{course}/prerequisites/check', [CourseController::class, 'checkPrerequisites'])
                ->name('courses.prerequisites.check');

            // Gestión de docentes
            Route::resource('teachers', TeacherController::class)->names('teachers');

            // Gestión de estudiantes
            Route::resource('students', StudentController::class)->names('students');
        });

    /*
    |--------------------------------------------------------------------------
    | DOCENTE
    |--------------------------------------------------------------------------
    */
    Route::middleware('checkrole:teacher')
        ->prefix('teacher')
        ->name('teacher.')
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])
                ->name('dashboard');

            // Cursos asignados al docente
            Route::get('/my-courses', [CourseGradeController::class, 'index'])
                ->name('my-courses');

            // Registro de notas (grade_details)
            Route::get('/courses/{course}/grades', [CourseGradeController::class, 'edit'])
                ->name('courses.grades');
            Route::post('/courses/{course}/grades', [CourseGradeController::class, 'update'])
                ->name('courses.grades.update');
            Route::get('/courses/{course}/grades/template', [CourseGradeController::class, 'downloadTemplate'])
                ->name('courses.grades.template');
            Route::post('/courses/{course}/grades/import', [CourseGradeController::class, 'import'])
                ->name('courses.grades.import');

            // Promedio final (final_grades)
            Route::get('/courses/{course}/summary', [CourseGradeController::class, 'summary'])
                ->name('courses.summary');
        });

    /*
    |--------------------------------------------------------------------------
    | ESTUDIANTE
    |--------------------------------------------------------------------------
    */
    Route::middleware('checkrole:student')
        ->prefix('student')
        ->name('student.')
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'studentDashboard'])
                ->name('dashboard');

            // Cursos matriculados (course_students)
            Route::get('/my-courses', [GradeViewController::class, 'myCourses'])
                ->name('my-courses');

            // Consulta de notas finales (final_grades)
            Route::get('/my-grades', [GradeViewController::class, 'myGrades'])
                ->name('my-grades');
        });
});
