<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Weekly Financial Reports
Illuminate\Support\Facades\Schedule::command('report:weekly-financial')->weeklyOn(0, '8:00');
