<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\ArticleRepository;
use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EloquentArticleRepository implements ArticleRepository
{
    public function list(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Article::query()
            ->with(['source:id,key,name', 'category:id,slug,name', 'author:id,name'])
            ->select([
                'id',
                'source_id',
                'title',
                'summary',
                'url',
                'image_url',
                'language',
                'published_at',
                'category_id',
                'author_id',
            ]);

        // full-text search in title and summary
        if (!empty($filters['q'])) {
            $term = $filters['q'];
            $query->where(function ($q) use ($term) {
                $q->where('title', 'ILIKE', "%{$term}%")
                    ->orWhere('summary', 'ILIKE', "%{$term}%");
            });
        }

        // fate range filters for the field published_at
        if (!empty($filters['from'])) {
            $query->where('published_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->where('published_at', '<=', $filters['to']);
        }

        // source filter values can be in this shape "newsapi,guardian"
        if (!empty($filters['source'])) {
            $sources = is_array($filters['source'])
                ? $filters['source']
                : explode(',', $filters['source']);

            $query->whereHas('source', function ($q) use ($sources) {
                $q->whereIn('key', $sources);
            });
        }

        // category filter by slug in this shape "sports,technology"
        if (!empty($filters['category'])) {
            $categories = is_array($filters['category'])
                ? $filters['category']
                : explode(',', $filters['category']);

            $query->whereHas('category', function ($q) use ($categories) {
                $q->whereIn('slug', $categories);
            });
        }

        // auth filter by sending his name
        if (!empty($filters['author'])) {
            $authors = is_array($filters['author'])
                ? $filters['author']
                : explode(',', $filters['author']);

            $query->whereHas('author', function ($q) use ($authors) {
                $q->whereIn('name', $authors);
            });
        }

        // lang filter
        if (!empty($filters['lang'])) {
            $languages = is_array($filters['lang'])
                ? $filters['lang']
                : explode(',', $filters['lang']);

            $query->whereIn('language', $languages);
        }

        // sort
        $sortField = $filters['sort_by'] ?? 'published_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        // Validate sort fields
        $allowedSortFields = ['published_at', 'title', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'published_at';
        }

        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortField, $sortOrder);


        return $query->paginate($perPage)->appends($filters);
    }

    public function findById(string $id): Model | null
    {
        $article = Article::query()
            ->select([
                'id',
                'source_id',
                'title',
                'summary',
                'url',
                'image_url',
                'language',
                'published_at',
                'category_id',
                'author_id',
                'content',
                'created_at'
            ])
            ->with(['source:id,key,name', 'category:id,slug,name', 'author:id,name'])
            ->find($id);

        if (!$article) {
            return null;
        }

        if ($article->published_at) {
            $article->published_at = $article->published_at->toIso8601String();
        }
        if ($article->created_at) {
            $article->created_at = $article->created_at->toIso8601String();
        }

        $article->setAttribute('source', $article->relationLoaded('source') && $article->source ? [
            'key'  => $article->source->key,
            'name' => $article->source->name,
        ] : null);

        $article->setAttribute('category', $article->relationLoaded('category') && $article->category ? [
            'slug' => $article->category->slug,
            'name' => $article->category->name,
        ] : null);

        $article->setAttribute('author', $article->relationLoaded('author') && $article->author ? [
            'name' => $article->author->name,
        ] : null);

        return $article;
    }
}
