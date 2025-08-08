<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     * กำหนดตารางเวลาของคำสั่งของแอปพลิเคชัน
     */
    protected function schedule(Schedule $schedule): void
    {
        // รันคำสั่งตรวจสอบสต็อกและวันหมดอายุทุกวันตอนตี 1
        $schedule->command('inventory:check-stock-alerts')->dailyAt('16:50');
        // หรือจะรันทุกชั่วโมงก็ได้
        // $schedule->command('inventory:check-stock-alerts')->hourly();
    }

    /**
     * Register the commands for the application.
     * ลงทะเบียนคำสั่งสำหรับแอปพลิเคชัน
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
