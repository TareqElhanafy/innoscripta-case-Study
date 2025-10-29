<?php

namespace App\Contracts\Repositories;

interface CategoryRepository
{
    public function list(): array;
    public function findBySlug(string $slug): ?array;
}
