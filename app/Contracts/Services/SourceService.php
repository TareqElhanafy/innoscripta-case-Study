<?php

namespace App\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface SourceService
{
    public function getAllSources(int $perPage = 20): LengthAwarePaginator;
    public function getSourceByKey(string $key): Model | null;
}
