<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /*
    |--------------------------------------------------------------------------
    | MEMBER NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    // Dipanggil saat admin tambah periode baru di Communication
    public function notifyNewCommunicationPeriod(string $periodLabel): void
    {
        $this->notifyAllMembers(
            type   : 'communication',
            title  : 'Data Communication Baru',
            message: "Data communication periode {$periodLabel} telah ditambahkan.",
            url    : '/member/communication',
        );
    }

    // Dipanggil saat admin tambah periode baru di Story Point
    public function notifyNewStoryPointPeriod(string $periodLabel): void
    {
        $this->notifyAllMembers(
            type   : 'story_point',
            title  : 'Data Story Point Baru',
            message: "Data story point periode {$periodLabel} telah ditambahkan.",
            url    : '/member/story-points',
        );
    }

    // Dipanggil saat admin tambah periode baru di Workload
    public function notifyNewWorkloadPeriod(string $periodLabel): void
    {
        $this->notifyAllMembers(
            type   : 'workload',
            title  : 'Data Workload Baru',
            message: "Data workload periode {$periodLabel} telah ditambahkan.",
            url    : '/member/workload',
        );
    }

    // Dipanggil saat admin kirim performance insight ke member
    public function notifyPerformanceInsightSent(int $userId, string $periodLabel): void
    {
        $this->send(
            userId : $userId,
            type   : 'performance_insight',
            title  : 'Performance Insight Tersedia',
            message: "Performance insight periode {$periodLabel} telah dikirimkan oleh admin.",
            url    : '/member/performance-insight',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    // Dipanggil saat user baru mendaftar dan menunggu approval
    public function notifyNewApprovalRequest(string $userName): void
    {
        $this->notifyAllAdmins(
            type   : 'approval',
            title  : 'Permintaan Registrasi Baru',
            message: "{$userName} meminta persetujuan untuk bergabung sebagai member.",
            url    : '/admin/approvals',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CORE HELPERS
    |--------------------------------------------------------------------------
    */

    private function notifyAllMembers(string $type, string $title, string $message, ?string $url = null): void
    {
        $members = User::where('role', 'member')->pluck('id');

        foreach ($members as $userId) {
            $this->send($userId, $type, $title, $message, $url);
        }
    }

    private function notifyAllAdmins(string $type, string $title, string $message, ?string $url = null): void
    {
        $admins = User::where('role', 'admin')->pluck('id');

        foreach ($admins as $userId) {
            $this->send($userId, $type, $title, $message, $url);
        }
    }

    private function send(int $userId, string $type, string $title, string $message, ?string $url = null): void
    {
        Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
        ]);
    }
}