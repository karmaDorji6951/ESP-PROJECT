<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $role = optional($user->role)->slug;

            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($role === 'supervisor') {
                return redirect()->route('supervisor.dashboard');
            } elseif ($role === 'staff') {
                return redirect()->route('staff.dashboard');
            }

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials are invalid.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
