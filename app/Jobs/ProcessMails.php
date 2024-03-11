<?php

namespace App\Jobs;

use App\Clients\GmailClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class ProcessMails implements ShouldQueue
{
    protected const BATCH_MAILS_MAX = 5;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        // ...
    }

    public function handle(): void
    {
        $gmailClient = new GmailClient(
            username: config('spambot.username'),
            passcode: config('spambot.passcode')
        );

        $mails = $gmailClient->getEmails(self::BATCH_MAILS_MAX);
        if (empty($mails)) {
            return;
        }

        $jobs = [];
        foreach ($mails as $mail) {
            $jobs[] = new ProcessMail($mail);
        }

        Bus::batch($jobs)->dispatch();
    }
}
