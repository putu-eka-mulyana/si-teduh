@extends('layouts.app')
@section('title', 'Home Page')

@section('content')
    <section
        class="bg-center bg-no-repeat bg-[url('https://flowbite.s3.amazonaws.com/docs/jumbotron/conference.jpg')] bg-gray-700 bg-blend-multiply">
        <div class="px-4 mx-auto max-w-screen-xl text-center py-24 lg:py-56">
            <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-white md:text-5xl lg:text-6xl">lebih
                mudah menjangkau pasien</h1>
            <p class="mb-8 text-lg font-normal text-gray-300 lg:text-xl sm:px-16 lg:px-48">silahkan gunakan aplikasi
                ini<br>untuk mengingatkan anda untuk selalu kontrol dan
                cek kesehatan</p>
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0">
                <a href="#"
                    class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                    <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4" />
                    </svg>
                    <span>UNDUH</span>
                </a>
            </div>
        </div>
    </section>


    <!-- 3 Info Boxes -->
    <section class="container mx-auto px-4 py-8 grid md:grid-cols-3 gap-4 text-center text-sm text-gray-700">
        <div class="bg-white shadow-md p-4 rounded-md">
            Si - teduh di rancang untuk mengingatkan pasien seperti jadwal untuk mengambil obat di puskesmas
        </div>
        <div class="bg-white shadow-md p-4 rounded-md">
            Memberikan fasilitas konsultasi dan edukasi kepada pasien mengenai HIV
        </div>
        <div class="bg-white shadow-md p-4 rounded-md">
            Puskesmas kami berada di lokasi karang pule, klik info mendapatkan pelayanan yang lebih baik silakan datang ke
            puskesmas karang pule. Info ada <a href="https://maps.app.goo.gl/NXi242M7fjv1NGYw7"
                class="text-blue-600 hover:underline">di sini</a>
        </div>
    </section>

    <!-- Penjelasan SI-TEDUH -->
    <section class="container mx-auto px-4 py-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">SI-TEDUH</h2>
            <p class="mb-2 text-gray-600 dark:text-gray-300">
                SI-TEDUH adalah pendekatan sistem informasi berbasis digital dan komunikasi untuk meningkatkan efektivitas
                penanganan HIV/AIDS melalui dukungan interaksi.
            </p>
            <h3 class="font-semibold text-gray-800 dark:text-white mb-2">Tujuan Utama SI-TEDUH:</h3>
            <ul class="list-disc ml-5 text-gray-600 dark:text-gray-300 space-y-1">
                <li>Meningkatkan kepatuhan pasien ODHA terhadap terapi ARV</li>
                <li>Mengingatkan jadwal dan lokasi pengambilan layanan primer bagi pengguna layanan</li>
                <li>Menjangkau stigma melalui notifikasi dan konseling</li>
                <li>Mendeteksi relaps dan status klinis pengguna</li>
                <li>Menetapkan ODHA sebagai pengguna aktif layanan dan informasi kesehatan</li>
            </ul>

            <h3 class="font-semibold text-gray-800 dark:text-white mt-4 mb-2">SI-TEDUH terdiri dari:</h3>
            <ul class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-2 text-gray-600 dark:text-gray-300">
                <li>S – Sistem Informasi</li>
                <li>I – Informasi Edukasi</li>
                <li>T – Tata Laksana Kontrol dan Infeksi Opportunistik</li>
                <li>E – Edukasi Pasien</li>
                <li>D – Dukungan Psikososial</li>
                <li>U – Uji laboratorium dan tindak lanjut</li>
                <li>H – Hubungan berkualitas dan tindak lanjut</li>
            </ul>
        </div>
    </section>
@endsection
