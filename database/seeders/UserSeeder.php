<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'it'],
            [
                'name' => 'Staff IT',
                'email' => 'it@hsitoperasional.com',
                'password' => Hash::make('it'),
            ]
        );

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Operasional',
                'email' => 'admin@hsitoperasional.com',
                'password' => Hash::make('admin'),
            ]
        );
    }
}
