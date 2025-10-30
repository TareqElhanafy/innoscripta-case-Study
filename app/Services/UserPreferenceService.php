<?php


namespace App\Services;

use App\Contracts\Repositories\UserPreferenceRepository;

class UserPreferenceService
{
    public function __construct(
        private UserPreferenceRepository $preferenceRepository
    ) {}

    public function getPreferences(int $userId): array
    {
        $prefs = $this->preferenceRepository->getByUserId($userId);

        if (!$prefs) {
            return [
                'preferred_sources' => [],
                'preferred_categories' => [],
                'preferred_authors' => [],
            ];
        }

        return $prefs;
    }

    public function updatePreferences(int $userId, array $data): array
    {
        // valate data structure
        $preferences = [
            'preferred_sources' => $data['preferred_sources'] ?? [],
            'preferred_categories' => $data['preferred_categories'] ?? [],
            'preferred_authors' => $data['preferred_authors'] ?? [],
        ];

        return $this->preferenceRepository->updateForUser($userId, $preferences);
    }
}
