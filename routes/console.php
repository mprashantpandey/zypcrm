<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('library:notify-expiring-subscriptions')->dailyAt('08:00');
Schedule::command('library:send-fee-reminders')->dailyAt('09:00');
