<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CategoryRepository;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class EloquentCategoryRepository implements CategoryRepository
{
    /**
     * Get all categories
     */
    public function list(int $perPage = 20): LengthAwarePaginator
    {
        $query =  Category::query()
            ->select(['id', 'slug', 'name', 'description']);
        return $query->paginate($perPage);
    }

    /**
     * Find a single category by slug
     */
    public function findBySlug(string $slug): Model | null
    {
        $category = Category::where('slug', $slug)->first();
        if (!$category) {
            return null;
        }
        return $category;
    }
}
