<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface SourceRepository
{
    public function list(int $perPage = 20): LengthAwarePaginator;
    public function findByKey(string $key): Model | null;
}
