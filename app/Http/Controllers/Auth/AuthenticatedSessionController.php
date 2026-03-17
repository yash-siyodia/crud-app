<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\ActivityLogService;
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
    public function store(LoginRequest $request): RedirectResponse
    {
        $email = $request->email;
        
        try {
            $request->authenticate();

            $request->session()->regenerate();

            // Log successful login
            ActivityLogService::logAuth('login', $email, true);

            return redirect()->intended(RouteServiceProvider::HOME);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log failed login attempt
            ActivityLogService::logAuth('login_failed', $email, false, 'Invalid credentials');
            throw $e;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log logout before destroying session
        if (Auth::check()) {
            ActivityLogService::logAuth('logout', Auth::user()->email, true);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
