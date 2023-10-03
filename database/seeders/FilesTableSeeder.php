<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $batchSize = 500; // Adjust the size as needed
        $totalRecords = 1000000; // Total number of records to create
        $numberOfBatches = intdiv($totalRecords, $batchSize) +
            (($totalRecords % $batchSize) > 0 ? 1 : 0);

        for ($i = 0; $i < $numberOfBatches; $i++) {
            File::factory()->count($batchSize)->create();
        }
    }
}
