<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{Course, CourseStudent, GradeDetail, FinalGrade};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseGradeController extends Controller
{
    public function index()
    {
        $teacherId = auth()->user()->teacher->id;
        $courses = Course::whereHas('courseTeachers', fn($q) => $q->where('teacher_id', $teacherId))->get();
        return view('teachers.my-courses', compact('courses'));
    }

    public function edit(Course $course)
    {
        $students = CourseStudent::with(['student.user', 'gradeDetail'])
            ->where('course_id', $course->id)
            ->get();

        return view('teachers.course-grades', compact('course', 'students'));
    }

    public function update(Request $request, Course $course)
    {
        DB::transaction(function () use ($request) {
            foreach ($request->grades as $studentId => $data) {
                $courseStudent = \App\Models\CourseStudent::where('student_id', $studentId)
                    ->where('course_id', $request->course_id)
                    ->first();

                if (!$courseStudent) continue;

                $detail = GradeDetail::updateOrCreate(
                    ['course_student_id' => $courseStudent->id],
                    $data
                );

                // cálculo promedio
                $practices = collect([$detail->practice1, $detail->practice2, $detail->practice3, $detail->practice4])
                    ->filter(fn($v) => $v !== null)
                    ->sortDesc()
                    ->take(3);

                $promPracticas = $practices->avg() ?? 0;
                $parcial = $detail->midterm ?? 0;
                $final = $detail->final ?? 0;

                $preliminar = round(($promPracticas * 0.4) + ($parcial * 0.3) + ($final * 0.3), 2);

                // sustitutorio
                if ($detail->substitute) {
                    if ($parcial < $final) $parcial = max($parcial, $detail->substitute);
                    else $final = max($final, $detail->substitute);
                    $preliminar = round(($promPracticas * 0.4) + ($parcial * 0.3) + ($final * 0.3), 2);
                }

                // aplazado
                $promedioFinal = $preliminar;
                if ($preliminar > 8 && $detail->makeup) {
                    $promedioFinal = round(($preliminar + $detail->makeup) / 2, 2);
                }

                $status = $promedioFinal >= 10.5 ? 'Aprobado' : 'Desaprobado';

                FinalGrade::updateOrCreate(
                    ['course_student_id' => $courseStudent->id],
                    ['average' => $promedioFinal, 'status' => $status]
                );
            }
        });

        return back()->with('success', 'Notas actualizadas correctamente.');
    }

    public function summary(Course $course)
    {
        $grades = FinalGrade::with('courseStudent.student.user')
            ->whereHas('courseStudent', fn($q) => $q->where('course_id', $course->id))
            ->get();

        return view('teachers.final-summary', compact('course', 'grades'));
    }
}