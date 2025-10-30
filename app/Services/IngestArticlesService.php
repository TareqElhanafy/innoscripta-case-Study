<?php

namespace App\Services;

use App\Contracts\Services\NewsProvider;
use App\DTO\ArticleDTO;
use App\Models\Article;
use App\Models\Source;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IngestArticlesService
{
    public function __construct(
        private CategoryMapperService $categoryMapper,
        private AuthorService $authorService
    ) {}

    // here i will ingest articles from a provider
    public function ingest(NewsProvider $provider, array $opts = []): array
    {
        $startTime = microtime(true);

        // Get source by provider key
        $source = Source::where('key', $provider->key())->firstOrFail();

        if (!$source) {
            Log::info("Source disabled, skipping ingestion", ['source' => $provider->key()]);
            return [
                'source' => $provider->key(),
                'fetched' => 0,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'duration' => 0,
            ];
        }

        Log::info("Starting ingestion", ['source' => $provider->key()]);
        // Fetch articles from provider
        $dtos = $provider->fetchRecent($opts);

        $stats = [
            'fetched' => $dtos->count(),
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        DB::transaction(function () use ($dtos, $source, $provider, &$stats) {
            foreach ($dtos as $dto) {
                try {
                    // map then create category dynamically as not all providers provide categories
                    $categoryId = $this->categoryMapper->mapCategory($dto->providerCategory);

                    // Get or create author
                    $authorId = $this->authorService->getOrCreateAuthor($dto->author);

                    // Generate checksum for deduplication
                    $checksum = $this->generateChecksum($dto, $provider->key());

                    // Check if article exists
                    $existing = Article::where('url', $dto->url)->first();

                    $wasCreated = false;

                    Article::updateOrCreate(
                        ['url' => $dto->url],
                        [
                            'source_id' => $source->id,
                            'external_id' => $dto->externalId,
                            'title' => $dto->title,
                            'summary' => $dto->summary,
                            'content' => $dto->content,
                            'image_url' => $dto->imageUrl,
                            'author_id' => $authorId,
                            'provider_category' => $dto->providerCategory,
                            'category_id' => $categoryId,
                            'language' => $dto->language,
                            'published_at' => $dto->publishedAt,
                            'checksum' => $checksum,
                            'metadata' => $dto->raw,
                        ]
                    );

                    if ($existing) {
                        $stats['updated']++;
                    } else {
                        $stats['created']++;
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to ingest article", [
                        'source' => $provider->key(),
                        'url' => $dto->url,
                        'error' => $e->getMessage(),
                    ]);
                    $stats['skipped']++;
                }
            }
        });

        // Update source metadata
        $source->update(['last_fetched_at' => now()]);

        $duration = round(microtime(true) - $startTime, 2);

        $result = [
            'source' => $provider->key(),
            'fetched' => $stats['fetched'],
            'created' => $stats['created'],
            'updated' => $stats['updated'],
            'skipped' => $stats['skipped'],
            'duration' => $duration,
        ];

        Log::info("Ingestion completed", $result);

        return $result;
    }

    private function generateChecksum(ArticleDTO $dto, string $providerKey): string
    {
        return sha1(
            mb_strtolower(trim($dto->title)) . '|' .
                $dto->publishedAt->toIso8601String() . '|' .
                $providerKey
        );
    }
}
