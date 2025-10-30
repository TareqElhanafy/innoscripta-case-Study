<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\IngestSourceJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new IngestSourceJob('guardian'))
    ->everyThirtySeconds()
    ->withoutOverlapping()
    ->onOneServer()
    ->name('ingest-guardian');



Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
