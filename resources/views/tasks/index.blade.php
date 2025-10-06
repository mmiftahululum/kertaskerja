<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Task') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-4 sm:p-6">
                   <div class="items-center justify-between mb-4">
    <div class="text-lg font-medium text-gray-900"> 
        <a target="blank" href="https://outlook.office.com/mail/">Email Calender</a> | 
        <a target="blank" href="https://servicedesk.sig.id/HomePage.do" >Service Desk</a>
    </div>

    <div class="flex flex-wrap">
<div id="saveBookmarkModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md mx-4 p-6">
            <h3 class="text-lg font-semibold mb-4">Simpan Filter sebagai Bookmark</h3>
            <form action="{{ route('tasks.bookmarks.store') }}" method="POST">
                @csrf

                {{-- Hidden input untuk menyimpan semua parameter filter saat ini --}}
                @foreach(request()->query() as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $sub_value)
                            <input type="hidden" name="filters[{{ $key }}][]" value="{{ $sub_value }}">
                        @endforeach
                    @else
                        <input type="hidden" name="filters[{{ $key }}]" value="{{ $value }}">
                    @endif
                @endforeach

                <div>
                    <label for="bookmark_name" class="block text-sm font-medium text-gray-700">Nama Bookmark</label>
                    <input type="text" name="bookmark_name" id="bookmark_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Contoh: Bug Prioritas Tinggi">
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="closeSaveBookmarkModal()" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-4 flex flex-wrap items-center gap-4 pt-5">
        <button onclick="openSaveBookmarkModal()" class="flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
            Simpan Filter
        </button>

       <div class="flex items-center gap-2">
        @foreach($bookmarks as $bookmark)
            <div class="flex items-center bg-gray-100 rounded-full">
                <a href="{{ route('tasks.index', $bookmark->filters) }}" class="block text-sm text-gray-800 pl-3 pr-2 py-1 hover:text-black">
                    {{ $bookmark->name }}
                </a>
                
                <form action="{{ route('tasks.bookmarks.destroy', $bookmark) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus bookmark \'{{ $bookmark->name }}\'?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold pr-3 pl-1" aria-label="Hapus bookmark {{ $bookmark->name }}">
                        &times;
                    </button>
                </form>
            </div>
        @endforeach
    </div>
    </div>

    </div>
    
    <div class="flex flex-wrap items-center gap-4 mt-4">
        <!-- Form Filter -->
        <form method="GET" action="{{ route('tasks.index') }}" id="filterForm" class="flex flex-wrap items-center gap-2">

        <div id="taskSearchSelectId" style="width: 300px; visibility: hidden;">
            <select name="search_ids[]" id="taskSearchSelect" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" multiple>
                @foreach($searchableTasks as $task)
                    <option value="{{ $task->id }}" 
                        {{-- Cek apakah ID task ada di dalam array request --}}
                        @selected(in_array($task->id, request('search_ids', [])))>
                        {{ $task->title }}
                    </option>
                @endforeach
            </select>
        </div>

    
            <!-- Checkbox Filter Close -->
            <label class="border rounded-md px-2 py-1 flex items-center gap-1">
                <input class="rounded" type="checkbox" name="filter_close" value="1" 
                       {{ request('filter_close') == '1' ? 'checked' : '' }} 
                       onchange="document.getElementById('filterForm').submit();">
                <span class="text-sm">Close</span>
            </label>

             <!-- Checkbox Filter Close -->
            <label class="border rounded-md px-2 py-1 flex items-center gap-1">
                <input class="rounded" type="checkbox" name="filter_mytask" value="1" 
                       {{ request('filter_mytask') == '1' ? 'checked' : '' }} 
                       onchange="document.getElementById('filterForm').submit();">
                <span class="text-sm">My Task</span>
            </label>

            <select name="filter_assigned_to" class="filter_assigned_to border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="document.getElementById('filterForm').submit();">
                <option value="">Semua Karyawan</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('filter_assigned_to') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->nama_karyawan }}
                    </option>
                @endforeach
            </select>

            <!-- Search Input -->
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="Cari judul task..."
                   class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-48">

            <!-- Operator Select -->
            <select name="operator" class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Pilih Operator</option>
                <option value="=" {{ request('operator') == '=' ? 'selected' : '' }}>= Sama dengan</option>
                <option value="!=" {{ request('operator') == '!=' ? 'selected' : '' }}>!= Tidak sama dengan</option>
            </select>

            <!-- Status Multi-Select -->
            <select name="status_ids[]" class="select2-filter" multiple="multiple" style="width: 300px; visibility: hidden;" data-placeholder="Pilih status...">
                @foreach($childStatuses as $status)
                    <option value="{{ $status->id }}" 
                            data-color="{{ $status->status_color ?? '#000000' }}"
                            {{ in_array($status->id, request('status_ids', [])) ? 'selected' : '' }}>
                        {{ $status->status_name }} ({{ $status->status_code }})
                    </option>
                @endforeach
            </select>

            <!-- Submit Button -->
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-md text-sm">
                Terapkan
            </button>

            <!-- Reset Button -->
            <a href="{{ route('tasks.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-1 rounded-md text-sm">
                Reset
            </a>

        </form>

        <!-- Add Task Button -->
        <a href="{{ route('tasks.create') }}">
            <button class="bg-cyan-500 rounded-md px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600 focus:outline-none">
                Tambah Task
            </button>
        </a>
    </div>

    <!-- Filter Summary -->
    @if(request('filter_close') || request('q') || request('operator') || request('status_ids'))
        <div class="mt-3 p-3 bg-gray-50 rounded-lg">
            <div class="flex flex-wrap gap-2 text-sm">
                <span class="text-gray-600">Filter aktif:</span>
                
                @if(request('filter_close') == '1')
                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">
                        Tampilkan CLOSE
                    </span>
                @endif

                @if(request('q'))
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                        Judul: "{{ request('q') }}"
                    </span>
                @endif

                @if(request('operator') && request('status_ids'))
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                        Status {{ request('operator') == '=' ? 'sama dengan' : 'tidak sama dengan' }}:
                        @foreach($childStatuses->whereIn('id', request('status_ids', [])) as $status)
                            <span class="inline-block w-2 h-2 rounded-full mr-1" 
                                  style="background-color: {{ $status->status_color }}"></span>
                            {{ $status->status_name }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </span>
                @endif

                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                    {{ $tasks->total() }} task ditemukan
                </span>
            </div>
        </div>
    @endif
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
                                   <th colspan="3" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Task</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12" colspan="2">Status Saat Ini</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">Plan start</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">Plan End</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mandays Plan</th> 
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">Actual start</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">Actual End</th>

                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mandays Actual</th>
                         
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignment</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link Reference</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Komentar Terakhir</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200"  id="task-list">
                                @foreach($tasks as $task)
                                        @include('tasks.task-row', ['task' => $task, 'level' => 0, 'childStatuses' => $childStatuses])
                                @endforeach
                                
                                @if($tasks->count() == 0)
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-gray-500">Belum ada task tersedia.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="context-menu" style="display:none; position:absolute; background:white; border:1px solid #ccc; box-shadow:0 2px 5px rgba(0,0,0,0.2); z-index:1000; width: 150px;">
        <ul style="list-style:none; margin:0; padding:5px 0;">
            <li><a href="#" id="context-timesheet" style="border-bottom: 1px solid #6e6d6dff; display:block; padding:8px 15px; color:#108f32; text-decoration:none;">Buat Timesheet</a></li>
            <li><a href="#" id="context-edit" style="display:block; padding:8px 15px; color:#fbbf24; text-decoration:none;">Edit</a></li>
            <li><a href="#" id="context-delete" style="display:block; padding:8px 15px; color:#dc2626; text-decoration:none;">Hapus</a></li>
        </ul>
    </div>

    {{-- Modal untuk Komentar --}}
    <div id="comment-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl mx-4 p-6">
            <h3 class="text-lg font-semibold mb-4" id="modal-task-title"></h3>

    

            <div class="mt-6">
                <h4 class="font-medium mb-2">Komentar Terbaru</h4>
                <div id="comment-list" class="space-y-3 max-h-60 overflow-y-auto">
                    <p class="text-gray-500 text-sm">Tidak ada komentar.</p>
                </div>
            </div>

             <form id="comment-form" class="mt-5" method="POST" action="">
                @csrf
                <input type="hidden" name="task_id" id="modal-task-id">
                 {{-- (BARU) Tambahkan input ini juga --}}
                <input type="hidden" name="_redirect_params" id="commentRedirectParams">
                <input name="comment"
                          class="w-full border rounded p-2 mb-3"
                          rows="3"
                          placeholder="Tuliskan komentar…"
                          required></input>
                <div class="flex justify-end space-x-2">
                    <button type="button"
                            class="px-4 py-2 bg-gray-300 rounded"
                            onclick="closeCommentModal()">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded">Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Task Status Timeline Modal -->
   <!-- Task Status Timeline Modal -->
<div id="taskStatusTimelineModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 relative">

      <div class="flex items-center justify-between border-b pb-3">
    <h2 class="text-lg font-semibold">Status Timeline</h2>
    <button onclick="closeTaskStatusTimelineModal()" 
            class="text-gray-500 hover:text-gray-700 text-2xl leading-none">
        &times;
    </button>
</div>
       

       

        <div id="status-timeline-container" class="mt-4 space-y-4">
            <div class="flex justify-center py-6">
                <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

    {{-- Modal untuk Tambah Child Task --}}
   <div id="createChildTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-4 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-medium text-gray-900">Tambah Sub-Task untuk "<span id="parentTaskTitle"></span>"</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeCreateChildModal()">
                <span class="sr-only">Tutup</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="mt-4">
            <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="parent_id" id="parentTaskId">

                {{-- (BARU) Tambahkan input ini untuk menyimpan parameter filter --}}
                <input type="hidden" name="_redirect_params" id="childRedirectParams">

                <div>
                    <label for="child_title" class="block text-sm font-medium text-gray-700 mb-1">Judul Sub-Task</label>
                    <input type="text" name="title" id="child_title" value="{{ old('title') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Masukkan judul sub-task" required>
                </div>

                <div>
                    <label for="child_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Deskripsikan sub-task"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-3">
                    <div>
                        <label for="planned_start" class="block text-sm font-medium text-gray-700">Plan Mulai</label>
                        <input type="date" name="planned_start" id="planned_start" value="{{ now()->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="planned_end" class="block text-sm font-medium text-gray-700">Plan Selesai</label>
                        <input type="date" name="planned_end" id="planned_end" value="" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="actual_start" class="block text-sm font-medium text-gray-700">Real Mulai</label>
                        <input type="date" name="actual_start" id="actual_start" value="" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="actual_end" class="block text-sm font-medium text-gray-700">Real Selesai</label>
                        <input type="date" name="actual_end" id="actual_end" value="" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                    <div>
                        <label for="head_status_id" class="block text-sm font-medium text-gray-700">Head Status</label>
                        <select name="head_status_id" id="head_status_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pilih Status Kepala</option>
                            @foreach($headStatuses as $status)
                            <option value="{{ $status->id }}" {{ (int)old('head_status_id') === $status->id ? 'selected' : '' }}>
                                {{ $status->head_status_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="current_status_id" class="block text-sm font-medium text-gray-700">Status Child</label>
                        <select name="current_status_id" id="current_status_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Memuat...</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="assignments" class="block text-sm font-medium text-gray-700">Assign ke Karyawan</label>
                    <select name="assignments[]" id="assignments" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" data-placeholder="Cari dan pilih karyawan...">
                        @if(isset($employees) && $employees->count())
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" data-username="{{ $emp->username_git ?? '' }}" {{ in_array($emp->id, old('assignments', [])) ? 'selected' : '' }}>
                            {{ $emp->nama_karyawan ?? $emp->name ?? 'Karyawan #'.$emp->id }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link References</label>
                    <div id="links-container" class="space-y-2">
                        </div>
                    <button type="button" id="add-link" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">+ Tambah Link</button>
                </div>

                <div>
                    <label for="child_attachment" class="block text-sm font-medium text-gray-700">Lampiran</label>
                    <input type="file" name="attachment" id="child_attachment" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t">
                    <button type="button" onclick="closeCreateChildModal()" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Sub-Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
    {{-- Form untuk Hapus --}}
<form id="delete-task-form" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="_redirect_params" id="deleteRedirectParams">
    @method('DELETE')
  
</form>

</x-app-layout>

 <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<!-- Styles and Scripts -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

 @include('layouts.tiny')

<style>
    .ts-control,
    .ts-control .item,
    .ts-control .ts-input,
    .ts-dropdown,
    .ts-dropdown .ts-option {
        background-color: #ffffff !important;
        color: #111827 !important;
    }
    .ts-control { border: 1px solid #d1d5db !important; box-shadow: none !important; }
    .ts-dropdown .ts-option,
    .ts-dropdown .ts-option * { transition: background-color 150ms ease, color 150ms ease; }
    .ts-dropdown .ts-option:hover,
    .ts-dropdown .ts-option.ts-selected,
    .ts-dropdown .ts-option.is-highlighted { background-color: #2563eb !important; color: #ffffff !important; }
    .ts-dropdown { z-index: 9999 !important; position: relative; overflow: auto; }
    
    .child-task {
        background-color: #f9fafb;
    }

    .select-s1 {
        font-size: 14px; /* Sesuaikan dengan input */
        padding: 2px 5px; /* Sesuaikan padding agar tampilan seragam */
        height: 30px; /* Sesuaikan tinggi sesuai input */
        border: 1px solid #cbd5e1; /* Contoh border warna abu-abu */
        border-radius: 4px; /* Membuat border agak melengkung */
        outline: none;
    }

    .select2-container{
        height: 30px !important;
    }

    .select2-results__option{
        font-size: 14px; 
    }

    .select2-selection__rendered span{
        font-size: 12px
        ;
    }

</style>

 <style>
        /* Style untuk elemen yang sedang di-drag */
        .sortable-drag {
            opacity: 0.7;
            background: #f0f8ff; /* Warna biru muda agar menonjol */
        }

        /* Style untuk placeholder (bayangan) di posisi baru */
        .ghost-style {
            opacity: 0.4;
            background: #e6e6e6;
            border: 1px dashed #999;
        }
        .ghost-style > td {
            visibility: hidden; /* Sembunyikan konten placeholder */
        }

        /* Style untuk placeholder saat akan menjadi child (reparenting) */
        .ghost-indented {
            background-color: #e6fffa; /* Warna hijau muda */
            border-left: 4px solid #38b2ac; /* Garis hijau di kiri */
        }
        .ghost-indented > td {
            visibility: hidden; /* Sembunyikan konten placeholder */
            padding-left: 40px !important;
        }
    </style>

<style>

    
    /* Styling untuk Timeline */
.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e0e0e0;
    left: 20px; /* Posisi garis vertikal */
    margin-left: -1.5px;
}

.timeline-item {
    margin-bottom: 20px;
    position: relative;
    padding-left: 60px; /* Jarak untuk ikon/titik */
    border-bottom: 1px dotted #eee; /* Garis pemisah antar item */
    padding-bottom: 10px;
}

.timeline-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.timeline-icon {
    color: #fff;
    width: 30px;
    height: 30px;
    line-height: 30px;
    font-size: 16px;
    text-align: center;
    position: absolute;
    top: 0;
    left: 5px; /* Posisi ikon relatif terhadap garis */
    background-color: #0d6efd; /* Warna default icon (biru) */
    border-radius: 50%;
    z-index: 1;
}

.timeline-icon.status-completed {
    background-color: #28a745; /* Hijau untuk selesai */
}

.timeline-icon.status-in-progress {
    background-color: #ffc107; /* Kuning untuk dalam proses */
}

/* Anda bisa menambahkan lebih banyak kelas sesuai status yang ada */

.timeline-panel {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    position: relative;
}

.timeline-heading {
    margin-top: 0;
    font-size: 1.1em;
}

.timeline-body p {
    margin-bottom: 5px;
}

.timeline-date {
    font-size: 0.85em;
    color: #666;
    margin-top: 5px;
}
</style>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const taskList = document.getElementById('task-list');
    if (!taskList) return; // Hentikan jika elemen tidak ditemukan

    let isReparenting = false; // Flag untuk menandai aksi reparent

    new Sortable(taskList, {
        handle: '.drag-handle',
        animation: 150,
        group: 'tasks',
        ghostClass: 'ghost-style', // Class standar untuk placeholder

       onMove: function (evt) {
    const targetEl = evt.related; // Ini adalah elemen <tr> yang sedang dilewati kursor

    // Pengecekan 'if' di awal ini tidak perlu dan bisa dihapus.
    // Opsi 'handle' sudah memastikan proses drag dimulai dari handle yang benar.

    // Ambil elemen placeholder (ghost) yang sedang aktif
    const ghostEl = evt.from.querySelector('.ghost-style, .ghost-indented');
    if (!ghostEl){
        console.log("ghostEl tidak ditemukan"); // Untuk debugging jika elemen ghost tidak ada
        return; // Hentikan jika ghost tidak ditemukan
    }

    const rect = targetEl.getBoundingClientRect();
    const offsetX = evt.originalEvent.clientX - rect.left;
    
    // Logika untuk mengubah style placeholder
    if (offsetX > 40) {
        isReparenting = true;
        ghostEl.classList.add('ghost-indented');
        ghostEl.classList.remove('ghost-style');
    } else {
        isReparenting = false;
        ghostEl.classList.remove('ghost-indented');
        ghostEl.classList.add('ghost-style');
    }
},

        onEnd: function (evt) {
            // Pastikan sortable instance ada
            if (evt.from.sortable) {
                // Selalu reset style setelah drop
                evt.from.sortable.options.ghostClass = 'ghost-style';
            }
            
            const draggedEl = evt.item;
            const taskId = draggedEl.dataset.taskId;

            if (!taskId) return; // Jangan lakukan apa-apa jika tidak ada ID

            if (isReparenting) {
                // --- AKSI REPARENT ---
                // Dapatkan elemen di bawah item yang di-drop. Jika tidak ada, berarti di drop di paling bawah
                const newParentEl = evt.to.children[evt.newIndex - 1];
                const newParentId = newParentEl ? newParentEl.dataset.taskId : null;

                // Pastikan tidak drop ke dirinya sendiri
                if (taskId !== newParentId) {
                    handleReparent(taskId, newParentId);
                } else {
                    // Jika drop ke dirinya sendiri, refresh halaman untuk reset
                    window.location.reload();
                }
            } else {
                // --- AKSI REORDER ---
                // Temukan parent ID dari elemen yang di-drag
                const parentId = draggedEl.dataset.parentId || 'root';
                
                // Kumpulkan semua sibling (elemen lain dengan parent ID yang sama)
                const siblingIds = Array.from(evt.to.children)
                    .filter(el => (el.dataset.parentId || 'root') === parentId)
                    .map(row => row.dataset.taskId);
                    
                handleReorder(siblingIds);
            }

            // Reset flag
            isReparenting = false;
        }
    });

    function handleReorder(ids) {
        if (ids.length === 0) return;

        fetch('{{ route("tasks.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids })
        }).then(response => {
            window.location.reload();
            if (!response.ok) {
                 // Muat ulang halaman jika ada error untuk mencegah tampilan yang salah
                 window.location.reload();
            }
        }).catch(error => {
            console.error('Error:', error);
            window.location.reload();
        });
    }

    function handleReparent(taskId, parentId) {
        fetch('{{ route("tasks.reparent") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ task_id: taskId, parent_id: parentId })
        }).then(response => {
            // Apapun hasilnya (sukses atau gagal), muat ulang halaman untuk konsistensi
            window.location.reload(); 
        }).catch(error => {
            console.error('Error:', error);
            window.location.reload();
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    /**
     * Fungsi ini akan menambahkan event listener ke sebuah modal.
     * Jika pengguna mengklik area latar belakang modal (bukan kontennya),
     * modal akan ditutup dengan memanggil fungsi yang sesuai.
     *
     * @param {string} modalId - ID dari elemen modal.
     * @param {Function} closeFunction - Fungsi JavaScript yang digunakan untuk menutup modal.
     */
    function setupModalOutsideClick(modalId, closeFunction) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(event) {
                // event.target adalah elemen yang diklik.
                // Kita cek apakah elemen yang diklik adalah modal itu sendiri.
                if (event.target === modal) {
                    closeFunction();
                }
            });
        }
    }

    // Terapkan fungsi di atas ke setiap modal yang Anda miliki
    setupModalOutsideClick('saveBookmarkModal', closeSaveBookmarkModal);
    setupModalOutsideClick('comment-modal', closeCommentModal);
    setupModalOutsideClick('taskStatusTimelineModal', closeTaskStatusTimelineModal);
    setupModalOutsideClick('createChildTaskModal', closeCreateChildModal);
});
</script>

<script>

    // (BARU) Script untuk Modal Bookmark
function openSaveBookmarkModal() {
    // Cek apakah ada filter aktif, jika tidak jangan buka modal
    const queryString = window.location.search;
    if (!queryString) {
        alert('Tidak ada filter aktif untuk disimpan.');
        return;
    }
    document.getElementById('saveBookmarkModal').classList.remove('hidden');
}

function closeSaveBookmarkModal() {
    document.getElementById('saveBookmarkModal').classList.add('hidden');
}

    
    // Script untuk Select2 status task
    // Script untuk Filter - Tambahkan setelah script Select2 yang sudah ada
$(document).ready(function () {

     // Inisialisasi Select2 untuk combobox pencarian task multiple
    $('#taskSearchSelect').select2({
        placeholder: "Pilih Task",
        allowClear: true
    });
    
    $('.filter_assigned_to').select2({
        placeholder: "Assigment",
        allowClear: true
    });

    $('#taskSearchSelectId').css('visibility', 'visible');
    // ==========================================================
    // (BARU) Event Listener untuk auto-submit saat pilihan berubah
    // ==========================================================
    $('#taskSearchSelect').on('change', function() {
        // Langsung submit form filter
        $('#filterForm').submit();
    });
     
     $('.statustask').select2({
            templateResult: function (data) {
                if (!data.id) return data.text;

                var color = $(data.element).data('color') || '#000000';

                var $circle = $('<span></span>').css({
                    'display': 'inline-block',
                    'width': '12px',
                    'height': '12px',
                    'border-radius': '50%',
                    'background-color': color,
                    'margin-right': '8px',
                    'vertical-align': 'middle'
                });

                var $text = $('<span></span>').text(data.text).css({
                    'vertical-align': 'middle'
                });

                return $('<span></span>').append($circle).append($text);
            },
            templateSelection: function (data) {
                if (!data.id) return data.text;

                var color = $(data.element).data('color') || '#000000';

                var $circle = $('<span></span>').css({
                    'display': 'inline-block',
                    'width': '12px',
                    'height': '12px',
                    'border-radius': '50%',
                    'background-color': color,
                    'margin-right': '8px',
                    'vertical-align': 'middle'
                });

                var $text = $('<span></span>').text(data.text).css({
                    'vertical-align': 'middle'
                });

                return $('<span></span>').append($circle).append($text);
            }
        });

    
    // ===== SELECT2 UNTUK FILTER STATUS =====
    $('.select2-filter').select2({
        placeholder: "Pilih status...",
        allowClear: true,
        templateResult: function (data) {
            if (!data.id) return data.text;

            var color = $(data.element).data('color') || '#000000';
            var $circle = $('<span></span>').css({
                'display': 'inline-block',
                'width': '12px',
                'height': '12px',
                'border-radius': '50%',
                'background-color': color,
                'margin-right': '8px',
                'vertical-align': 'middle'
            });

            var $text = $('<span></span>').text(data.text).css({
                'vertical-align': 'middle'
            });

            return $('<span></span>').append($circle).append($text);
        },
        templateSelection: function (data) {
            if (!data.id) return data.text;

            var color = $(data.element).data('color') || '#000000';
            var $circle = $('<span></span>').css({
                'display': 'inline-block',
                'width': '10px',
                'height': '10px',
                'border-radius': '50%',
                'background-color': color,
                'margin-right': '6px',
                'vertical-align': 'middle'
            });

            var $text = $('<span></span>').text(data.text).css({
                'vertical-align': 'middle',
                'font-size': '13px'
            });

            return $('<span></span>').append($circle).append($text);
        }
    });

    // ===== AUTO SUBMIT KETIKA OPERATOR BERUBAH =====
    $('select[name="operator"]').on('change', function() {
        var operator = $(this).val();
        var statusSelect = $('.select2-filter');
        
        if (operator === '') {
            // Jika operator kosong, disable status selection
            statusSelect.prop('disabled', true).trigger('change');
        } else {
            // Enable status selection
            statusSelect.prop('disabled', false);
        }
    });

    // ===== AUTO SUBMIT KETIKA STATUS BERUBAH =====
    $('.select2-filter').on('change', function() {
        var statusIds = $(this).val();
        var operator = $('select[name="operator"]').val();
        
        // Auto submit jika operator sudah dipilih dan ada status
        if (operator && statusIds && statusIds.length > 0) {
            setTimeout(function() {
                $('#filterForm').submit();
            }, 300); // Delay 300ms untuk UX yang lebih baik
        }
    });

    // ===== SEARCH INPUT DENGAN DEBOUNCE =====
    let searchTimeout;
    $('input[name="q"]').on('input', function() {
        clearTimeout(searchTimeout);
        var query = $(this).val().trim();
        
        searchTimeout = setTimeout(function() {
            if (query.length >= 3 || query.length === 0) {
                $('#filterForm').submit();
            }
        }, 800); // Delay 800ms untuk search
    });

    // ===== SUBMIT FORM DENGAN ENTER =====
    $('input[name="q"]').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            clearTimeout(searchTimeout);
            $('#filterForm').submit();
        }
    });

    // ===== INITIALIZE DISABLED STATE =====
    var initialOperator = $('select[name="operator"]').val();
    if (!initialOperator) {
        $('.select2-filter').prop('disabled', true);
    }

    // ===== LOADING STATE UNTUK FORM =====
    $('#filterForm').on('submit', function() {
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        
        submitBtn.prop('disabled', true).text('Memproses...');
        
        // Reset setelah 5 detik (fallback)
        setTimeout(function() {
            submitBtn.prop('disabled', false).text(originalText);
        }, 5000);
    });

    // ===== CLEAR FILTER FUNCTION =====
    window.clearFilter = function(filterType) {
        var form = $('#filterForm');
        
        switch(filterType) {
            case 'search':
                $('input[name="q"]').val('');
                break;
            case 'operator':
                $('select[name="operator"]').val('');
                $('.select2-filter').val(null).trigger('change').prop('disabled', true);
                break;
            case 'status':
                $('.select2-filter').val(null).trigger('change');
                break;
            case 'close':
                $('input[name="filter_close"]').prop('checked', false);
                break;
        }
        
        form.submit();
    };

    // ===== QUICK FILTER FUNCTIONS =====
    window.quickFilterStatus = function(statusId, operator = '=') {
        $('select[name="operator"]').val(operator);
        $('.select2-filter').val([statusId]).trigger('change').prop('disabled', false);
        $('#filterForm').submit();
    };

    window.quickFilterSearch = function(query) {
        $('input[name="q"]').val(query);
        $('#filterForm').submit();
    };

    // ===== EXPORT FILTERED DATA =====
    window.exportFilteredData = function() {
        var formData = $('#filterForm').serialize();
        var exportUrl = "{{ route('tasks.export-filtered') }}?" + formData;
        
        // Bisa menggunakan window.open atau AJAX
        $.get(exportUrl)
            .done(function(response) {
                alert('Export berhasil! Total: ' + response.total_tasks + ' task');
            })
            .fail(function() {
                alert('Export gagal. Silakan coba lagi.');
            });
    };
});

