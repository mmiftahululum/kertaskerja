<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Form Tambah Karyawan</h3>
                    <a href="{{ route('karyawans') }}" class="text-sm text-indigo-600 hover:underline">Kembali ke daftar</a>
                </div>

                @if ($errors->any())
                    <div class="mb-4">
                        <div class="text-sm text-red-700 bg-red-50 border border-red-100 p-3 rounded">
                            <strong class="font-semibold">Terjadi kesalahan:</strong>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('karyawans.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="nama_karyawan" class="block text-sm font-medium text-gray-700">Nama Karyawan</label>
                        <input type="text" name="nama_karyawan" id="nama_karyawan" value="{{ old('nama_karyawan') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Contoh: Budi Santoso" required>
                    </div>

                    <div>
                        <label for="username_git" class="block text-sm font-medium text-gray-700">Username Git</label>
                        <input type="text" name="username_git" id="username_git" value="{{ old('username_git') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="username_git" required>
                    </div>

                    <div>
                        <label for="username_vpn" class="block text-sm font-medium text-gray-700">Username VPN (opsional)</label>
                        <input type="text" name="username_vpn" id="username_vpn" value="{{ old('username_vpn') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="username_vpn">
                    </div>

                    <div>
                        <label for="tanggal_berakhir_kontrak" class="block text-sm font-medium text-gray-700">Tanggal Berakhir Kontrak (opsional)</label>
                        <input type="date" name="tanggal_berakhir_kontrak" id="tanggal_berakhir_kontrak" value="{{ old('tanggal_berakhir_kontrak') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6">
                        <a href="{{ route('karyawans') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
