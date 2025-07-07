<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class TestMailCommand extends Command
{
    protected $signature = 'test:mail {email}';
    protected $description = 'Test mail configuration';

    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            Mail::raw('This is a test email from Laravel', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email');
            });
            
            $this->info('Test email sent successfully to: ' . $email);
            
        } catch (\Exception $e) {
            $this->error('Failed to send test email: ' . $e->getMessage());
        }
    }
}