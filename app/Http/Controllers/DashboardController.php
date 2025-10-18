<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\{
    School,
    Course,
    Teacher,
    Student,
    CourseTeacher,
    CourseStudent,
    GradeDetail,
    FinalGrade
};

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => abort(403, 'Rol no autorizado'),
        };
    }

    public function adminDashboard()
    {
        return view('dashboard.index', [
            'title' => 'Panel del Administrador',
            'schoolsCount' => School::count(),
            'teachersCount' => Teacher::count(),
            'studentsCount' => Student::count(),
            'coursesCount' => Course::count(),
        ]);
    }

    public function teacherDashboard()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            abort(403, 'El usuario autenticado no tiene un perfil de docente asignado.');
        }

        $teacherId = $teacher->id;

        $assignments = CourseTeacher::query()
            ->where('teacher_id', $teacherId)
            ->get(['course_id', 'period_id']);

        $courseIds = $assignments->pluck('course_id')->unique();

        $coursesCount = $courseIds->count();

        $studentsCount = CourseStudent::query()
            ->join('course_teachers', function ($join) use ($teacherId) {
                $join->on('course_students.course_id', '=', 'course_teachers.course_id')
                    ->on('course_students.period_id', '=', 'course_teachers.period_id')
                    ->where('course_teachers.teacher_id', $teacherId);
            })
            ->distinct('course_students.student_id')
            ->count('course_students.student_id');

        $gradesCount = GradeDetail::query()
            ->join('course_students', 'grade_details.course_student_id', '=', 'course_students.id')
            ->join('course_teachers', function ($join) use ($teacherId) {
                $join->on('course_students.course_id', '=', 'course_teachers.course_id')
                    ->on('course_students.period_id', '=', 'course_teachers.period_id')
                    ->where('course_teachers.teacher_id', $teacherId);
            })
            ->count('grade_details.id');

        $activeAssignments = CourseTeacher::query()
            ->where('teacher_id', $teacherId)
            ->whereHas('period', fn ($query) => $query->where('status', 'active'))
            ->get(['course_id', 'period_id']);

        $activeCourseIds = $activeAssignments->pluck('course_id')->unique();
        $activePeriodIds = $activeAssignments->pluck('period_id')->unique();

        $activeCoursesCount = $activeCourseIds->count();

        $activeCourses = $activeCourseIds->isNotEmpty()
            ? Course::query()
                ->whereIn('id', $activeCourseIds)
                ->select('id', 'code', 'name', 'credits')
                ->withCount([
                    'courseStudents as students_count' => function ($query) use ($activePeriodIds) {
                        $query->when(
                            $activePeriodIds->isNotEmpty(),
                            fn ($q) => $q->whereIn('period_id', $activePeriodIds)
                        );
                    },
                ])
                ->orderBy('name')
                ->get()
            : collect();

        if ($activeCourseIds->isNotEmpty()) {
            $averagesByCourse = FinalGrade::query()
                ->join('course_students', 'final_grades.course_student_id', '=', 'course_students.id')
                ->whereIn('course_students.course_id', $activeCourseIds)
                ->when(
                    $activePeriodIds->isNotEmpty(),
                    fn ($query) => $query->whereIn('course_students.period_id', $activePeriodIds)
                )
                ->selectRaw('course_students.course_id as course_id, AVG(final_grades.average) as average')
                ->groupBy('course_students.course_id')
                ->pluck('average', 'course_id');

            $activeCourses->transform(function ($course) use ($averagesByCourse) {
                $course->average = $averagesByCourse->has($course->id)
                    ? round((float) $averagesByCourse[$course->id], 2)
                    : null;

                return $course;
            });
        }

        return view('dashboard.index', [
            'title' => 'Panel del Docente',
            'coursesCount' => $coursesCount,
            'studentsCount' => $studentsCount,
            'gradesCount' => $gradesCount,
            'activeCoursesCount' => $activeCoursesCount,
            'activeCourses' => $activeCourses,
            'lastAccess' => $user->updated_at,
        ]);
    }

    public function studentDashboard()
    {
        return view('dashboard.index', [
            'title' => 'Panel del Estudiante',
        ]);
    }
}
