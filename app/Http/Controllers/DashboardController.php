<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\{School, Course, Teacher, Student};

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => abort(403, 'Rol no autorizado'),
        };
    }

    public function adminDashboard()
    {
        return view('dashboard.index', [
            'title' => 'Panel del Administrador',
            'schoolsCount' => School::count(),
            'teachersCount' => Teacher::count(),
            'studentsCount' => Student::count(),
            'coursesCount' => Course::count(),
        ]);
    }

    public function teacherDashboard()
    {
        return view('dashboard.index', [
            'title' => 'Panel del Docente',
        ]);
    }

    public function studentDashboard()
    {
        return view('dashboard.index', [
            'title' => 'Panel del Estudiante',
        ]);
    }
}