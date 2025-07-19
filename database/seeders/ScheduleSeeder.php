<?php

namespace Database\Seeders;

use App\Models\Officer;
use App\Models\Patient;
use App\Models\Schedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Schedule::create([
                'patient_id' => Patient::inRandomOrder()->first()->id,
                'officer_id' => Officer::inRandomOrder()->first()->id,
                'datetime' => now()->addDays($i),
                'type' => ['edukasi', 'konsultasi', 'ambil obat'][array_rand(['edukasi', 'konsultasi', 'ambil obat'])],
                'status' => [1, 2, 3][array_rand([1, 2, 3])],
                'message' => "Hai anda memiliki jadwal konsultasi hari ini pada silahkan datang ke puskesmas",
            ]);
        }
    }
}
