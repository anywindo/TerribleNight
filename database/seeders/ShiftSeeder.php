<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        // Standard Morning Shift
        Shift::factory()->create([
            'shift_name' => 'Morning Shift',
            'default_start_time' => '08:00:00',
            'default_end_time' => '17:00:00',
        ]);

        // Standard Evening Shift
        Shift::factory()->create([
            'shift_name' => 'Evening Shift',
            'default_start_time' => '15:00:00',
            'default_end_time' => '23:00:00',
        ]);

        // Night Shift
        Shift::factory()->create([
            'shift_name' => 'Night Shift',
            'default_start_time' => '23:00:00',
            'default_end_time' => '07:00:00',
        ]);
    }
}
