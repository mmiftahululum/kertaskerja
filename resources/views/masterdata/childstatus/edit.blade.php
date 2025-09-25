<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-gray-900">{{ __('Edit Child Status') }}</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-medium text-gray-900">Form Edit Child Status</h3>
                    <a href="{{ route('head-statuses.index') }}" class="text-sm text-gray-600 hover:underline">Kembali</a>
                </div>

                @if($errors->any())
                    <div class="mb-4">
                        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2 rounded">
                            Silakan perbaiki kesalahan pada form.
                        </div>
                    </div>
                @endif

                <form action="{{ route('child-statuses.update', $childStatus) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="head_status_id" class="block text-sm font-medium text-gray-700">Head Status</label>
                        <select id="head_status_id" name="head_status_id" required
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 bg-white">
                            <option value="">-- Pilih Head Status --</option>
                            @foreach($headStatuses as $h)
                                <option value="{{ $h->id }}" {{ (old('head_status_id', $childStatus->head_status_id) == $h->id) ? 'selected' : '' }}>
                                    {{ $h->head_status_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('head_status_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status_name" class="block text-sm font-medium text-gray-700">Nama Child Status</label>
                        <input id="status_name" name="status_name" type="text" required
                               value="{{ old('status_name', $childStatus->status_name) }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        @error('status_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status_code" class="block text-sm font-medium text-gray-700">Kode Status (unik)</label>
                        <input id="status_code" name="status_code" type="text" required
                               value="{{ old('status_code', $childStatus->status_code) }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        @error('status_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div x-data="{ color: '{{ old('status_color', $childStatus->status_color ?? '#000000') }}' }">
                        <label for="status_color" class="block text-sm font-medium text-gray-700">Warna Status (hex)</label>
                        <div class="mt-1 flex items-center gap-3">
                            <input id="status_color" name="status_color" type="text" required
                                   x-model="color"
                                   placeholder="#RRGGBB"
                                   class="block w-36 border border-gray-300 rounded-md px-3 py-2">
                            <input type="color" x-model="color" class="w-10 h-10 p-0 border-0 rounded">
                            <div class="flex-1 text-sm text-gray-600">
                                <div class="inline-flex items-center gap-2">
                                    <span class="text-sm text-gray-500">Preview:</span>
                                    <span :style="`background:${color}`" class="inline-block w-8 h-6 rounded border"></span>
                                    <span class="text-xs text-gray-500 ml-2" x-text="color"></span>
                                </div>
                            </div>
                        </div>
                        @error('status_color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <a href="{{ route('child-statuses.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-100 hover:bg-gray-200 text-sm text-gray-700">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</x-app-layout>