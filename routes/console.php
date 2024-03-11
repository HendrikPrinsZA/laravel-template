<?php

use App\Console\Commands\MailReadCommand;
use Illuminate\Support\Facades\Schedule;

if (config('spambot.scheduled.enabled')) {
    Schedule::command(MailReadCommand::class)->everyFiveMinutes();
}
