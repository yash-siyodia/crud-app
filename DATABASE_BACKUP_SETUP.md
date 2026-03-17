# Database Backup Setup Guide

## Overview
This Laravel application includes an automated database backup system that creates daily backups of your MySQL database.

## Features
- ✅ Automatic daily backups at 2:00 AM
- ✅ Compressed backup files (.sql.gz)
- ✅ Automatic cleanup of backups older than 7 days
- ✅ Manual backup command available
- ✅ Backup logs stored in `storage/logs/backup.log`

## Manual Backup

You can create a backup manually at any time using:

```bash
php artisan db:backup
```

To create a compressed backup:

```bash
php artisan db:backup --compress
```

## Automatic Backup Setup

### For Windows (WAMP/XAMPP)

Since you're using WAMP on Windows, you have two options:

#### Option 1: Windows Task Scheduler (Recommended for Windows)

1. Open **Task Scheduler** (search for it in Windows Start menu)
2. Click **Create Basic Task**
3. Name it: "Laravel Database Backup"
4. Set trigger: **Daily** at **2:00 AM**
5. Set action: **Start a program**
6. Program/script: `C:\wamp64\bin\php\php8.x.x\php.exe` (replace with your PHP version)
7. Add arguments: `C:\wamp64\www\crud-app\artisan schedule:run`
8. Start in: `C:\wamp64\www\crud-app`
9. Click **Finish**

#### Option 2: Laravel Scheduler (Requires cron-like setup)

For Windows, you can use a package like `laravel-schedule-listener` or set up a Windows service.

### For Linux/Unix/Mac

Add this line to your crontab:

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

To edit crontab:
```bash
crontab -e
```

## Backup Location

Backups are stored in: `storage/app/backups/`

Example backup filename: `backup_database_name_2024-01-15_020000.sql.gz`

## Backup Retention

- Backups are automatically deleted after **7 days**
- You can modify this in `app/Console/Commands/BackupDatabase.php` (change `$daysToKeep` variable)

## Backup Schedule

Current schedule: **Daily at 2:00 AM**

To change the schedule, edit `app/Console/Kernel.php`:

```php
// Examples:
$schedule->command('db:backup --compress')->daily();           // Daily at midnight
$schedule->command('db:backup --compress')->dailyAt('03:00');  // Daily at 3 AM
$schedule->command('db:backup --compress')->hourly();          // Every hour
$schedule->command('db:backup --compress')->twiceDaily();       // Twice daily (1 AM & 13 PM)
```

## Requirements

- MySQL `mysqldump` command must be available in your system PATH
- For WAMP: Usually located at `C:\wamp64\bin\mysql\mysql8.x.x\bin\mysqldump.exe`
- Ensure PHP has permission to write to `storage/app/backups/`

## Troubleshooting

### Backup fails with "mysqldump not found"

1. Add MySQL bin directory to your system PATH:
   - Windows: Add `C:\wamp64\bin\mysql\mysql8.x.x\bin` to PATH
   - Or specify full path in the command

### Permission errors

Ensure the `storage/app/backups` directory has write permissions:
```bash
chmod -R 755 storage/app/backups
```

### Check backup logs

View backup execution logs:
```bash
tail -f storage/logs/backup.log
```

## Restoring a Backup

To restore a backup:

```bash
mysql -u username -p database_name < storage/app/backups/backup_file.sql
```

Or for compressed backups:
```bash
gunzip < storage/app/backups/backup_file.sql.gz | mysql -u username -p database_name
```
