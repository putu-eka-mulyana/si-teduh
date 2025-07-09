@extends('layouts.admin-app')
@section('title', 'Registrasi Pasien')
@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="h-20"></div>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-black">Registrasi Pasien</h2>
        </div>

        <!-- Form Registrasi Pasien -->
        <form action="{{ route('admin.store-patient') }}" method="POST"
            class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomer Rekam
                        medis</label>
                    <input type="text" id="nik" name="nik" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIK</label>
                    <input type="text" id="nik" name="nik" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="bpjs_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">No.
                        BPJS</label>
                    <input type="text" id="bpjs_number" name="bpjs_number"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                        Lengkap</label>
                    <input type="text" id="full_name" name="full_name" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis
                        Kelamin</label>
                    <select id="gender" name="gender" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male">Laki-laki</option>
                        <option value="female">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                        lahir</label>
                    <input type="date" id="birth_date" name="birth_date" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="occupation"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pekerjaan</label>
                    <input type="text" id="occupation" name="occupation" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kontak</label>
                    <input type="text" id="contact" name="contact" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="password"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input type="password" id="password" name="password" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="password_confirmation"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>


                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                    <textarea id="address" name="address" rows="3" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"></textarea>
                </div>

            </div>
            <div class="mt-6">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition">
                    Daftar Pasien
                </button>
            </div>
        </form>
    </div>
@endsection
