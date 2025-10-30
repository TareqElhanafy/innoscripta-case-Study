<?php


namespace App\Http\Controllers;

use App\Contracts\Services\ArticleService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleIndexRequest;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function __construct(
        private ArticleService $articleService
    ) {}


    public function index(ArticleIndexRequest $request): JsonResponse
    {

        $userId = $request->user()?->id;

        $articles = $this->articleService->list(
            $request->filters(),
            $request->perPage(),
            $userId
        );
        return $this->showAll($articles);
    }


    public function show(string $id): JsonResponse
    {
        $article = $this->articleService->get($id);

        if (!$article) {
            return $this->errorResponse(404, "No article found with ID: {$id}", "Article not found");
        }

        return $this->showOne($article);
    }
}
