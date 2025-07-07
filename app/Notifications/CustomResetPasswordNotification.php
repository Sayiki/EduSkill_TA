<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Kita hanya perlu extend class Notification dasar dan implementasi ShouldQueue
class CustomResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Token untuk reset password.
     * @var string
     */
    public $token;

    /**
     * Buat instance notifikasi baru.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Tentukan channel pengiriman notifikasi (hanya email).
     * @return array
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Buat representasi email dari notifikasi.
     * Metode ini adalah satu-satunya yang kita perlukan.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // 1. Buat URL yang benar untuk frontend Anda
        $url = config('app.frontend_url')
            . "/reset-password?token={$this->token}&email="
            . urlencode($notifiable->getEmailForPasswordReset());

        // 2. Buat dan kembalikan pesan email
        return (new MailMessage)
                    ->subject('Notifikasi Reset Password - EduSkill')
                    ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
                    ->action('Reset Password', $url)
                    ->line('Tautan reset password ini akan kedaluwarsa dalam 60 menit.')
                    ->line('Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.');
    }
}