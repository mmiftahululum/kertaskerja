<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="flex items-center justify-between mb-6 pb-4 border-b">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h3>
                            <p class="text-sm text-gray-500">Detail informasi untuk tugas</p>
                        </div>
                        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            ← Kembali
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="md:col-span-2 space-y-6">
                            
                          @if($breadcrumbs->isNotEmpty())
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Path Tugas</label>
                                <div class="mt-2 flex items-center gap-2 text-md text-gray-700 flex-wrap bg-gray-50 p-3 rounded-md border">
                                    @foreach($breadcrumbs as $ancestor)
                                        <a href="{{ route('tasks.view', $ancestor->id) }}" class="text-indigo-600 hover:underline">
                                            {{ $ancestor->title }}
                                        </a>
                                        <span class="text-gray-400">/</span>
                                    @endforeach
                                    {{-- Menampilkan judul task saat ini (tidak bisa diklik) --}}
                                    <span class="font-semibold text-gray-900">{{ $task->title }}</span>
                                </div>
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Deskripsi</label>
                                <div class="prose max-w-none p-4 bg-gray-50 rounded-md border text-gray-800">
                                    {!! $task->description !!}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Link Referensi</label>
                                <div class="mt-2 space-y-1">
                                    @forelse ($task->links as $link)
                                        <a href="{{ $link->url }}" target="_blank" class="flex items-center gap-2 text-indigo-600 hover:underline text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            <span>{{ $link->name }}</span>
                                        </a>
                                    @empty
                                        <p class="text-sm text-gray-500">Tidak ada link.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Komentar</label>
                                @if($task->comments->isNotEmpty())
                                    <div class="mt-2 space-y-4 max-h-72 overflow-y-auto border rounded-md p-3 bg-gray-50">
                                        @foreach($task->comments->sortBy('created_at') as $comment)
                                            <div class="border-b pb-2 last:border-b-0">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs font-semibold text-gray-800">{{ $comment->user->name ?? 'Unknown' }}</span>
                                                    <span class="text-xs text-gray-500">{{ $comment->created_at->format('d M Y, H:i') }}</span>
                                                </div>
                                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $comment->comment }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 mt-1">Belum ada komentar.</p>
                                @endif
                            </div>

                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <p class="mt-1 flex items-center gap-2">
                                    <span class="inline-block w-3 h-3 rounded-full" style="background-color: {{ $task->currentStatus->status_color ?? '#ccc' }}"></span>
                                    <span class="text-md font-semibold text-gray-800">{{ $task->currentStatus->status_name ?? 'N/A' }}</span>
                                </p>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Plan Mulai</label>
                                    <p class="mt-1 text-md text-gray-900">{{ optional($task->planned_start)->format('d M Y') ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Plan Selesai</label>
                                    <p class="mt-1 text-md text-gray-900">{{ optional($task->planned_end)->format('d M Y') ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Real Mulai</label>
                                    <p class="mt-1 text-md text-gray-900">{{ optional($task->actual_start)->format('d M Y') ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Real Selesai</label>
                                    <p class="mt-1 text-md text-gray-900">{{ optional($task->actual_end)->format('d M Y') ?? '-' }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Ditugaskan Kepada</label>
                                <ul class="mt-2 space-y-2">
                                    @forelse($task->assignments as $assign)
                                        <li class="text-sm text-gray-900 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                            <span>{{ $assign->nama_karyawan ?? $assign->name ?? 'N/A' }}</span>
                                        </li>
                                    @empty
                                        <p class="text-sm text-gray-500">Tidak ada.</p>
                                    @endforelse
                                </ul>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Lampiran</label>
                                <ul class="mt-2 space-y-1">
                                    @forelse($task->files as $file)
                                        <li class="text-sm text-indigo-600 hover:underline">
                                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M15.5 14h-11a1.5 1.5 0 01-1.5-1.5V7.879a1.5 1.5 0 01.44-1.06L7.94 2.44A1.5 1.5 0 019.002 2h5.996A1.5 1.5 0 0116.5 3.5v9a1.5 1.5 0 01-1.5 1.5zM10 2.75a.75.75 0 00-.75.75v3c0 .414.336.75.75.75h3a.75.75 0 000-1.5h-2.25V3.5a.75.75 0 00-.75-.75z"></path></svg>
                                                <span>{{ $file->file_name }}</span>
                                            </a>
                                        </li>
                                    @empty
                                        <p class="text-sm text-gray-500">Tidak ada lampiran.</p>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>