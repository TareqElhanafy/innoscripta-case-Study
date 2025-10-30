<?php

namespace App\Http\Controllers;

use App\Contracts\Services\SourceService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SourceController extends Controller
{
    public function __construct(
        private SourceService $sourceService
    ) {}

    // get all sources
    public function index(): JsonResponse
    {
        $sources = $this->sourceService->getAllSources();
        return $this->showAll($sources);
    }

    // get single source by key
    public function show(string $key): JsonResponse
    {
        $source = $this->sourceService->getSourceByKey($key);

        if (!$source) {
            return $this->errorResponse(404, "No source found with key: {$key}", "Source not found");
        }

        return $this->showOne($source);
    }
}
