
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Task') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">


        
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Daftar Task</h3>

                        <div class="flex items-center gap-2">
                            <form method="GET" action="{{ route('tasks.index') }}" class="flex items-center gap-2">
                                <input
                                    type="search"
                                    name="q"
                                    value="{{ request('q') }}"
                                    placeholder="Cari judul task"
                                    class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                <button class="bg-white border border-gray-300 px-3 py-1 rounded-md text-sm hover:bg-gray-50">Cari</button>
                            </form>

                            <a href="{{ route('tasks.create') }}">
                                <button class="bg-cyan-500 rounded-md px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600 focus:outline-none">Tambah Task</button>
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
                                    <th></th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-4/12">Judul Task</th>
                                    <!-- <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Kepala</th> -->
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Status Saat Ini</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider  w-2/12">Assignment</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah File</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar Terakhir</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($tasks as $task)
                                    <tr>
                                        <td>
                                               <button
                                               type="button"
                                               onclick='openCreateChildModal({{ $task->id }}, {!! json_encode($task->title) !!})'
                                               class="text-cyan-600 hover:text-cyan-900 mr-2 inline-flex items-center gap-1"
                                           ><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg></button>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900 {{ $task->par ? 'pl-8' : '' }}">
    {{ $task->title }}
</td>
                                        <!-- <td class="px-4 py-3 text-sm text-gray-700">{{ $task->headStatus ? $task->headStatus->head_status_name : '-' }}</td> -->
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                                 <select required
                                                    data-task-id="{{ $task->id }}"
                                                    onchange="updateTaskStatus(this.dataset.taskId, this.value)"
                                                     class="statustask mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                >
                                                     @foreach ($childStatuses as $status)
                                                        @if($task->head_status_id == $status->head_status_id)
                                                             <option value="{{ $status->id }}" data-color="{{ $status->status_color ?? '#000000' }}" @selected($task->current_status_id == $status->id)>
                                                                     {{ $status->status_name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                        
                                        </td>
                                          <td class="px-4 py-3 text-sm text-gray-700">
                                            @if($task->assignments && $task->assignments->count())
                                                <ul class="list-inside list-disc">
                                                    @foreach($task->assignments as $assignment)
                                                             <span class="mb-1 inline-block bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                                               {{ $assignment->nama_karyawan }}
                                                            </span>
                                                    @endforeach
                                                </ul>
                                            @else
                                                -
                                            @endif
                                       </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $task->files->count() }}</td>
                                        <!-- <td class="px-4 py-3 text-sm text-gray-700">{{ $task->comments->count() }}</td> -->
                                      {{-- Contoh kolom untuk menampilkan komentar terakhir --}}
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            <div x-data="{ expanded: false }">
                                               @php
                                                    $lastComment = $task->comments->sortByDesc('created_at')->first();
                                                @endphp
                                                @if ($lastComment)
                                                    <div class="text-xs">
                                                        <strong>{{ $lastComment->user->name ?? 'N/A' }}:</strong> {{ Str::limit($lastComment->comment, 50) }}
                                                        <span class="text-gray-500">({{ $lastComment->created_at->diffForHumans() }})</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-500">Belum ada komentar.</span>
                                                @endif
                                                {{-- Tombol Tambah Komentar --}}
                                                 <button
                                                        type="button"
                                                        class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-1 px-3 rounded"
                                                        onclick="openCommentModal({{ $task->id }}, {{ json_encode($task->title) }})"
                                                    >
                                                        Tambah Komentar
                                                    </button>
                                            </div>
                                        </td>
                                        
                                        <td class="px-4 py-3 text-sm text-right">
                                            <a href="{{ route('tasks.show', $task->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Detail</a> <br/>
                                            <a href="{{ route('tasks.edit', $task->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-2">Edit</a> <br/>
                                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus task ini ? ');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-gray-500">Belum ada task tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination jika menggunakan paginate --}}
                    <div class="mt-4">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>

      {{-- ====================== MODAL ====================== --}}
            <div id="comment-modal"
                 class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg w-full max-w-lg mx-4 p-6">
                    <h3 class="text-lg font-semibold mb-4" id="modal-task-title"></h3>

                    {{-- Form tambah komentar --}}
                    <form id="comment-form" method="POST" action="">
                        @csrf
                        <input type="hidden" name="task_id" id="modal-task-id">
                        <textarea name="comment"
                                  class="w-full border rounded p-2 mb-3"
                                  rows="3"
                                  placeholder="Tuliskan komentar…"
                                  required></textarea>
                        <div class="flex justify-end space-x-2">
                            <button type="button"
                                    class="px-4 py-2 bg-gray-300 rounded"
                                    onclick="closeCommentModal()">Batal</button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded">Kirim</button>
                        </div>
                    </form>

                    {{-- Daftar komentar yang sudah ada --}}
                    <div class="mt-6">
                        <h4 class="font-medium mb-2">Komentar Terbaru</h4>
                        <div id="comment-list" class="space-y-3 max-h-60 overflow-y-auto">
                            {{-- Konten di‑isi via AJAX (JS) --}}
                            <p class="text-gray-500 text-sm">Tidak ada komentar.</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ==================================================== --}}

    
    {{-- Modal untuk Tambah Child Task --}}
    <div id="createChildTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-10 sm:top-20 mx-auto p-5 border w-7/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-medium text-gray-900">Tambah Sub-Task untuk "<span id="parentTaskTitle"></span>"</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeCreateChildModal()">
                    <span class="sr-only">Tutup</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-2">
                <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="parent_id" id="parentTaskId">

                    <div>
                        <label for="child_title" class="block text-sm font-medium text-gray-700">Judul Sub-Task</label>
                        <input type="text" name="title" id="child_title" value="{{ old('title') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Masukkan judul sub-task" required>
                    </div>

                    <div>
                        <label for="child_description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="child_description" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Deskripsikan sub-task">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="planned_start" class="block text-sm font-medium text-gray-700">Plan Mulai</label>
                            <input type="date" name="planned_start" id="planned_start" value="{{ old('planned_start', optional($task->planned_start)->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="planned_end" class="block text-sm font-medium text-gray-700">Plan Selesai</label>
                            <input type="date" name="planned_end" id="planned_end" value="{{ old('planned_end', optional($task->planned_end)->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="actual_start" class="block text-sm font-medium text-gray-700">Real Mulai</label>
                            <input type="date" name="actual_start" id="actual_start" value="{{ old('actual_start', optional($task->actual_start)->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="actual_end" class="block text-sm font-medium text-gray-700">Real Selesai</label>
                            <input type="date" name="actual_end" id="actual_end" value="{{ old('actual_end', optional($task->actual_end)->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                     <div>
                        <label for="head_status_id" class="block text-sm font-medium text-gray-700">Head Status</label>
                        <select name="head_status_id" id="head_status_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pilih Status Kepala</option>
                            @foreach($headStatuses as $status)
                                <option value="{{ $status->id }}" {{ (int)old('head_status_id', $task->head_status_id) === $status->id ? 'selected' : '' }}>
                                    {{ $status->head_status_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="current_status_id" class="block text-sm font-medium text-gray-700">Status Child</label>
                        <select name="current_status_id" id="current_status_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Memuat...</option>
                        </select>
                    </div>

                  <div>
                        <label for="assignments" class="block text-sm font-medium text-gray-700">Assign ke Karyawan</label>
                        <select name="assignments[]" id="assignments" multiple
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                data-placeholder="Cari dan pilih karyawan...">
                            @if(isset($employees) && $employees->count())
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}"
                                            data-username="{{ $emp->username_git ?? '' }}"
                                            {{ in_array($emp->id, old('assignments', (
                                                $task->assignments->pluck('employee_id')->count()
                                                    ? $task->assignments->pluck('employee_id')->toArray()
                                                    : $task->assignments->pluck('id')->toArray()
                                            ))) ? 'selected' : '' }}>
                                        {{ $emp->nama_karyawan ?? $emp->name ?? 'Karyawan #'.$emp->id }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Ketik untuk mencari, tekan Enter untuk memilih.</p>
                    </div>

                    <div>
                        <label for="child_attachment" class="block text-sm font-medium text-gray-700">Lampiran</label>
                        <input type="file" name="attachment" id="child_attachment"
                               class="mt-1 block w-full text-sm text-gray-500
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-md file:border-0
                               file:text-sm file:font-semibold
                               file:bg-indigo-50 file:text-indigo-700
                               hover:file:bg-indigo-100">
                        <p class="mt-2 text-sm text-gray-500">Opsional: Upload file terkait task.</p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeCreateChildModal()" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Sub-Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

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
</style>



<!-- ############### STATUS TAKS ###############333 -->
  <script>
 $(document).ready(function () {
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
});


</script>





<!-- ############### MODAL SHOW ADD TUGAS ###############333 -->
    <script>
                    function openCreateChildModal(parentId, parentTitle) {
                        document.getElementById('parentTaskId').value = parentId;
                        document.getElementById('parentTaskTitle').innerText = parentTitle;
                        document.getElementById('createChildTaskModal').classList.remove('hidden');
                    }

                    function closeCreateChildModal() {
                        document.getElementById('createChildTaskModal').classList.add('hidden');
                        // Opsional: reset form setelah ditutup
                        document.getElementById('createChildTaskModal').querySelector('form').reset();
                        document.getElementById('parentTaskTitle').innerText = ''; // Clear title
                    }

                    // Menutup modal jika klik di luar area modal
                    window.onclick = function(event) {
                        const modal = document.getElementById('createChildTaskModal');
                        if (event.target == modal) {
                            closeCreateChildModal();
                        }
                    }
                
                   </script>


<!-- ############### ASSIGMENT ###############333 -->
<script>
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

    const headSelect = document.getElementById('head_status_id');
    headSelect.addEventListener('change', function () {
        loadChildStatuses(this.value, null);
    });

    // load initial child statuses and set selected current_status_id
    const initialHead = '{{ old('head_status_id', $task->head_status_id) }}';
    const initialCurrent = '{{ old('current_status_id', $task->current_status_id) }}';
    if (initialHead) {
        loadChildStatuses(initialHead, initialCurrent);
    } else {
        document.getElementById('current_status_id').innerHTML = '<option value="">Pilih Status Child</option>';
    }
});
</script>

