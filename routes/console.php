<?php

use Illuminate\Foundation\Inspiring;

// Here you may define all of your Closure based console commands.

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');
