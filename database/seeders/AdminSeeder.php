<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Data admin yang akan dibuat
    $admins = [
      [
        'fullname' => 'Dr. Ahmad Supriadi',
        'jobtitle' => 'Kepala Puskesmas',
        'phone_number' => '081234567890',
        'password' => 'password123',
      ],
    ];

    foreach ($admins as $adminData) {
      // Buat record admin
      $admin = Admin::create([
        'fullname' => $adminData['fullname'],
        'jobtitle' => $adminData['jobtitle'],
      ]);

      // Buat user account untuk admin
      User::create([
        'phone_number' => $adminData['phone_number'],
        'password' => Hash::make($adminData['password']),
        'role' => 'ADMIN',
        'owner_id' => $admin->id,
      ]);
    }

    $this->command->info('Admin seeder berhasil dijalankan!');
  }
}
