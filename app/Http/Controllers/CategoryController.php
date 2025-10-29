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
        $result = $this->categoryService->getAllCategories();

        return response()->json([
            'data' => $result['categories'],
            'meta' => $result['meta'],
        ]);
    }

    // get single category by slug
    public function show(string $slug): JsonResponse
    {
        $category = $this->categoryService->getCategoryBySlug($slug);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'error' => "No category found with slug: {$slug}",
            ], 404);
        }

        return response()->json([
            'data' => $category,
        ]);
    }
}
