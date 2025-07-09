@extends('layouts.admin-app')
@section('title', 'Tambah Konseling')

@section('content')
    {{-- gunakan flowbite css untuk form ini --}}
    <div class="container mx-auto px-4 py-6">
        <div class="h-20"></div>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-black">Tambah Konseling</h2>
            {{-- tambahkan button tambah pasien dan tambah dokter
            <perawat></perawat> --}}
            <div>
                <a href="{{ route('admin.add-patient') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Tambah Pasien
                </a>
                {{-- <a href="{{ route('admin.add-staff') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Tambah Dokter/Suster
                </a> --}}
            </div>
        </div>
        <!-- Form Tambah Konseling -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
            {{-- untuk form tambahkan search dropdown berdasarkan rekam medis. isi formnya nomor rekam medis, nama pasien tanggal, pilih doketer/suster(berupa dropdown), dan catatan --}}
            <form action="{{ route('admin.store-schedule') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="medical_record_number"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. Rekam Medis</label>
                        <input type="text" id="medical_record_number" name="medical_record_number" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="patient_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                            Pasien</label>
                        <input type="text" id="patient_name" name="patient_name" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="session_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Waktu
                            Konseling</label>
                        <input type="datetime-local" id="session_time" name="session_time" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="staff_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dokter/Suster</label>
                        <select id="staff_id" name="staff_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            <option value="">Pilih Dokter/Suster</option>
                            {{-- @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
