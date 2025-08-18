<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DeployCommand extends Command
{
    protected $signature = 'deploy:run {--branch=main}';
    protected $description = 'Deploy project from GitHub push webhook';

    public function handle()
    {
        $this->info('🚀 Starting deployment...');

        $basePath = base_path();
        $branch   = $this->option('branch');

        // Step 0: Mark repo as safe
        $this->runCmd("git config --global --add safe.directory {$basePath}", $basePath);

        // Step 1: Git operations
        $this->runCmd("git reset --hard", $basePath);
        $this->runCmd("git pull origin {$branch}", $basePath);

        // Step 2: Run composer install only if composer.lock changed
        $lastHashFile = storage_path('app/deploy/composer.lock.hash');
        $lockFile     = base_path('composer.lock');
        $currentHash  = File::exists($lockFile) ? md5_file($lockFile) : null;

        if (!File::exists($lastHashFile) || File::get($lastHashFile) !== $currentHash) {
            $this->info('🔄 composer.lock changed → running composer install...');
            $this->runCmd("composer install --no-interaction --prefer-dist --optimize-autoloader", $basePath);

            File::ensureDirectoryExists(dirname($lastHashFile));
            File::put($lastHashFile, $currentHash ?? '');
        } else {
            $this->info('✅ composer.lock unchanged → skipping composer install.');
        }

        // Step 3: Run Laravel optimization
        $this->runCmd("php artisan migrate --force", $basePath);
        $this->runCmd("php artisan config:clear", $basePath);
        $this->runCmd("php artisan cache:clear", $basePath);
        $this->runCmd("php artisan route:clear", $basePath);
        $this->runCmd("php artisan view:clear", $basePath);

        $this->info('🎉 Deployment finished successfully!');
        return Command::SUCCESS;
    }

    private function runCmd(string $command, string $workingDir)
    {
        $this->info("➡️  $command");
        Log::info("Running command: $command");

        $output = shell_exec("cd {$workingDir} && {$command} 2>&1");
        $this->line($output ?? 'No output');
    }
}
