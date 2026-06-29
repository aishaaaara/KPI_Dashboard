<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Halaman riwayat notifikasi
    public function page(Request $request)
    {
        
        $notifications = Notification::forUser(auth()->id())
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    // Tandai satu sebagai dibaca (untuk keperluan lain)
    public function markAsRead(int $id)
    {
        Notification::forUser(auth()->id())
            ->findOrFail($id)
            ->update(['read_at' => now()]);

        return back();
    }

    // Tandai semua dibaca
    public function markAllAsRead()
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}