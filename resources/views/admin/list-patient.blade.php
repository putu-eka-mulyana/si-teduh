@extends('layouts.admin-app')

@section('title', 'List Patient')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="h-20"></div>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-black ">Daftar Pasien</h2>
            <div class="flex gap-2">
                <!-- Pencarian Pasien -->
                <input type="text" placeholder="Cari Pasien..."
                    class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                <button type="submit"
                    class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                    </svg>
                    Cari
                </button>
                <!-- Tombol Tambah Pasien -->
                <a class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition"
                    href="{{ route('admin.add-patient') }}">
                    Tambah Pasien
                </a>
            </div>
        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-lg shadow">
            <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3">No. Rekan Medis</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">No. BPJS</th>
                        <th class="px-6 py-3">Kontak</th>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">Jenis Kelamin</th>
                        <th class="px-6 py-3">Umur</th>
                        <th class="px-6 py-3">Pekerjaan</th>
                        <th class="px-6 py-3">Alamat</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Contoh Data -->
                    <tr class="border-b last:border-0 dark:border-gray-700">
                        <td class="px-6 py-4">RM001</td>
                        <td class="px-6 py-4">1234567890123456</td>
                        <td class="px-6 py-4">BPJS001</td>
                        <td class="px-6 py-4">+628123456789</td>
                        <td class="px-6 py-4">Andi Saputra</td>
                        <td class="px-6 py-4">Laki-laki</td>
                        <td class="px-6 py-4">30</td>
                        <td class="px-6 py-4">Software Engineer</td>
                        <td class="px-6 py-4">Jl. Merdeka No. 1, Jakarta</td>
                        <td class="px-6 py-4">
                            <button class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition w-full">
                                Edit
                            </button>
                            <button
                                class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition mt-2 w-full">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <!-- Tambahkan baris lainnya sesuai kebutuhan -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col md:flex-row items-center justify-between mt-6 gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan <span class="font-semibold text-gray-900">1-10</span> dari
                <span class="font-semibold text-gray-900">100</span>
            </span>
            <ul class="inline-flex items-center -space-x-px text-sm">
                <li>
                    <a href="#"
                        class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        Sebelumnya
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="px-3 py-2 border-t border-b border-gray-300 bg-white text-gray-700 font-semibold dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        1
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="px-3 py-2 border-t border-b border-gray-300 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        2
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="px-3 py-2 border-t border-b border-gray-300 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        3
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        Selanjutnya
                    </a>
                </li>
            </ul>
        </div>
    </div>
@endsection
