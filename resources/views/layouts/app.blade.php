<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    @vite('resources/css/app.css')
</head>

<body>
    <nav class="bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="{{ route('beranda') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="{{ url('/images/logo-puskesmas.png') }}" class="h-8" alt="Puskesmas">
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">SI - TEDUH</span>
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                @if (auth()->check())
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

    <main>
        @yield('content') <!-- Tempat konten dinamis ditampilkan -->
    </main>

    <footer>
        <!-- Footer -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    @if (auth()->check() && auth()->user()->role === 'USER')
        <script>
            window.userId = {{ auth()->user()->id }};
        </script>
        <script src="{{ asset('js/push-notification.js') }}"></script>
    @endif
</body>

</html>
