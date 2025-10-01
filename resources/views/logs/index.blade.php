<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Log Aktivitas Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktivitas</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail Perubahan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($activities as $activity)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $activity->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $activity->causer->name ?? 'Sistem' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <p class="font-semibold">{{ $activity->description }}</p>
                                            <p class="text-xs">
                                                <span class="font-medium">{{ $activity->log_name }}</span>:
                                                <span class="text-gray-800">{{ $activity->subject->title ?? $activity->subject->name ?? '' }}</span>
                                            </p>
                                        </td>
                                        <td class="px-4 py-4 whitespace-normal text-xs text-gray-700">
                                            {{-- Cek apakah ada properti perubahan --}}
                                            @if ($activity->properties->has('attributes') || $activity->properties->has('old'))
                                                <ul class="space-y-1">
                                                    @if($activity->event === 'updated')
                                                        @foreach($activity->properties['old'] as $key => $value)
                                                            <li>
                                                                <strong class="font-semibold">{{ $key }}:</strong>
                                                                <span class="text-red-600 line-through">{{ $value }}</span> → 
                                                                <span class="text-green-600">{{ $activity->properties['attributes'][$key] }}</span>
                                                            </li>
                                                        @endforeach
                                                    @elseif($activity->event === 'deleted' || $activity->event === 'created')
                                                        @foreach($activity->properties['attributes'] as $key => $value)
                                                            <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            Belum ada aktivitas yang tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>