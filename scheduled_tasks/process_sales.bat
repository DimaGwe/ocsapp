@echo off
REM Sales Scheduler - Processes scheduled sales
REM This script should be run hourly via Windows Task Scheduler

echo [%date% %time%] Processing scheduled sales...

REM Make HTTP request to process scheduled sales
curl -s "http://localhost/ocsapp/public/admin/sales/process-scheduled" >> C:\xampp\htdocs\ocsapp\storage\logs\sales_cron.log 2>&1

echo [%date% %time%] Scheduled sales processed successfully.
