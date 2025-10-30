<?php

namespace App\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ArticleService
{

    public function list(array $filters, int $perPage = 20): LengthAwarePaginator;
    public function get(string $id): ?array;
}
