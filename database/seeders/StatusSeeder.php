<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $statuses = [
            ['name' => 'Preproc',         'sort_order' => 1, 'is_final' => false],
            ['name' => 'Bidding',         'sort_order' => 2, 'is_final' => false],
            ['name' => 'Successful Bid',  'sort_order' => 3, 'is_final' => false],
            ['name' => 'Contract Signed', 'sort_order' => 4, 'is_final' => false],
            ['name' => 'Obligated',       'sort_order' => 5, 'is_final' => false],
            ['name' => 'Delivered',       'sort_order' => 6, 'is_final' => true],
            ['name' => 'Cancelled',       'sort_order' => 7, 'is_final' => true],
        ];

        foreach ($statuses as $status) {
            Status::updateOrCreate(['name' => $status['name']], $status);
        }
    }
}
