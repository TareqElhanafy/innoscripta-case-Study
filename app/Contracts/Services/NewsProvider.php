<?php

namespace App\Contracts\Services;

use Illuminate\Support\Collection;

interface NewsProvider
{
    public function fetchRecent(array $opts = []): Collection;

    public function key(): string; // Unique key identifying the news provider
}
