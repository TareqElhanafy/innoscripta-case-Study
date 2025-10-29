<?php

namespace App\Providers;

use App\Contracts\Repositories\CategoryRepository;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\SourceRepository;
use App\Contracts\Services\CategoryService;
use App\Repositories\Eloquent\EloquentSourceRepository;
use App\Contracts\Services\SourceService;
use App\Repositories\Eloquent\EloquentCategoryRepository;
use App\Services\CategoryServiceImpl;
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

        $this->app->bind(CategoryRepository::class, EloquentCategoryRepository::class);
        $this->app->bind(
            CategoryService::class,
            CategoryServiceImpl::class
        );
    }
}
