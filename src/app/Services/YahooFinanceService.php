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
            $this->client = ApiClientFactory::createApiClient(
                retries: 3,
                retryDelay: 1000,
                clientOptions: [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36',
                        'Accept-Language' => 'en-US,en;q=0.9',
                        'Accept-Encoding' => 'gzip, deflate, br',
                        'Connection' => 'keep-alive',
                    ],
                ],
            );
        }

        return $this->client;
    }

    public function buscarCotacao(string $ticker): ?array
    {
        try {
            $quote = $this->client()->getQuote("{$ticker}.SA");

            return [
                'symbol' => $quote->getSymbol(),
                'shortName' => $quote->getShortName(),
                'longName' => $quote->getLongName(),
                'currency' => $quote->getCurrency(),
                'regularMarketPrice' => $quote->getRegularMarketPrice(),
                'regularMarketChange' => $quote->getRegularMarketChange(),
                'regularMarketChangePercent' => $quote->getRegularMarketChangePercent(),
                'regularMarketDayHigh' => $quote->getRegularMarketDayHigh(),
                'regularMarketDayLow' => $quote->getRegularMarketDayLow(),
                'regularMarketVolume' => $quote->getRegularMarketVolume(),
                'regularMarketTime' => $quote->getRegularMarketTime()?->format('Y-m-d H:i:s'),
                'marketCap' => $quote->getMarketCap(),
            ];    

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