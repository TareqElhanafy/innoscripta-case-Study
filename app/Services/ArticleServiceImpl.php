<?php

namespace App\Services;

use App\Contracts\Repositories\ArticleRepository;
use App\Contracts\Services\ArticleService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class ArticleServiceImpl implements ArticleService
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private UserPreferenceService $preferenceService

    ) {}

    // we can add caching layer to fast the result
    public function list(array $filters, int $perPage = 20, ?int $userId = null): LengthAwarePaginator
    {
        if ($userId && empty($filters['source']) && empty($filters['category']) && empty($filters['author'])) {
            $preferences = $this->preferenceService->getPreferences($userId);

            // this filters will be only aplied if user has set prefernces 
            if (!empty($preferences['preferred_sources'])) {
                $filters['source'] = implode(',', $preferences['preferred_sources']);
            }
            if (!empty($preferences['preferred_categories'])) {
                $filters['category'] = implode(',', $preferences['preferred_categories']);
            }
            if (!empty($preferences['preferred_authors'])) {
                $filters['author'] = implode(',', $preferences['preferred_authors']);
            }
        }
        return $this->articleRepository->list($filters, $perPage);
    }

    public function get(string $id): Model | null
    {
        return $this->articleRepository->findById($id);
    }
}
