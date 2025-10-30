<?php

namespace App\Services\Providers;

use App\DTO\ArticleDTO;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Contracts\Services\NewsProvider;

class GuardianProvider implements NewsProvider
{
    private ?Source $source = null;

    public function key(): string
    {
        return 'guardian';
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
        $from = $opts['from'] ?? now()->subHours(24)->toDateString();
        $to = $opts['to'] ?? now()->toDateString();
        $pageSize = $opts['page_size'] ?? 50;

        try {
            $response = Http::timeout(10)
                ->retry(2, 200)
                ->get($source->base_url . '/search', [
                    'from-date' => $from,
                    'to-date' => $to,
                    'order-by' => 'newest',
                    'page-size' => $pageSize,
                    'show-fields' => 'trailText,thumbnail,byline,bodyText',
                    'show-tags' => 'contributor',
                    'api-key' => config('news.guardian.key'),
                ])
                ->throw()
                ->json();

            $items = data_get($response, 'response.results', []);

            return collect($items)->map(function ($item) {
                // Get author from tags (contributors) or byline
                $author = null;
                $contributors = collect($item['tags'] ?? [])
                    ->where('type', 'contributor')
                    ->pluck('webTitle')
                    ->filter()
                    ->first();

                if ($contributors) {
                    $author = $contributors;
                } elseif (!empty($item['fields']['byline'])) {
                    $author = $item['fields']['byline'];
                }

                return new ArticleDTO(
                    title: $item['webTitle'],
                    summary: data_get($item, 'fields.trailText'),
                    content: data_get($item, 'fields.bodyText'),
                    url: $item['webUrl'],
                    imageUrl: data_get($item, 'fields.thumbnail'),
                    author: $author,
                    providerCategory: $item['sectionId'] ?? null,
                    language: 'en',
                    publishedAt: Carbon::parse($item['webPublicationDate']),
                    externalId: $item['id'],
                    raw: $item
                );
            });
        } catch (\Exception $e) {
            Log::error('Guardian API fetch failed', [
                'provider' => 'guardian',
                'error' => $e->getMessage(),
            ]);
            return collect([]);
        }
    }
}
