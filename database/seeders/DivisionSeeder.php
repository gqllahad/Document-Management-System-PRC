<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            'NIISD',
            'DMSD',
            'SDMD',
        ];

        foreach ($divisions as $division) {
            Division::firstOrCreate([
                'name' => $division,
            ]);
        }
    }
}
