<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Nightly automatic database backup, in addition to the manual "Backup
// Now" button in Settings. Runs at 2 AM server time, when POS traffic is
// least likely -- --single-transaction in BackupService still makes this
// safe to run during business hours if needed, but off-peak is kinder to
// the database server's I/O.
Schedule::command('backup:run')->dailyAt('02:00');
