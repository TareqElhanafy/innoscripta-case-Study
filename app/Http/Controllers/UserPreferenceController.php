<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePreferencesRequest;
use App\Services\UserPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function __construct(
        private UserPreferenceService $preferenceService
    ) {}

    // prefrences of the authenticated usr
    public function show(Request $request): JsonResponse
    {
        $preferences = $this->preferenceService->getPreferences($request->user()->id);

        return $this->successResponse($preferences, 200);
    }

    // update user prefernces
    public function update(UpdatePreferencesRequest $request): JsonResponse
    {
        $preferences = $this->preferenceService->updatePreferences(
            $request->user()->id,
            $request->validated()
        );

        return $this->successResponse($preferences, 201, "Preferences updated successfully'");
    }
}
