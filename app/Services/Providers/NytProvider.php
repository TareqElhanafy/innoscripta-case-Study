<?php

namespace App\Services\Providers;

use App\Contracts\Services\NewsProvider;
use App\DTO\ArticleDTO;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NytProvider implements NewsProvider
{
    private ?Source $source = null;

    public function key(): string
    {
        return 'nyt';
    }

    /**
     * Get source from database
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

        // theye use YYYYMMDD format
        $from = $opts['from'] ?? now()->subHours(24)->format('Ymd');
        $to = $opts['to'] ?? now()->format('Ymd');

        try {
            $response = Http::timeout(10)
                ->retry(2, 200)
                ->get($source->base_url . '/svc/search/v2/articlesearch.json', [
                    'begin_date' => $from,
                    'end_date' => $to,
                    'sort' => 'newest',
                    'api-key' => config('news.nyt.key'),
                ])
                ->throw()
                ->json();

            $items = data_get($response, 'response.docs', []);

            return collect($items)->map(function ($item) {
                // get image URL from multimedia
                $imageUrl = null;
                $multimedia = $item['multimedia'] ?? [];
                if (!empty($multimedia)) {
                    $image = collect($multimedia)
                        ->sortByDesc('width')
                        ->first();
                    if ($image) {
                        $imageUrl = 'https://static01.nyt.com/' . $image['url'];
                    }
                }

                // gett author from byline
                $author = data_get($item, 'byline.original');

                return new ArticleDTO(
                    title: data_get($item, 'headline.main', 'Untitled'),
                    summary: $item['abstract'] ?? null,
                    content: $item['lead_paragraph'] ?? null, // they don't procide full text in their free paln
                    url: $item['web_url'],
                    imageUrl: $imageUrl,
                    author: $author,
                    providerCategory: $item['section_name'] ?? $item['news_desk'] ?? null,
                    language: 'en',
                    publishedAt: Carbon::parse($item['pub_date']),
                    externalId: $item['_id'],
                    raw: $item
                );
            });
        } catch (\Exception $e) {
            Log::error('NYT API fetch failed', [
                'provider' => 'nyt',
                'error' => $e->getMessage(),
            ]);
            return collect([]);
        }
    }
}
