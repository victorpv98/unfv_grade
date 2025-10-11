<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user')->paginate(10);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|unique:teachers,user_id',
            'specialty' => 'nullable|max:100',
        ]);

        Teacher::create($request->only('user_id', 'specialty'));

        return redirect()->route('admin.teachers.index')->with('success', 'Docente registrado correctamente.');
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'specialty' => 'nullable|max:100',
        ]);

        $teacher->update($request->only('specialty'));

        return redirect()->route('admin.teachers.index')->with('success', 'Docente actualizado correctamente.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return back()->with('success', 'Docente eliminado correctamente.');
    }
}