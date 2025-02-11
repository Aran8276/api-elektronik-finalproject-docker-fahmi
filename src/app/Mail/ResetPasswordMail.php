<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $name;

    public function __construct($token, $name)
    {
        $this->token = $token;
        $this->name = $name;
    }

    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject('Reset Password Request')
                    ->view('emails.reset-password')
                    ->with([
                        'token' => $this->token,
                        'name' => $this->name
                    ]);
    }
}
