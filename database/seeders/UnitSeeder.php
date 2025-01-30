<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
{
    $units = [
        ['name' => 'pieces', 'symbol' => 'pcs'],
        ['name' => 'kilograms', 'symbol' => 'kg'],
        ['name' => 'liters', 'symbol' => 'l'],
        ['name' => 'meters', 'symbol' => 'm'],
        ['name' => 'boxes', 'symbol' => 'bx'],
        ['name' => 'sets', 'symbol' => 'set'],
        ['name' => 'dozen', 'symbol' => 'dz'],
        // Add more units here as needed
    ];

    foreach ($units as $unit) {
        Unit::create($unit);
    }
}
}
