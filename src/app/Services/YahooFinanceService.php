<?php

namespace App\Services;

use Scheb\YahooFinanceApi\ApiClientFactory;
use Illuminate\Support\Facades\Log;

class YahooFinanceService
{
    public function buscarCotacao(string $ticker): ?array
    {
        $client = ApiClientFactory::createApiClient(retries: 2, retryDelay: 1000);

        try {
            $quote = $client->getQuote("{$ticker}.SA");
            return (array) $quote;
        } catch (\Exception $e) {
            Log::warning("Yahoo cotacao falhou para {$ticker}: " . $e->getMessage());
            return null;
        }
    }

    public function buscarDividendos(string $ticker, int $anos = 1): array
    {
        $client = ApiClientFactory::createApiClient(retries: 2, retryDelay: 1000);

        try {
            return $client->getHistoricalDividendData(
                "{$ticker}.SA",
                new \DateTime("-{$anos} years"),
                new \DateTime("today")
            );
        } catch (\Exception $e) {
            Log::warning("Yahoo dividendos falhou para {$ticker}: " . $e->getMessage());
            return [];
        }
    }
}