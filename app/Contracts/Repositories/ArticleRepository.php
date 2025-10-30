<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ArticleRepository
{

    public function list(array $filters, int $perPage = 20): LengthAwarePaginator;
    public function findById(string $id): ?array;
}
