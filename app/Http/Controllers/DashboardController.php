<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Redirección al dashboard según rol
     */
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

    /**
     * Dashboard de Administrador
     */
    public function adminDashboard()
    {
        return view('dashboard.index', [
            'title' => 'Panel de Administración',
        ]);
    }

    /**
     * Dashboard de Docente
     */
    public function teacherDashboard()
    {
        return view('dashboard.index', [
            'title' => 'Panel del Docente',
        ]);
    }

    /**
     * Dashboard de Estudiante
     */
    public function studentDashboard()
    {
        return view('dashboard.index', [
            'title' => 'Panel del Estudiante',
        ]);
    }
}