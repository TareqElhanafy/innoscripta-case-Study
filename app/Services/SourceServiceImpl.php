<?php

namespace App\Services;

use App\Contracts\Repositories\SourceRepository;
use App\Contracts\Services\SourceService;

class SourceServiceImpl implements SourceService
{
    public function __construct(
        private SourceRepository $sourceRepository
    ) {}


    //get all sources with metadata

    public function getAllSources(): array
    {
        $sources = $this->sourceRepository->list();

        return [
            'sources' => $sources,
            'meta' => [
                'total' => count($sources),
            ],
        ];
    }

    //get single source details
    public function getSourceByKey(string $key): ?array
    {
        return $this->sourceRepository->findByKey($key);
    }
}
