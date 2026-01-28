<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserClientController extends Controller
{
    // Ambil semua client (role=client)
    public function index()
    {
        $clients = User::where('role', 'client')->get()->map(function($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'avatar' => $u->avatar ? asset('storage/' . $u->avatar) : null,
                'alamat' => $u->alamat,
                'telepon' => $u->telepon,
                'stok' => $u->stok,
                'tanggal_bergabung' => $u->tanggal_bergabung,
                'created_at' => $u->created_at,
                'updated_at' => $u->updated_at,
            ];
        });
        
        return view('admin.client', compact('clients'));
    }

    // Tambah client baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|string', // base64
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'stok' => 'nullable|integer',
            'bergabung' => 'required|date',
        ]);

        // Simpan avatar jika base64
        $avatarPath = null;
        if (!empty($validated['avatar']) && str_starts_with($validated['avatar'], 'data:image')) {
            $avatarData = $validated['avatar'];
            $avatarName = 'avatar_' . time() . '.png';
            $avatarPath = 'avatars/' . $avatarName;
            $data = explode(',', $avatarData);
            Storage::disk('public')->put($avatarPath, base64_decode(end($data)));
        }

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'] ?? null;
        $user->password = isset($validated['password']) ? Hash::make($validated['password']) : Hash::make('client123');
        $user->role = 'client';
        $user->avatar = $avatarPath;
        $user->alamat = $validated['alamat'] ?? null;
        $user->telepon = $validated['telepon'] ?? null;
        $user->stok = $validated['stok'] ?? 0;
        $user->tanggal_bergabung = $validated['bergabung'];
        $user->save();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'alamat' => $user->alamat,
            'telepon' => $user->telepon,
            'stok' => $user->stok,
            'tanggal_bergabung' => $user->tanggal_bergabung,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    // Update client
    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->where('role', 'client')->first();
        
        if (!$user) {
            return response()->json(['message' => 'Client tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|unique:users,email,' . $user->id . ',id',
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|string', // base64 atau path existing
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'stok' => 'nullable|integer',
            'bergabung' => 'nullable|date',
        ]);

        // Update hanya field yang diisi
        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        if (isset($validated['alamat'])) {
            $user->alamat = $validated['alamat'];
        }
        if (isset($validated['telepon'])) {
            $user->telepon = $validated['telepon'];
        }
        if (isset($validated['stok'])) {
            $user->stok = $validated['stok'];
        }
        if (isset($validated['bergabung'])) {
            $user->tanggal_bergabung = $validated['bergabung'];
        }

        // Handle avatar update
        if (isset($validated['avatar'])) {
            if (str_starts_with($validated['avatar'], 'data:image')) {
                // Avatar baru (base64)
                $avatarData = $validated['avatar'];
                $avatarName = 'avatar_' . time() . '.png';
                $avatarPath = 'avatars/' . $avatarName;
                $data = explode(',', $avatarData);
                Storage::disk('public')->put($avatarPath, base64_decode(end($data)));
                $user->avatar = $avatarPath;
            } elseif (!empty($validated['avatar']) && !str_starts_with($validated['avatar'], 'http')) {
                // Avatar existing (path tanpa http)
                $user->avatar = $validated['avatar'];
            }
            // Jika avatar kosong atau null, tidak update (biarkan existing)
        }

        $user->save();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'alamat' => $user->alamat,
            'telepon' => $user->telepon,
            'stok' => $user->stok,
            'tanggal_bergabung' => $user->tanggal_bergabung,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    // Hapus client
    public function destroy($id)
    {
        $user = User::where('id', $id)->where('role', 'client')->first();
        
        if (!$user) {
            return response()->json(['message' => 'Client tidak ditemukan'], 404);
        }

        // Hapus avatar file jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return response()->json(['message' => 'Client berhasil dihapus']);
    }
}
