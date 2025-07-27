@extends('layouts.app')
@section('title', 'Selamat Datang')


@section('content')
    <div class="container mx-auto p-4">
        <div class="h-16"></div>
        <h1 class="text-3xl font-bold mb-6">Selamat Datang <span
                class="text-blue-700">{{ Auth::user()->patient->fullname }}</span>
            di SI - TEDUH</h1>
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                <span class="font-medium">Success!</span> {{ session('success') }}
            </div>
        @endif

        <section class="bg-white rounded shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Jadwal Untuk Anda</h2>
            @php
                // $jadwals = $schedules;
                // ?? [
                //     (object) [
                //         'jenis_notifikasi' => 'Pengingat Obat',
                //         'nama_pasien' => 'Budi Santoso',
                //         'alamat' => 'Jl. Merpati No. 12',
                //         'tanggal_jam' => '2024-06-10 08:00',
                //         'pesan' => 'Jangan lupa minum obat pagi.',
                //     ],
                //     (object) [
                //         'jenis_notifikasi' => 'Kontrol Dokter',
                //         'nama_pasien' => 'Siti Aminah',
                //         'alamat' => 'Jl. Kenanga No. 5',
                //         'tanggal_jam' => '2024-06-11 10:30',
                //         'pesan' => 'Jadwal kontrol ke dokter umum.',
                //     ],
                // ];
                // $perPage = 2;
                // $page = request()->get('page', 1);
                // $total = count($jadwals);
                // $pages = ceil($total / $perPage);
                // $pagedJadwals = collect($jadwals)->slice(($page - 1) * $perPage, $perPage);
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($schedules as $schedule)
                    <div
                        class="flex flex-col gap-2 rounded-xl border border-gray-200 bg-white p-6 shadow-md transition hover:shadow-lg flowbite-card">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-black-700 text-bold">Jenis Notifikasi:</span>
                            <span class="text-sm">{{ $schedule->type }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700">Nama Pasien:</span>
                            <span class="text-sm">{{ $schedule->patient->fullname }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700">Lokasi:</span>
                            <span class="text-sm"><a class="text-blue-700"
                                    href="https://maps.app.goo.gl/EJmQHdoKNn5M2FDg7">Puskesmas Karang
                                    Pule</a></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700">Tanggal/Jam:</span>
                            <span class="text-sm">
                                {{ \Carbon\Carbon::parse($schedule->datetime)->locale('id')->translatedFormat('l, d F Y H:i') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700">Pesan:</span>
                            <span class="text-sm">{{ $schedule->message }}</span>
                        </div>
                        @if ($schedule->status == 1)
                            <form method="POST" action="{{ route('schedule.confirm', ['id' => $schedule->id]) }}"
                                class="mt-4">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                    Konfirmasi Akan Datang
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="col-span-2 text-center text-gray-500">Tidak ada jadwal notifikasi.</div>
                @endforelse
            </div>

            {{-- Pagination --}}
            {{-- @if ($pages > 1)
                <div class="flex justify-center mt-6">
                    <nav>
                        <ul class="inline-flex items-center -space-x-px text-sm flowbite-pagination">
                            @for ($i = 1; $i <= $pages; $i++)
                                <li>
                                    <a href="?page={{ $i }}"
                                        class="px-3 py-2 leading-tight {{ $i == $page ? 'text-white bg-blue-600 border border-blue-600' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 hover:text-blue-700' }} rounded-md mx-1 transition">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor
                        </ul>
                    </nav>
                </div>
            @endif --}}
        </section>
    </div>
@endsection
