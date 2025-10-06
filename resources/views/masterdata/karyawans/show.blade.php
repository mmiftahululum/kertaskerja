<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Karyawan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Profil Karyawan</h3>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('karyawans') }}"
                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-800 text-sm rounded-md hover:bg-gray-200">
                            ← Kembali
                        </a>

                        <a href="{{ route('karyawans.edit', $karyawan->id) }}"
                           class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                            Edit
                        </a>

                        <form action="{{ route('karyawans.destroy', $karyawan->id) }}" method="POST" onsubmit="return confirm('Hapus karyawan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nama Karyawan</p>
                        <p class="mt-1 text-gray-900 font-medium">{{ $karyawan->nama_karyawan }}</p>
                    </div>

                     <div>
                        <p class="text-sm text-gray-500">Nick Name</p>
                        <p class="mt-1 text-gray-900 font-medium">{{ $karyawan->nickname }}</p>
                    </div>

                     <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="mt-1 text-gray-900 font-medium">{{ $karyawan->email }}</p>
                    </div>

                     <div>
                        <p class="text-sm text-gray-500">No Telepon</p>
                        <p class="mt-1 text-gray-900 font-medium">{{ $karyawan->phone_no }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Username Git</p>
                        <p class="mt-1 text-gray-900 font-medium">{{ $karyawan->username_git }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Username VPN</p>
                        <p class="mt-1 text-gray-900 font-medium">{{ $karyawan->username_vpn ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Tanggal Berakhir Kontrak</p>
                        <p class="mt-1 text-gray-900 font-medium">
                            {{ optional($karyawan->tanggal_berakhir_kontrak)->format('d M Y') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Dibuat</p>
                        <p class="mt-1 text-gray-900 text-sm">{{ optional($karyawan->created_at)->format('d M Y H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Terakhir diupdate</p>
                        <p class="mt-1 text-gray-900 text-sm">{{ optional($karyawan->updated_at)->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
