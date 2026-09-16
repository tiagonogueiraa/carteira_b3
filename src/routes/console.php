<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\YahooFinanceService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cotação principal (Brapi) — continua 1x ao dia
Schedule::command('app:atualizar-cotacoes')->dailyAt('10:10');

// Cotação experimental via Yahoo — de 30 em 30 min, só durante o pregão (seg-sex, 10h-17h)
Schedule::command('app:atualizar-cotacoes-yahoo')
    ->everyThirtyMinutes()
    ->between('10:00', '17:00')
    ->weekdays();

// Dividendos — dado histórico, roda 1x por dia, fora do horário de pico (evita concorrer com as cotações)
Schedule::command('app:sincronizar-dividendos')
    ->dailyAt('08:00')
    ->weekdays();

// desativado porem no futuro vou colocar talvez todo dia 1
// Schedule::command('app:sincronizar-catalogo-b3')->cron('0 12 1 * *');
