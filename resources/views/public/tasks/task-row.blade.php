
@php
    $isAssignedToMe = $task->assignments->contains('id', $currentKaryawanId);
@endphp

<tr style="background:{{ $task->currentStatus->status_color }}0D; padding-left: {{ $level * 20 }}px;" class="{{ $level > 0 ? 'child-task' : '' }} task-row" 
data-edit-url="{{ route('tasks.edit', $task) }}"
data-delete-url="{{ route('tasks.destroy', $task) }}"
 data-is-assigned-to-me="{{ $isAssignedToMe ? 'true' : 'false' }}" 
 data-parentid="{{ $task->parent_id }}" 
 data-parent-id="{{ $task->parent_id ?? 'root' }}"
 data-childid="{{ $task->id }}" 
 data-task-id="{{ $task->id }}"
 data-level="{{ $level }}"
 x-data="{ open: false }">

    <td class="px-1 py-1 whitespace-nowrap text-sm font-medium" style="padding-left: {{ ($level * 20) + 1 }}px">
           <!-- Icon expand/collapse hanya jika ada child -->
      
          <div class="flex items-center">

    <div class="flex-shrink-0 w-8 text-center pt-2">
      @if($task->children->isNotEmpty())
    <button class="toggle-child" data-id="{{ $task->id }}" aria-label="Tampilkan/Tutup anak tugas">
        {{-- (DIUBAH) Ikon panah kanan + class rotate-90 agar defaultnya ke bawah --}}
        <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200 rotate-90" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
    </button>
@endif
    </div>

    <div class="flex-grow text-sm">
        <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background-color:{{ $task->currentStatus->status_color }};  margin-right: 8px; vertical-align: middle;"></span>
        <a href="{{ route('public.tasks.view', $task) . (request()->getQueryString() ? '?' . request()->getQueryString() : '') }}" target="blank" class="text-gray-900 hover:underline font-semibold">
            {{ $task->title }}
        </a>
    </div>
    
</div>
        </td>
        <td>
            <button
                type="button"
                onclick='openCreateChildModal({{ $task->id }}, {!! json_encode($task->title) !!}, {{ $task->head_status_id }})'
                class="bg-cyan-100 text-cyan-700 hover:bg-cyan-200 rounded-md px-3 py-1 flex items-center gap-1 shadow-sm transition duration-200"
                aria-label="Tambah anak tugas"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
        </td>
    <td class="px-1 py-1 text-xs text-gray-700">
        <select required
            data-task-id="{{ $task->id }}"
            onchange="updateTaskStatus(this.dataset.taskId, this.value)"
            class="statustask mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @foreach ($childStatuses as $status)
                @if($task->head_status_id == $status->head_status_id)
                    <option value="{{ $status->id }}" data-color="{{ $status->status_color ?? '#000000' }}" @selected($task->current_status_id == $status->id)>
                        {{ $status->status_name }}
                    </option>
                @endif
            @endforeach
        </select>
    </td>
        <td class="px-1 py-1 text-xs text-gray-700">
        <button
            type="button"
            class="btn btn-sm btn-outline-info flex items-center gap-1"
            onclick="showTaskStatusTimeline({{ $task->id }})"
            title="Lihat Histori Status">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V12"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8H12.01"/>
            </svg>
            <span class="sr-only">Histori Status</span>
        </button>
    </td>

    <!-- Tanggal Mulai -->
    <td class="px-1 py-1 text-xs text-gray-700">
        {{ $task->planned_start ? $task->planned_start->format('d M y') : '-' }}
    </td>

     <!-- Tanggal Selesai (warna merah jika sudah lewat) -->
    <td class="px-1 py-1 text-xs text-gray-700">
        {{ $task->planned_end ? $task->planned_end->format('d M y') : '-' }}
    </td>

    <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-600">
    @if($task->planned_start && $task->planned_end)
        @php
            // Pastikan keduanya adalah objek Carbon untuk perhitungan
            $start = \Carbon\Carbon::parse($task->planned_start);
            $end = \Carbon\Carbon::parse($task->planned_end);
            
            // Hitung selisih hari, tambahkan 1 agar inklusif
            // Contoh: 1 Jan - 1 Jan dihitung 1 hari
            $mandays = $start->diffInDays($end) + 1;
        @endphp
        <span class="font-medium">{{ $mandays }} d</span>
    @else
        {{-- Jika salah satu tanggal kosong, tampilkan strip --}}
        -
    @endif
