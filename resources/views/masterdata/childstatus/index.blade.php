
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-gray-900">{{ __('Daftar Child Status') }}</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-medium text-gray-900">Child Status</h3>
                    <a href="{{ route('child-statuses.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700">
                        Tambah Child
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
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width:48px">#</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warna</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Head</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="width:220px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($childStatuses as $index => $child)
                                <tr class="group">
                                    <td class="px-3 py-3 text-sm text-gray-700 align-top">{{ $index + 1 }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700 align-top">{{ $child->status_name }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700 align-top">{{ $child->status_code }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700 align-top">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-block w-6 h-6 rounded border" style="background: {{ $child->status_color }};"></span>
                                            <span class="text-xs text-gray-500">{{ $child->status_color }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-700 align-top">
                                        {{ optional($child->headStatus)->head_status_name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-700 text-right align-top">
                                        <div class="inline-flex items-center space-x-2">
                                            <a href="{{ route('child-statuses.edit', $child) }}" class="inline-flex items-center px-2 py-1 bg-yellow-400 text-white text-xs rounded hover:bg-yellow-500">Edit</a>

                                            <form action="{{ route('child-statuses.destroy', $child) }}" method="POST" onsubmit="return confirm('Hapus child status ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada Child Status.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($childStatuses, 'links'))
                    <div class="mt-4">
                        {{ $childStatuses->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>