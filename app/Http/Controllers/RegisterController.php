<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\RegisterRequest;
use App\Services\NotificationService;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    $exists = RegisterRequest::where('email', $value)
                        ->whereIn('status', ['pending', 'approved'])
                        ->exists();

                    if ($exists) {
                        $fail('Email ini sudah terdaftar atau sedang menunggu persetujuan.');
                    }
                },
            ],
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Kalau email pernah rejected, update datanya
        $existing = RegisterRequest::where('email', $request->email)->first();

        if ($existing) {
            $existing->update([
                'name'     => $request->name,
                'password' => Hash::make($request->password),
                'status'   => 'pending',
            ]);
        } else {
            RegisterRequest::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'status'   => 'pending',
            ]);
        }

        app(NotificationService::class)->notifyNewApprovalRequest($request->name);

        return redirect()
            ->route('login')
            ->with('success', 'Permintaan registrasi berhasil dikirim. Silakan tunggu approval admin.');
    }
}