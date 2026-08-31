<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\B3Ticker;

#[Signature('app:sincronizar-catalogo-b3')]
#[Description('Command description')]
class SincronizarCatalogoB3 extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 'app:sincronizar-catalogo-b3';
    // para sincronizar o catálogo b3 com o banco de dados por enquanto vou utilizar manual
    // para não gastar tantas requisições
    public function handle()
    {
        $page = 1;

        do {
            $response = Http::get('https://brapi.dev/api/v2/tickers', [
                'limit' => 2000,
                'page' => $page,
                'token' => config('services.brapi.token'),
            ]);

            $tickers = $response->json('results', []);

            foreach ($tickers as $item) {
                B3Ticker::updateOrCreate(
                    ['symbol' => $item['symbol']],
                    [
                        'name' => $item['name'] ?? null,
                        'long_name' => $item['longName'] ?? null,
                        'asset_type' => $item['assetType'] ?? null,
                        'sub_type' => $item['subType'] ?? null,
                        'sector' => $item['sector'] ?? null,
                        'subsector' => $item['subsector'] ?? null,
                        'is_active' => $item['isActive'] ?? true,
                        'logo_url' => $item['logoUrl'] ?? null,
                    ]
                );
            }

            $this->info("Página {$page} processada: " . count($tickers) . " tickers.");

            $hasNext = $response->json('pagination.hasNextPage', false);
            $page++;
        } while ($hasNext);

        $this->info('Catálogo B3 sincronizado com sucesso.');
    }
}
