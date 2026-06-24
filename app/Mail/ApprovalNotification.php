<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $status;
    public string $userName;
    public ?string $reason;

    public function __construct(string $status, string $userName, ?string $reason = null)
    {
        $this->status   = $status;
        $this->userName = $userName;
        $this->reason   = $reason;
    }

    public function build(): self
    {
        $subject = $this->status === 'approved'
            ? 'Akun Anda Telah Disetujui'
            : 'Permintaan Registrasi Anda Ditolak';

        return $this->subject($subject)
                    ->view('emails.approval-notification');
    }
}