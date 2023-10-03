<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use App\Models\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        ActivityType::factory()->count(10)->create();
    }
}
