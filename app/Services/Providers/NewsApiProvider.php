<?php


namespace App\Services\Providers;

use App\Contracts\Services\NewsProvider;
use App\DTO\ArticleDTO;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiProvider implements NewsProvider
{
    private ?Source $source = null;

    public function key(): string
    {
        return 'newsapi';
    }

    /**
     * get source from database 
     */
    private function getSource(): Source
    {
        if (!$this->source) {
            $this->source = Source::where('key', $this->key())->firstOrFail();
        }
        return $this->source;
    }

    public function fetchRecent(array $opts = []): Collection
    {
        $source = $this->getSource();
        $from = $opts['from'] ?? now()->subHours(24)->toIso8601String();
        $to = $opts['to'] ?? now()->toIso8601String();
        $pageSize = $opts['page_size'] ?? 50;

        try {
            $response = Http::timeout(10)
                ->retry(2, 200)
                ->get($source->base_url . '/everything', [
                    'language' => 'en',
                    'q' => $opts['q'] ?? 'technology OR business OR world OR sports',
                    'from' => $from,
                    'to' => $to,
                    'sortBy' => 'publishedAt',
                    'pageSize' => $pageSize,
                    'page' => 1,
                    'apiKey' => config('news.newsapi.key'),
                ])
                ->throw()
                ->json();

            $items = data_get($response, 'articles', []);

            return collect($items)->map(function ($item) {
                return new ArticleDTO(
                    title: $item['title'] ?? 'Untitled',
                    summary: $item['description'],
                    content: $item['content'],
                    url: $item['url'],
                    imageUrl: $item['urlToImage'],
                    author: $item['author'],
                    providerCategory: data_get($item, 'source.name'), // newsapi does not provide category directly
                    language: 'en',
                    publishedAt: Carbon::parse($item['publishedAt']),
                    externalId: null,
                    raw: $item
                );
            })->filter(fn($dto) => !empty($dto->url)); // filter out removed articles if the url not exists

        } catch (\Exception $e) {
            Log::error('NewsAPI fetch failed', [
                'provider' => 'newsapi',
                'error' => $e->getMessage(),
            ]);
            return collect([]);
        }
    }
}
