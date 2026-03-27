<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProAccessRequest extends Notification
{
    use Queueable;

    public function __construct(protected array $data) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle demande d\'accès Pro — Mama Witch')
            ->greeting('Nouvelle demande pro')
            ->line("{$this->data['first_name']} {$this->data['last_name']} ({$this->data['structure']}) a demandé un accès à l'Espace Pro.")
            ->line("Email : {$this->data['email']}")
            ->action('Gérer les comptes pro', url('/admin/pro-accounts'))
            ->line('Connectez-vous au back-office pour valider ou refuser la demande.');
    }
}
