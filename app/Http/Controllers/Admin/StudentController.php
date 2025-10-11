<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('user')->paginate(10);
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|unique:students,user_id',
            'code' => 'required|unique:students,code|max:15',
            'admission_year' => 'required|integer|min:2000',
        ]);

        Student::create($request->only('user_id', 'code', 'admission_year'));

        return redirect()->route('admin.students.index')->with('success', 'Estudiante registrado correctamente.');
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'code' => 'required|max:15|unique:students,code,' . $student->id,
            'admission_year' => 'required|integer|min:2000',
        ]);

        $student->update($request->only('code', 'admission_year'));

        return redirect()->route('admin.students.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return back()->with('success', 'Estudiante eliminado correctamente.');
    }
}