<?php

namespace App\Console\Commands;

use App\Mail\WelcomeMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MailSendCommand extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Command description';

    public function handle(): void
    {
        Mail::to(config('spambot.username'))->send(new WelcomeMail('Test 1', 'Hallo there!'));
    }
}
