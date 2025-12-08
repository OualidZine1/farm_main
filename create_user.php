<?php

use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Create or update the user
User::updateOrCreate(
    ['email' => 'oualid.zine@uit.ac.ma'],
    [
        'name' => 'Oualid Zine',
        'password' => bcrypt('password123'),
    ]
);

echo "User created or updated successfully.\n";
