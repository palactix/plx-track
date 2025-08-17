<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeployCommand extends Command
{
    protected $signature = 'deploy:run {--hash=}';
    protected $description = 'Deploy project from GitHub push webhook';

    public function handle()
    {
        $this->info('🚀 Starting deployment...');

        // Step 1: Git operations
        $this->runCmd('git reset --hard');
        $this->runCmd('git pull origin main');

        // Step 2: Run composer install only if composer.lock changed
        $lastHashFile = storage_path('app/deploy/composer.lock.hash');
        $currentHash  = md5_file(base_path('composer.lock'));

        if (!File::exists($lastHashFile) || File::get($lastHashFile) !== $currentHash) {
            $this->info('🔄 composer.lock changed → running composer install...');
            $this->runCmd('composer install --no-interaction --prefer-dist --optimize-autoloader');

            File::ensureDirectoryExists(dirname($lastHashFile));
            File::put($lastHashFile, $currentHash);
        } else {
            $this->info('✅ composer.lock unchanged → skipping composer install.');
        }

        // Step 3: Run Laravel optimization
        $this->runCmd('php artisan migrate --force');
        $this->runCmd('php artisan config:clear');
        $this->runCmd('php artisan cache:clear');
        $this->runCmd('php artisan route:clear');
        $this->runCmd('php artisan view:clear');

        $this->info('🎉 Deployment finished successfully!');
        return Command::SUCCESS;
    }

    private function runCmd($command)
    {
        $this->info("➡️  $command");
        $output = shell_exec($command . ' 2>&1');
        $this->line($output);
    }
}
