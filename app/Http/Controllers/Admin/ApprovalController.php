<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegisterRequest;
use App\Models\Member;
use App\Models\User;
use App\Mail\ApprovalNotification;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    public function index()
    {
        $requests = RegisterRequest::with('member')->latest()->get();
        $members  = Member::whereNull('user_id')->get();

        return view('admin.approvals.index', compact('requests', 'members'));
    }

public function approve(Request $request, $id)
{
    $registerRequest = RegisterRequest::findOrFail($id);

    $request->validate([
        'member_id' => 'required|exists:members,id',
    ]);

    // Ganti User::create dengan ini //PASTIKAN URUTANNYA INI BENARR!!TANYA CLAUDE
    $user = \App\Models\User::firstOrCreate(
        ['email' => $registerRequest->email],
        [
            'name'      => $registerRequest->name,
            'password'  => $registerRequest->password,
            'role'      => 'member',
            'is_active' => 1,
        ]
    );

    // Pastikan is_active = 1 kalau usernya sudah ada tapi nonaktif
    if (! $user->wasRecentlyCreated) {
        $user->update(['is_active' => 1]);
    }

    \App\Models\Member::where('id', $request->member_id)
        ->update(['user_id' => $user->id]);

    $registerRequest->update([
        'status'      => 'approved',
        'member_id'   => $request->member_id,
        'approved_by' => auth()->id(),
        'approved_at' => now(),
    ]);

    // Kirim email notifikasi approved
    Mail::to($registerRequest->email)->send(
        new ApprovalNotification('approved', $registerRequest->name)
    );

    return redirect()
        ->route('approvals.index')
        ->with('success', 'Registrasi berhasil disetujui dan email notifikasi telah dikirim.');
}

    public function reject($id)
    {
        $registerRequest = RegisterRequest::findOrFail($id);

        $registerRequest->update(['status' => 'rejected']);

        // Kirim email notifikasi rejected
        Mail::to($registerRequest->email)->send(
            new ApprovalNotification('rejected', $registerRequest->name)
        );

        return redirect()->back()->with('success', 'Request berhasil ditolak dan email notifikasi telah dikirim.');
    }
}