<?php

namespace App\Services;

use Scheb\YahooFinanceApi\ApiClientFactory;
use Scheb\YahooFinanceApi\ApiClient;
use Illuminate\Support\Facades\Log;

class YahooFinanceService
{
    private ?ApiClient $client = null;

    private function client(): ApiClient
    {
        if (!$this->client) {
            $this->client = ApiClientFactory::createApiClient(retries: 2, retryDelay: 1000);
        }

        return $this->client;
    }

    public function buscarCotacao(string $ticker): ?array
    {
        try {
            $quote = $this->client()->getQuote("{$ticker}.SA");
            return (array) $quote;
        } catch (\Exception $e) {
            Log::warning("Yahoo cotacao falhou para {$ticker}: " . $e->getMessage());
            return null;
        }
    }

    public function buscarDividendos(string $ticker, int $anos = 1): array
    {
        try {
            return $this->client()->getHistoricalDividendData(
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