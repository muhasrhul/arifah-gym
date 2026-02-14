<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendaftaranSuksesMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function build()
    {
        return $this->view('emails.pendaftaran_sukses')
                    ->subject('Selamat Datang di ARIFAH Gym - Bukti Pendaftaran');
    }
}