document.addEventListener('DOMContentLoaded', function() {
    var taskTimelineModal = document.getElementById('taskStatusTimelineModal');
    if (taskTimelineModal) {
        console.log('Modal found');
        taskTimelineModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = event.relatedTarget;
            // Extract info from data-bs-task-id attributes
            var taskId = button.getAttribute('data-bs-task-id');

            // Sekarang Anda memiliki taskId, Anda bisa menggunakannya untuk:
            // 1. Membuat permintaan AJAX ke endpoint API Anda
            //    untuk mendapatkan data timeline status task tersebut.
            //    Contoh: /api/tasks/{taskId}/timeline
            // 2. Mengisi konten modal dengan data yang diterima.

            var modalTitle = taskTimelineModal.querySelector('.modal-title');
            var modalBody = taskTimelineModal.querySelector('.modal-body');

            modalTitle.textContent = 'Histori Status Tugas #' + taskId;
            modalBody.innerHTML = '<p>Memuat timeline untuk Tugas ID: ' + taskId + '...</p>'; // Teks loading

            // Contoh AJAX dengan Fetch API (asumsi Anda punya endpoint API untuk timeline)
            fetch('/api/tasks/' + taskId + '/timeline') // Ganti dengan endpoint API Anda
                .then(response => response.json())
                .then(data => {
                    // Pastikan Anda sudah mengatur modalBody untuk menampilkan timeline dengan rapi
                    let timelineHtml = '<ul>';
                    data.forEach(log => {
                        timelineHtml += `<li><strong>${log.status_name}</strong> - Oleh: ${log.user_name} pada: ${new Date(log.changed_at).toLocaleString()}</li>`;
                    });
                    timelineHtml += '</ul>';
                    modalBody.innerHTML = timelineHtml;
                })
                .catch(error => {
                    console.error('Error fetching task timeline:', error);
                    modalBody.innerHTML = '<p class="text-danger">Gagal memuat timeline status.</p>';
                });
        });
    }
});


    // Script untuk modal child task
    function openCreateChildModal(parentId, parentTitle, heatstatusid) {
        document.getElementById('parentTaskId').value = parentId;
        document.getElementById('parentTaskTitle').innerText = parentTitle;
         // (BARU) Ambil query string dari URL dan masukkan ke input
    document.getElementById('childRedirectParams').value = window.location.search;
        const statusSelect = document.getElementById('head_status_id');
        statusSelect.value = heatstatusid;
        loadChildStatuses(heatstatusid, null);


        document.getElementById('createChildTaskModal').classList.remove('hidden');
    }

    function closeCreateChildModal() {
        document.getElementById('createChildTaskModal').classList.add('hidden');
        document.getElementById('createChildTaskModal').querySelector('form').reset();
        document.getElementById('parentTaskTitle').innerText = '';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('createChildTaskModal');
        if (event.target == modal) {
            closeCreateChildModal();
        }
    }

    // Script untuk assignment select
    document.addEventListener('DOMContentLoaded', function () {
        if (document.querySelector('#assignments')) {
            new TomSelect('#assignments', {
                plugins: ['remove_button'],
                maxItems: null,
                persist: false,
                create: false,
                placeholder: document.querySelector('#assignments')?.dataset?.placeholder || 'Cari karyawan...',
                allowEmptyOption: true,
                render: {
                    option: function(data, escape) {
                        var username = data.username ? '<div class="text-xs text-gray-500">' + escape(data.username) + '</div>' : '';
                        return '<div class="flex items-center"><div class="ml-1">' + escape(data.text) + username + '</div></div>';
                    },
                    item: function(data, escape) {
                        return '<div>' + escape(data.text) + '</div>';
                    }
                }
            });
        }

      

        const headSelect = document.getElementById('head_status_id');
        headSelect.addEventListener('change', function () {
            loadChildStatuses(this.value, null);
        });

        // load initial child statuses
        const initialHead = '{{ old('head_status_id') }}';
        const initialCurrent = '{{ old('current_status_id') }}';
        if (initialHead) {
            loadChildStatuses(initialHead, initialCurrent);
        } else {
            document.getElementById('current_status_id').innerHTML = '<option value="">Pilih Status Child</option>';
        }
    });

      function loadChildStatuses(headStatusId, selectedId) {
            const childStatusSelect = document.getElementById('current_status_id');
            childStatusSelect.innerHTML = '<option value="">Memuat...</option>';
            if (!headStatusId) {
                childStatusSelect.innerHTML = '<option value="">Pilih Status Child</option>';
                return;
            }
            fetch(`/api-child-statuses/${headStatusId}`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Pilih Status Child</option>';
                    data.forEach(function (item) {
                        const sel = selectedId && selectedId == item.id ? 'selected' : '';
                        options += `<option value="${item.id}" ${sel}>${item.status_name}</option>`;
                    });
                    childStatusSelect.innerHTML = options;
                })
                .catch(() => {
                    childStatusSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                });
        }

    // Script untuk komentar
    function openCommentModal(taskId, taskTitle) {
        document.getElementById('modal-task-title').textContent = 'Komentar untuk: ' + taskTitle;
        document.getElementById('modal-task-id').value = taskId;

         // (BARU) Ambil query string dari URL dan masukkan ke input
        document.getElementById('commentRedirectParams').value = window.location.search;


        const form = document.getElementById('comment-form');
        form.action = `/tasks/${taskId}/comments`;

        document.getElementById('comment-modal').classList.remove('hidden');
        loadComments(taskId);

         setTimeout(function() {
            document.querySelector('#comment-form input[name="comment"]').focus();
        }, 100);
    }

    function closeCommentModal() {
        document.getElementById('comment-modal').classList.add('hidden');
        document.getElementById('comment-list').innerHTML = '<p class="text-gray-500 text-sm">Tidak ada komentar.</p>';
    }

 function loadComments(taskId) {
    fetch(`/tasks/${taskId}/comments`)
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('comment-list');

            if (data.length === 0) {
                list.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada komentar.</p>';
                return;
            }

            // urutkan komentar dari lama ke baru
            data.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

            list.innerHTML = '';

            data.forEach((comment, index) => {
                // kalau bukan komentar pertama → tambahkan selang waktu SEBELUM komentar ini
                if (index > 0) {
                    const prev = new Date(data[index - 1].created_at);
                    const curr = new Date(comment.created_at);
                    const diffText = timeDiff(prev, curr);

                    const diffDiv = document.createElement('div');
                    diffDiv.className = 'my-2 text-xs text-gray-600 italic text-center';
                    diffDiv.innerHTML = `⏱ Selang ${diffText}`;
                    list.appendChild(diffDiv);
                }

                // kotak komentar
                const div = document.createElement('div');
                div.className = 'p-2 border rounded bg-gray-50';
                div.innerHTML = `
                    <p class="text-sm">
                        <strong>${comment.user_name}</strong> – 
                        ${new Date(comment.created_at).toLocaleString('id-ID')}
                    </p>
                    <p class="mt-1 text-gray-700">${comment.comment}</p>
                `;
                list.appendChild(div);
            });
        })
        .catch(err => {
            console.error('Gagal memuat komentar', err);
        });
}

