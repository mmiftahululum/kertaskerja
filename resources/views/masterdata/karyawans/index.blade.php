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
                                       {{-- MODIFIED: Email --}}
            <td class="px-4 py-3 text-sm text-gray-700">
                <div class="flex items-center justify-between group">
                    <span>{{ $k->email }}</span>
                    <button onclick="copyToClipboard(this, '{{ $k->email }}')" class="p-1 text-gray-400  hover:text-gray-800 rounded-md transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                </div>
            </td>

            {{-- MODIFIED: Phone --}}
            <td class="px-4 py-3 text-sm text-gray-700">
                <div class="flex items-center justify-between group">
                    <span>{{ $k->phone_no }}</span>
                    <button onclick="copyToClipboard(this, '{{ $k->phone_no }}')" class="p-1 text-gray-400 hover:text-gray-800 rounded-md transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                </div>
            </td>
            
            {{-- MODIFIED: Username Git --}}
            <td class="px-4 py-3 text-sm text-gray-700">
                <div class="flex items-center justify-between group">
                    <span>{{ $k->username_git }}</span>
                    <button onclick="copyToClipboard(this, '{{ $k->username_git }}')" class="p-1 text-gray-400 hover:text-gray-800 rounded-md transition-opacity">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                </div>
            </td>

            {{-- MODIFIED: Username VPN --}}
            <td class="px-4 py-3 text-sm text-gray-700">
                <div class="flex items-center justify-between group">
                    <span>{{ $k->username_vpn ?? '-' }}</span>
                    @if($k->username_vpn)
                    <button onclick="copyToClipboard(this, '{{ $k->username_vpn }}')" class="p-1 text-gray-400 opacity-0 group-hover:opacity-100 hover:text-gray-800 rounded-md transition-opacity">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                    @endif
                </div>
            </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $k->sebagai ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $k->tanggal_berakhir_kontrak ? $k->tanggal_berakhir_kontrak->format('Y-m-d') : '-' }}
                                        </td>
                               
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{-- Hanya jalankan jika tanggal berakhir kontrak ada isinya --}}
                                            @if($k->tanggal_berakhir_kontrak)
                                                @php
                                                    // Hitung sisa hari, jangan izinkan hasil negatif
                                                    $sisaHari = now()->startOfDay()->diffInDays($k->tanggal_berakhir_kontrak->startOfDay(), false);
                                                @endphp

                                                @if ($sisaHari > 30)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ $sisaHari }} hari
                                                    </span>
                                                @elseif ($sisaHari > 0)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        {{ $sisaHari }} hari
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Habis
                                                    </span>
                                                @endif
                                            @else
                                                {{-- Jika tanggal kosong, tampilkan strip --}}
                                                <span>-</span>
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

<script>
    function copyToClipboard(element, text) {
        // Jangan lakukan apa-apa jika teks kosong atau hanya berisi strip
        if (!text || text.trim() === '-' || text.trim() === '') {
            return;
        }

        // Cek apakah Clipboard API modern tersedia
        if (navigator.clipboard && window.isSecureContext) {
            // Gunakan API modern jika memungkinkan (lebih aman dan efisien)
            navigator.clipboard.writeText(text).then(function() {
                showFeedback(element);
            });
        } else {
            // Fallback ke metode lama untuk browser/lingkungan yang tidak mendukung
            let textArea = document.createElement("textarea");
            textArea.value = text;
            
            // Buat textarea tidak terlihat
            textArea.style.position = "fixed";
            textArea.style.top = "-999999px";
            textArea.style.left = "-999999px";
            
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                showFeedback(element);
            } catch (err) {
                console.error('Gagal menyalin teks dengan metode lama: ', err);
                alert('Oops, gagal menyalin teks.');
            } finally {
                document.body.removeChild(textArea);
            }
        }
    }

    function showFeedback(element) {
        // Simpan konten asli (ikon SVG)
        const originalContent = element.innerHTML;
        // Tampilkan pesan "Tersalin!"
        element.innerHTML = '<span class="text-xs text-green-600 font-semibold">Tersalin!</span>';
        
        // Kembalikan ke ikon semula setelah 1.5 detik
        setTimeout(() => {
            element.innerHTML = originalContent;
        }, 1500);
    }
</script>

</x-app-layout>
