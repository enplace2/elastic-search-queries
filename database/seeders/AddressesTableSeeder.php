<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $batchSize = 500; // Adjust the size as needed
        $totalRecords = 100000; // Total number of records to create
        $numberOfBatches = intdiv($totalRecords, $batchSize) +
            (($totalRecords % $batchSize) > 0 ? 1 : 0);

        for ($i = 0; $i < $numberOfBatches; $i++) {
            Address::factory()->count($batchSize)->create();
        }
    }
}
