<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Tugas') }}
        </h2>
    </x-slot>

    <div class="py-6 border">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                <!-- Header: Judul & Kembali -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Detail Tugas</h3>
                    <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:underline">
                        ← Kembali ke daftar tugas
                    </a>
                </div>

                <!-- Judul dan Status -->
                <div class="mb-5">
                    <h4 class="font-medium text-gray-900 text-lg mb-2">{{ $task->title }}</h4>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($task->actual_end) bg-green-100 text-green-800
                        @elseif($task->actual_start) bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        @if($task->actual_end)
                            Selesai
                        @elseif($task->actual_start)
                            Sedang Berjalan
                        @else
                            Menunggu Mulai
                        @endif
                    </span>
                </div>

                <!-- Kolom Detail (Plan & Real) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Plan Mulai</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $task->planned_start ? \Carbon\Carbon::parse($task->planned_start)->format('d/m/Y') : '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Plan Selesai</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $task->planned_end ? \Carbon\Carbon::parse($task->planned_end)->format('d/m/Y') : '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Real Mulai</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $task->actual_start ? \Carbon\Carbon::parse($task->actual_start)->format('d/m/Y') : '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Real Selesai</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $task->actual_end ? \Carbon\Carbon::parse($task->actual_end)->format('d/m/Y') : '-' }}</p>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <div class="mt-1 text-sm text-gray-900 whitespace-pre-wrap mt-5 border p-3">{!! $task->description ?? '-' !!}</div>
                </div>

                <!-- External Links -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Tautan (Link)</label>
                    @if($task->links && is_array(json_decode($task->links, true)))
                        @foreach(json_decode($task->links, true) as $link)
                            <a href="{{ $link['url'] }}" target="_blank" class="block text-sm text-indigo-600 hover:underline mb-1">
                                {{ $link['label'] ?? 'Lihat Tautan' }}
                            </a>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">Tidak ada tautan</p>
                    @endif
                </div>

                <!-- Assignment (Penerima Tugas) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Penerima Tugas</label>
                    @if($task->assignments->isNotEmpty())
                        <ul class="mt-1 space-y-1">
                            @foreach($task->assignments as $assign)
                                <li class="text-sm text-gray-900 flex items-center gap-2">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                    {{ $assign->employee->nama_karyawan ?? 'Nama tidak tersedia' }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">Tidak ada penerima tugas</p>
                    @endif
                </div>

                <!-- Files (Dokumen Terlampir) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Dokumen Terlampir</label>
                    @if($task->files->isNotEmpty())
                        <ul class="mt-1 space-y-1">
                            @foreach($task->files as $file)
                                <li class="text-sm text-indigo-600 hover:underline">
                                    <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm0 3a1 1 0 011-1h3a1 1 0 110 2H7a1 1 0 01-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $file->file_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">Tidak ada dokumen terlampir</p>
                    @endif
                </div>

                <!-- Comments (Komentar) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Komentar</label>
                    @if($task->comments->isNotEmpty())
                        <div class="mt-2 space-y-4 max-h-60 overflow-y-auto border rounded-md p-3 bg-gray-50">
                            @foreach($task->comments as $comment)
                                <div class="border-b pb-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-medium text-gray-700">
                                            {{ $comment->user->name ?? 'Unknown' }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($comment->created_at)->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-900">{{ $comment->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 mt-1">Belum ada komentar</p>
                    @endif
                </div>

                <!-- Tombol Print (Opsional) -->
                <div class="mt-6 text-right">
                    <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                        🔖 Cetak Halaman
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
