<?php

namespace App\Services;

use App\Models\Author;
use App\Contracts\Services\AuthorService as AuthorServiceContract;
use Illuminate\Pagination\LengthAwarePaginator;

class AuthorService implements AuthorServiceContract
{
    /**
     * Get all authors with pagination
     */
    public function getAllAuthors(int $perPage = 20): LengthAwarePaginator
    {
        return Author::orderBy('name')->paginate($perPage);
    }

    /**
     * Get author by ID
     */
    public function getAuthorById(int $id): ?Author
    {
        return Author::find($id);
    }

    /**
     * Get author by name
     */
    public function getAuthorByName(string $name): ?Author
    {
        return Author::where('name', $name)->first();
    }

    /**
     * Get or create author by name
     */
    public function getOrCreateAuthor(?string $name): ?int
    {
        if (empty($name)) {
            return null;
        }
        // Clean name
        $cleanName = trim($name);
        if (empty($cleanName)) {
            return null;
        }

        $author = Author::firstOrCreate(
            ['name' => $cleanName]
        );
        return $author->id;
    }

    /**
     * Get authors with their article counts
     */
    public function getAuthorsWithArticleCounts(int $perPage = 20): LengthAwarePaginator
    {
        return Author::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->orderBy('name')
            ->paginate($perPage);
    }
}
