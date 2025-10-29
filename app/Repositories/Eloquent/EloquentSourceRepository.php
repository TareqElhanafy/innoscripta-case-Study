<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\SourceRepository;
use App\Models\Source;

class EloquentSourceRepository implements SourceRepository
{
    /**
     * Get all sources
     */
    public function list(): array
    {
        return Source::query()
            ->select(['id', 'key', 'name', 'base_url',])
            ->orderBy('name')
            ->get()
            ->map(function ($source) {
                return [
                    'id' => $source->id,
                    'key' => $source->key,
                    'name' => $source->name,
                    'base_url' => $source->base_url,
                ];
            })
            ->toArray();
    }

    /**
     * Find a single source by key
     */
    public function findByKey(string $key): ?array
    {
        $source = Source::where('key', $key)->first();

        if (!$source) {
            return null;
        }

        return [
            'id' => $source->id,
            'key' => $source->key,
            'name' => $source->name,
            'base_url' => $source->base_url,
        ];
    }
}
