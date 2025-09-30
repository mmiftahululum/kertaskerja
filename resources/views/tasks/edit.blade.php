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

                    <!-- Parent Task -->
                    <div class="mb-4">
                        <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Task</label>
                        <select name="parent_id" id="parent_id_set" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Tidak ada (Task Utama)</option>
                            @foreach ($mTasks as $mTask)
                                 @if($task->id ==  $mTask->id)
                                    @continue
                                @endif

                                <option data-name="{{ $mTask->title }}" value="{{ $mTask->id }}" {{ $mTask->id == $task->parent_id ? 'selected' : '' }}>
                                    {{ $mTask->title }}
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

                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700">Lampiran File (opsional)</label>
                        <input type="file" name="file" id="file"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6">
                        <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Batal
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
    });
</script>