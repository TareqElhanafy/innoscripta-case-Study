<?php

namespace App\Contracts\Services;

interface CategoryService
{
    public function getAllCategories(): array;
    public function getCategoryBySlug(string $slug): ?array;
}
