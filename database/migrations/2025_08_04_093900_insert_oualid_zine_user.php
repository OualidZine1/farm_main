<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')->insert([
            'firstname' => 'Oualid',
            'lastname' => 'Zine',
            'email' => 'oualid.zine@uit.ac.ma',
            'password' => Hash::make('password'),
            'role' => 'administrator',
            'cin' => 'AB123456',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('email', 'oualid.zine@uit.ac.ma')->delete();
    }
};
