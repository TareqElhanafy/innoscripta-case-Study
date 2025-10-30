<?php


namespace App\Http\Controllers\Api;

use App\Contracts\Services\ArticleService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\IndexRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class ArticleController extends Controller
{
    public function __construct(
        private ArticleService $articleService
    ) {}


    public function index(Request $request): JsonResponse
    {
        $articles = $this->articleService->list($request->all(), $request->get('per_page', 20));

        return response()->json([
            'data' => $articles->items(),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
                'last_page' => $articles->lastPage(),
                'from' => $articles->firstItem(),
                'to' => $articles->lastItem(),
            ],
            'links' => [
                'first' => $articles->url(1),
                'last' => $articles->url($articles->lastPage()),
                'prev' => $articles->previousPageUrl(),
                'next' => $articles->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Get single article by ID
     *
     * GET /api/articles/{id}
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $article = $this->articleService->get($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article not found',
                'error' => "No article found with ID: {$id}",
            ], 404);
        }

        return response()->json([
            'data' => $article,
        ]);
    }
}
