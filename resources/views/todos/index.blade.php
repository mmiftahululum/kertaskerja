{{-- resources/views/todos/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My To-do List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @php
                        // Kelompokkan semua to-do.
                        // Logika sorting dari Controller (incomplete first, completed desc)
                        // memastikan urutan grupnya sudah benar.
                        $groupedTodos = $todos->groupBy(function ($todo) {
                            
                            // 1. Grup untuk yang belum selesai
                            if (!$todo->completed_at) {
                                return 'Tugas Aktif';
                            }
                            
                            // 2. Grup untuk hari ini
                            if ($todo->completed_at->isToday()) {
                                return 'Selesai Hari Ini';
                            }
                            
                            // 3. Grup untuk kemarin
                            if ($todo->completed_at->isYesterday()) {
                                return 'Selesai Kemarin';
                            }
                            
                            // 4. Grup untuk tanggal lainnya
                            // format 'd M Y' akan menghasilkan cth: "25 Okt 2025"
                            return 'Selesai ' . $todo->completed_at->format('d M Y');
                        });
                    @endphp

                    <div class="space-y-8"> {{-- Beri jarak antar GRUP tanggal --}}
                        
                        @forelse ($groupedTodos as $groupName => $items)
                            
                            {{-- Ini adalah JUDUL BLOK TANGGAL --}}
                            <div class="group-wrapper">
                                <h3 class="text-lg font-semibold mb-3
                                    @if($groupName == 'Tugas Aktif') 
                                        text-indigo-600 border-b border-indigo-200
                                    @else 
                                        text-gray-800 border-b border-gray-200
                                    @endif">
                                    {{ $groupName }}
                                </h3>

                                {{-- Ini adalah wrapper untuk item di dalam grup --}}
                                <div class="space-y-4 mt-4">
                                    
                                    {{-- Loop ini sama seperti kode asli Anda --}}
                                    @foreach ($items as $todo)
                                        <div class="p-4 border rounded-md flex justify-between items-start 
                                            @if($todo->completed_at) bg-gray-50 text-gray-500 @endif">
                                            
                                            <div>
                                                <p class="text-lg @if($todo->completed_at) line-through @endif">
                                                    {{ $todo->content }}
                                                </p>
                                                
                                                @if ($todo->task)
                                                    <div class="mt-2 text-sm text-gray-600">
                                                        Dari Tugas: 
                                                        
                                                        {{-- Path Parent (dari customisasi kita sblmnya) --}}
                                                        <span class="text-gray-500">
                                                            {{ $todo->task->getParentPathString() }}
                                                        </span>
                                                        
                                                        <a href="{{ route('tasks.view', $todo->task->id) }}" class="text-indigo-600 hover:underline font-medium">
                                                            {{ $todo->task->title }}
                                                        </a>
                                                        
                                                        @if ($todo->task->masterApp)
                                                            <span class="ml-2">(Parent: {{ $todo->task->masterApp->app_name }})</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="ml-4 flex-shrink-0">
                                                @if (!$todo->completed_at)
                                                    <form action="{{ route('todos.complete', $todo) }}" method="POST">
                                                        @csrf
                                                        <x-primary-button>
                                                            {{ __('Selesai') }}
                                                        </x-primary-button>
                                                    </form>
                                                @else
                                                    <span class="text-sm text-green-600 font-medium">
                                                        Selesai
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    {{-- Akhir loop item --}}
                                    
                                </div>
                            </div>
                            
                        @empty
                            <p>Tidak ada to-do list.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>