<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = $request->input('email');

        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $attemptCredentials = [
            $fieldType => $loginInput,
            'password' => $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($attemptCredentials, $remember)) {

            $user = Auth::user();

            // Check if user is active
            if ((int) $user->status !== 1) {

                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors([
                        'email' => 'Your account is currently inactive. Please contact the administrator.'
                    ]);
            }

            // Update login logs
            $logs = $user->logs ?? [];

            $logs['last_login_at'] = now()->toDateTimeString();
            $logs['last_login_ip'] = $request->ip();
            $logs['user_agent'] = $request->userAgent();

            $user->update([
                'logs' => $logs
            ]);

            // Regenerate session
            $request->session()->regenerate();

            // Redirect according to role
            return $this->redirectByRole($user);
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'The provided credentials do not match our records.'
            ]);
    }

    /**
     * Redirect user according to role.
     */
    private function redirectByRole($user)
    {
        $user->load('role');

        if (!$user->role) {
            Auth::logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'No role has been assigned to your account.'
                ]);
        }

        return match ($user->role->short_name) {

            'admin' => redirect()->route('dashboard'),

            'sale' => redirect()->route('dashboard.sale'),

            'purchase' => redirect()->route('dashboard.purchase'),

            'gate' => redirect()->route('dashboard.gate'),

            'weight' => redirect()->route('dashboard.weight'),

            'unloader' => redirect()->route('dashboard.unloader'),

            'dispatch' => redirect()->route('dashboard.dispatch'),

            'lab' => redirect()->route('dashboard.lab'),

            'production' => redirect()->route('dashboard.production'),

            // 'lab_production' => redirect()->route('dashboard.lab-production'),

            'account' => redirect()->route('dashboard.account'),

            default => redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'No dashboard is assigned to your role.'
                ]),
        };
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been successfully logged out.');
    }
}
