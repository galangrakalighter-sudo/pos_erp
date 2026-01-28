<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'client_id' => ['required', 'string', 'max:255'],
        ]);

        // Validasi bahwa client_id ada dan valid
        $client = Client::where('client_id', $request->client_id)->first();
        
        if (!$client) {
            return back()->withErrors([
                'client_id' => 'Client ID tidak valid atau tidak ditemukan.'
            ])->withInput();
        }

        // Cek apakah user dengan client_id ini sudah ada
        $existingUser = User::where('client_id', $request->client_id)->first();
        if ($existingUser) {
            return back()->withErrors([
                'client_id' => 'Akun dengan Client ID ini sudah terdaftar.'
            ])->withInput();
        }

        // Buat user baru dengan client_id yang sudah ada
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client', // Set default role untuk user baru
            'client_id' => $request->client_id, // Gunakan client_id yang sudah ada
        ]);

        // Sinkronkan nama dengan tabel client jika berbeda
        if ($client->nama !== $request->name) {
            $client->update([
                'nama' => $request->name,
                'diperbaharui' => now()
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
