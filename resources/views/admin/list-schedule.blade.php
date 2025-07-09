@extends('layouts.admin-app')
@section('title', 'List Counseling')

@section('content')
    // edit styling mengguknan flowbit css
    <div class="container mx-auto px-4 py-6">
        <div class="h-20"></div>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-black">Daftar Konseling</h2>
            <div class="flex gap-2">
                <!-- Pencarian Konseling -->
                <input type="text" placeholder="Cari Konseling..."
                    class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                <!-- Tombol Tambah Konseling -->
                <a class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition"
                    href="{{ route('admin.add-schedule') }}">
                    Tambah Jadwal
                </a>
            </div>
        </div>

        <!-- Tabel -->
        <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-300">
            <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3">No. Rekam Medis</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Waktu</th>
                    <th class="px-6 py-3">Dokter/Perawat</th>
                    <th class="px-6 py-3">Catatan</th>
                    <th class="px-6 py-3">Status Notifikasi</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach ($counselings as $counseling)
                <tr>
                    <td>{{ $counseling->medical_record_number }}</td>
                    <td>{{ $counseling->patient_name }}</td>
                    <td>{{ $counseling->session_time }}</td>
                    <td>{{ $counseling->staff_name }}</td>
                    <td>{{ $counseling->notes }}</td>
                    <td>{{ $counseling->notification_status }}</td>
                    <td>
                        <a href="{{ route('admin.edit-counseling', $counseling->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('admin.destroy-counseling', $counseling->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach --}}
            </tbody>
        </table>
    @endsection
