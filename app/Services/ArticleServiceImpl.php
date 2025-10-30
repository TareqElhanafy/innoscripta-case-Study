<?php

namespace App\Services;

use App\Contracts\Repositories\ArticleRepository;
use App\Contracts\Services\ArticleService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class ArticleServiceImpl implements ArticleService
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {}

    // we can add caching layer to fast the result
    public function list(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->articleRepository->list($filters, $perPage);
    }

    public function get(string $id): Model | null
    {
        return $this->articleRepository->findById($id);
    }
}
