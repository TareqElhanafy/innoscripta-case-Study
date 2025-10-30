<?php

namespace App\Contracts\Repositories;

interface UserPreferenceRepository
{
    // get user prefernce by user id
    public function getByUserId(int $userId): ?array;
    // update preferences for user
    public function updateForUser(int $userId, array $data): array;
}
