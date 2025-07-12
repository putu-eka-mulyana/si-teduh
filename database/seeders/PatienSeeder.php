<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Patient::create([
                'medical_record_number' => 'MRN-' . uniqid(),
                'nik' => 'NIK-' . uniqid(),
                'bpjs_number' => 'BPJS-' . uniqid(),
                'phone_number' => '08123456789',
                'fullname' => 'Test Patient ' . ($i + 1),
                'gender' => 'M',
                'birthday' => now()->subYears(30),
                'job_title' => 'Software Engineer',
                'address' => '123 Main St, Anytown, USA',
            ]);
        }
    }
}
