<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MigrateS3ToLocal extends Command
{
    protected $signature = 'storage:migrate-s3-to-local
                            {--dry-run : List files without downloading}
                            {--force : Overwrite existing local files}';

    protected $description = 'Migrate all files from S3/R2 to local public disk';

    private int $success = 0;
    private int $skipped = 0;
    private int $failed  = 0;
    private array $errors = [];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force  = $this->option('force');

        $this->info('=======================================================');
        $this->info(' S3/R2 → Local Disk Migration');
        $this->info('=======================================================');

        if ($dryRun) {
            $this->warn(' DRY RUN — no files will be downloaded');
        }

        // Step 1: List all files
        $this->info('');
        $this->info('→ Fetching file list from S3/R2...');

        try {
            $files = Storage::disk('s3')->allFiles('/');
        } catch (Throwable $e) {
            $this->error('Failed to list S3 files: ' . $e->getMessage());
            return self::FAILURE;
        }

        $total = count($files);
        $this->info("  Found {$total} files");

        if ($total === 0) {
            $this->warn('No files found in S3. Exiting.');
            return self::SUCCESS;
        }

        $this->info('');

        // Step 2: Process each file
        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->start();

        foreach ($files as $file) {
            $bar->setMessage($file);

            if ($dryRun) {
                $this->line('');
                $this->line("  [DRY RUN] Would download: {$file}");
                $this->skipped++;
                $bar->advance();
                continue;
            }

            // Skip if already exists locally and not forcing
            if (!$force && Storage::disk('public')->exists($file)) {
                $bar->advance();
                $this->skipped++;
                continue;
            }

            try {
                $contents = Storage::disk('s3')->get($file);

                if ($contents === null) {
                    throw new \RuntimeException('Empty response from S3');
                }

                Storage::disk('public')->put($file, $contents);
                $this->success++;

            } catch (Throwable $e) {
                $this->failed++;
                $this->errors[] = ['file' => $file, 'error' => $e->getMessage()];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info('');

        // Step 3: Summary
        $this->info('=======================================================');
        $this->info(' Migration Summary');
        $this->info('=======================================================');
        $this->info("  Total files   : {$total}");
        $this->info("  ✅ Downloaded : {$this->success}");
        $this->info("  ⏭  Skipped   : {$this->skipped}");
        $this->info("  ❌ Failed     : {$this->failed}");

        if (!empty($this->errors)) {
            $this->info('');
            $this->warn(' Failed files:');
            foreach ($this->errors as $err) {
                $this->error("  • {$err['file']}");
                $this->error("    Reason: {$err['error']}");
            }
        }

        $this->info('');

        if ($this->failed > 0) {
            $this->warn('Migration completed with errors. Re-run with --force to retry failed files.');
            return self::FAILURE;
        }

        $this->info('✅ All files migrated successfully!');
        $this->info('');
        $this->info('Next steps:');
        $this->info('  1. Update .env: FILESYSTEM_DISK=public');
        $this->info('  2. Update .env: MEDIA_DISK=public');
        $this->info('  3. Run: php artisan config:cache');
        $this->info('  4. Run: php artisan storage:link');
        $this->info('');

        return self::SUCCESS;
    }
}
