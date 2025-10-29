<?php

namespace App\Services;

use App\Contracts\Repositories\CategoryRepository;
use App\Contracts\Services\CategoryService;

class CategoryServiceImpl implements CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {}


    //get all categories with metadata

    public function getAllCategories(): array
    {
        $categories = $this->categoryRepository->list();

        return [
            'categories' => $categories,
            'meta' => [
                'total' => count($categories),
            ],
        ];
    }

    //get single category details
    public function getCategoryBySlug(string $slug): ?array
    {
        return $this->categoryRepository->findBySlug($slug);
    }
}