<!-- ############### KOMENTAR ###############333 -->
<script>
/**
 * Buka modal, set action form & muat komentar yang sudah ada.
 *
 * @param {Number} taskId   ID task
 * @param {String} taskTitle Judul task (untuk ditampilkan di header modal)
 */
function openCommentModal(taskId, taskTitle) {
    // Set judul & hidden input
    document.getElementById('modal-task-title').textContent = 'Komentar untuk: ' + taskTitle;
    document.getElementById('modal-task-id').value = taskId;

    // Set URL aksi form (menggunakan route Laravel)
    const form = document.getElementById('comment-form');
    form.action = `/tasks/${taskId}/comments`;   // sesuaikan dengan route Anda

    // Tampilkan modal
    document.getElementById('comment-modal').classList.remove('hidden');

    // Muat komentar via fetch API
    loadComments(taskId);
}

/**
 * Tutup modal dan bersihkan daftar komentar.
 */
function closeCommentModal() {
    document.getElementById('comment-modal').classList.add('hidden');
    document.getElementById('comment-list').innerHTML = '<p class="text-gray-500 text-sm">Tidak ada komentar.</p>';
}

/**
 * Ambil komentar dengan AJAX (fetch) dan render ke dalam #comment‑list.
 *
 * @param {Number} taskId
 */
