<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Tugas') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form action="{{ route('tasks.update', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="parent_id" id="parent_id" value="{{ old('parent_id', $task->parent_id) }}">
                     {{-- (BARU) Tambahkan input ini untuk menyimpan parameter filter --}}
                    <input type="hidden" name="_redirect_params" value="{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}">

                    <!-- Parent Task -->
                    <div class="mb-4">
                        <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Task</label>
                        <select name="parent_id" id="parent_id_set" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Tidak ada (Task Utama)</option>
                            @foreach ($mTasks as $mTask)
                                 @if($task->id ==  $mTask->id)
                                    @continue
                                @endif

                                {{-- Kode BARU --}}
                                <option data-name="{{ $mTask->title }}" value="{{ $mTask->id }}" {{ $mTask->id == $task->parent_id ? 'selected' : '' }}>
                                    {{-- Tampilkan atribut 'path' yang sudah kita buat di controller --}}
                                    {{ $mTask->path }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Judul Tugas</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Masukkan judul tugas" required>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Deskripsikan tugas">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-4 gap-1">
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

               <div class="grid grid-cols-2 gap-1">
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
</div>
                  <div>
    <label for="assignments" class="block text-sm font-medium text-gray-700">Assign ke Karyawan</label>
    <select name="assignments[]" id="assignments" multiple
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            data-placeholder="Cari dan pilih karyawan...">
        @if(isset($employees) && $employees->count())
            @foreach($employees as $emp)
                @php
                    // Cara yang lebih reliable untuk cek selected
                    $isSelected = false;
                    foreach ($task->assignments as $assignment) {
                        if ($assignment->id == $emp->id) {
                            $isSelected = true;
                            break;
                        }
                    }
                    // Juga cek old values jika form di-submit dengan error
                    $isSelected = $isSelected || (is_array(old('assignments')) && in_array($emp->id, old('assignments')));
                @endphp
                <option value="{{ $emp->id }}"
                        data-username="{{ $emp->username_git ?? '' }}"
                        {{ $isSelected ? 'selected' : '' }}>
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
        @if(old('link_names', $task->links->pluck('name')->toArray()))
            @foreach(old('link_names', $task->links->pluck('name')->toArray()) as $index => $name)
                <div class="link-item flex gap-2 mb-2">
                    <input type="text" name="link_names[]" 
                           value="{{ $name }}"
                           placeholder="Nama Link"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <input type="url" name="link_urls[]" 
                           value="{{ old('link_urls.' . $index, $task->links[$index]->url ?? '') }}"
                           placeholder="https://example.com"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="button" class="remove-link bg-red-500 text-white px-3 rounded-md hover:bg-red-600">
                        Hapus
                    </button>
                </div>
            @endforeach
        @endif
    </div>
    <button type="button" id="add-link" class="mt-2 bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
        + Tambah Link
    </button>
</div>

<div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Lampiran Saat Ini</h3>
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <ul class="divide-y divide-gray-200" id="file-list-container">
                        @forelse($task->files as $file)
                            {{-- Tambahkan id unik untuk setiap baris --}}
                            <li id="file-row-{{ $file->id }}" class="px-4 py-3 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center min-w-0">
                                    <div class="flex-shrink-0 mr-4">
                                        {{-- (SVG Ikon di sini) --}}
                                        @if(Str::startsWith($file->mime_type, 'image/'))
                                            <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                        @elseif($file->mime_type === 'application/pdf')
                                            <svg class="w-6 h-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                        @else
                                            <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('tasks.files.download', $file->id) }}" target="_blank" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 truncate" title="{{ $file->file_name }}">
                                            {{ \Illuminate\Support\Str::limit($file->file_name, 40) }}
                                        </a>
                                        <p class="text-xs text-gray-500">
                                            {{ $file->formatted_size }} &middot; Diunggah pada {{ $file->created_at->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    {{-- TOMBOL HAPUS BARU DENGAN DATA ATTRIBUTES --}}
                                    <button type="button" 
                                            class="delete-file-btn text-gray-500 hover:text-red-600 p-1" 
                                            title="Hapus file"
                                            data-url="{{ route('tasks.files.delete', $file->id) }}"
                                            data-file-id="{{ $file->id }}">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.067-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </button>
                                </div>
                            </li>
                        @empty
                            <li id="no-files-message" class="px-4 py-4 text-center text-sm text-gray-500">
                                Tidak ada file lampiran.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>


<div class="mb-6 mt-4">
    <label for="files" class="block text-sm font-medium text-gray-700 mb-1">Tambah Lampiran Baru</label>
    <input type="file" name="files[]" multiple class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-200 file:text-gray-700 file:border-0 file:px-4 file:py-2 file:mr-4 file:hover:bg-gray-300">
    <p class="mt-1 text-xs text-gray-500">Ukuran file maksimal: 10MB per file.</p>
</div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        {{-- Ganti link "Batal" yang lama dengan ini --}}
                        <a href="{{ route('tasks.index') . (request()->getQueryString() ? '?' . request()->getQueryString() : '') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                            {{ __('Batal') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<!-- Styles and Scripts -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.body.addEventListener('click', function(e) {
            // Targetkan tombol hapus, termasuk yang mungkin muncul di masa depan
            if (e.target && e.target.closest('.delete-file-btn')) {
                e.preventDefault();
                const button = e.target.closest('.delete-file-btn');
                const url = button.dataset.url;
                const fileId = button.dataset.fileId;
                
                if (confirm('Anda yakin ingin menghapus file ini?')) {
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hapus elemen baris file dari tampilan
                            const fileRow = document.getElementById('file-row-' + fileId);
                            if (fileRow) {
                                fileRow.style.transition = 'opacity 0.5s ease';
                                fileRow.style.opacity = '0';
                                setTimeout(() => {
                                    fileRow.remove();
                                    // Cek jika tidak ada file tersisa
                                    const container = document.getElementById('file-list-container');
                                    if (container.children.length === 0) {
                                        container.innerHTML = '<li class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada file lampiran.</li>';
                                    }
                                }, 500);
                            }
                            // Opsional: Tampilkan notifikasi sukses
                            // alert(data.message); 
                        } else {
                            // Tampilkan pesan error jika gagal
                            alert(data.message || 'Terjadi kesalahan.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Tidak dapat menghubungi server.');
                    });
                }
            }
        });
    });
    </script>
    
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

    // fungsi untuk load child statuses berdasarkan head_status_id dan set selected jika ada
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
  // Script untuk Select2 status task
    $(document).ready(function () {
        $('#parent_id_set').select2();

        // (BARU) Tambahkan event listener ini
        $('#parent_id_set').on('change', function() {
            const selectedId = $(this).val();
            const pathDisplay = $('#parent-path-display');

            if (!selectedId) {
                pathDisplay.html('<span class="italic">Ini akan menjadi task utama (level 0)</span>');
                return;
            }

            pathDisplay.html('<span>Memuat path...</span>');

            // Panggil API yang sudah kita buat
            fetch(`/api/tasks/${selectedId}/breadcrumbs`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        let pathHtml = '';
                        data.forEach((item, index) => {
                            // Gunakan route() helper dari Ziggy jika ada, atau hardcode URL
                            const viewUrl = `{{ url('/tasks/view') }}/${item.id}`;
                            pathHtml += `<a href="${viewUrl}" target="_blank" class="text-indigo-600 hover:underline">${item.title}</a>`;
                            if (index < data.length - 1) {
                                pathHtml += `<span class="mx-1 text-gray-400">/</span>`;
                            }
                        });
                        pathDisplay.html(pathHtml);
                    } else {
                        pathDisplay.html('<span>Parent tidak ditemukan.</span>');
                    }
                })
                .catch(() => {
                    pathDisplay.html('<span class="text-red-500">Gagal memuat path.</span>');
                });
        });

    });
</script>