<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Tugas Baru') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Form Buat Tugas</h3>
                    <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:underline">Kembali ke daftar tugas</a>
                </div>

                @if ($errors->any())
                    <div class="mb-4">
                        <div class="text-sm text-red-700 bg-red-50 border border-red-100 p-3 rounded">
                            <strong class="font-semibold">Terjadi kesalahan:</strong>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Judul Tugas</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Masukkan judul tugas" required>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Deskripsikan tugas" required>{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="planned_start" class="block text-sm font-medium text-gray-700">Plan Mulai</label>
                            <input type="date" name="planned_start" id="planned_start" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="planned_end" class="block text-sm font-medium text-gray-700">Plan Selesai</label>
                            <input type="date" name="planned_end" id="planned_end" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="actual_start" class="block text-sm font-medium text-gray-700">Real Mulai</label>
                            <input type="date" name="actual_start" id="actual_start" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="actual_end" class="block text-sm font-medium text-gray-700">Real Selesai</label>
                            <input type="date" name="actual_end" id="actual_end" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Contoh dropdown untuk memilih head_status (relasi) --}}
                   <div>
                        <label for="head_status_id" class="block text-sm font-medium text-gray-700">Head Status</label>
                        <select name="head_status_id" id="head_status_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pilih Status Kepala</option>
                            @foreach($headStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->head_status_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="current_status_id" class="block text-sm font-medium text-gray-700">Status Child</label>
                        <select name="current_status_id" id="current_status_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pilih Status Child</option>
                            <!-- Opsi akan diisi otomatis lewat AJAX -->
                        </select>
                    </div>

                   <div>
                        <label for="assignments" class="block text-sm font-medium text-gray-700">Assign ke Karyawan</label>
                        <select name="assignments[]" id="assignments" multiple
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @if(isset($employees) && $employees->count())
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}"
                                        {{ in_array($emp->id, old('assignments', [])) ? 'selected' : '' }}>
                                        {{ $emp->nama_karyawan ?? $emp->name ?? 'Karyawan #'.$emp->id }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">Tidak ada karyawan tersedia</option>
                            @endif
                        </select>

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

                    {{-- Input file jika diperlukan --}}
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
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<style>
    /* Pastikan control dan dropdown Tom Select ber-background putih */
    .ts-control,
    .ts-control .item,
    .ts-control .ts-input,
    .ts-dropdown,
    .ts-dropdown .ts-option {
        background-color: #ffffff !important;
        color: #111827 !important; /* teks gelap */
    }

    /* Border dan bayangan agar terlihat di atas latar putih */
    .ts-control {
        border: 1px solid #d1d5db !important; /* gray-300 */
        box-shadow: none !important;
    }

    /* Hover dan selected option -> biru dengan teks putih */
    .ts-dropdown .ts-option,
    .ts-dropdown .ts-option * {
        transition: background-color 150ms ease, color 150ms ease;
    }

    .ts-dropdown .ts-option:hover,
    .ts-dropdown .ts-option.ts-selected,
    .ts-dropdown .ts-option.is-highlighted {
        background-color: #2563eb !important; /* blue-600 */
        color: #ffffff !important;
    }

    /* Jika TomSelect me-render class berbeda, tambahkan fallback */
    .tom-select .ts-dropdown .ts-option:hover,
    .tom-select .ts-dropdown .ts-option.ts-selected {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }

    /* Pastikan z-index agar dropdown tidak tertutup elemen lain */
    .ts-dropdown { z-index: 9999 !important; }

    /* Jika menggunakan Tom Select yang menghasilkan elemen .tomselected / .ts-control lain, tambahkan fallback */
    .tom-select .ts-control { background-color: #ffffff !important; }
</style>


<script>

     new TomSelect('#assignments', {
        plugins: ['remove_button'],
        maxItems: null,
        persist: false,
        create: false,
        placeholder: document.querySelector('#assignments')?.dataset?.placeholder || 'Cari karyawan...',
        allowEmptyOption: true,
        onInitialize: function() {
            // opsi sudah ada di DOM; Tom Select akan meng-hydrate selected options otomatis
        }
    });

    document.getElementById('head_status_id').addEventListener('change', function () {
        const headStatusId = this.value;
        const childStatusSelect = document.getElementById('current_status_id');
        
        // Kosongkan dulu opsi child status
        childStatusSelect.innerHTML = '<option value="">Memuat...</option>';

        if (!headStatusId) {
            childStatusSelect.innerHTML = '<option value="">Pilih Status Child</option>';
            return;
        }

        // Panggil endpoint untuk ambil child status berdasar head status terpilih
        fetch(`/api-child-statuses/${headStatusId}`)
            .then(response => response.json())
            .then(data => {
                // Kosongkan ulang opsi
                let options = '<option value="">Pilih Status Child</option>';
                data.forEach(function (item) {
                    options += `<option value="${item.id}">${item.status_name}</option>`;
                });
                childStatusSelect.innerHTML = options;
            })
            .catch(() => {
                childStatusSelect.innerHTML = '<option value="">Gagal memuat data</option>';
            });
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