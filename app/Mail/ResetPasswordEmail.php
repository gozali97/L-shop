<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $emaildata;

    public function __construct($emaildata)
    {
        $this->emaildata = $emaildata;
    }

    public function build()
    {
        $token = $this->emaildata['token'];

        return $this->subject('Reset Password')
            ->view('email.email-resetpass', compact('token'));
    }

}
