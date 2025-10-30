<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryMapperService
{
    private const MAPPING_RULES = [
        // Guardian
        'world' => 'world',
        'business' => 'business',
        'technology' => 'technology',
        'sport' => 'sports',
        'science' => 'science',
        'politics' => 'politics',
        'commentisfree' => 'opinion',
        'lifeandstyle' => 'entertainment',
        'culture' => 'entertainment',
        'music' => 'entertainment',
        'film' => 'entertainment',
        'books' => 'entertainment',
        'media' => 'business',
        'environment' => 'science',
        'health' => 'health',

        // NYT
        'sports' => 'sports',
        'opinion' => 'opinion',
        'arts' => 'entertainment',
        'style' => 'entertainment',
        'u.s.' => 'world',
        'theater' => 'entertainment',
        'movies' => 'entertainment',
        'dining' => 'entertainment',

        // NewsAPI
        'general' => 'world',
        'entertainment' => 'entertainment',
    ];

    public function mapCategory(?string $providerCategory): ?int
    {
        if (empty($providerCategory)) {
            return null;
        }

        $normalized = Str::slug(strtolower(trim($providerCategory)));

        // Try direct mapping
        $slug = self::MAPPING_RULES[$normalized] ?? null;

        // Fallback: fuzzy match
        if (!$slug) {
            $slug = $this->findSimilarCategory($normalized);
        }

        // Create dynamically if still not found
        if (!$slug) {
            return $this->createDynamicCategory($normalized, $providerCategory);
        }

        $category = $this->getOrCreateCategory($slug);
        return $category?->id;
    }

    private function findSimilarCategory(string $normalized): ?string
    {
        foreach (self::MAPPING_RULES as $key => $value) {
            if (str_contains($normalized, $key) || str_contains($key, $normalized)) {
                return $value;
            }
        }
        return null;
    }

    private function createDynamicCategory(string $slug, string $originalName): ?int
    {
        $name = Str::title(str_replace(['-', '_'], ' ', $originalName));

        $category = Category::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'description' => "Auto-created from provider: {$originalName}",
            ]
        );

        Log::info("Dynamic category created", [
            'slug' => $slug,
            'name' => $name,
            'original' => $originalName,
        ]);


        return $category->id;
    }

    private function getOrCreateCategory(string $slug): ?Category
    {
        return Category::firstOrCreate(
            ['slug' => $slug],
            ['name' => Str::title($slug)]
        );
    }
}
