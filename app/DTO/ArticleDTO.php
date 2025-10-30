<?php

namespace App\DTO;

use Carbon\Carbon;

class ArticleDTO
{
    public function __construct(
        public string $title,
        public ?string $summary,
        public ?string $content,
        public string $url,
        public ?string $imageUrl,
        public ?string $author,
        public ?string $providerCategory,
        public string $language,
        public Carbon $publishedAt,
        public ?string $externalId = null,
        public array $raw = []
    ) {}
}
