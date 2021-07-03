<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.nl',
            'password' => bcrypt('1234567890'),
            'is_admin' => true
        ]);

        User::create([
            'name' => 'gebruiker',
            'email' => 'gebruiker@gebruiker.nl',
            'password' => bcrypt('1234567890'),
            'is_admin' => false
        ]);
    }
}
