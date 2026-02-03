<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OualidUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'oualid.zine@uit.ac.ma'],
            [
                'firstname' => 'Oualid',
                'lastname' => 'Zine',
                'email' => 'oualid.zine@uit.ac.ma',
                'cin' => 'CD789012',
                'role' => 'administrator',
                'password' => Hash::make('password'),
            ]
        );
    }
}
