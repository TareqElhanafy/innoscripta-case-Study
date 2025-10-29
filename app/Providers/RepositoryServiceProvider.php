<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\SourceRepository;
use App\Repositories\Eloquent\EloquentSourceRepository;
use App\Contracts\Services\SourceService;
use App\Services\SourceServiceImpl;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SourceRepository::class, EloquentSourceRepository::class);
        $this->app->bind(
            SourceService::class,
            SourceServiceImpl::class
        );
    }
}
