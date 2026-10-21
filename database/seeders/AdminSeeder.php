<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Division;
use Illuminate\Support\Str;

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
                'status' => 'active',
            ]
        );


        Division::all()->each(function ($division) {
            User::firstOrCreate(
                ['email' => Str::slug($division->name) . '@example.com'],
                [
                    'name'        => $division->name . ' prc',
                    'password'    => Hash::make('123456789'),
                    'role'        => 'unassigned',
                    'division_id' => $division->id,
                    'status'      => 'pending',
                ]
            );
        });
    }
}
