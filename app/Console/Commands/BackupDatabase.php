<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--compress : Compress the backup file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        // Get database configuration
        $connection = DB::connection()->getConfig();
        $database = $connection['database'];
        $username = $connection['username'];
        $password = $connection['password'];
        $host = $connection['host'];
        $port = $connection['port'] ?? 3306;

        // Create backups directory if it doesn't exist
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
            $this->info("Created backup directory: {$backupDir}");
        }

        // Generate backup filename with timestamp
        $timestamp = Carbon::now()->format('Y-m-d_His');
        $filename = "backup_{$database}_{$timestamp}.sql";
        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        // Find mysqldump executable
        $mysqldump = $this->findMysqldump();
        if (!$mysqldump) {
            $this->error('mysqldump not found! Please ensure MySQL is installed and mysqldump is in your PATH.');
            $this->info('For WAMP, mysqldump is usually at: C:\\wamp64\\bin\\mysql\\mysql8.x.x\\bin\\mysqldump.exe');
            return Command::FAILURE;
        }

        // Build mysqldump command
        // Use proper path separators for Windows
        $filepathEscaped = str_replace('/', DIRECTORY_SEPARATOR, $filepath);
        
        if (PHP_OS_FAMILY === 'Windows') {
            // Windows command
            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s --password=%s %s > "%s"',
                $mysqldump,
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                $filepathEscaped
            );
        } else {
            // Unix/Linux/Mac command
            $command = sprintf(
                '%s --host=%s --port=%s --user=%s --password=%s %s > %s',
                escapeshellarg($mysqldump),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($filepathEscaped)
            );
        }

        // Execute backup command
        $this->info("Backing up database: {$database}");
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Database backup failed!');
            $this->error('Make sure mysqldump is installed and accessible.');
            return Command::FAILURE;
        }

        // Check if file was created and has content
        if (!file_exists($filepath) || filesize($filepath) === 0) {
            $this->error('Backup file was not created or is empty!');
            return Command::FAILURE;
        }

        $fileSize = $this->formatBytes(filesize($filepath));
        $this->info("Backup created successfully: {$filename} ({$fileSize})");

        // Compress if requested
        if ($this->option('compress')) {
            $compressedFile = $filepath . '.gz';
            $gz = gzopen($compressedFile, 'w9');
            gzwrite($gz, file_get_contents($filepath));
            gzclose($gz);
            
            // Delete original file
            unlink($filepath);
            
            $compressedSize = $this->formatBytes(filesize($compressedFile));
            $this->info("Backup compressed: {$filename}.gz ({$compressedSize})");
        }

        // Clean old backups (keep last 7 days)
        $this->cleanOldBackups($backupDir);

        $this->info('Database backup completed successfully!');
        return Command::SUCCESS;
    }

    /**
     * Find mysqldump executable path
     */
    private function findMysqldump()
    {
        // First, try to find mysqldump in PATH
        $mysqldump = null;
        
        if (PHP_OS_FAMILY === 'Windows') {
            // Try common WAMP/XAMPP paths
            $wampPaths = [
                'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
                'C:\\wamp64\\bin\\mysql\\mysql8.0.30\\bin\\mysqldump.exe',
                'C:\\wamp64\\bin\\mysql\\mysql8.0.29\\bin\\mysqldump.exe',
                'C:\\wamp64\\bin\\mysql\\mysql8.0.28\\bin\\mysqldump.exe',
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            ];
            
            // Check common WAMP paths
            foreach ($wampPaths as $path) {
                if (file_exists($path)) {
                    return $path;
                }
            }
            
            // Try to find in wamp64 directory
            $wampBase = 'C:\\wamp64\\bin\\mysql';
            if (is_dir($wampBase)) {
                $mysqlDirs = glob($wampBase . '\\mysql*', GLOB_ONLYDIR);
                foreach ($mysqlDirs as $mysqlDir) {
                    $mysqldumpPath = $mysqlDir . '\\bin\\mysqldump.exe';
                    if (file_exists($mysqldumpPath)) {
                        return $mysqldumpPath;
                    }
                }
            }
            
            // Try mysqldump command (if in PATH)
            exec('where mysqldump 2>nul', $output, $returnVar);
            if ($returnVar === 0 && !empty($output[0])) {
                return trim($output[0]);
            }
        } else {
            // Unix/Linux/Mac - try which command
            exec('which mysqldump', $output, $returnVar);
            if ($returnVar === 0 && !empty($output[0])) {
                return trim($output[0]);
            }
        }
        
        return null;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Clean old backup files (older than 7 days)
     */
    private function cleanOldBackups($backupDir)
    {
        $files = glob($backupDir . '/backup_*.sql*');
        $deletedCount = 0;
        $daysToKeep = 7;

        foreach ($files as $file) {
            if (filemtime($file) < strtotime("-{$daysToKeep} days")) {
                unlink($file);
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("Cleaned {$deletedCount} old backup file(s) (older than {$daysToKeep} days)");
        }
    }
}
