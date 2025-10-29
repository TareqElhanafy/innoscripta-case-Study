<?php

namespace App\Contracts\Services;

interface SourceService
{
    public function getAllSources(): array;
    public function getSourceByKey(string $key): ?array;
}
