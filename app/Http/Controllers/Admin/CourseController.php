<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Course, School, CourseStudent, Student};
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('school')->orderBy('name')->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $schools = School::all();
        return view('admin.courses.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:courses,code|max:10',
            'name' => 'required|max:150',
            'credits' => 'required|integer|min:1',
            'school_id' => 'required|exists:schools,id',
        ]);

        Course::create($request->only('code', 'name', 'credits', 'school_id'));

        return redirect()->route('admin.courses.index')->with('success', 'Curso creado correctamente.');
    }

    public function edit(Course $course)
    {
        $schools = School::all();
        return view('admin.courses.edit', compact('course', 'schools'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'code' => 'required|max:10|unique:courses,code,' . $course->id,
            'name' => 'required|max:150',
            'credits' => 'required|integer|min:1',
            'school_id' => 'required|exists:schools,id',
        ]);

        $course->update($request->only('code', 'name', 'credits', 'school_id'));

        return redirect()->route('admin.courses.index')->with('success', 'Curso actualizado correctamente.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return back()->with('success', 'Curso eliminado correctamente.');
    }

    public function students(Course $course)
    {
        $students = Student::whereHas('courseStudents', fn($q) => $q->where('course_id', $course->id))->get();
        return view('admin.courses.students', compact('course', 'students'));
    }
}