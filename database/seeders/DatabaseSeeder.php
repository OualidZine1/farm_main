<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
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
                'firstname' => 'Oualid',
                'lastname' => 'Zine',
                'password' => Hash::make('password'),
                'role' => 'administrator',
                'cin' => 'AB123456',
                'email_verified_at' => now(),
            ]
        );

        // Create regular user

        // Seed categories, products, and fields if needed
        $this->call([
            ManagersSeeder::class,
            InventoryTransactionSeeder::class,
        ]);

        // Assign admin role if using spatie/laravel-permission
        if (class_exists('Spatie\\Permission\\Models\\Role') && class_exists('Spatie\\Permission\\Models\\Permission')) {
            $admin->assignRole('admin');
        }


        // Call demo data seeder
        $this->call(DemoDataSeeder::class);
    }
}
