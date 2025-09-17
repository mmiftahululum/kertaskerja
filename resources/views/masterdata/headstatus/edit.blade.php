<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Head Status') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Edit Head Status</h3>
                        <a href="{{ route('head-statuses.index') }}" class="text-sm text-gray-600 hover:underline">Kembali</a>
                    </div>

                    @if($errors->any())
                        <div class="mb-4">
                            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2 rounded">
                                Silakan perbaiki kesalahan pada form.
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('head-statuses.update', $headStatus) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="head_status_name" class="block text-sm font-medium text-gray-700">Nama Head Status</label>
                            <input
                                id="head_status_name"
                                name="head_status_name"
                                type="text"
                                value="{{ old('head_status_name', $headStatus->head_status_name) }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required
                            >
                            @error('head_status_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('head-statuses.index') }}" class="px-4 py-2 rounded-md bg-gray-100 hover:bg-gray-200 text-sm">Batal</a>
                            <button type="submit" class="px-4 py-2 rounded-md bg-cyan-600 text-white hover:bg-cyan-700 text-sm">Simpan Perubahan</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>