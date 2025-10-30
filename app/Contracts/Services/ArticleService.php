<?php

namespace App\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface ArticleService
{

    public function list(array $filters, int $perPage = 20): LengthAwarePaginator;
    public function get(string $id): Model | null;
}
