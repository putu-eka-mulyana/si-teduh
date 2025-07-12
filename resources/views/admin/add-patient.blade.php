@extends('layouts.admin-app')
@section('title', 'Registrasi Pasien')
@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="h-20"></div>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-black">Registrasi Pasien</h2>
        </div>

        <!-- Form Registrasi Pasien -->
        @if ($errors->any())
            <div
                class="mb-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-red-800 dark:bg-red-900 dark:text-red-200 dark:border-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @php
            $id = $id ?? null;
        @endphp

        <form action="{{ $id ? route('admin.update-patient', $id) : route('admin.store-patient') }}" method="POST"
            class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
            @csrf
            <input type="hidden" name="role" value="USER">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="medical_record_number"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomer Rekam
                        medis</label>
                    <input type="text" id="medical_record_number" name="medical_record_number" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                        value="{{ $id ? $patient->medical_record_number : old('medical_record_number') }}">
                </div>
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIK</label>
                    <input type="text" id="nik" name="nik" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                        value="{{ $id ? $patient->nik : old('nik') }}">
                </div>
                <div>
                    <label for="bpjs_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">No.
                        BPJS</label>
                    <input type="text" id="bpjs_number" name="bpjs_number"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                        value="{{ $id ? $patient->bpjs_number : old('bpjs_number') }}">
                </div>
                <div>
                    <label for="fullname" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                        Lengkap</label>
                    <input type="text" id="fullname" name="fullname" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                        value="{{ $id ? $patient->fullname : old('fullname') }}">
                </div>
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis
                        Kelamin</label>
                    <select id="gender" name="gender" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="M" {{ $id && $patient->gender == 'M' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="F" {{ $id && $patient->gender == 'F' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label for="birthday" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                        lahir</label>
                    <input type="date" id="birthday" name="birthday" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                        value="{{ $id ? $patient->birthday : old('birthday') }}">
                </div>
                <div>
                    <label for="job_title"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pekerjaan</label>
                    <input type="text" id="job_title" name="job_title" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                        value="{{ $id ? $patient->job_title : old('job_title') }}">
                </div>
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomer
                        WA/Telp</label>
                    <input type="text" id="phone_number" name="phone_number" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                        value="{{ $id ? $patient->phone_number : old('phone_number') }}">
                </div>
                <div>
                    <label for="password"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input type="password" id="password" name="password" {{ $id ? '' : 'required' }}
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="password_confirmation"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        {{ $id ? '' : 'required' }}
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>


                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                    <textarea id="address" name="address" rows="3" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">{{ $id ? $patient->address : old('address') }}</textarea>
                </div>

            </div>
            <div class="mt-6">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition">
                    {{ $id ? 'Update Pasien' : 'Daftar Pasien' }}
                </button>
            </div>
        </form>
    </div>
@endsection
