<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'oualid.zine@uit.ac.ma'],
            [
                'name' => 'Oualid Zine',
                'firstname' => 'Oualid',
                'lastname' => 'Zine',
                'password' => Hash::make('password'),
                'role' => 'administrator',
                'cin' => 'AB123456',
                'email_verified_at' => now(),
            ]
        );

        // Create regular user
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular Manager',
                'firstname' => 'Regular',
                'lastname' => 'Manager',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'cin' => 'CD789012',
                'email_verified_at' => now(),
            ]
        );

        // Seed categories, products, and fields if needed
        $this->call([
            InventoryTransactionSeeder::class,
        ]);

        // Assign admin role if using spatie/laravel-permission
        if (class_exists('Spatie\\Permission\\Models\\Role') && class_exists('Spatie\\Permission\\Models\\Permission')) {
            $admin->assignRole('admin');
        }

        // Create some test users if in development
        if (app()->environment('local')) {
            \App\Models\User::factory(5)->create();
        }

        // Call demo data seeder
        $this->call(DemoDataSeeder::class);
    }
}
