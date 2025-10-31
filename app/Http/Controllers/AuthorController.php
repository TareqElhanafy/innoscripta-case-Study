<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AuthorService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function __construct(
        private AuthorService $authorService
    ) {}

    /**
     * Get all authors with pagination
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 20), 100); // Max 100 per page
        $withCounts = $request->boolean('with_counts', false);

        if ($withCounts) {
            $authors = $this->authorService->getAuthorsWithArticleCounts($perPage);
        } else {
            $authors = $this->authorService->getAllAuthors($perPage);
        }

        return $this->showAll($authors);
    }

    /**
     * Get single author by ID
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $author = $this->authorService->getAuthorById($id);

        if (!$author) {
            return $this->errorResponse(404, "No author found with ID: {$id}", "Author not found");
        }

        return $this->showOne($author);
    }

    /**
     * Get author with their articles
     * 
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function articles(int $id, Request $request): JsonResponse
    {
        $author = $this->authorService->getAuthorById($id);

        if (!$author) {
            return $this->errorResponse(404, "No author found with ID: {$id}", "Author not found");
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        
        // Load articles with pagination
        $articles = $author->articles()
            ->with(['source', 'category'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return $this->showAll($articles);
    }
}