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
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authenticate credentials
        $request->authenticate();

        $user = Auth::user();

        // CHECK ACCOUNT STATUS (Para sa Resident)
        if ($user->isResident() && $user->account_status !== 'verified') {
            // Logout agad kung hindi pa verified
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = $user->account_status === 'pending'
                ? 'Your account is currently pending approval. Please wait for confirmation.'
                : 'Your account is rejected by admin. Reason: ' . ($user->rejection_reason ?? 'Invalid documents');

            return redirect()->route('login')->withErrors([
                'email' => $message,
            ]);
        }

        // Regenerate session
        $request->session()->regenerate();

        // Role-based Redirection
        if ($user->isStaff()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('resident.dashboard'));
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