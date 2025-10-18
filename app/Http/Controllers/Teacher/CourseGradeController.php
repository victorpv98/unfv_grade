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
        DB::transaction(function () use ($request, $course) {
            foreach ($request->input('grades', []) as $studentId => $inputGrades) {
                $courseStudent = CourseStudent::where('student_id', $studentId)
                    ->where('course_id', $course->id)
                    ->first();

                if (!$courseStudent) {
                    continue;
                }

                $fields = [
                    'practice1',
                    'practice2',
                    'practice3',
                    'practice4',
                    'midterm',
                    'final',
                    'substitute',
                    'makeup',
                ];

                $normalized = collect($fields)->mapWithKeys(function ($field) use ($inputGrades) {
                    $value = $inputGrades[$field] ?? null;

                    if ($value === '' || $value === null) {
                        return [$field => null];
                    }

                    if (!is_numeric($value)) {
                        return [$field => null];
                    }

                    return [$field => (int) round((float) $value)];
                })->toArray();

                $detail = GradeDetail::updateOrCreate(
                    ['course_student_id' => $courseStudent->id],
                    $normalized
                );

                $scores = collect([
                    $detail->practice1,
                    $detail->practice2,
                    $detail->practice3,
                    $detail->practice4,
                    $detail->midterm,
                    $detail->final,
                ])->filter(fn($score) => $score !== null)->values();

                $finalAverage = $scores->isNotEmpty()
                    ? round($scores->avg(), 2)
                    : 0;

                $substitute = $detail->substitute;
                if ($substitute !== null && $substitute > 0 && $scores->isNotEmpty()) {
                    $lowestIndex = $scores->search($scores->min());
                    if ($lowestIndex !== false) {
                        $scores[$lowestIndex] = $substitute;
                        $finalAverage = round($scores->avg(), 2);
                    }
                }

                $makeup = $detail->makeup;
                if ($makeup !== null && $makeup > 0) {
                    $finalAverage = round(($finalAverage + $makeup) / 2, 2);
                }

                $status = match (true) {
                    $makeup !== null && $makeup > 0 => $finalAverage >= 10.5 ? 'A' : 'R',
                    $substitute !== null && $substitute > 0 => $finalAverage >= 10.5 ? 'A' : 'S',
                    default => $finalAverage >= 10.5 ? 'A' : 'D',
                };

                FinalGrade::updateOrCreate(
                    ['course_student_id' => $courseStudent->id],
                    [
                        'average' => $finalAverage,
                        'status' => $status,
                    ]
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
