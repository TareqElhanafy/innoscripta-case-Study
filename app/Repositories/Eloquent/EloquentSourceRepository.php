<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\SourceRepository;
use App\Models\Source;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class EloquentSourceRepository implements SourceRepository
{
    /**
     * Get all sources
     */
    public function list(int $perPage = 20): LengthAwarePaginator
    {
        $query =  Source::query()
            ->select(['id', 'key', 'name', 'base_url']);
        return $query->paginate($perPage);
    }

    /**
     * Find a single source by key
     */
    public function findByKey(string $key): Model| null
    {
        $source = Source::where('key', $key)->first();

        if (!$source) {
            return null;
        }

        return $source;
    }
}
