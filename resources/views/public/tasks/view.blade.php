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
                       <a href="{{ route('tasks.edit', $task->id) . (request()->getQueryString() ? '?' . request()->getQueryString() : '') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
    EDIT
</a>
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

                           <div class="mt-4">
    <h4>Lampiran File</h4>
    @if($task->files->isNotEmpty())
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <ul class="divide-y divide-gray-200">
                @foreach($task->files as $file)
                    <li class="px-4 py-3 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center min-w-0">
                             <div class="flex-shrink-0 mr-4">
                                @switch($file->file_type)
                                    @case('image')
                                        <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" /></svg>
                                        @break
                                    @case('pdf')
                                        <svg class="w-6 h-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9 12.75h-9" /></svg>
                                        @break
                                    @case('word')
                                        <svg class="w-6 h-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5" /></svg>
                                        @break
                                    @case('excel')
                                        <svg class="w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5" /></svg>
                                        @break
                                    @case('archive')
                                        <svg class="w-6 h-6 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
                                        @break
                                    @default
                                        <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                @endswitch
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('tasks.files.download', $file->id) }}" target="_blank" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 truncate" title="{{ $file->file_name }}">
                                    {{ \Illuminate\Support\Str::limit($file->file_name, 50) }}
                                </a>
                                <p class="text-xs text-gray-500">
                                    {{ $file->formatted_size }} &middot; Diunggah pada {{ $file->created_at->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <p>Tidak ada file lampiran.</p>
    @endif
</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>