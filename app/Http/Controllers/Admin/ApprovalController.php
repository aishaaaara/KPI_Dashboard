<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegisterRequest;
use App\Models\Member;
use App\Models\User;

class ApprovalController extends Controller
{
    public function index()
    {
        $requests = RegisterRequest::with('member')
        ->latest()
        ->get();

        $members = Member::whereNull('user_id')->get();

        return view(
            'admin.approvals.index',
            compact(
                'requests',
                'members'
            )
        );
    }

    public function approve(Request $request, $id)
    {
        $registerRequest =
            RegisterRequest::findOrFail($id);

        $member =
            Member::findOrFail(
                $request->member_id
            );

        $user = User::create([

            'name' => $registerRequest->name,

            'email' => $registerRequest->email,

            'password' => $registerRequest->password,

            'role' => 'member'

        ]);

        $member->update([

            'user_id' => $user->id

        ]);

        $registerRequest->update([

            'status' => 'approved',

            'member_id' => $member->id,

            // 'approved_by' => auth()->id(), //diatangani nanti setelah login system selesai
            'approved_by' => 1,
            'approved_at' => now()

        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'User berhasil diapprove'
            );
    }

    public function reject($id)
    {
        $registerRequest =
            RegisterRequest::findOrFail($id);

        $registerRequest->update([

            'status' => 'rejected'

        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Request berhasil ditolak'
            );
    }
}