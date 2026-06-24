# Windows Task Scheduler Setup for Automated Sales

This guide will help you set up Windows Task Scheduler to automatically process scheduled sales (start and end sales based on their scheduled dates).

## What This Does

The scheduled task runs every hour and:
- **Starts sales** that have reached their start date/time
- **Ends sales** that have reached their end date/time
- **Logs results** to `storage/logs/sales_cron.log`

## Setup Steps

### Step 1: Open Task Scheduler

1. Press `Win + R` to open Run dialog
2. Type `taskschd.msc` and press Enter
3. Task Scheduler window will open

### Step 2: Create a New Task

1. In the right panel, click **"Create Basic Task..."**
2. Name: `OCSAPP Sales Processor`
3. Description: `Automatically processes scheduled sales (start/end dates)`
4. Click **Next**

### Step 3: Set Trigger (When to Run)

1. Select **"Daily"**
2. Click **Next**
3. Set start date to today
4. Set start time to `00:00:00` (midnight)
5. Click **Next**

### Step 4: Set Action (What to Run)

1. Select **"Start a program"**
2. Click **Next**
3. In "Program/script" field, enter:
   ```
   C:\xampp\htdocs\ocsapp\scheduled_tasks\process_sales.bat
   ```
4. Leave "Add arguments" and "Start in" blank
5. Click **Next**

### Step 5: Review and Finish

1. Check **"Open the Properties dialog for this task when I click Finish"**
2. Click **Finish**

### Step 6: Configure Advanced Settings

The Properties dialog will open automatically. Configure these settings:

#### General Tab:
- Check **"Run whether user is logged on or not"**
- Check **"Run with highest privileges"**

#### Triggers Tab:
1. Double-click the trigger you created
2. Check **"Repeat task every:"** and set to **1 hour**
3. Set **"for a duration of:"** to **Indefinitely**
4. Click **OK**

#### Conditions Tab:
- Uncheck **"Start the task only if the computer is on AC power"**
- Uncheck **"Stop if the computer switches to battery power"**

#### Settings Tab:
- Check **"Allow task to be run on demand"**
- Check **"Run task as soon as possible after a scheduled start is missed"**
- Select **"If the task is already running, then the following rule applies:"** → **"Do not start a new instance"**

Click **OK** to save all settings.

### Step 7: Test the Task

1. In Task Scheduler Library, find your task **"OCSAPP Sales Processor"**
2. Right-click it and select **"Run"**
3. Check the log file at: `C:\xampp\htdocs\ocsapp\storage\logs\sales_cron.log`
4. You should see an entry like:
   ```
   {"success":true,"started":0,"ended":0,"timestamp":"2026-01-07 12:00:00"}
   ```

## Verification

### Check if it's running:
1. Open Task Scheduler
2. Find your task in Task Scheduler Library
3. Check the **"Last Run Time"** column
4. Check the **"Last Run Result"** column (should be `0x0` for success)

### Check the logs:
```batch
type C:\xampp\htdocs\ocsapp\storage\logs\sales_cron.log
```

### Manual test via browser:
Visit: `http://localhost/ocsapp/public/admin/sales/process-scheduled`

You should see JSON output showing how many sales were started/ended.

## Troubleshooting

### Task doesn't run automatically
- Make sure XAMPP Apache is running
- Check that Windows Task Scheduler service is running
- Verify the batch file path is correct
- Check Windows Event Viewer for task scheduler errors

### "Access Denied" error
- Right-click the task → Properties → General tab
- Make sure **"Run with highest privileges"** is checked
- Make sure you're running the task as an Administrator account

### No sales are being processed
- Check if you have any scheduled sales in the database

### Log file not being created
- Make sure the directory exists: `C:\xampp\htdocs\ocsapp\storage\logs\`
- Make sure the web server has write permissions to that directory
- Check if `curl` command is available (it should be in Windows 10/11)

## Production Deployment

For production servers, consider:

1. **Linux/Apache**: Use crontab instead
2. **Security**: Protect the endpoint with IP whitelist or token
3. **Monitoring**: Set up alerts if the cron fails
4. **Logging**: Rotate log files to prevent them from growing too large
