<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['user.school'])->paginate(10);
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $schools = School::orderBy('name')->get();
        return view('admin.students.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'document_type' => 'nullable|string|max:10',
            'document_number' => 'nullable|string|max:20',
            'school_id' => 'required|exists:schools,id',
            'status' => 'nullable|in:active,suspended',
            'code' => 'required|string|max:20|unique:students,code',
            'enrollment_year' => 'required|integer|min:2000|max:' . now()->year,
            'password' => 'nullable|string|min:6',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password ?: '123456'),
                'role' => 'student',
                'school_id' => $request->school_id,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'status' => $request->status ?? 'active',
            ]);

            Student::create([
                'user_id' => $user->id,
                'code' => $request->code,
                'enrollment_year' => $request->enrollment_year,
            ]);
        });

        return redirect()->route('admin.students.index')->with('success', 'Estudiante registrado correctamente.');
    }

    public function edit(Student $student)
    {
        $student->load('user');
        $schools = School::orderBy('name')->get();

        return view('admin.students.edit', compact('student', 'schools'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($student->user_id),
            ],
            'document_type' => 'nullable|string|max:10',
            'document_number' => 'nullable|string|max:20',
            'school_id' => 'required|exists:schools,id',
            'status' => 'nullable|in:active,suspended',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('students', 'code')->ignore($student->id),
            ],
            'enrollment_year' => 'required|integer|min:2000|max:' . now()->year,
            'password' => 'nullable|string|min:6',
        ]);

        DB::transaction(function () use ($request, $student) {
            $user = $student->user ?? new User(['role' => 'student']);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->school_id = $request->school_id;
            $user->status = $request->status ?? $user->status ?? 'active';
            $user->document_type = $request->document_type;
            $user->document_number = $request->document_number;
            $user->role = 'student';

            if (! $user->exists) {
                $user->password = Hash::make($request->password ?: '123456');
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            if (! $student->user_id) {
                $student->user_id = $user->id;
            }

            $student->code = $request->code;
            $student->enrollment_year = $request->enrollment_year;
            $student->save();
        });

        return redirect()->route('admin.students.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return back()->with('success', 'Estudiante eliminado correctamente.');
    }
}
