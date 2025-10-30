<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserPreferenceRepository;
use App\Models\UserPreference;

class EloquentUserPreferenceRepository implements UserPreferenceRepository
{
    public function getByUserId(int $userId): ?array
    {
        $prefs = UserPreference::where('user_id', $userId)->first();

        if (!$prefs) {
            return null;
        }

        return [
            'preferred_sources' => $prefs->preferred_sources ?? [],
            'preferred_categories' => $prefs->preferred_categories ?? [],
            'preferred_authors' => $prefs->preferred_authors ?? [],
        ];
    }

    public function updateForUser(int $userId, array $data): array
    {
        $prefs = UserPreference::updateOrCreate(
            ['user_id' => $userId],
            [
                'preferred_sources' => $data['preferred_sources'] ?? [],
                'preferred_categories' => $data['preferred_categories'] ?? [],
                'preferred_authors' => $data['preferred_authors'] ?? [],
            ]
        );

        return [
            'preferred_sources' => $prefs->preferred_sources ?? [],
            'preferred_categories' => $prefs->preferred_categories ?? [],
            'preferred_authors' => $prefs->preferred_authors ?? [],
        ];
    }
}
