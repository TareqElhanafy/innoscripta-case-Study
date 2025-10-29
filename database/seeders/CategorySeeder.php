<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['slug' => 'world', 'name' => 'World', 'description' => 'International news'],
            ['slug' => 'business', 'name' => 'Business', 'description' => 'Business and finance'],
            ['slug' => 'technology', 'name' => 'Technology', 'description' => 'Tech and innovation'],
            ['slug' => 'sports', 'name' => 'Sports', 'description' => 'Sports news'],
            ['slug' => 'science', 'name' => 'Science', 'description' => 'Science and research'],
            ['slug' => 'health', 'name' => 'Health', 'description' => 'Health and wellness'],
            ['slug' => 'entertainment', 'name' => 'Entertainment', 'description' => 'Entertainment and culture'],
            ['slug' => 'politics', 'name' => 'Politics', 'description' => 'Political news'],
            ['slug' => 'opinion', 'name' => 'Opinion', 'description' => 'Opinion and commentary'],
            ['slug' => 'other', 'name' => 'Other', 'description' => 'Uncategorized'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
