<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface CategoryRepository
{
    public function list(int $perPage = 20): LengthAwarePaginator;
    public function findBySlug(string $slug): Model | null;
}
