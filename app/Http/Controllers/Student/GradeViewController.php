<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\{CourseStudent, FinalGrade};
use Illuminate\Support\Facades\Auth;

class GradeViewController extends Controller
{
    public function myCourses()
    {
        $studentId = Auth::user()->student->id;
        $courses = CourseStudent::with('course')
            ->where('student_id', $studentId)
            ->get();

        return view('students.my-courses', compact('courses'));
    }

    public function myGrades()
    {
        $studentId = Auth::user()->student->id;
        $grades = FinalGrade::with(['courseStudent.course'])
            ->whereHas('courseStudent', fn($q) => $q->where('student_id', $studentId))
            ->get();

        return view('students.my-grades', compact('grades'));
    }
}