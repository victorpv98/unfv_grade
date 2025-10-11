<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::orderBy('name')->paginate(10);
        return view('admin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('admin.schools.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:schools,code|max:10',
            'name' => 'required|max:160',
        ]);

        School::create($request->only('code', 'name'));

        return redirect()->route('admin.schools.index')->with('success', 'Escuela creada correctamente.');
    }

    public function edit(School $school)
    {
        return view('admin.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $request->validate([
            'code' => 'required|max:10|unique:schools,code,' . $school->id,
            'name' => 'required|max:160',
        ]);

        $school->update($request->only('code', 'name'));

        return redirect()->route('admin.schools.index')->with('success', 'Escuela actualizada correctamente.');
    }

    public function destroy(School $school)
    {
        $school->delete();
        return back()->with('success', 'Escuela eliminada correctamente.');
    }
}