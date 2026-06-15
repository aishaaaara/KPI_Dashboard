<?php
namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $member = Member::with([
                'position',
                'team',
                'employmentType',
            ])
            ->where('user_id', auth()->id())
            ->first();

        return view('member.profile.index', compact('member'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'password'         => 'nullable|min:6|confirmed',
        ]);

        /** @var User $user */
        $user = auth()->user();

        $user->update(['name' => $request->name]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}