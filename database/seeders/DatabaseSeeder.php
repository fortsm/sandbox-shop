<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()
            ->count(3)
            ->has(Wallet::factory())
            ->create();

        Product::factory()
            ->count(10)
            ->create();

        User::all()->each(function ($user) {
            $products = [];
            foreach (Product::all()->random(3)->pluck('id')->toArray() as $id) {
                $products[$id] = [
                    'price' => round(rand(100, 990) / 10, 2),
                    'quantity' => rand(100, 200),
                ];
            }
            $user->products()->attach($products);
        });
    }
}
