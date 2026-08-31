<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\StockMarket;
use App\Models\StockSyncLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AtualizarCotacoes extends Command
{
    protected $signature = 'app:atualizar-cotacoes';
    protected $description = 'Busca cotações atualizadas da Brapi para as ações cadastradas';

    public function handle()
    {
        $tickers = Stock::query()->distinct()->pluck('ticker');

        $this->info("Sincronizando {$tickers->count()} ações...");

        foreach ($tickers as $ticker) {
            $response = Http::get("https://brapi.dev/api/quote/{$ticker}", [
                'token' => config('services.brapi.token'),
            ]);

            

            $stock = Stock::where('ticker', $ticker)->first();
            $result = $response->json('results.0');

            if ($response->successful() && $result && isset($result['symbol'])) {
                $data = $result;

                StockMarket::create([
                    'stock_id' => $stock->id,
                    'short_name' => $data['shortName'] ?? null,
                    'long_name' => $data['longName'] ?? null,
                    'currency' => $data['currency'] ?? null,
                    'regular_market_price' => $data['regularMarketPrice'] ?? null,
                    'regular_market_day_high' => $data['regularMarketDayHigh'] ?? null,
                    'regular_market_day_low' => $data['regularMarketDayLow'] ?? null,
                    'regular_market_day_range' => $data['regularMarketDayRange'] ?? null,
                    'regular_market_change' => $data['regularMarketChange'] ?? null,
                    'regular_market_change_percent' => $data['regularMarketChangePercent'] ?? null,
                    'regular_market_time' => $data['regularMarketTime'] ?? null,
                    'market_cap' => $data['marketCap'] ?? null,
                    'regular_market_volume' => $data['regularMarketVolume'] ?? null,
                    'regular_market_previous_close' => $data['regularMarketPreviousClose'] ?? null,
                    'regular_market_open' => $data['regularMarketOpen'] ?? null,
                    'fifty_two_week_range' => $data['fiftyTwoWeekRange'] ?? null,
                    'fifty_two_week_low' => $data['fiftyTwoWeekLow'] ?? null,
                    'fifty_two_week_high' => $data['fiftyTwoWeekHigh'] ?? null,
                    'logo_url' => $data['logourl'] ?? null,
                ]);

                StockSyncLog::create([
                    'ticker' => $ticker,
                    'status' => 'success',
                    'status_code' => $response->status(),
                    'response' => $result,
                ]);

                $this->info("{$ticker}: R$ {$data['regularMarketPrice']}");
            } else {
                StockSyncLog::create([
                    'ticker' => $ticker,
                    'status' => 'error',
                    'response' => $response->json(),
                    'error_message' => $response->status() . ' - ' . $response->body(),
                ]);

                $this->error("{$ticker}: falhou");
            }
        }
    }
}