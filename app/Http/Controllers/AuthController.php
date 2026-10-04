<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('procurements.index');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required'    => 'Email atau username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $login = trim($request->input('login'));
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $remember = $request->boolean('remember');

        $credentials = [
            $fieldType => $login,
            'password'  => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        // Secondary fallback: allow logging in by 'name' if username check failed
        if ($fieldType === 'username') {
            if (Auth::attempt(['name' => $login, 'password' => $request->input('password')], $remember)) {
                $request->session()->regenerate();
                return redirect()->intended(route('home'));
            }
        }

        return back()->withInput($request->only('login', 'remember'))->withErrors([
            'login' => 'Email/Username atau password yang Anda masukkan tidak cocok.',
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil keluar.');
    }
}
