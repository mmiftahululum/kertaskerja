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
                    <div class="items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Daftar Task</h3>
                        <div class="flex items-center gap-2">

                                    <form method="GET" action="{{ route('tasks.index') }}" id="filterForm">
                                        <label class="border rounded-md  px-2 py-1">
                                            <input class="mb-1" type="checkbox" name="filter_close" value="1" 
                                            {{ request('filter_close') == '1' ? 'checked' : '' }} 
                                            onchange="document.getElementById('filterForm').submit();">
                                           Close
                                        </label>
                                    </form>

                    
                                    <select name="operator" class="select-s1 border border-gray-300 rounded-md">
                                         <option value="" selected>Pilih Operator</option>
                                        <option value="=">= Sama dengan</option>
                                        <option value="!=" >!= Tidak sama dengan</option>
                                    </select>

                                
                                    <select name="status_ids[]" class="select-s1 select2" multiple="multiple" style="width: 400px;">
                                        @foreach($childStatuses as $status)
                                            <option data-color="{{ $status->status_color ?? '#000000' }}" value="{{ $status->id }}">{{ $status->status_name }} ({{ $status->status_code }})</option>
                                        @endforeach
                                    </select>

                                     <button class="bg-white border border-gray-300 px-3 py-1 rounded-md text-sm hover:bg-gray-50">Terapkan</button>

                                <input
                                    type="search"
                                    name="q"
                                    value="{{ request('q') }}"
                                    placeholder="Cari judul task"
                                    class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                <button class="bg-white border border-gray-300 px-3 py-1 rounded-md text-sm hover:bg-gray-50">Cari</button>

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
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-4/12" colspan="3">Judul Task</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Status Saat Ini</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">Plan start</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">Plan End</th>
                         
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">Actual start</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">Actual End</th>
                         
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider  w-2/12">Assignment</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link Reference</th>
                                    <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Komentar Terakhir</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tasks-container">
                                @foreach($tasks as $task)
                                    @if(!$task->parent_id)
                                        @include('tasks.task-row', ['task' => $task, 'level' => 0, 'childStatuses' => $childStatuses])
                                    @endif
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
            <li><a href="#" id="context-detail" style="display:block; padding:8px 15px; color:#4f46e5; text-decoration:none;">Detail</a></li>
            <li><a href="#" id="context-edit" style="display:block; padding:8px 15px; color:#fbbf24; text-decoration:none;">Edit</a></li>
            <li><a href="#" id="context-delete" style="display:block; padding:8px 15px; color:#dc2626; text-decoration:none;">Hapus</a></li>
        </ul>
    </div>

    {{-- Modal untuk Komentar --}}
    <div id="comment-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-lg mx-4 p-6">
            <h3 class="text-lg font-semibold mb-4" id="modal-task-title"></h3>

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

            <div class="mt-6">
                <h4 class="font-medium mb-2">Komentar Terbaru</h4>
                <div id="comment-list" class="space-y-3 max-h-60 overflow-y-auto">
                    <p class="text-gray-500 text-sm">Tidak ada komentar.</p>
                </div>
            </div>
        </div>
    </div>

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
                                  placeholder="Deskripsikan sub-task"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="planned_start" class="block text-sm font-medium text-gray-700">Plan Mulai</label>
                            <input type="date" name="planned_start" id="planned_start" value="{{  now()->format('Y-m-d') }}"

                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="planned_end" class="block text-sm font-medium text-gray-700">Plan Selesai</label>
                            <input type="date" name="planned_end" id="planned_end" value=""
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="actual_start" class="block text-sm font-medium text-gray-700">Real Mulai</label>
                            <input type="date" name="actual_start" id="actual_start" value=""
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="actual_end" class="block text-sm font-medium text-gray-700">Real Selesai</label>
                            <input type="date" name="actual_end" id="actual_end" value=""
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label for="head_status_id" class="block text-sm font-medium text-gray-700">Head Status</label>
                        <select name="head_status_id" id="head_status_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                                            {{ in_array($emp->id, old('assignments', [])) ? 'selected' : '' }}>
                                        {{ $emp->nama_karyawan ?? $emp->name ?? 'Karyawan #'.$emp->id }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Ketik untuk mencari, tekan Enter untuk memilih.</p>
                    </div>

                    <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Link References</label>
    <div id="links-container">
                <div class="link-item flex gap-2 mb-2">
                    <input type="text" name="link_names[]" 
                           value=""
                           placeholder="Nama Link"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <input type="url" name="link_urls[]" 
                           value=""
                           placeholder="https://example.com"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="button" class="remove-link bg-red-500 text-white px-3 rounded-md hover:bg-red-600">
                        Hapus
                    </button>
                </div>
    </div>
    <button type="button" id="add-link" class="mt-2 bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
        + Tambah Link
    </button>
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

<!-- Styles and Scripts -->
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


<script>
    // Script untuk Select2 status task
    $(document).ready(function () {

          $('.select2').select2({

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

    // Script untuk modal child task
    function openCreateChildModal(parentId, parentTitle, heatstatusid) {
        document.getElementById('parentTaskId').value = parentId;
        document.getElementById('parentTaskTitle').innerText = parentTitle;
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

        const form = document.getElementById('comment-form');
        form.action = `/tasks/${taskId}/comments`;

        document.getElementById('comment-modal').classList.remove('hidden');
        loadComments(taskId);
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
    const contextMenu = document.getElementById('context-menu');
    let selectedTaskId = null;

    // Fungsi untuk sembunyikan menu konteks
    function hideContextMenu() {
        contextMenu.style.display = 'none';
        selectedTaskId = null;
    }

    // Event klik kanan di baris tabel dengan class 'task-row'
    document.querySelectorAll('.task-row').forEach(row => {
        row.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            
            selectedTaskId = this.getAttribute('data-task-id');
            
            // atur posisi menu konteks di mouse pointer
            contextMenu.style.top = `${e.pageY}px`;
            contextMenu.style.left = `${e.pageX}px`;
            contextMenu.style.display = 'block';
        });
    });

    // Klik di luar menu konteks akan sembunyikan menu
    document.addEventListener('click', function(e) {
        if (!contextMenu.contains(e.target)) {
            hideContextMenu();
        }
    });

    // Klik menu "Detail"
    document.getElementById('context-detail').addEventListener('click', function(e) {
        e.preventDefault();
        if (!selectedTaskId) return;
        window.location.href = `/tasks/${selectedTaskId}`;
    });

    // Klik menu "Edit"
    document.getElementById('context-edit').addEventListener('click', function(e) {
        e.preventDefault();
        if (!selectedTaskId) return;
        window.location.href = `/tasks/${selectedTaskId}/edit`;
    });

    // Klik menu "Hapus"
    document.getElementById('context-delete').addEventListener('click', function(e) {
        e.preventDefault();
        if (!selectedTaskId) return;
        if (confirm('Yakin ingin menghapus task ini?')) {
            // Buat form dan submit secara dinamis
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/tasks/${selectedTaskId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
</script>
<!-- 
<script>
document.querySelectorAll('.toggle-child').forEach(button => {
     const toggleButtons = document.querySelectorAll('.toggle-child');
    button.addEventListener('click', () => {
        const parentId = button.getAttribute('data-id');
        const childRows = document.querySelectorAll(`.task-row[data-parentid="${parentId}"]`);
        const isOpen = button.getAttribute('data-open') === 'true'; // Gunakan atribut data untuk track state

        // Ubah ikon berdasarkan status
        const eyeOpen = `
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        `;

        const eyeClosed = `
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7A10.05 10.05 0 0112 11c4.478 0 8.268 2.943 9.543 7a10.05 10.05 0 01-1.125 1.21" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5a3 3 0 11-3-3 3 3 0 013 3z" />
            </svg>
        `;

        childRows.forEach(row => {
            row.style.display = isOpen ? 'none' : 'table-row';
        });

        // Ganti ikon secara dinamis
        if (isOpen) {
            button.innerHTML = eyeClosed; // Tertutup
            button.setAttribute('data-open', 'false');
        } else {
            button.innerHTML = eyeOpen; // Terbuka
            button.setAttribute('data-open', 'true');
        }
    });
});

</script> -->

<!-- 
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('.toggle-child');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const taskId = this.getAttribute('data-id');
            const parentId = this.getAttribute('data-parentid');
            const isOpen = this.getAttribute('data-open') === 'true';

            // Toggle tombol (mata) -> dari "buka" ke "tutup" atau sebaliknya

              const eyeOpen = `
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        `;

        const eyeClosed = `
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7A10.05 10.05 0 0112 11c4.478 0 8.268 2.943 9.543 7a10.05 10.05 0 01-1.125 1.21" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5a3 3 0 11-3-3 3 3 0 013 3z" />
            </svg>
        `;

            const icon = this.querySelector('svg');
           
            if (isOpen) {
                this.setAttribute('data-open', 'false');
                icon.innerHTML = eyeClosed;
            } else {
                this.setAttribute('data-open', 'true');
                icon.innerHTML = eyeOpen;
            }

            // Cari semua child task yang punya parent_id = taskId
            const childRows = document.querySelectorAll(`.task-row[data-parentid="${taskId}"]`);

            if (isOpen) {
                // Sembunyikan semua child
                childRows.forEach(row => {
                    row.classList.add('hidden');
                });
            } else {
                // Tampilkan semua child
                childRows.forEach(row => {
                    row.classList.remove('hidden');
                });
            }
        });
    });
});
</script> -->


<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('.toggle-child');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const taskId = this.getAttribute('data-id');
            const isOpen = this.getAttribute('data-open') === 'true';

             const eyeOpen = `
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        `;

        const eyeClosed = `
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7A10.05 10.05 0 0112 11c4.478 0 8.268 2.943 9.543 7a10.05 10.05 0 01-1.125 1.21" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5a3 3 0 11-3-3 3 3 0 013 3z" />
            </svg>
        `;

            // Toggle status tombol
            const icon = this.querySelector('svg');
            if (isOpen) {
                this.setAttribute('data-open', 'false');
                icon.innerHTML = eyeClosed;
            } else {
                this.setAttribute('data-open', 'true');
                icon.innerHTML = eyeOpen;
            }

            // Ambil semua row anak yang terkait dengan taskId (level 1 ke bawah)
            const allChildRows = document.querySelectorAll(`.task-row[data-parentid="${taskId}"]`);

            if (isOpen) {
                // Sembunyikan semua anak (termasuk anak dari anak)
                hideAllChildrenRecursively(allChildRows);
            } else {
                // Tampilkan semua anak (terus ke bawah)
                showAllChildrenRecursively(allChildRows);
            }
        });
    });

    // Fungsi rekursif: Sembunyikan semua child (dan child mereka)
    function hideAllChildrenRecursively(childRows) {
        childRows.forEach(row => {
            row.classList.add('hidden');

            // Cek apakah row ini punya anak sendiri (child dari anak)
            const childId = row.getAttribute('data-childid');
            const grandChildRows = document.querySelectorAll(`.task-row[data-parentid="${childId}"]`);
            if (grandChildRows.length > 0) {
                hideAllChildrenRecursively(grandChildRows);
            }
        });
    }

    // Fungsi rekursif: Tampilkan semua child (dan child mereka)
    function showAllChildrenRecursively(childRows) {
        childRows.forEach(row => {
            row.classList.remove('hidden');

            // Cari dan tampilkan anak dari anak
            const childId = row.getAttribute('data-childid');
            const grandChildRows = document.querySelectorAll(`.task-row[data-parentid="${childId}"]`);
            if (grandChildRows.length > 0) {
                showAllChildrenRecursively(grandChildRows);
            }
        });
    }
});
</script>