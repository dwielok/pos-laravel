<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

/**
 * `php artisan backup:run` -- intended to be scheduled (see
 * routes/console.php or app/Console/Kernel.php in older Laravel versions;
 * in Laravel 11+, the schedule() closure lives in routes/console.php) for
 * an automatic nightly backup, in addition to the manual "Backup Now"
 * button in the Settings UI which calls BackupService directly.
 */
class BackupDatabase extends Command
{
    protected $signature = 'backup:run';
    protected $description = 'Create a full database backup';

    public function handle(BackupService $backupService): int
    {
        $this->info('Starting database backup...');

        try {
            $filename = $backupService->create();
            $this->info("Backup created: {$filename}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Backup failed: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
