<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:atualizar-cotacoes')->dailyAt('10:10');

// desativado porem no futuro vou colocar talvez todo dia 1
// Schedule::command('app:sincronizar-catalogo-b3')->cron('0 12 1 * *');
