<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'manager@manager.com',
            'name' => 'App Manager',
            'is_manager' => true
        ]);

        $users = User::factory(10)->create();

        Ticket::factory(100)
            ->recycle($users)
            ->create();
    }
}
