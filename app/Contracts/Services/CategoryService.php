<?php

namespace App\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface CategoryService
{
    public function getAllCategories(int $perPage = 20): LengthAwarePaginator;
    public function getCategoryBySlug(string $slug): Model | null;
}
