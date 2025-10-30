<?php


namespace App\Jobs;

use App\Services\IngestArticlesService;
use App\Services\Providers\GuardianProvider;
use App\Services\Providers\NewsApiProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class IngestSourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes
    public $tries = 3;

    public function __construct(
        public string $sourceKey
    ) {}

    public function backoff(): array
    {
        return [30, 60, 120]; // retry after 30s, 1m, 2m
    }

    public function handle(IngestArticlesService $service): void
    {
        Log::info("IngestSourceJob started", ['source' => $this->sourceKey]);

        $provider = match ($this->sourceKey) {
            'guardian' => app(GuardianProvider::class),
            'newsapi' => app(NewsApiProvider::class),
            default => null
        };

        if (!$provider) {
            Log::error("Unknown provider", ['source' => $this->sourceKey]);
            return;
        }

        // Fetch articles from last 24 hours
        $result = $service->ingest($provider, [
            'from' => now()->subHours(24),
            'to' => now(),
        ]);

        Log::info("IngestSourceJob completed", $result);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("IngestSourceJob failed", [
            'source' => $this->sourceKey,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
