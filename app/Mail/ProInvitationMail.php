<?php

namespace App\Mail;

use App\Models\ProAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ProAccount $proAccount) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mama Witch — Invitation Espace Pro',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pro-invitation',
        );
    }
}
