<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Timesheet Saya') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg">Timesheet: {{ $karyawan->nama_karyawan }}</h3>
                    <a href="{{ route('timesheets.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Isi Timesheet</a>
                </div>

                 @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                         <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Tugas</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Waktu</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($timesheets as $timesheet)
                                <tr>
                                    <td class="px-4 py-3 text-sm">{{ $timesheet->tanggal->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $timesheet->task->title }}</td>
                                    <td class="px-4 py-3 text-sm">{{ date('H:i', strtotime($timesheet->jam_mulai)) }} - {{ date('H:i', strtotime($timesheet->jam_selesai)) }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $timesheet->keterangan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">Anda belum memiliki data timesheet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 <div class="mt-4">{{ $timesheets->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>