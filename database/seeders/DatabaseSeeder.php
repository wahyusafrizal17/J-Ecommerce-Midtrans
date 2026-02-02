<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $categoryNames = [
            'Elektronik',
            'Fashion',
            'Kesehatan',
            'Rumah Tangga',
            'Aksesoris',
            'Hobi',
        ];

        foreach ($categoryNames as $name) {
            Category::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => null, 'is_active' => true]
            );
        }

        $categories = Category::query()->get();
        if ($categories->isNotEmpty() && Product::query()->count() < 24) {
            Product::factory()
                ->count(24)
                ->state(fn () => ['category_id' => $categories->random()->id])
                ->create();
        }
    }
}
