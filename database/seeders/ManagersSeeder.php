<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManagersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Manager names as provided
        $managers = [
            ['firstname' => 'Reda', 'lastname' => 'Hrimmich'],
            ['firstname' => 'Mahdi', 'lastname' => 'El Alaoui'],
            ['firstname' => 'Kaoutar', 'lastname' => 'Bounajar'],
            ['firstname' => 'Hajar', 'lastname' => 'Lahrir'],
            ['firstname' => 'Rida', 'lastname' => 'Ouda'],
            ['firstname' => 'Achraf', 'lastname' => 'Serrar'],
        ];

        foreach ($managers as $index => $manager) {
            User::create([
                'firstname' => $manager['firstname'],
                'lastname' => $manager['lastname'],
                'email' => strtolower($manager['firstname']) . '@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'cin' => 'MG' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
            ]);
        }
    }
}

