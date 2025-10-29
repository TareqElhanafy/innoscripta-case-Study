<?php

namespace App\Contracts\Repositories;

interface SourceRepository
{
    public function list(): array;
    public function findByKey(string $key): ?array;
}
