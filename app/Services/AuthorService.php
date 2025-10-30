<?php

namespace App\Services;

use App\Models\Author;

class AuthorService
{
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
}