// helper: selisih antar komentar
function timeDiff(prev, curr) {
    let diff = Math.abs(curr - prev) / 1000; // detik
    let days = Math.floor(diff / 86400);
    diff -= days * 86400;
    let hours = Math.floor(diff / 3600);
    diff -= hours * 3600;
    let minutes = Math.floor(diff / 60);

    let parts = [];
    if (days > 0) parts.push(days + " hari");
    if (hours > 0) parts.push(hours + " jam");
    if (minutes > 0) parts.push(minutes + " menit");
    if (parts.length === 0) parts.push("beberapa detik");

    return parts.join(" ");
}



    // Fungsi update task status
    async function updateTaskStatus(taskId, newStatusId) {
        try {
            const response = await fetch(`/tasks/${taskId}/update-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ current_status_id: newStatusId })
            });

            const data = await response.json();

            if (response.ok) {
                console.log('Status task berhasil diperbarui!');
            } else {
                alert('Gagal memperbarui status task: ' + (data.message || 'Error tidak diketahui'));
            }
        } catch (error) {
            console.error('Terjadi kesalahan:', error);
            alert('Terjadi kesalahan saat berkomunikasi dengan server.');
        }
    }
</script>

<!-- LINK REFERENCE -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const linksContainer = document.getElementById('links-container');
    const addLinkBtn = document.getElementById('add-link');
    
    addLinkBtn.addEventListener('click', function() {
        const linkItem = document.createElement('div');
        linkItem.className = 'link-item flex gap-2 mb-2';
        linkItem.innerHTML = `
            <input type="text" name="link_names[]" 
                   placeholder="Nama Link"
                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <input type="url" name="link_urls[]" 
                   placeholder="https://example.com"
                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="button" class="remove-link bg-red-500 text-white px-3 rounded-md hover:bg-red-600">
                Hapus
            </button>
        `;
        linksContainer.appendChild(linkItem);
        
        // Add event listener to remove button
        linkItem.querySelector('.remove-link').addEventListener('click', function() {
            linkItem.remove();
        });
    });
    
    // Add event listeners to existing remove buttons
    document.querySelectorAll('.remove-link').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.link-item').remove();
        });
    });
});
</script>


 <script>

// resources/views/tasks/index.blade.php (di dalam @push('scripts'))

// document.addEventListener('DOMContentLoaded', function () {
//     const taskContainer = document.getElementById('tasks-container'); // Ambil tbody
//     const taskRows = document.querySelectorAll('.task-row');

//     // --- Event Listener untuk Setiap Baris (TR) ---
//     taskRows.forEach(row => {
//         // Saat mulai men-drag sebuah baris
//         row.addEventListener('dragstart', (e) => {
//             e.target.classList.add('opacity-50', 'bg-yellow-100');
//             e.dataTransfer.setData('text/plain', e.target.dataset.taskId);
//             e.dataTransfer.effectAllowed = 'move';
//         });

//         // Saat baris yang di-drag berada DI ATAS baris lain (memberi highlight)
//         row.addEventListener('dragover', (e) => {
//             e.preventDefault(); // Izinkan drop di atas baris ini
//             e.target.closest('.task-row').classList.add('bg-blue-100');
//         });

//         // Saat baris yang di-drag meninggalkan area baris lain
//         row.addEventListener('dragleave', (e) => {
//             e.target.closest('.task-row').classList.remove('bg-blue-100');
//         });

//         // Saat baris yang di-drag DILEPASKAN di atas baris lain
//         row.addEventListener('drop', async (e) => {
//             e.preventDefault();
//             e.stopPropagation(); // (PENTING) Hentikan event agar tidak 'bubble up' ke tbody
//             e.target.closest('.task-row').classList.remove('bg-blue-100');

//             const draggedTaskId = e.dataTransfer.getData('text/plain');
//             const targetTaskId = e.target.closest('.task-row').dataset.taskId;

//             // Jangan lakukan apa-apa jika dilepas di atas dirinya sendiri
//             if (draggedTaskId === targetTaskId) return;

//             await updateTaskParent(draggedTaskId, targetTaskId);
//         });

//         // Saat proses drag selesai
//         row.addEventListener('dragend', (e) => {
//             e.target.classList.remove('opacity-50', 'bg-yellow-100');
//         });
//     });

//     // --- (BARU) Event Listener untuk Container Utama (TBODY) ---
//     if (taskContainer) {
//         // Saat baris yang di-drag berada DI ATAS area tbody (di antara baris)
//         taskContainer.addEventListener('dragover', (e) => {
//             e.preventDefault(); // (SANGAT PENTING) Izinkan drop di area ini
//             // Ini akan menghilangkan ikon "stop"
//         });

//         // Saat baris yang di-drag DILEPASKAN di area tbody (bukan di atas baris lain)
//         taskContainer.addEventListener('drop', async (e) => {
//             e.preventDefault();
//             const draggedTaskId = e.dataTransfer.getData('text/plain');
 
//             // Melepas di area utama berarti menjadikannya parent level 0 (parent_id = null)
//             await updateTaskParent(draggedTaskId, null);
//         });
//     }

//     // Fungsi untuk mengirim perubahan ke server (tetap sama)
//      async function updateTaskParent(taskId, newParentId) {
//         // Tampilkan loading indicator (opsional, tapi bagus untuk UX)
//         document.body.style.cursor = 'wait';

//         try {
//             const response = await fetch(`/tasks/${taskId}/set-parent`, {
//                 method: 'PATCH',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     // Mengambil token CSRF dari meta tag di layout Anda
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//                 },
//                 body: JSON.stringify({
//                     parent_id: newParentId
//                 })
//             });

//             // Ubah kembali cursor setelah request selesai
//             document.body.style.cursor = 'default';

//             if (response.ok) {
//                 // Jika berhasil (status 200-299), muat ulang halaman untuk melihat struktur baru.
//                 // Ini adalah cara paling sederhana dan andal.
//                 window.location.reload();
//             } else {
//                 // Jika ada error validasi dari server (status 422) atau error lainnya.
//                 const data = await response.json();
//                 alert('Gagal memindahkan tugas: ' + (data.error || 'Terjadi kesalahan di server.'));
//             }
//         } catch (error) {
//             document.body.style.cursor = 'default';
//             console.error('Error saat mengirim request:', error);
//             // alert('Gagal terhubung ke server. Silakan periksa koneksi Anda.');
//         }
//     }
// });
    
    document.addEventListener('DOMContentLoaded', function () {
        // --- Bagian Pengaturan Elemen ---
        const contextMenu = document.getElementById('context-menu');
        const contextEdit = document.getElementById('context-edit');
        const contextDelete = document.getElementById('context-delete');
        const contextTimesheet = document.getElementById('context-timesheet');
        const deleteTaskForm = document.getElementById('delete-task-form'); // Pastikan form ini ada di HTML Anda
        const taskRows = document.querySelectorAll('.task-row');

        // Variabel untuk menyimpan URL yang aktif saat menu muncul
        let activeDeleteUrl = null;

        // --- Event Listener untuk Setiap Baris Tugas ---
        taskRows.forEach(function (row) {
            row.addEventListener('contextmenu', function (e) {
                e.preventDefault(); // Mencegah menu default browser
                
                // Ambil semua data dari atribut baris
                const editUrl = row.getAttribute('data-edit-url');
                const deleteUrl = row.getAttribute('data-delete-url');
                const taskId = row.getAttribute('data-task-id');
                const isAssignedToMe = row.getAttribute('data-is-assigned-to-me') === 'true';

                 const redirectParams = window.location.search;

                // 1. Atur link untuk tombol Edit dengan parameter filter
                contextEdit.href = editUrl + redirectParams;
                
                // 2. Logika untuk tombol Timesheet (tampilkan/sembunyikan)
                if (isAssignedToMe) {
                    const timesheetUrl = `{{ url('/timesheets/create') }}?task_id=${taskId}`;
                    contextTimesheet.href = timesheetUrl;
                    contextTimesheet.parentElement.style.display = 'block';
                } else {
                    contextTimesheet.parentElement.style.display = 'none';
                }

                // 3. Simpan URL Hapus yang sedang aktif
                activeDeleteUrl = deleteUrl;

                // Tampilkan menu di posisi kursor
                contextMenu.style.top = e.pageY + 'px';
                contextMenu.style.left = e.pageX + 'px';
                contextMenu.style.display = 'block';
            });
        });
        
        // --- Event Listener untuk Tombol Hapus ---
        contextDelete.addEventListener('click', function (e) {
            e.preventDefault(); // Mencegah link berpindah halaman
            
            // Gunakan URL yang sudah disimpan
            if (activeDeleteUrl && confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
                  // (DIUBAH) Set parameter filter ke input tersembunyi
            document.getElementById('deleteRedirectParams').value = window.location.search;
            
                deleteTaskForm.action = activeDeleteUrl;
                deleteTaskForm.submit();
            }
        });

        // --- Sembunyikan Menu Saat Klik di Luar ---
        window.addEventListener('click', function () {
            if (contextMenu.style.display === 'block') {
                contextMenu.style.display = 'none';
                // Reset URL aktif setelah menu ditutup untuk keamanan
                activeDeleteUrl = null; 
            }
        });
    });
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
     function hideAllDescendants(parentId) {
        // Temukan semua anak langsung dari parentId
        const children = document.querySelectorAll(`.task-row[data-parentid="${parentId}"]`);

        children.forEach(child => {
            // 1. Sembunyikan baris anak ini
            child.classList.add('hidden');

            // 2. Reset ikon panahnya ke posisi "tertutup" (kanan)
            const childIcon = child.querySelector('.toggle-child svg');
            if (childIcon) {
                childIcon.classList.remove('rotate-90');
            }
            
            // 3. Panggil fungsi ini lagi untuk anak ini, untuk menyembunyikan "cucu"
            const childId = child.dataset.taskId; // Ambil ID dari anak ini
            if (childId) {
                hideAllDescendants(childId); // Rekursi
            }
        });
    }

    const toggleButtons = document.querySelectorAll('.toggle-child');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const taskId = this.getAttribute('data-id');
            const icon = this.querySelector('svg');

            // Cek apakah kita akan membuka (isOpening) atau menutup
            // Jika tidak ada class 'rotate-90', berarti panah ke kanan (tertutup), dan kita akan membukanya.
            const isOpening = !icon.classList.contains('rotate-90');

            // Toggle rotasi panah
            icon.classList.toggle('rotate-90');

            if (isOpening) {
                // Jika MEMBUKA: tampilkan HANYA anak-anak langsung (level 1)
                const directChildren = document.querySelectorAll(`.task-row[data-parentid="${taskId}"]`);
                directChildren.forEach(child => {
                    child.classList.remove('hidden');
                });
            } else {
                // Jika MENUTUP: panggil fungsi rekursif untuk menyembunyikan SEMUA level di bawahnya
                hideAllDescendants(taskId);
            }
        });
    });

});
</script>
<script>
function showTaskStatusTimeline(taskId) {
    const modal = document.getElementById("taskStatusTimelineModal");
    const container = document.getElementById("status-timeline-container");

    // isi loader
    container.innerHTML = `
        <div class="flex justify-center py-6">
            <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </div>
    `;

    // buka modal
    modal.classList.remove("hidden");

    // ambil data status timeline
    fetch(`/tasks/${taskId}/status-timeline`)
        .then(res => res.json())
        .then(data => {
            if (!data.timeline || data.timeline.length === 0) {
                container.innerHTML = `<p class="text-gray-500">Belum ada log status</p>`;
                return;
            }

            // sort ascending biar terbaru di bawah
            data.timeline.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

           let html = `
                <div class="relative ml-6">
                    <!-- Garis dasar abu -->
                    <div class="absolute left-0 top-0 h-full border-l-2 border-gray-300"></div>
                    <!-- Garis progress biru (sampai item terakhir) -->
                    <div class="absolute left-0 top-0 h-full border-l-2 border-blue-500 animate-[grow_1s_ease-out_forwards]"></div>
                    <div class="space-y-8">
                `;

data.timeline.forEach((item, index) => {
    const next = data.timeline[index + 1];
    let duration = '-';
    duration = item.duration;

    const isLast = index === data.timeline.length - 1;
    const circleClass = isLast
        ? "w-5 h-5  border-2 border-white ring-4 ring-green-300"
        : "w-4 h-4  border-2 border-white";

    html += `
      <div class="relative pl-6">
        <!-- Titik status -->
        <span class="absolute -left-3 top-1 ${circleClass} rounded-full" style="background-color:${item.status_color};"></span>

        <!-- Isi status -->
        <div class="flex justify-between items-center">
          <p class="font-semibold text-gray-800">${item.status}</p>
          <span class="text-sm text-gray-500">${item.created_at}</span>
        </div>
        <p class="text-sm text-gray-600">by ${item.changer}</p>
        <p class="text-xs text-gray-400">Durasi: ${duration}</p>
      </div>
    `;
});

html += `</div></div>`;


            container.innerHTML = html;
        })
        .catch(err => {
            container.innerHTML = `<p class="text-red-500">Gagal memuat timeline</p>`;
            console.error(err);
        });
}

function closeTaskStatusTimelineModal() {
    document.getElementById("taskStatusTimelineModal").classList.add("hidden");
}
</script>
