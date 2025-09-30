<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Isi Timesheet Baru') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('timesheets.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="task_id" class="block text-sm font-medium text-gray-700">Pilih Tugas</label>
                        <select name="task_id" id="task_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">-- Pilih Tugas yang Tersedia --</option>
                            @foreach($tasks as $task)
                                <option value="{{ $task->id }}" {{ (old('task_id', $selectedTaskId ?? '') == $task->id) ? 'selected' : '' }}>
                                    {{ $task->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                         @error('tanggal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="jam_mulai" class="block text-sm font-medium text-gray-700">Jam Mulai</label>
                            <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            @error('jam_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="jam_selesai" class="block text-sm font-medium text-gray-700">Jam Selesai</label>
                            <input type="time" name="jam_selesai" id="jam_selesai" value="{{ old('jam_selesai') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            @error('jam_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Total Durasi: <span id="durasi_tampil" class="font-bold">0 Jam</span></p>
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('keterangan') }}</textarea>
                        @error('keterangan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('timesheets.mine') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jamMulaiInput = document.getElementById('jam_mulai');
            const jamSelesaiInput = document.getElementById('jam_selesai');
            const durasiTampil = document.getElementById('durasi_tampil');

            function calculateDuration() {
                const mulai = jamMulaiInput.value;
                const selesai = jamSelesaiInput.value;

                if (mulai && selesai) {
                    const [jamMulai, menitMulai] = mulai.split(':').map(Number);
                    const [jamSelesai, menitSelesai] = selesai.split(':').map(Number);
                    
                    const totalMenitMulai = (jamMulai * 60) + menitMulai;
                    const totalMenitSelesai = (jamSelesai * 60) + menitSelesai;
                    
                    let selisihMenit = totalMenitSelesai - totalMenitMulai;

                    if (selisihMenit < 0) {
                        durasiTampil.textContent = 'Jam selesai harus lebih besar';
                        durasiTampil.classList.add('text-red-500');
                        return;
                    }
                    
                    durasiTampil.classList.remove('text-red-500');
                    const jam = Math.floor(selisihMenit / 60);
                    const menit = selisihMenit % 60;
                    
                    durasiTampil.textContent = `${jam} Jam ${menit} Menit`;
                } else {
                    durasiTampil.textContent = '0 Jam';
                    durasiTampil.classList.remove('text-red-500');
                }
            }

            jamMulaiInput.addEventListener('change', calculateDuration);
            jamSelesaiInput.addEventListener('change', calculateDuration);
        });
    </script>
</x-app-layout>