<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen flex flex-col">
    <nav class="bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="{{ route('beranda') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="{{ url('/images/logo-puskesmas.png') }}" class="h-8" alt="Puskesmas">
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">SI - TEDUH</span>
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                @if (auth()->check())
                    @if (auth()->user()->role === 'USER')
                        <!-- Notification Status Indicator -->
                        <div class="flex items-center mr-3">
                            <div id="notification-status"
                                class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm cursor-pointer hover:opacity-80 transition-opacity"
                                onclick="showNotificationModal()">
                                <span id="status-icon" class="w-4 h-4 rounded-full"></span>
                                <span id="status-text" class="hidden md:inline">Notifikasi</span>
                            </div>

                        </div>
                    @endif
                    <a href="{{ route('logout') }}">
                        <button type="button"
                            class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">

                            Logout</button>
                    </a>
                @else
                    <a href="{{ route('login') }}">
                        <button type="button"
                            class="text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Login</button>
                    </a>
                @endif
                <button data-collapse-toggle="navbar-sticky" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                    aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>
            </div>
            <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                {{-- buat menu navbar aktif berdasarkan route --}}
                @php
                    $currentRoute = request()->route()->getName();
                @endphp
                {{-- {{ dd(request()->route()->getPrefix()) }} --}}
                @if (request()->route()->getPrefix() == '/admin')
                    <ul
                        class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                        <li>
                            <a href="{{ route('admin.list-schedule') }}"
                                class="block py-2 px-3 rounded-sm md:p-0
                            {{ $currentRoute == 'admin.list-schedule' ? 'text-white bg-blue-700 md:bg-transparent md:text-blue-700 md:dark:text-blue-500' : 'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700' }}"
                                aria-current="{{ $currentRoute == 'admin.list-schedule' ? 'page' : '' }}">
                                Daftar Jadwal
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.list-patient') }}"
                                class="block py-2 px-3 rounded-sm md:p-0
                            {{ $currentRoute == 'admin.list-patient' ? 'text-white bg-blue-700 md:bg-transparent md:text-blue-700 md:dark:text-blue-500' : 'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700' }}">
                                Daftar Pasien
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.list') }}"
                                class="block py-2 px-3 rounded-sm md:p-0
                            {{ $currentRoute == 'admin.list' ? 'text-white bg-blue-700 md:bg-transparent md:text-blue-700 md:dark:text-blue-500' : 'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700' }}">
                                Daftar Admin
                            </a>
                        </li>
                    </ul>
                @elseif (request()->route()->getPrefix() == '/user')
                    <ul
                        class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                        {{-- <li>
                            <a href="{{ route('beranda') }}"
                                class="block py-2 px-3 rounded-sm md:p-0
                                {{ $currentRoute == 'beranda' ? 'text-white bg-blue-700 md:bg-transparent md:text-blue-700 md:dark:text-blue-500' : 'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700' }}"
                                aria-current="{{ $currentRoute == 'beranda' ? 'page' : '' }}">
                                Beranda
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('edukasi-hiv') }}"
                                class="block py-2 px-3 rounded-sm md:p-0
                                {{ $currentRoute == 'edukasi-hiv' ? 'text-white bg-blue-700 md:bg-transparent md:text-blue-700 md:dark:text-blue-500' : 'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700' }}">
                                Edukasi Hiv
                            </a>
                        </li> --}}
                    </ul>
                @else
                    <ul
                        class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                        <li>
                            <a href="{{ route('beranda') }}"
                                class="block py-2 px-3 rounded-sm md:p-0
                                {{ $currentRoute == 'beranda' ? 'text-white bg-blue-700 md:bg-transparent md:text-blue-700 md:dark:text-blue-500' : 'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700' }}"
                                aria-current="{{ $currentRoute == 'beranda' ? 'page' : '' }}">
                                Beranda
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('edukasi-hiv') }}"
                                class="block py-2 px-3 rounded-sm md:p-0
                                {{ $currentRoute == 'edukasi-hiv' ? 'text-white bg-blue-700 md:bg-transparent md:text-blue-700 md:dark:text-blue-500' : 'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700' }}">
                                Edukasi Hiv
                            </a>
                        </li>
                    </ul>
                @endif

            </div>
        </div>
    </nav>

    <main class="flex-1 pt-16">
        @yield('content') <!-- Tempat konten dinamis ditampilkan -->
    </main>

    <footer class="bg-gray-800 mt-auto">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <div class="text-white text-center py-4">
                <p>&copy; {{ date('Y') }} Puskesmas Karang Pule</p>
            </div>
            <div class="flex justify-end space-x-4">
                <img src="{{ asset('images/universitas-jember.jpeg') }}" alt="Logo Universitas Jember"
                    class="rounded-full w-10 h-10">
                <img src="{{ asset('images/dinaskota-mataram.jpeg') }}" alt="Logo Dinas Kota Mataram"
                    class="rounded-full w-10 h-10">
                <img src="{{ asset('images/puskesmas-karang-pule.jpeg') }}" alt="Logo Puskesmas Karang Pule"
                    class="rounded-full w-10 h-10">
            </div>
    </footer>

    <!-- Flowbite Alert untuk Izin Push Notification -->
    {{-- <div id="push-permission-alert"
        class="hidden fixed bottom-4 right-4 z-50 w-full max-w-xs bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <div class="flex p-4">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-blue-700 dark:text-blue-800" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zm-2 14a2 2 0 104 0H8z" />
                </svg>
            </div>
            <div class="ml-3 text-sm font-normal text-gray-700 dark:text-gray-200">
                Aktifkan notifikasi agar tidak ketinggalan informasi penting dari SI TEDUH.
            </div>
        </div>
        <div class="flex justify-end px-4 pb-4">
            <button id="allow-push-btn"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Izinkan
            </button>
        </div>
    </div> --}}


    {{-- test --}}
    <!-- Modal -->
    <div id="push-permission-modal" tabindex="-1"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">

                <!-- Close -->
                <button type="button"
                    class="absolute top-3 right-3 text-gray-400 hover:bg-gray-200 rounded-lg p-1.5 dark:hover:bg-gray-600"
                    data-modal-hide="push-permission-modal">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>

                <!-- Body -->
                <div class="p-6 text-center">
                    <svg class="mx-auto mb-4 text-blue-600 w-12 h-12 dark:text-blue-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>

                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
                        Aktifkan Notifikasi SI TEDUH
                    </h3>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Dapatkan informasi penting tentang jadwal konsultasi, edukasi HIV, dan update terbaru dari SI
                        TEDUH.
                    </p>

                    <div class="mb-5 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-left">
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            <strong>Yang akan Anda dapatkan:</strong>
                        </p>
                        <ul class="text-xs text-blue-600 dark:text-blue-400 mt-1 space-y-1">
                            <li>• Pengingat jadwal konsultasi</li>
                            <li>• Update edukasi HIV terbaru</li>
                            <li>• Informasi penting dari puskesmas</li>
                            <li>• Notifikasi real-time</li>
                        </ul>
                    </div>

                    <div class="flex justify-center space-x-2">
                        <button id="btn-allow-push"
                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="btn-text">Izinkan Notifikasi</span>
                            <span id="btn-loading" class="hidden">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                        <button data-modal-hide="push-permission-modal"
                            class="text-gray-500 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-sm px-4 py-2 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            Nanti saja
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Trigger hidden (Flowbite akan otomatis inisialisasi) -->
    <button id="dummy-trigger" data-modal-target="push-permission-modal" data-modal-toggle="push-permission-modal"
        class="hiddenx"></button>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    @if (auth()->check() && auth()->user()->role === 'USER')
        <script>
            window.userId = {{ auth()->user()->id }};
        </script>
        <script src="{{ asset('js/push-notification.js') }}"></script>
        {{-- <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Tampilkan alert jika permission belum granted
                if ('Notification' in window && Notification.permission !== 'granted') {
                    document.getElementById('push-permission-alert').classList.remove('hidden');
                }


                document.getElementById('allow-push-btn').addEventListener('click', async function() {
                    const permission = await Notification.requestPermission();
                    if (permission === 'granted') {
                        document.getElementById('push-permission-alert').classList.add('hidden');
                        // Inisialisasi ulang push notification jika perlu
                        if (window.pushHandler && typeof window.pushHandler.init === 'function') {
                            window.pushHandler.init();
                        }
                    }
                });
            });
        </script> --}}

        <script>
            // Function untuk menampilkan modal permission secara manual
            function showNotificationModal() {
                if (Notification.permission === 'default') {
                    // Tampilkan modal permission
                    document.getElementById('dummy-trigger').click();
                } else if (Notification.permission === 'denied') {
                    // Tampilkan instruksi untuk mengaktifkan manual
                    showErrorMessage('Notifikasi diblokir. Silakan aktifkan manual di pengaturan browser.');
                } else if (Notification.permission === 'granted') {
                    // Tampilkan status sukses
                    showSuccessMessage('Notifikasi sudah aktif!');
                }
            }

            // Function untuk test notifikasi
            function testNotification() {
                if (Notification.permission !== 'granted') {
                    showErrorMessage('Permission notifikasi belum diberikan. Silakan aktifkan dulu.');
                    return;
                }

                if (window.pushHandler) {
                    // Test notifikasi langsung
                    window.pushHandler.testNotification();

                    // Test push notification via service worker
                    setTimeout(() => {
                        window.pushHandler.sendTestPushNotification();
                    }, 1000);

                    showSuccessMessage('Test notifikasi dikirim! Cek console untuk detail.');
                } else {
                    showErrorMessage('Push handler belum tersedia.');
                }
            }

            // Function untuk refresh service worker
            function refreshServiceWorker() {
                if (window.pushHandler) {
                    window.pushHandler.forceRefreshServiceWorker().then((success) => {
                        if (success) {
                            showSuccessMessage('Service worker berhasil di-refresh!');
                            // Refresh halaman setelah beberapa detik
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            showErrorMessage('Gagal refresh service worker.');
                        }
                    });
                } else {
                    showErrorMessage('Push handler belum tersedia.');
                }
            }

            // Function untuk debug notifikasi
            function debugNotification() {
                console.log('=== DEBUG NOTIFICATION ===');

                // 1. Check permission
                console.log('1. Notification Permission:', Notification.permission);

                // 2. Check service worker
                if (window.pushHandler && window.pushHandler.swRegistration) {
                    console.log('2. Service Worker Registration:', window.pushHandler.swRegistration);
                    console.log('2a. Service Worker Active:', window.pushHandler.swRegistration.active);
                    console.log('2b. Service Worker Waiting:', window.pushHandler.swRegistration.waiting);
                    console.log('2c. Service Worker Installing:', window.pushHandler.swRegistration.installing);
                } else {
                    console.log('2. Service Worker: NOT AVAILABLE');
                }

                // 3. Check push handler
                if (window.pushHandler) {
                    console.log('3. Push Handler:', window.pushHandler);
                    console.log('3a. Is Supported:', window.pushHandler.isSupported);
                    console.log('3b. Is Initialized:', window.pushHandler.isInitialized);
                } else {
                    console.log('3. Push Handler: NOT AVAILABLE');
                }

                // 4. Check subscription
                if (window.pushHandler && window.pushHandler.swRegistration) {
                    window.pushHandler.swRegistration.pushManager.getSubscription().then(subscription => {
                        if (subscription) {
                            console.log('4. Push Subscription:', {
                                endpoint: subscription.endpoint,
                                keys: subscription.keys
                            });
                        } else {
                            console.log('4. Push Subscription: NOT SUBSCRIBED');
                        }
                    });
                }

                // 5. Test minimal notification
                if (Notification.permission === 'granted') {
                    try {
                        const testNotif = new Notification('Debug Test', {
                            body: 'Ini adalah test minimal notification',
                            icon: '/images/logo-puskesmas.png'
                        });
                        console.log('5. Minimal Notification: SUCCESS');
                        testNotif.onshow = () => console.log('5a. Notification shown');
                        testNotif.onerror = (e) => console.log('5a. Notification error:', e);
                        setTimeout(() => testNotif.close(), 3000);
                    } catch (error) {
                        console.log('5. Minimal Notification: FAILED -', error);
                    }
                } else {
                    console.log('5. Minimal Notification: SKIPPED (permission not granted)');
                }

                // 6. Check server subscription
                fetch('/debug/webpush', {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('6. Server Subscription Status:', data);
                    })
                    .catch(error => {
                        console.log('6. Server Subscription Check: FAILED -', error);
                    });

                console.log('=== END DEBUG ===');
                showSuccessMessage(
                    'Debug info telah ditampilkan di console. Buka Developer Tools → Console untuk melihat detail.');
            }

            // Helper functions untuk feedback (global scope)
            function showSuccessMessage(message) {
                // Buat toast sukses sederhana
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg';
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }

            function showErrorMessage(message) {
                // Buat toast error sederhana
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg';
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }

            document.addEventListener('DOMContentLoaded', () => {
                // Function untuk update status notifikasi
                function updateNotificationStatus() {
                    const statusIcon = document.getElementById('status-icon');
                    const statusText = document.getElementById('status-text');
                    const statusContainer = document.getElementById('notification-status');

                    if (statusIcon && statusText && statusContainer) {
                        if (Notification.permission === 'granted') {
                            statusIcon.className = 'w-4 h-4 rounded-full bg-green-500';
                            statusText.textContent = 'Notifikasi Aktif';
                            statusContainer.className =
                                'flex items-center space-x-2 px-3 py-2 rounded-lg text-sm bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 cursor-pointer hover:opacity-80 transition-opacity';
                        } else if (Notification.permission === 'denied') {
                            statusIcon.className = 'w-4 h-4 rounded-full bg-red-500';
                            statusText.textContent = 'Notifikasi Diblokir';
                            statusContainer.className =
                                'flex items-center space-x-2 px-3 py-2 rounded-lg text-sm bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 cursor-pointer hover:opacity-80 transition-opacity';
                        } else {
                            statusIcon.className = 'w-4 h-4 rounded-full bg-yellow-500';
                            statusText.textContent = 'Notifikasi Belum Diaktifkan';
                            statusContainer.className =
                                'flex items-center space-x-2 px-3 py-2 rounded-lg text-sm bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 cursor-pointer hover:opacity-80 transition-opacity';
                        }
                    }
                }

                // Update status saat halaman dimuat
                updateNotificationStatus();

                // Check if user has already seen the modal
                const hasSeenModal = localStorage.getItem('si-teduh-notification-modal-shown');

                // Tampilkan modal hanya jika permission belum diputuskan dan belum pernah ditampilkan
                if (Notification.permission === 'default' && !hasSeenModal) {
                    // Flowbite akan otomatis membuat instance karena ada data-modal-toggle
                    // Kita cukup trigger click pada tombol 
                    console.log("Menampilkan modal permission notification");
                    document.getElementById('dummy-trigger').click();

                    // Mark modal as shown
                    localStorage.setItem('si-teduh-notification-modal-shown', 'true');
                }

                // Listener izin
                document.getElementById('btn-allow-push')?.addEventListener('click', async () => {
                    const btn = document.getElementById('btn-allow-push');
                    const btnText = document.getElementById('btn-text');
                    const btnLoading = document.getElementById('btn-loading');

                    try {
                        // Disable button dan tampilkan loading
                        btn.disabled = true;
                        btnText.classList.add('hidden');
                        btnLoading.classList.remove('hidden');

                        // Gunakan method dari pushHandler
                        const success = await window.pushHandler.requestPermissionAndSubscribe();

                        if (success) {
                            // Tutup modal via Flowbite
                            const btnClose = document.querySelector(
                                '[data-modal-hide="push-permission-modal"]');
                            btnClose?.click();

                            // Update status notifikasi
                            updateNotificationStatus();

                            // Tampilkan pesan sukses
                            console.log('Notification permission granted successfully');

                            // Optional: Tampilkan toast/alert sukses
                            showSuccessMessage('Notifikasi berhasil diaktifkan!');
                        } else {
                            console.log('Failed to get notification permission');
                            showErrorMessage('Gagal mengaktifkan notifikasi. Silakan coba lagi.');
                        }
                    } catch (error) {
                        console.error('Error handling permission request:', error);
                        showErrorMessage('Terjadi kesalahan. Silakan coba lagi.');
                    } finally {
                        // Reset button state
                        btn.disabled = false;
                        btnText.classList.remove('hidden');
                        btnLoading.classList.add('hidden');
                    }
                });

                // Listener untuk tombol "Nanti saja"
                document.querySelector('[data-modal-hide="push-permission-modal"]')?.addEventListener('click', () => {
                    // Mark modal as shown even if user dismisses it
                    localStorage.setItem('si-teduh-notification-modal-shown', 'true');
                });
            });
        </script>
    @endif
</body>

</html>
