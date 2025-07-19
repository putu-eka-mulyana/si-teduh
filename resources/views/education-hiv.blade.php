@extends('layouts.app')
@section('title', 'Edukasi HIV')

@section('content')
    <section class="bg-white dark:bg-gray-900">
        <div class="h-16"></div>
        <div class="py-8 px-4 mx-auto max-w-screen-xl text-center lg:py-16">
            <h1
                class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
                EDUKASI TENTANG HIV</h1>
            <p class="mb-8 text-lg font-normal text-gray-500 lg:text-xl sm:px-16 lg:px-48 dark:text-gray-400">Di sini
                kami memberikan informasi dan edukasi tentang HIV untuk meningkatkan kesadaran dan pemahaman masyarakat.</p>
        </div>
    </section>


    <!-- Main Content -->
    <main class="container mx-auto p-6 space-y-12 max-w-screen-xl">

        <!-- Apa Itu HIV -->
        <section id="apa-itu-hiv">
            <h2 class="text-2xl font-bold mb-3">1. Apa Itu HIV?</h2>
            <p>
                HIV (Human Immunodeficiency Virus) adalah virus yang menyerang sistem kekebalan tubuh, terutama sel CD4 (sel
                T).
                Bila tidak diobati, HIV dapat berkembang menjadi AIDS (Acquired Immunodeficiency Syndrome), yaitu kondisi di
                mana sistem kekebalan tubuh melemah secara signifikan.
            </p>
        </section>

        <!-- Cara Penularan -->
        <section id="penularan">
            <h2 class="text-2xl font-bold mb-3">2. Cara Penularan HIV</h2>
            <p><strong>HIV ditularkan melalui:</strong></p>
            <ul class="mb-4">
                <li>Hubungan seksual berisiko tanpa kondom (vaginal, anal, oral).</li>
                <li>Transfusi darah yang terkontaminasi.</li>
                <li>Jarum suntik yang tidak steril.</li>
                <li>Ibu ke anak saat kehamilan, persalinan, atau menyusui.</li>
                <li>Transplantasi organ atau jaringan dari donor yang terinfeksi HIV.</li>
            </ul>
            <p><strong>HIV TIDAK menular melalui:</strong></p>
            <ul>
                <li>Pelukan, cium pipi, berjabat tangan.</li>
                <li>Makan bersama, berbagi toilet.</li>
                <li>Gigitan nyamuk atau serangga.</li>
            </ul>
        </section>

        <!-- Gejala -->
        <section id="gejala">
            <h2 class="text-2xl font-bold mb-3">3. Gejala Umum HIV</h2>
            <p>Gejala dapat berbeda-beda, tapi umumnya:</p>
            <ul>
                <li>Demam, kelelahan, sakit kepala.</li>
                <li>Pembengkakan kelenjar getah bening.</li>
                <li>Ruam kulit.</li>
                <li>Sariawan atau infeksi jamur mulut berulang.</li>
                <li>Penurunan berat badan tanpa sebab jelas.</li>
            </ul>
            <p class="mt-3">
                Banyak orang tidak menyadari dirinya terinfeksi karena gejalanya samar.
            </p>
        </section>

        <!-- Pencegahan -->
        <section id="pencegahan">
            <h2 class="text-2xl font-bold mb-3">4. Pencegahan HIV</h2>
            <ul>
                <li>Gunakan kondom saat berhubungan seksual.</li>
                <li>Tidak berbagi jarum suntik.</li>
                <li>Lakukan tes HIV secara rutin, terutama bagi yang berisiko tinggi.</li>
                <li>Ibu hamil perlu skrining HIV untuk mencegah penularan ke bayi.</li>
                <li>Gunakan alat pelindung diri saat menangani darah/produk darah.</li>
            </ul>
        </section>

        <!-- Tes dan Diagnosis -->
        <section id="tes">
            <h2 class="text-2xl font-bold mb-3">5. Tes dan Diagnosis HIV</h2>
            <p>Tes HIV dilakukan dengan mengambil sampel darah atau cairan mulut. Ada beberapa jenis tes:</p>
            <ul>
                <li>Tes antibodi (ELISA).</li>
                <li>Tes antigen/antibodi.</li>
                <li>Tes RNA HIV.</li>
            </ul>
            <p class="mt-3">Semua hasil positif perlu dikonfirmasi dengan tes lanjutan.</p>
        </section>

        <!-- Pengobatan -->
        <section id="pengobatan">
            <h2 class="text-2xl font-bold mb-3">6. Pengobatan HIV</h2>
            <p>Belum ada obat yang menyembuhkan HIV, tapi pengobatan ARV (antiretroviral) dapat:</p>
            <ul>
                <li>Menurunkan jumlah virus dalam tubuh (viral load).</li>
                <li>Meningkatkan sistem kekebalan.</li>
                <li>Mencegah penularan ke orang lain.</li>
                <li>Membuat pasien tetap sehat dan produktif.</li>
            </ul>
            <p class="mt-3">
                Pasien harus minum ARV seumur hidup, sesuai anjuran tenaga kesehatan.
            </p>
        </section>

        <!-- Hidup dengan HIV -->
        <section id="hidup">
            <h2 class="text-2xl font-bold mb-3">7. Hidup dengan HIV</h2>
            <p>
                HIV bukan akhir segalanya. Dengan ARV dan gaya hidup sehat, orang dengan HIV (ODHA) bisa hidup normal.
                Dukungan keluarga dan masyarakat sangat penting. Hindari stigma dan diskriminasi terhadap ODHA.
            </p>
        </section>

        <!-- Peran Keluarga dan Masyarakat -->
        <section id="peran">
            <h2 class="text-2xl font-bold mb-3">8. Peran Keluarga dan Masyarakat</h2>
            <ul>
                <li>Memberikan dukungan emosional dan psikologis.</li>
                <li>Membantu ODHA menjaga kepatuhan pengobatan.</li>
                <li>Menyebarkan informasi benar tentang HIV untuk mengurangi stigma.</li>
                <li>Menjadi bagian dari pencegahan dan promosi kesehatan.</li>
            </ul>
        </section>

        <!-- Pesan Kunci -->
        <section id="pesan">
            <h2 class="text-2xl font-bold mb-3">9. Pesan Kunci</h2>
            <ul class="list-disc pl-5 space-y-1">
                <li>✅ HIV bisa dicegah</li>
                <li>✅ HIV bisa dikendalikan dengan ARV</li>
                <li>✅ Tes HIV itu penting</li>
                <li>✅ Jangan diskriminasi ODHA</li>
            </ul>
        </section>

    </main>

@endsection
