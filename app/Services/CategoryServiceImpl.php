<?php

namespace App\Services;

use App\Contracts\Repositories\CategoryRepository;
use App\Contracts\Services\CategoryService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class CategoryServiceImpl implements CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {}


    //get all categories with metadata

    public function getAllCategories(int $perPage = 20): LengthAwarePaginator
    {
        return $this->categoryRepository->list($perPage);
    }

    //get single category details
    public function getCategoryBySlug(string $slug): Model | null
    {
        return $this->categoryRepository->findBySlug($slug);
    }
}
