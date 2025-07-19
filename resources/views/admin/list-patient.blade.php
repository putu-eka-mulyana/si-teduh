@extends('layouts.app')

@section('title', 'List Patient')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="h-20"></div>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-black ">Daftar Pasien</h2>
            <div class="flex gap-2">
                <!-- Pencarian Pasien -->
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
                <!-- Tombol Tambah Pasien -->
                <a class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition"
                    href="{{ route('admin.add-patient') }}">
                    Tambah Pasien
                </a>
            </div>
        </div>
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="mb-4 text-green-600">
                {{ session('success') }}
            </div>
        @endif

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
                        <th class="px-6 py-3">Tanggal Lahir</th>
                        <th class="px-6 py-3">Umur</th>
                        <th class="px-6 py-3">Pekerjaan</th>
                        <th class="px-6 py-3">Alamat</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($patients as $patient)
                        <tr class="border-b last:border-0 dark:border-gray-700">
                            <td class="px-6 py-4">{{ $patient->medical_record_number }}</td>
                            <td class="px-6 py-4">{{ $patient->nik }}</td>
                            <td class="px-6 py-4">{{ $patient->bpjs_number }}</td>
                            <td class="px-6 py-4">{{ $patient->phone_number }}</td>
                            <td class="px-6 py-4">{{ $patient->fullname }}</td>
                            <td class="px-6 py-4">{{ $patient->gender }}</td>
                            <td class="px-6 py-4">{{ $patient->birthday }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($patient->birthday)->age }}</td>
                            <td class="px-6 py-4">{{ $patient->job_title }}</td>
                            <td class="px-6 py-4">{{ $patient->address }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.delete-patient', $patient->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pasien ini?');"
                                    class="mb-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition w-full">
                                        Hapus
                                    </button>
                                </form>
                                <a href="{{ route('admin.edit-patient', $patient->id) }}"
                                    class="block w-full px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition text-center">
                                    Edit
                                </a>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col md:flex-row items-center justify-between mt-6 gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan <span
                    class="font-semibold text-gray-900">{{ $patients->firstItem() }}-{{ $patients->lastItem() }}</span>
                dari
                <span class="font-semibold text-gray-900">{{ $total }}</span>
            </span>
            <div class="mt-4 md:mt-0">
                {{ $patients->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
@endsection
