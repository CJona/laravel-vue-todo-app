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
        // \App\Models\User::factory(10)->create();
        User::create([
            'name' => 'test',
            'email' => 'test@test.nl',
            'password' => bcrypt('1234567890'),
            'is_admin' => true
        ]);

        User::create([
            'name' => 'test2',
            'email' => 'test2@test.nl',
            'password' => bcrypt('1234567890'),
            'is_admin' => false
        ]);
    }
}