function loadComments(taskId) {
    fetch(`/tasks/${taskId}/comments`)   // endpoint JSON – buat route di web.php & controller
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('comment-list');
            if (data.length === 0) {
                list.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada komentar.</p>';
                return;
            }
            list.innerHTML = '';
            data.forEach(comment => {
                const div = document.createElement('div');
                div.className = 'p-2 border rounded bg-gray-50';
                div.innerHTML = `
                    <p class="text-sm"><strong>${comment.user_name}</strong> – ${new Date(comment.created_at).toLocaleString()}</p>
                    <p class="mt-1 text-gray-700">${comment.comment}</p>
                `;
                list.appendChild(div);
            });
        })
        .catch(err => {
            console.error('Gagal memuat komentar', err);
        });
}
</script>

<!-- FUNGSI UPDATE TAKS STATUS  -->
<script>
     async function updateTaskStatus(taskId, newStatusId) {
        try {
            const response = await fetch(`/tasks/${taskId}/update-status`, { // Pastikan URL ini sesuai dengan route Anda
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Penting untuk Laravel
                },
                body: JSON.stringify({ current_status_id: newStatusId })
            });

            const data = await response.json();

            if (response.ok) {
                console.log('Status task berhasil diperbarui!');
                // Opsional: perbarui UI jika ada elemen lain yang perlu di-refresh
            } else {
                alert('Gagal memperbarui status task: ' + (data.message || 'Error tidak diketahui'));
            }
        } catch (error) {
            console.error('Terjadi kesalahan:', error);
            alert('Terjadi kesalahan saat berkomunikasi dengan server.');
        }
    }
</script>