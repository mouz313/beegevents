<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Hall / Farmhouse', 'slug' => 'hall'],
            ['name' => 'Decor', 'slug' => 'decor'],
            ['name' => 'Catering', 'slug' => 'catering'],
            ['name' => 'Photography', 'slug' => 'photography'],
            ['name' => 'DJ / Sound', 'slug' => 'dj'],
            ['name' => 'Car Rental', 'slug' => 'car'],
        ];

        foreach ($categories as $cat) {
            ServiceCategory::create($cat);
        }
    }
}
