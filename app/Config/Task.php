<?php

namespace Config;

use CodeIgniter\Tasks\Config\Tasks as BaseTasks;
use CodeIgniter\Tasks\Scheduler;

class Tasks extends BaseTasks
{
    /**
     * Register any tasks within this method for the application.
     *
     * @param Scheduler $schedule
     */
    public function init(Scheduler $schedule)
    {
        $schedule->call(function () {
            normalize();
        })->everyMinute(10);
        $schedule->call(function () {
            normalize2();
        })->everyMinute(1);
    }
}
