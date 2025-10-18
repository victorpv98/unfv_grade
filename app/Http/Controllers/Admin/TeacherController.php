<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with(['user.school'])->paginate(10);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $schools = School::orderBy('name')->get();
        return view('admin.teachers.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'school_id' => 'required|exists:schools,id',
            'specialty' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
            'status' => 'nullable|in:active,suspended',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password ?: '123456'),
                'role' => 'teacher',
                'school_id' => $request->school_id,
                'status' => $request->status ?? 'active',
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'specialty' => $request->specialty,
            ]);
        });

        return redirect()->route('admin.teachers.index')->with('success', 'Docente registrado correctamente.');
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        $schools = School::orderBy('name')->get();

        return view('admin.teachers.edit', compact('teacher', 'schools'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($teacher->user_id),
            ],
            'school_id' => 'required|exists:schools,id',
            'specialty' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
            'status' => 'nullable|in:active,suspended',
        ]);

        DB::transaction(function () use ($request, $teacher) {
            $user = $teacher->user ?? new User(['role' => 'teacher']);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->school_id = $request->school_id;
            $user->status = $request->status ?? $user->status ?? 'active';
            $user->role = 'teacher';

            if (! $user->exists) {
                $user->password = Hash::make($request->password ?: '123456');
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            if (! $teacher->user_id) {
                $teacher->user_id = $user->id;
            }

            $teacher->specialty = $request->specialty;
            $teacher->save();
        });

        return redirect()->route('admin.teachers.index')->with('success', 'Docente actualizado correctamente.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return back()->with('success', 'Docente eliminado correctamente.');
    }
}
