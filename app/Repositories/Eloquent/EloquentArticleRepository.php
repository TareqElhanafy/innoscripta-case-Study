<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\ArticleRepository;
use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

        return $query->paginate($perPage)->appends($filters);
    }

    public function findById(string $id): ?array
    {
        $article = Article::query()
            ->with(['source:id,key,name', 'category:id,slug,name', 'author:id,name'])
            ->find($id);

        if (!$article) {
            return null;
        }

        return [
            'id' => $article->id,
            'title' => $article->title,
            'summary' => $article->summary,
            'content' => $article->content,
            'url' => $article->url,
            'image_url' => $article->image_url,
            'language' => $article->language,
            'published_at' => $article->published_at?->toIso8601String(),
            'source' => $article->source ? [
                'key' => $article->source->key,
                'name' => $article->source->name,
            ] : null,
            'category' => $article->category ? [
                'slug' => $article->category->slug,
                'name' => $article->category->name,
            ] : null,
            'author' => $article->author ? [
                'name' => $article->author->name,
            ] : null,
            'created_at' => $article->created_at?->toIso8601String(),
        ];
    }
}