</td>

     <!-- Tanggal Mulai -->
    <td class="px-1 py-1 text-gray-700 text-xs">
        {{ $task->actual_start ? $task->actual_start->format('d M y') : '-' }}
    </td>

     <!-- Tanggal Selesai (warna merah jika sudah lewat) -->
    <td class="px-1 py-1 text-gray-700 text-xs">
          {{ $task->actual_end ? $task->actual_end->format('d M y') : '-' }}
    </td>

    <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-600">
    @if($task->actual_start && $task->actual_end)
        @php
            $start = \Carbon\Carbon::parse($task->actual_start);
            $end = \Carbon\Carbon::parse($task->actual_end);
            
            // Hitung selisih hari, tambahkan 1 agar inklusif
            $mandays = $start->diffInDays($end) + 1;
        @endphp
        <span class="font-medium">{{ $mandays }} d</span>
    @else
        {{-- Jika salah satu tanggal kosong, tampilkan strip --}}
        -
    @endif
</td>


    <td class="px-1 py-1 text-xs text-gray-700">
        @if($task->assignments && $task->assignments->count())
            <ul class="list-inside list-disc">
                @foreach($task->assignments as $assignment)
                  <a target="blank" href="https://wa.me/{{  $assignment->phone_no }}?text={{ $task->title }}"><span class="mb-1 inline-block bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                        {{ $assignment->nickname }}
                    </span></a>
                @endforeach
            </ul>
        @else
            -
        @endif
    </td>

  <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">
    @if($task->links_count > 0)
        <div class="flex items-center">
            <svg class="h-4 w-4 text-indigo-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            
            @foreach($task->links as $index => $link)
                <a href="{{ $link->url }}" 
                   target="_blank"
                   class="text-indigo-600 hover:text-indigo-900 underline mr-1"
                   title="{{ $link->name }}">
                    {{ $link->name }}
                </a>
                @if(!$loop->last), @endif
            @endforeach
            
            <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full ml-2">
                {{ $task->links_count }}
            </span>
        </div>
    @else
        <span class="text-gray-500">0 link</span>
    @endif
</td>

    <td class="px-1 py-1 text-xs text-gray-700" style="cursor: pointer;" 
    onclick="openCommentModal({{ $task->id }}, {{ json_encode($task->title) }})">
    <div x-data="{ expanded: false }">
        @php
            $lastComment = $task->comments->sortByDesc('created_at')->first();
        @endphp

        @if ($lastComment)
            @php
                // Tentukan warna hanya untuk timestamp
                $daysDiff = $lastComment->created_at->diffInDays(now());
                $timeColorClass = $daysDiff < 1 ? 'text-green-500' : 'text-yellow-500';
            @endphp

            <div class="text-xs">
                <strong>{{ $lastComment->user->name ?? 'N/A' }}:</strong>
                {{ Str::limit($lastComment->comment, 50) }}
                <span class="{{ $timeColorClass }}">
                    ({{ $lastComment->created_at->diffForHumans() }})
                </span>
            </div>
        @else
            <span class="text-xs text-red-500">Belum ada komentar.</span> <br/>
        @endif
    </div>
</td>

 
</tr>

@foreach($task->children as $child)
    @include('public.tasks.task-row', ['task' => $child, 'level' => $level + 1, 'childStatuses' => $childStatuses])
@endforeach