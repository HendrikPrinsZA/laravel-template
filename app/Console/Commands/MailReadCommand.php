<?php

namespace App\Console\Commands;

use App\Jobs\ProcessMails;
use Illuminate\Console\Command;

class MailReadCommand extends Command
{
    protected $signature = 'mail:read';

    protected $description = 'Command description';

    public function handle(): int
    {
        ProcessMails::dispatch();

        return self::SUCCESS;
    }
}
