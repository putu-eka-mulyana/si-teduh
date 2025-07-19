@extends('layouts.app')
@section('title', 'List Counseling')

@section('content')
    // edit styling mengguknan flowbit css
    <div class="container mx-auto px-4 py-6">
        <div class="h-20"></div>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-black">Daftar Notifikasi</h2>
            <div class="flex gap-2">
                <!-- Pencarian Konseling -->
                <form method="GET" class="flex items-center gap-1">
                    <input type="text" placeholder="Cari Pasien..." name="search"
                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                        value="{{ request('search') }}">
                    <button type="submit"
                        class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                        </svg>
                        Cari
                    </button>
                </form>
                <!-- Tombol Tambah Konseling -->
                <a class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition"
                    href="{{ route('admin.add-schedule') }}">
                    Tambah Jadwal
                </a>
            </div>
        </div>

        <!-- Tabel -->
        <div class="w-full overflow-x-auto bg-white dark:bg-gray-900 rounded-lg shadow">
            <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-300 whitespace-nowrap">
                <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3">No. Rekam Medis</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Kontak / Whatsapp</th>
                        <th class="px-10 py-3 min-w-[100px]">Tipe Jadwal</th>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Dokter/Perawat</th>
                        <th class="px-6 py-3 min-w-[200px]">Status Notifikasi</th>
                        <th class="px-6 py-3">Action</th>
                        <th class="px-6 py-3 max-w-[100px]">Pesan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schedules as $schedule)
                        <tr class="border-b last:border-0 dark:border-gray-700">
                            <td class="px-6 py-4">{{ $schedule->patient->medical_record_number }}</td>
                            <td class="px-6 py-4">{{ $schedule->patient->fullname }}</td>
                            <td class="px-6 py-4">{{ $schedule->patient->phone_number }}</td>
                            <td scope="row" class="px-6 py-4"><span
                                    class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 border {{ $schedule->type == 'edukasi' ? 'dark:text-green-400 border border-green-400' : ($schedule->type == 'konsultasi' ? 'dark:text-blue-400 border border-blue-400' : 'dark:text-red-400 border border-red-400') }}">{{ $schedule->type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($schedule->datetime)->translatedFormat('d F Y H:i') }}
                            </td>
                            <td class="px-6 py-4">{{ $schedule->officer->position }} {{ $schedule->officer->fullname }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm {{ $schedule->status == 1 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : ($schedule->status == 2 ? 'dark:bg-green-900 dark:text-green-300 bg-green-100 text-green-800' : 'dark:bg-gray-900 dark:text-gray-300 bg-gray-100 text-gray-800') }}">{{ $schedule->status == 1 ? 'Belum Dibaca' : ($schedule->status == 2 ? 'Sudah Dibaca' : 'Sesi Selesai') }}</span>
                            </td>

                            <td>
                                {{-- <a href="{{ route('admin.edit-counseling', $counseling->id) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('admin.destroy-counseling', $counseling->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form> --}}
                                {{-- buat buttin untuk update status selesai --}}
                                @if ($schedule->status != 3)
                                    <form action="{{ route('admin.update-schedule', $schedule->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menandai jadwal ini sebagai selesai?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-1 text-center me-2 mb-2">Selesai</button>
                                    </form>
                                @endif
                            </td>
                            <td class="px-6 py-4 max-w-[100px]">{{ $schedule->message }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex flex-col md:flex-row items-center justify-between mt-6 gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan <span
                    class="font-semibold text-gray-900">{{ $schedules->firstItem() }}-{{ $schedules->lastItem() }}</span>
                dari
                <span class="font-semibold text-gray-900">{{ $total }}</span>
            </span>
            <div class="mt-4 md:mt-0 gap-4">
                {{ $schedules->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
@endsection
