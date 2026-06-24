<?php
// app/Http/Controllers/PasswordResetController.php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // Halaman forgot password
    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->with(
            'success',
            'Jika email terdaftar, link reset password akan dikirim.'
        );
    }

    $token = Str::random(64);

    // Ganti update() pakai assignment langsung
    $user->reset_token            = $token;
    $user->reset_token_expires_at = now()->addMinutes(30);
    $user->save();

    $resetUrl = route('password.reset.form', [
        'token' => $token,
        'email' => $user->email,
    ]);

    Mail::to($user->email)->send(
        new ResetPasswordMail($resetUrl, $user->name)
    );

    return back()->with(
        'success',
        'Link reset password telah dikirim ke email kamu.'
    );
}

    // showReset — ambil token dari route parameter
    public function showReset(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Proses reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required',
            'password'              => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        // resetPassword — bandingkan langsung
        if (
            !$user ||
            !$user->reset_token ||
            $user->reset_token !== $request->token || // ← langsung compare, tidak Hash::check
            now()->isAfter($user->reset_token_expires_at)
        ) {
            return back()->with('error', 'Link reset password tidak valid atau sudah kadaluarsa.');
        }

        $user->update([
            'password'               => Hash::make($request->password),
            'reset_token'            => null,
            'reset_token_expires_at' => null,
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Password berhasil direset. Silakan login.');
    }
}