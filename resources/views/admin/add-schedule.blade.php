@extends('layouts.app')
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
                    {{-- <div>
                        <label for="medical_record_number"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. Rekam Medis</label>
                        <input type="text" id="medical_record_number" name="medical_record_number" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    </div> --}}
                    <div class="relative">
                        <label for="medical_record_number_input"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            No. Rekam Medis
                        </label>
                        <input type="text" id="medical_record_number_input" name="medical_record_number_input" required
                            placeholder="Cari rekam medis..." autocomplete="off"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" />

                        <!-- Dropdown yang tidak mendorong elemen di bawah -->
                        <div id="medical_record_number_dropdown"
                            class="absolute top-full left-0 mt-1 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-full z-10 max-h-48 overflow-auto dark:bg-gray-700 dark:border-gray-600">
                            <input type="text" id="medical_record_number_search_inner" placeholder="Cari..."
                                class="block w-full p-2 text-sm border-b border-gray-200 bg-gray-50 dark:bg-gray-600 dark:text-white dark:border-gray-500" />
                            <ul id="medical_record_number_list"
                                class="text-sm text-gray-700 dark:text-gray-200 px-3 py-2 max-h-40 overflow-y-auto"></ul>
                            <div id="medical_record_number_no_res"
                                class="p-3 text-sm text-gray-500 dark:text-gray-400 hidden">
                                Tidak ada hasil silahkan tambahkan pasien baru.
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="patient_id" id="patient_id">
                    <div>
                        <label for="schedule_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe
                            Jadwal</label>
                        <select id="schedule_type" name="type" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            <option value="" disabled selected>Pilih tipe jadwal</option>
                            <option value="edukasi">Edukasi</option>
                            <option value="konsultasi">Konsultasi</option>
                            <option value="ambil obat">Ambil Obat</option>
                        </select>
                    </div>
                    {{-- <div>
                        <label for="patient_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                            Pasien</label>
                        <input type="text" id="patient_name" name="patient_name" required readonly
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    </div> --}}
                    <div>
                        <label for="session_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Waktu
                            Konseling</label>
                        <input type="datetime-local" id="session_time" name="session_time" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="relative">
                        <label for="staff_input" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Dokter/Suster
                        </label>
                        <input type="text" id="staff_input" name="staff_name" required placeholder="Cari atau tambah..."
                            autocomplete="off"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" />

                        <!-- Dropdown yang tidak mendorong elemen di bawah -->
                        <div id="staff_dropdown"
                            class="absolute top-full left-0 mt-1 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-full z-10 max-h-48 overflow-auto dark:bg-gray-700 dark:border-gray-600">
                            <input type="text" id="staff_search_inner" placeholder="Cari..."
                                class="block w-full p-2 text-sm border-b border-gray-200 bg-gray-50 dark:bg-gray-600 dark:text-white dark:border-gray-500" />
                            <ul id="staff_list"
                                class="text-sm text-gray-700 dark:text-gray-200 px-3 py-2 max-h-40 overflow-y-auto"></ul>
                            <div id="staff_no_res" class="p-3 text-sm text-gray-500 dark:text-gray-400 hidden">
                                Tidak ada hasil. <button type="button" id="btn_add_new_staff"
                                    class="text-blue-600 hover:underline" data-modal-target="modal_add_new_staff"
                                    data-modal-toggle="modal_add_new_staff">
                                    Tambah baru
                                </button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="officer_id" id="officer_id">

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pesan
                            Notifikasi</label>
                        <textarea id="message" name="message" rows="4" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">Halo, jadwal Anda telah berhasil dibuat. Silakan hadir sesuai waktu yang telah ditentukan. Terima kasih.</textarea>
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


    <!-- Modal -->
    <div id="modal_add_new_staff" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="modal_add_new_staff">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1l12 12M13 1L1 13" />
                    </svg>
                    <span class="sr-only">Close</span>
                </button>
                <div class="px-6 py-6 lg:px-8">
                    <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Tambah Staff Baru</h3>
                    <form id="form_add_new_staff" class="space-y-4">
                        <div>
                            <label for="staff_name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                                Lengkap</label>
                            <input type="text" name="staff_name" id="staff_name" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Masukkan nama lengkap">
                        </div>
                        <div>
                            <label for="staff_position"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jabatan</label>
                            <input type="text" name="staff_position" id="staff_position" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Masukkan jabatan">
                        </div>
                        <button type="submit"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        // medical record number
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('medical_record_number_input');
            const dropdown = document.getElementById('medical_record_number_dropdown');
            const searchInner = document.getElementById('medical_record_number_search_inner');
            const listElm = document.getElementById('medical_record_number_list');
            const noRes = document.getElementById('medical_record_number_no_res');

            var data = [];

            // Fetch data dari backend
            function getData(query = "") {
                console.log("query = " + query)
                fetch('/api/patients?search=' +
                        query)
                    .then(res => res.json())
                    .then(json => {
                        data = json;
                        renderList(data);
                    })
                    .catch(console.error);
            }
            getData()

            function renderList(items) {
                listElm.innerHTML = '';
                if (items.length === 0) {
                    noRes.classList.remove('hidden');
                } else {
                    noRes.classList.add('hidden');
                    items.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = `${item.medical_record_number} - ${item.fullname}`;
                        li.className =
                            'p-2 rounded-sm hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer';
                        li.addEventListener('click', () => {
                            input.value = `${item.medical_record_number} - ${item.fullname}`;
                            dropdown.classList.add('hidden');
                            document.getElementById('patient_id').value = item.id;
                            // jika perlu simpan ID hidden
                        });
                        listElm.appendChild(li);
                    });
                }
            }

            // Tampilkan dropdown saat input fokus
            input.addEventListener('focus', () => {
                dropdown.classList.remove('hidden');
                searchInner.value = '';
                renderList(data);
                searchInner.focus();
            });

            // Tutup dropdown saat klik di luar
            document.addEventListener('click', e => {
                if (!dropdown.contains(e.target) && e.target !== input) {
                    dropdown.classList.add('hidden');
                }
            });

            // Filter live search
            searchInner.addEventListener('input', () => {
                const q = searchInner.value.trim().toLowerCase();
                let newData = data.filter(i => i.fullname.toLowerCase().includes(q) || i.position
                    .toLowerCase().includes(q))
                if (newData.length > 0) {
                    renderList(newData);
                } else {
                    getData(q)
                }
            });

        });

        // staff
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('staff_input');
            const dropdown = document.getElementById('staff_dropdown');
            const searchInner = document.getElementById('staff_search_inner');
            const listElm = document.getElementById('staff_list');
            const noRes = document.getElementById('staff_no_res');


            var data = [];

            // Fetch data dari backend
            function getData(query = "") {
                console.log("query = " + query)
                fetch('/api/officers?search=' +
                        query)
                    .then(res => res.json())
                    .then(json => {
                        data = json.data;
                        renderList(data);
                    })
                    .catch(console.error);
            }
            getData()

            function renderList(items) {
                listElm.innerHTML = '';
                if (items.length === 0) {
                    noRes.classList.remove('hidden');
                } else {
                    noRes.classList.add('hidden');
                    items.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = `${item.position} - ${item.fullname}`;
                        li.className =
                            'p-2 rounded-sm hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer';
                        li.addEventListener('click', () => {
                            input.value = `${item.position} - ${item.fullname}`;
                            dropdown.classList.add('hidden');
                            document.getElementById('officer_id').value = item.id;
                            // jika perlu simpan ID hidden
                        });
                        listElm.appendChild(li);
                    });
                }
            }

            // Tampilkan dropdown saat input fokus
            input.addEventListener('focus', () => {
                dropdown.classList.remove('hidden');
                searchInner.value = '';
                renderList(data);
                searchInner.focus();
            });

            // // Tutup dropdown saat klik di luar
            document.addEventListener('click', e => {
                if (!dropdown.contains(e.target) && e.target !== input) {
                    dropdown.classList.add('hidden');
                }
            });

            // Filter live search
            searchInner.addEventListener('input', () => {
                const q = searchInner.value.trim().toLowerCase();
                let newData = data.filter(i => i.fullname.toLowerCase().includes(q) || i.position
                    .toLowerCase().includes(q))
                console.log("newData " + newData.length)
                if (newData.length > 0) {
                    renderList(newData);
                } else {
                    getData(q)
                }
            });

            // Tambah baru bila tidak ditemukan
            // addBtn.addEventListener('click', () => {
            //     const nama = searchInner.value.trim();
            //     if (!nama) return;
            //     // window.location.href = `/staffs/create?name=${encodeURIComponent(nama)}`;
            // });
        });
    </script>

    <script>
        document.getElementById('form_add_new_staff').addEventListener('submit', function(e) {
            e.preventDefault();
            const fullname = document.getElementById('staff_name').value.trim();
            const position = document.getElementById('staff_position').value.trim();

            if (!fullname || !position) return;

            // Kirim AJAX (atau redirect) ke backend Laravel
            fetch('/api/officers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        fullname,
                        position
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert('Staff berhasil ditambahkan!');
                    // Optional: tutup modal
                    document.querySelector('[data-modal-hide="modal_add_new_staff"]').click();
                    // Clear form
                    e.target.reset();
                    // Tambahkan ke dropdown jika perlu...
                })
                .catch(err => {
                    alert('Gagal menambahkan staff.');
                    console.error(err);
                });
        });
    </script>

@endsection
