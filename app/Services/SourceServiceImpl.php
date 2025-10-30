<?php

namespace App\Services;

use App\Contracts\Repositories\SourceRepository;
use App\Contracts\Services\SourceService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class SourceServiceImpl implements SourceService
{
    public function __construct(
        private SourceRepository $sourceRepository
    ) {}


    //get all sources with metadata

    public function getAllSources(int $perPage = 20): LengthAwarePaginator
    {
        return $this->sourceRepository->list($perPage);
    }

    //get single source details
    public function getSourceByKey(string $key): Model | null
    {
        return $this->sourceRepository->findByKey($key);
    }
}
