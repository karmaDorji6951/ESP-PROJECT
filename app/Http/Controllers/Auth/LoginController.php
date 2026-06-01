<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function create(Request $request)
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $normalize = function ($value): string {
            $value = (string) $value;

            // Remove UTF-8 BOM if present.
            $value = preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;

            // Remove common zero-width characters.
            $value = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $value) ?? $value;

            // Normalize non-breaking spaces.
            $value = str_replace("\xC2\xA0", ' ', $value);

            return Str::lower(trim($value));
        };

        $allowedEmails = collect(config('access.allowed_login_emails', []))
            ->map(fn ($email) => $normalize($email))
            ->filter();

        $allowedHandles = $allowedEmails
            ->map(fn ($email) => Str::before($email, '@'))
            ->values();

        $loginInput = $normalize($request->email);

        // Support logging in via email handle like "karma.dorji".
        if (!Str::contains($loginInput, '@') && $allowedHandles->contains($loginInput)) {
            $mappedEmail = $allowedEmails->first(fn ($email) => Str::before($email, '@') === $loginInput);
            if ($mappedEmail) {
                $request->merge(['email' => $mappedEmail]);
                $loginInput = $mappedEmail;
            }
        }

        // Only allow authentication for explicitly allowed accounts.
        if ($allowedEmails->isEmpty() || !Str::contains($loginInput, '@') || !$allowedEmails->contains($loginInput)) {
            return back()->withErrors([
                'email' => 'Access denied. This account is not allowed to log in.',
            ])->onlyInput('email');
        }

        // Check if the input is an email or a username
        $loginField = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
        $authCredentials = [
            $loginField => $request->email,
            'password' => $request->password,
        ];

        try {
            if (Auth::attempt($authCredentials, $request->boolean('remember'))) {
                $request->session()->regenerate();

                $user = Auth::user();
                
                // Ensure role relationship is loaded
                if (!$user->role) {
                    Auth::logout();
                    $request->session()->invalidate();
                    return back()->withErrors([
                        'email' => 'User role is not assigned. Please contact administrator.',
                    ])->onlyInput('email');
                }

                $role = $user->role->slug ?? $user->role->name;
                $role = Str::lower(trim((string) $role));

                $dashboardRoute = match ($role) {
                    'admin' => 'admin.dashboard',
                    'supervisor' => 'supervisor.dashboard',
                    'staff' => 'staff.dashboard',
                    default => 'dashboard',
                };

                if (! Route::has($dashboardRoute)) {
                    $dashboardRoute = 'dashboard';
                }

                return redirect()->intended(route($dashboardRoute));
            }
        } catch (QueryException $e) {
            report($e);

            return back()->withErrors([
                'email' => 'Authentication database is currently unavailable. Please try again later or contact the administrator.',
            ])->onlyInput('email');
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
