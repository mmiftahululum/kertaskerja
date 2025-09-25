<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Daftar Karyawan</h3>

                        <div class="flex items-center gap-2">
                            <form method="GET" action="{{ route('karyawans') }}" class="flex items-center gap-2">
                                <input
                                    type="search"
                                    name="q"
                                    value="{{ request('q') }}"
                                    placeholder="Cari nama atau username"
                                    class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                <button class="bg-white border border-gray-300 px-3 py-1 rounded-md text-sm hover:bg-gray-50">Cari</button>
                            </form>

                            <a href="{{ route('karyawans.create') }}">
                                <button class="bg-blue-500 rounded-md bg-cyan-500 px-4 py-2 text-sm font-semibold text-white opacity-100 focus:outline-none">Tambah Karyawan</button>
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-4">
                            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-2 rounded">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nickname</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username Git</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username VPN</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sebagai</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Berakhir Kontrak</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Hari Kontrak</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($karyawans as $k)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $k->nama_karyawan }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $k->nickname }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $k->email }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $k->phone_no }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $k->username_git }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $k->username_vpn ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $k->sebagai ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $k->tanggal_berakhir_kontrak ? $k->tanggal_berakhir_kontrak->format('Y-m-d') : '-' }}
                                        </td>
                                       <!-- ★ Kolom “Sisa Hari Kontrak” -->
        <!-- ★ Kolom “Sisa Hari Kontrak” -->
        <td class="px-4 py-3 text-sm text-center">
            @php
                // Hitung selisih (tanggal akhir – hari ini) dengan tanda
                $sisaHari = \Carbon\Carbon::parse($k->tanggal_berakhir_kontrak)
                              ->diffInDays(\Carbon\Carbon::today(), false);
            @endphp

            @if($sisaHari < 0)
                <span class="text-red-600 font-medium">{{ $sisaHari }}</span>
            @else
                <span class="text-green-600 font-medium">{{ $sisaHari }}</span>
            @endif
        </td>


                                        <td class="px-4 py-3 text-sm text-right">
                                            <div class="inline-flex items-center space-x-1">
                                                <a href="{{ route('karyawans.show', $k) }}" class="text-indigo-600 hover:underline text-sm">Lihat</a>
                                                <a href="{{ route('karyawans.edit', $k) }}" class="text-yellow-600 hover:underline text-sm">Edit</a>

                                                <!-- Modal menggunakan Alpine per-row -->
                                                <div x-data="{ open:false }" class="inline">
                                                    <button @click="open = true" class="text-red-600 hover:underline text-sm">Hapus</button>

                                                    <!-- Modal -->
                                                    <div
                                                        x-show="open"
                                                        x-cloak
                                                        x-transition.opacity
                                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4"
                                                        @keydown.escape.window="open = false"
                                                    >
                                                        <div @click.stop class="bg-white rounded-lg shadow-lg max-w-md w-full">
                                                            <div class="p-4 border-b">
                                                                <h4 class="text-lg font-medium text-gray-900">Hapus Karyawan</h4>
                                                            </div>
                                                            <div class="p-4">
                                                                <p>Apakah Anda yakin ingin menghapus karyawan <strong>{{ $k->nama_karyawan }}</strong>?</p>
                                                            </div>
                                                            <div class="p-4 flex justify-end gap-2 border-t">
                                                                <button @click="open = false" class="px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200">Batal</button>

                                                                <form action="{{ route('karyawans.destroy', $k) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    <button type="submit" class="px-3 py-1 rounded-md bg-red-600 text-white hover:bg-red-700">Ya, Hapus</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end modal -->
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada data karyawan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Menampilkan {{ $karyawans->firstItem() ?? 0 }} - {{ $karyawans->lastItem() ?? 0 }} dari {{ $karyawans->total() ?? 0 }}
                        </div>
                        <div>
                            {{ $karyawans->withQueryString()->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
