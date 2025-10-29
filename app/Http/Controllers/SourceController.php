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
        $result = $this->sourceService->getAllSources();

        return response()->json([
            'data' => $result['sources'],
            'meta' => $result['meta'],
        ]);
    }

    // get single source by key
    public function show(string $key): JsonResponse
    {
        $source = $this->sourceService->getSourceByKey($key);

        if (!$source) {
            return response()->json([
                'message' => 'Source not found',
                'error' => "No source found with key: {$key}",
            ], 404);
        }

        return response()->json([
            'data' => $source,
        ]);
    }
}
