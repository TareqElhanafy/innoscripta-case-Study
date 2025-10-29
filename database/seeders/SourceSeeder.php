<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $now = now();

        $sources = [
            [
                'key' => 'newsapi',
                'name' => 'NewsAPI.org',
                'base_url' => 'https://newsapi.org/v2',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'guardian',
                'name' => 'The Guardian',
                'base_url' => 'https://content.guardianapis.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'nyt',
                'name' => 'New York Times',
                'base_url' => 'https://api.nytimes.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($sources as $source) {
            DB::table('sources')->updateOrInsert(
                ['key' => $source['key']],
                $source
            );
        }
    }
}
