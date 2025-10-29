<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CategoryRepository;
use App\Models\Category;

class EloquentCategoryRepository implements CategoryRepository
{
    /**
     * Get all categories
     */
    public function list(): array
    {
        return Category::query()
            ->select(['id', 'slug', 'name', 'description'])
            ->orderBy('slug')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'slug' => $category->slug,
                    'name' => $category->name,
                    'description' => $category->description,
                ];
            })
            ->toArray();
    }

    /**
     * Find a single category by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return null;
        }

        return [
            'id' => $category->id,
            'slug' => $category->slug,
            'name' => $category->name,
            'description' => $category->desciption,
        ];
    }
}
