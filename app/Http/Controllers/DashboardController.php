<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Period;
use App\Models\CourseStudent;
use App\Models\CourseTeacher;
use App\Models\GradeDetail;
use App\Models\FinalGrade;

class DashboardController extends Controller
{
    public function admin()
    {
        $metrics = [
            'total_users'      => User::count(),
            'total_students'   => User::where('role','student')->count(),
            'total_teachers'   => User::where('role','teacher')->count(),
            'total_courses'    => Course::count(),
            'open_periods'     => Period::where('status','open')->count(),
            'last_period'      => Period::orderBy('id','desc')->value('code') ?? '—',
        ];

        return view('dashboards.admin', compact('metrics'));
    }

    public function teacher(Request $request)
    {
        $teacher = $request->user()->teacherProfile; // relación hasOne
        $teacherId = optional($teacher)->id;

        $periodId = Period::where('status','open')->orderBy('id','desc')->value('id');

        $myCoursesCount = $teacherId
            ? CourseTeacher::where('teacher_id', $teacherId)
                ->when($periodId, fn($q) => $q->where('period_id',$periodId))
                ->count()
            : 0;

        $pendingGrades = $teacherId && $periodId
            ? GradeDetail::where('teacher_id',$teacherId)
                ->where('period_id',$periodId)
                ->whereNull('score')
                ->count()
            : 0;

        $metrics = [
            'current_period' => $periodId ? Period::find($periodId)->code : '—',
            'my_courses'     => $myCoursesCount,
            'pending_grades' => $pendingGrades,
        ];

        return view('dashboards.teacher', compact('metrics'));
    }

    public function student(Request $request)
    {
        $student = $request->user()->studentProfile;
        $studentId = optional($student)->id;

        $period = Period::where('status','open')->orderBy('id','desc')->first();
        $periodId = optional($period)->id;

        $enrolled = $studentId && $periodId
            ? CourseStudent::where('student_id',$studentId)
                ->where('period_id',$periodId)
                ->count()
            : 0;

        $gradesLoaded = $studentId && $periodId
            ? GradeDetail::where('student_id',$studentId)
                ->where('period_id',$periodId)
                ->whereNotNull('score')
                ->count()
            : 0;

        $final = $studentId && $periodId
            ? FinalGrade::where('student_id',$studentId)
                ->where('period_id',$periodId)
                ->count()
            : 0;

        $metrics = [
            'current_period' => optional($period)->code ?? '—',
            'my_enrollments' => $enrolled,
            'grades_loaded'  => $gradesLoaded,
            'final_records'  => $final,
        ];

        return view('dashboards.student', compact('metrics'));
    }
}