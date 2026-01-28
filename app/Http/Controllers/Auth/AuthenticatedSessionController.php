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
     * Tampilkan halaman login
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login dan arahkan berdasarkan role
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $role = $request->user()->role;

        // Arahkan berdasarkan role
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'client') {
            return redirect()->route('client.dashboard');
        } else {
            // Jika role tidak dikenal
            Auth::logout();
            return redirect('/login')->withErrors(['role' => 'Role tidak dikenali.']);
        }
    }


    /**
     * Logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
