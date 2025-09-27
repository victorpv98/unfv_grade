<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // Permitir login con email o documento
        $loginField = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'document_number';

        $credentials = [
            $loginField => $request->input('email'),
            'password'  => $request->input('password'),
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => __('Credenciales incorrectas.'),
            ])->onlyInput('email');
        }

       $request->session()->regenerate();

        $user = $request->user();
        if ($user->role === 'admin')   { return redirect()->route('admin.dashboard'); }
        if ($user->role === 'teacher') { return redirect()->route('teacher.dashboard'); }
        if ($user->role === 'student') { return redirect()->route('student.dashboard'); }

        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
