<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CategoryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    // get all categories
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return $this->showAll($categories);
    }

    // get single category by slug
    public function show(string $slug): JsonResponse
    {
        $category = $this->categoryService->getCategoryBySlug($slug);

        if (!$category) {
            return $this->errorResponse(404, "No category found with slug: {$slug}", "Category not found");
        }

        return $this->showOne($category);
    }
}
