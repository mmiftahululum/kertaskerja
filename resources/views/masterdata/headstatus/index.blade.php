<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-gray-900">{{ __('Head Status') }}</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-medium text-gray-900">Daftar Head Status</h3>
                    <a href="{{ route('head-statuses.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700">
                        Buat Head Status
                    </a>
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
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width:60px">#</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Head</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width:140px">Jumlah Child</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="width:320px">Aksi</th>
                            </tr>
                        </thead>
                       
                            @forelse($headStatuses as $index => $head)
                                <tbody x-data="{ open: false }" class="bg-white">
                                    <tr class="group">
                                        <td class="px-3 py-3 text-sm text-gray-700 align-top">{{ $index + 1 }}</td>
                                        <td class="px-3 py-3 text-sm text-gray-700 align-top">{{ $head->head_status_name }}</td>
                                        <td class="px-3 py-3 text-sm text-gray-700 align-top">{{ $head->childStatuses->count() }}</td>
                                        <td class="px-3 py-3 text-sm text-gray-700 text-right align-top">
                                            <div class="inline-flex items-center space-x-2">
                                                <a href="{{ route('child-statuses.create', ['head_status_id' => $head->id]) }}" class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Tambah Child</a>

                                                <button @click="open = !open" type="button" class="inline-flex items-center px-2 py-1 bg-slate-100 text-slate-700 text-xs rounded hover:bg-slate-200">
                                                    Lihat Child
                                                    <svg :class="open ? 'rotate-180' : ''" class="ml-1 h-3 w-3 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>

                                                <a href="{{ route('head-statuses.edit', $head) }}" class="inline-flex items-center px-2 py-1 bg-yellow-400 text-white text-xs rounded hover:bg-yellow-500">Edit</a>

                                                <form action="{{ route('head-statuses.destroy', $head) }}" method="POST" onsubmit="return confirm('Hapus Head Status ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr x-show="open" x-cloak x-transition class="bg-gray-50">
                                        <td colspan="4" class="p-0">
                                            <div class="px-4 py-4 border-t border-gray-200">
                                                @if($head->childStatuses->isEmpty())
                                                    <div class="text-sm text-gray-500">Belum ada child untuk head ini.</div>
                                                @else
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full text-sm">
                                                            <thead>
                                                                <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                                                                    <th class="px-2 py-2" style="width:60px">#</th>
                                                                    <th class="px-2 py-2">Nama Child</th>
                                                                    <th class="px-2 py-2">Keterangan</th>
                                                                    <th class="px-2 py-2 text-right" style="width:180px">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-200 bg-white">
                                                                @foreach($head->childStatuses as $ci => $child)
                                                                    <tr>
                                                                        <td class="px-2 py-2 align-top">{{ $ci + 1 }}</td>
                                                                       <td class="px-2 py-2 align-top">{{ $child->status_name ?? $child->status_name ?? '-' }}</td>
                                                                        <td class="px-2 py-2 align-top">{{ $child->status_code ?? $child->status_code ?? '-' }}</td>
                                                                        <td class="px-2 py-2 text-right align-top">
                                                                            <a href="{{ route('child-statuses.edit', $child) }}" class="inline-flex items-center px-2 py-1 border border-yellow-300 text-yellow-700 text-xs rounded hover:bg-yellow-50 mr-1">Edit</a>

                                                                            <form action="{{ route('child-statuses.destroy', $child) }}" method="POST" onsubmit="return confirm('Hapus child ini?');" class="inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="inline-flex items-center px-2 py-1 border border-red-300 text-red-700 text-xs rounded hover:bg-red-50">Hapus</button>
                                                                            </form>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada Head Status.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</x-app-layout>
