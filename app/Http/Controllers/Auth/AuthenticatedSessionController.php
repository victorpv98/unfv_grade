<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra la vista de login.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Procesa el inicio de sesión.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Las credenciales no son válidas.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return match ($user->role) {
            'admin'   => redirect()->route('admin.dashboard')->with('success', 'Bienvenido al panel de administrador'),
            'teacher' => redirect()->route('teacher.dashboard')->with('success', 'Bienvenido al panel de docente'),
            'student' => redirect()->route('student.dashboard')->with('success', 'Bienvenido al panel de estudiante'),
            default   => redirect()->route('dashboard'),
        };
    }

    /**
     * Cierra la sesión.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Sesión cerrada correctamente.');
    }
}