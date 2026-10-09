<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder // run seed php artisan db:seed --class=AdminSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'ict@example.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('qwertyuiop'),
                'role' => 'admin',
                'division_id' => null,
            ]
        );
    }
}
