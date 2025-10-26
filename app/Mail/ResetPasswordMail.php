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

    public $url; // accessible dans la vue

    // ✅ On reçoit $url ici
    public function __construct($url)
    {
        $this->url = $url;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Réinitialisation du mot de passe',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.reset_password',
            with: ['url' => $this->url] // passe $url à la vue
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
