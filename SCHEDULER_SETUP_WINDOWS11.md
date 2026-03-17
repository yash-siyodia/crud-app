# Windows 11 Task Scheduler Setup Guide

## Step 1: Find Task Scheduler

Windows 11 **definitely has Task Scheduler**. Try these methods:

### Method A: Via Run Dialog (Fastest)
1. Press **`Windows + R`** keys together
2. Type: `taskschd.msc`
3. Press **Enter**

### Method B: Via Start Menu
1. Press **`Windows`** key
2. Type: `Task Scheduler`
3. Click on **"Task Scheduler"** app (not "Task Scheduler Library")

### Method C: Via Settings
1. Press **`Windows + I`** (Settings)
2. Click **"System"** → **"About"**
3. Search for "Task Scheduler" in the Settings search bar

## Step 2: If Task Scheduler Still Doesn't Open

If `taskschd.msc` gives an error, Task Scheduler might be disabled or corrupted. Try:

### Enable Task Scheduler Service
1. Press **`Windows + R`**
2. Type: `services.msc` and press Enter
3. Find **"Task Scheduler"** service
4. Right-click → **Properties**
5. Set **Startup type** to **"Automatic"**
6. Click **"Start"** if it's stopped
7. Click **OK**

Then try opening Task Scheduler again.

## Step 3: Quick Setup Using Batch File

I've created a batch file (`run-scheduler.bat`) for easier setup:

1. **Update PHP path** in `run-scheduler.bat`:
   - Open `run-scheduler.bat` in a text editor
   - Change `php8.2.0` to your actual PHP version
   - Check your PHP version at: `C:\wamp64\bin\php\`

2. **Create Task in Task Scheduler**:
   - Program/script: `C:\wamp64\www\crud-app\run-scheduler.bat`
   - Start in: `C:\wamp64\www\crud-app`
   - Trigger: Repeat every 1 minute, indefinitely

## Alternative: Using PowerShell Script

If Task Scheduler doesn't work, we can create a PowerShell script that runs in the background.

## Need Help?

Let me know:
- What happens when you type `taskschd.msc` in Run dialog?
- Do you see any error messages?
