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
        'email'    => 'required|email|unique:register_requests,email',
        'password' => 'required|min:6|confirmed',
    ]);

    RegisterRequest::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'status'   => 'pending',
    ]);

    // Notifikasi ke semua admin
    app(NotificationService::class)->notifyNewApprovalRequest($request->name);

    return redirect()
        ->route('login')
        ->with('success', 'Permintaan registrasi berhasil dikirim. Silakan tunggu approval admin.');
}
}