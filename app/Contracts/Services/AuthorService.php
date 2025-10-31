<?php

namespace App\Contracts\Services;

use App\Models\Author;
use Illuminate\Pagination\LengthAwarePaginator;

interface AuthorService
{

    public function getAllAuthors(int $perPage = 20): LengthAwarePaginator;
    public function getAuthorById(int $id): ?Author;
    public function getAuthorByName(string $name): ?Author;
    public function getOrCreateAuthor(?string $name): ?int;
    //Get authors with their article counts
    public function getAuthorsWithArticleCounts(int $perPage = 20): LengthAwarePaginator;
}
