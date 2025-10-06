<?php

namespace App\Http\Controllers;

use App\Models\TaskFile; 
use Illuminate\Support\Facades\Storage; 
use App\Models\Task;
use App\Models\Karyawan;
use App\Models\ChildStatus;
use App\Models\TaskStatusLog;
use App\Models\HeadStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;        
use Carbon\Carbon;  // <-- ditambah
use Illuminate\Support\Collection;   

class TaskController extends Controller
{
    /**
     * Menampilkan semua tugas dengan relasi ke view
     *
     * @return \Illuminate\View\View
     */


public function index(Request $request)
{
    // Ambil semua parameter filter
    $filterClose = $request->get('filter_close', '0');
    $filterMyTask  = $request->get('filter_mytask', '0');
    $operator = $request->get('operator', '');
    $filterAssignedTo = $request->get('filter_assigned_to', '');
    $statusIds = $request->get('status_ids', []);
    $searchQuery = $request->get('q', '');
    $searchIds = $request->get('search_ids', []); // (DIUBAH) Menerima array ID

    $searchTitles = [];
    // **LOGIKA BARU**: Jika ada ID dari combobox, ambil semua judulnya
    if (!empty($searchIds)) {
        $searchTitles = Task::whereIn('id', $searchIds)->pluck('title')->toArray();
    }

    $userId = Auth::id();
    $userEmail = Auth::user()->email ?? '';
    
    // Query dasar
    $validTasksQuery = Task::with([
        'parent', 'headStatus', 'currentStatus', 'assignments', 'files',
        'comments' => fn($q) => $q->latest()->take(20),
        'comments.user'
    ]);

    $matchingTaskIds = [];

    // **LOGIKA BARU**: Menggabungkan pencarian dari input teks dan combobox
    if (!empty($searchQuery) || !empty($searchTitles)) {
        $matchingTaskIds = Task::query()
            ->where(function ($query) use ($searchQuery, $searchTitles) {
                // Pencarian dari input teks
                if (!empty($searchQuery)) {
                    $query->where('title', 'LIKE', '%' . $searchQuery . '%');
                }
                // Pencarian dari combobox (multiple)
                if (!empty($searchTitles)) {
                    foreach ($searchTitles as $title) {
                        $query->orWhere('title', 'LIKE', '%' . $title . '%');
                    }
                }
            })
            ->pluck('id')->toArray();

        if (!empty($matchingTaskIds)) {
            $descendantIds = $this->collectDescendantIds($matchingTaskIds);
            $allRelevantIds = array_unique(array_merge($matchingTaskIds, $descendantIds));
            $validTasksQuery->whereIn('id', $allRelevantIds);
        } else {
            $validTasksQuery->whereRaw('0 = 1');
        }
    }


      if($filterMyTask == "1" || $filterAssignedTo){

        $emailToFilter = '';
        if (!empty($filterAssignedTo)) {
            $assignedEmployee = Karyawan::find($filterAssignedTo);
            if ($assignedEmployee) {
                $emailToFilter = $assignedEmployee->email;
            }
        } elseif ($filterMyTask == '1') {
            $emailToFilter = $userEmail;
        }

        $assignedTaskIds = Task::whereHas('assignments', fn($q) => $q->where('email', $emailToFilter))->pluck('id')->toArray();
        
        // 1. Ambil semua ID task yang di-assign ke user yang sedang login
      

        // 2. Jika user punya assignment
        if (!empty($assignedTaskIds)) {
            // 3. Ambil semua parent dari task-task tersebut (untuk konteks)
            $ancestorIds = $this->collectAncestorIds($assignedTaskIds);

            // 4. Ambil semua child dari task-task tersebut (sub-tugas)
            $descendantIds = $this->collectDescendantIds($assignedTaskIds);
            
            // 5. Gabungkan semua ID yang relevan (assigned, parents, children)
            $relevantTaskIds = array_unique(array_merge($assignedTaskIds, $ancestorIds, $descendantIds));
            
            // 6. Terapkan ke query utama
            $validTasksQuery->whereIn('id', $relevantTaskIds);
        } else {
            // Jika user tidak punya assignment sama sekali, jangan tampilkan apa-apa
            $validTasksQuery->whereRaw('0 = 1');
        }
    }
  
    // ... Sisa filter lainnya tetap sama ...
    if (!$request->has('filter_close') || $request->filter_close != '1') {
        $validTasksQuery->whereHas('currentStatus', fn($q) => $q->where('status_code', '<>', 'CLOSE'));
    }

    if (!empty($operator) && !empty($statusIds)) {
        $validTasksQuery->where(fn($q) => $operator === '=' ? $q->whereIn('current_status_id', $statusIds) : $q->whereNotIn('current_status_id', $statusIds));
    }
    
    $allValidTasks = $validTasksQuery->orderBy('order_column', 'asc')->orderBy('planned_start', 'ASC')->get();
    $taskTree = $this->buildTaskTree($allValidTasks);
    
    $tasksForDisplay = collect();
    if (!empty($searchQuery) || !empty($searchTitles)) {
        $tasksForDisplay = $taskTree->whereIn('id', $matchingTaskIds);
    } else {
        $tasksForDisplay = $taskTree->where('parent_id', null);
    }
    
    // ... Sisa kode pagination dan return view tetap sama ...
    $page = $request->get('page', 1);
    $perPage = 100;
    $offset = ($page - 1) * $perPage;
    $paginatedTasks = $tasksForDisplay->slice($offset, $perPage)->values();
    
    $tasks = new \Illuminate\Pagination\LengthAwarePaginator(
        $paginatedTasks,
        $tasksForDisplay->count(),
        $perPage,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );
    
    $searchableTasks = Task::whereHas('currentStatus', fn($q) => $q->where('status_code', '<>', 'CLOSE'))
                            ->orderBy('title')
                            ->get(['id', 'title']);

    $childStatuses = ChildStatus::get();
    $headStatuses = HeadStatus::select('id', 'head_status_name')->get();
    $employees = Karyawan::select('id', 'nama_karyawan', 'nickname')->get();
    $currentKaryawan = Karyawan::where('email', $userEmail)->first();
    $currentKaryawanId = $currentKaryawan ? $currentKaryawan->id : null; 
    
    $bookmarks = Auth::user()->taskFilterBookmarks()->orderBy('name')->get();

    return view('tasks.index', compact(
            'employees', 'tasks', 'childStatuses', 'headStatuses', 'currentKaryawanId',
            'searchableTasks','bookmarks'
        ))
        ->with([
            'filter_close' => $request->filter_close ?? null,
            'filter_mytask' => $request->filter_mytask ?? null,
            'selected_operator' => $operator,
            'selected_status_ids' => $statusIds,
            'search_query' => $searchQuery
        ]);
}

  /* ------------------------------------------------------------------
     *  Helper: mengumpulkan semua ancestor (parent‑parent‑…) dari
     *          sekumpulan task ID.
     * ------------------------------------------------------------------ */
    private function collectAncestorIds(array $taskIds): array
    {
        $ancestors = [];

        foreach ($taskIds as $id) {
            $task = Task::find($id);
            while ($task && $task->parent_id) {
                $parentId = $task->parent_id;
                if (!in_array($parentId, $ancestors)) {
                    $ancestors[] = $parentId;
                }
                $task = Task::find($parentId); // naik satu level lagi
            }
        }

        return $ancestors;
    }

    /* ------------------------------------------------------------------
     *  Helper: mengumpulkan semua descendant (children‑children‑…) dari
     *          sekumpulan task ID (rekursif).
     * ------------------------------------------------------------------ */
    private function collectDescendantIds(array $taskIds): array
    {
        $descendants = [];

        $stack = $taskIds; // gunakan stack untuk iterasi DFS

        while (!empty($stack)) {
            $currentId = array_pop($stack);
            $children  = Task::where('parent_id', $currentId)->pluck('id')->toArray();

            foreach ($children as $childId) {
                if (!in_array($childId, $descendants)) {
                    $descendants[] = $childId;
                    $stack[] = $childId; // eksplorasi deeper level
                }
            }
        }

        return $descendants;
    }


/**
 * Build task tree dari flat collection
 */
private function buildTaskTree($tasks)
{
    // Group tasks by parent_id
    $groupedTasks = $tasks->groupBy('parent_id');
    
    // Set children untuk setiap task
    $tasks->each(function ($task) use ($groupedTasks) {
        if (isset($groupedTasks[$task->id])) {
            $task->setRelation('children', $groupedTasks[$task->id]);
        } else {
            $task->setRelation('children', collect());
        }
    });
    
    return $tasks;
}

    /**
     * Menampilkan detail satu tugas beserta semua relasinya ke view
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
   // app/Http/Controllers/TaskController.php

    public function show($id)
    {
        // Query untuk mengambil task tetap sama
        $task = Task::with([
            'parent',
            'children',
            'headStatus',
            'currentStatus',
            'assignments', // Pastikan relasi ini benar
            'files',
            'comments.user',
            'links',
        ])->find($id);

        if (!$task) {
            return redirect()->route('tasks.index')
                ->with('error', 'Tugas tidak ditemukan');
        }

        // (BARU) Logika untuk mengumpulkan breadcrumbs
        $breadcrumbs = collect();
        $currentParent = $task->parent;
        while ($currentParent) {
            $breadcrumbs->prepend($currentParent); // prepend() untuk urutan dari atas ke bawah
            $currentParent = $currentParent->parent;
        }

        // (DIUBAH) Kirim variabel $breadcrumbs ke view
        return view('tasks.view', compact('task', 'breadcrumbs'));
    }

    /**
     * Menampilkan form buat tugas baru
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $headStatuses = HeadStatus::select('id', 'head_status_name')->get();
        $employees = Karyawan::select('id', 'nama_karyawan')->get();

        return view('tasks.create', compact('headStatuses', 'employees'));
    }

    /**
     * Menyimpan tugas baru dan redirect ke index dengan flash message
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:tasks,id',
            'head_status_id' => 'required|exists:head_statuses,id',
            'current_status_id' => 'required|exists:child_statuses,id',
            'planned_start' => 'nullable|date',
            'planned_end' => 'nullable|date|after_or_equal:planned_start',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'files.*' => 'file|max:10240',
        ]);

          if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $taskData = $request->except(['assignments','files']);
        $maxOrder = Task::where('parent_id', $request->parent_id)->max('order_column');
        $taskData['order_column'] = $maxOrder + 1;
        $task = Task::create($taskData);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('public/tugas/' . $task->id);

                    $task->files()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getClientMimeType(),
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now(),
                    ]);
                }
            }
        }

         if ($request->has('assignments') && is_array($request->assignments)) {
            // map daftar id menjadi format id => [pivot data]
            $now = now();
            $pivot = collect($request->assignments)->mapWithKeys(function ($id) use ($now) {
                return [$id => [
                    'assigned_at'  => $now,
                    'completed_at' => null,
                    'is_completed' => false,
                ]];
            })->toArray();

            $task->assignments()->sync($pivot);
        }

         if ($request->has('link_names') && is_array($request->link_names)) {
            foreach ($request->link_names as $index => $name) {
                if (!empty($name) && !empty($request->link_urls[$index])) {
                    $task->links()->create([
                        'name' => $name,
                        'url' => $request->link_urls[$index]
                    ]);
                }
            }
        }
        
          TaskStatusLog::create([
            'task_id'  => $task->id,
            'child_status_id' => $request->current_status_id,
            'user_id' => auth()->id()
        ]);

        // Kode BARU
        $redirectUrl = route('tasks.index') . $request->input('_redirect_params', '');

        return redirect($redirectUrl)
            ->with('success', 'Tugas berhasil dibuat');
    }

    public function reparent(Request $request)
{
    $request->validate([
        'task_id' => 'required|exists:tasks,id',
        'parent_id' => 'nullable|exists:tasks,id', // Parent bisa null (jadi root task)
    ]);

    $task = Task::find($request->task_id);
    
    // Cek agar task tidak menjadi parent dari dirinya sendiri
    if ($task->id == $request->parent_id) {
        return response()->json(['status' => 'error', 'message' => 'Task tidak bisa menjadi parent dari dirinya sendiri.'], 422);
    }

    $task->parent_id = $request->parent_id;

    // Set order_column ke posisi terakhir di dalam parent baru
    $maxOrder = Task::where('parent_id', $request->parent_id)->max('order_column');
    $task->order_column = $maxOrder + 1;
    
    $task->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Parent task berhasil diubah.'
    ], 200);
}

public function reorder(Request $request)
{
    $request->validate([
        'ids' => 'required|array'
    ]);

    foreach ($request->ids as $index => $id) {
        // Update order_column sesuai urutan baru dari array
        Task::where('id', $id)->update(['order_column' => $index]);
    }

    return response()->json(['status' => 'success'], 200);
}

    private function getTaskPath(Task $task): string
    {
        $path = collect();
        $current = $task;
        while ($current) {
            $path->prepend($current->title);
            $current = $current->parent;
        }
        return $path->implode(' / ');
    }
    /**
     * Menampilkan form edit tugas
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
   public function edit($id)
    {
        $task = Task::with(['assignments', 'links', 'parent'])->find($id);

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Tugas tidak ditemukan');
        }

        // (DIUBAH) Logika untuk memfilter dan menambahkan path ke $mTasks
        // 1. Ambil semua task dengan relasi yang dibutuhkan
        $allTasks = Task::with(['currentStatus', 'parent'])->get();

        // 2. Filter koleksi DAN tambahkan atribut 'path' baru
        $mTasks = $allTasks->filter(function ($potentialParent) use ($task) {
            // Aturan 1: Sebuah task tidak bisa menjadi parent untuk dirinya sendiri
            if ($potentialParent->id === $task->id) {
                return false;
            }
            // Aturan 2: Cek apakah task ini atau parent di atasnya ada yang berstatus 'CLOSE'
            if ($this->hasClosedAncestor($potentialParent)) {
                return false;
            }
            return true;
        })->map(function ($validParent) {
            // Aturan 3: Buat dan tambahkan atribut 'path' ke setiap task yang valid
            $validParent->path = $this->getTaskPath($validParent);
            return $validParent;
        });

        $headStatuses = HeadStatus::select('id', 'head_status_name')->get();
        $employees = Karyawan::select('id', 'nama_karyawan', 'nickname')->get();

        return view('tasks.edit', compact('task', 'headStatuses', 'employees', 'mTasks'));
    }


 /**
     * Helper method untuk memeriksa apakah sebuah task atau salah satu parentnya
     * memiliki status 'CLOSE'.
     */
    private function hasClosedAncestor(Task $task)
    {
        $current = $task;
        // Lakukan perulangan ke atas melalui rantai parent
        while ($current) {
            // Periksa status task saat ini
            if ($current->currentStatus && $current->currentStatus->status_code === 'CLOSE') {
                return true; // Ditemukan status CLOSE, hentikan dan kembalikan true
            }
            // Pindah ke parent berikutnya
            $current = $current->parent;
        }
        return false; // Tidak ada status CLOSE di seluruh rantai
    }

    /**
     * Memperbarui tugas dan redirect ke index dengan flash message
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $task = Task::find($id);

         if (!$task) {
            return redirect()->route('tasks.index')
                ->with('error', 'Tugas tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'parent_id' => 'nullable|exists:tasks,id',
        'head_status_id' => 'required|exists:head_statuses,id',
        'current_status_id' => 'required|exists:child_statuses,id',
        'planned_start' => 'nullable|date',
        'planned_end' => 'nullable|date|after_or_equal:planned_start',
        'progress_percent' => 'nullable|integer|min:0|max:100',
        'link_names' => 'nullable|array',
        'link_names.*' => 'string|max:255',
        'link_urls' => 'nullable|array',
        'link_urls.*' => 'url',
        'files.*' => 'file|max:10240',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $oldStatusId = $task->current_status_id;

    $task->update($request->except(['assignments', 'link_names', 'link_urls','files']));


    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            if ($file->isValid()) {
                $path = $file->store('public/tugas/' . $task->id);

                $task->files()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => now(),
                ]);
            }
        }
    }

    // Handle assignments
   $assignments = $request->get('assignments', []); // Ambil 'assignments', jika tidak ada, default ke array kosong.

    if (is_array($assignments)) {
        $now = now();
        $pivot = collect($assignments)->mapWithKeys(function ($id) use ($now) {
            return [$id => [
                'assigned_at'  => $now,
                'completed_at' => null,
                'is_completed' => false,
            ]];
        })->toArray();

        // Jika $pivot kosong (assignments: []), sync([]) akan menghapus semua.
        $task->assignments()->sync($pivot);
    }

    // Handle links
     $task->links()->delete(); // 🔥 Hapus semua link lama
    if ($request->has('link_names') && is_array($request->link_names)) {
        foreach ($request->link_names as $index => $name) {
            if (!empty($name) && !empty($request->link_urls[$index])) {
                $task->links()->create([
                    'name' => $name,
                    'url' => $request->link_urls[$index]
                ]);
            }
        }
    }
    

    // (LOGIKA BARU) Hanya buat log jika statusnya benar-benar berubah
    if ($oldStatusId != $request->current_status_id) {
        TaskStatusLog::create([
            'task_id'         => $task->id,
            'child_status_id' => $request->current_status_id,
            'user_id'         => auth()->id()
        ]);
    }

    // Ganti baris return yang lama dengan ini:
    $redirectUrl = route('tasks.index') . $request->input('_redirect_params', '');

    return redirect($redirectUrl)
        ->with('success', 'Tugas berhasil diperbarui');

    }

     public function deleteFile(TaskFile $file)
    {
        try {
            // Opsional: Tambahkan pengecekan otorisasi di sini
            // $this->authorize('delete', $file);

            // Hapus file dari storage
            Storage::delete($file->file_path);

            // Hapus record file dari database
            $file->delete();

            // Kirim respon sukses dalam format JSON
            return response()->json(['success' => true, 'message' => 'File berhasil dihapus.']);

        } catch (\Exception $e) {
            // Jika terjadi error, kirim respon error
            return response()->json(['success' => false, 'message' => 'Gagal menghapus file.'], 500);
        }
    }
    
    public function downloadFile(TaskFile $file)
    {
        // Anda bisa menambahkan pengecekan otorisasi di sini jika perlu
        return Storage::download($file->file_path, $file->file_name);
    }

    public function updateStatus(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'current_status_id' => 'required|exists:child_statuses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $task->current_status_id = $request->current_status_id;
        $task->save();

          TaskStatusLog::create([
            'task_id'  => $task->id,
            'child_status_id' => $request->current_status_id,
            'user_id' => auth()->id()
        ]);

        return response()->json(['message' => 'Status task berhasil diperbarui.', 'task' => $task->fresh()]);
    }


    public function setParent(Request $request, Task $task)
{
    // Ambil ID parent baru dari request, bisa null jika dilepas ke area utama
    $newParentId = $request->input('parent_id');

    // Validasi 1: Sebuah tugas tidak bisa menjadi parent untuk dirinya sendiri
    if ($task->id == $newParentId) {
        return response()->json(['error' => 'Sebuah tugas tidak bisa menjadi parent untuk dirinya sendiri.'], 422);
    }

    // Validasi 2: Parent baru tidak boleh merupakan turunan dari tugas yang dipindah
    // Ini untuk mencegah loop tak terbatas (misal: A > B > C, lalu C dipindah jadi parent A)
    if ($newParentId) {
        // Kita gunakan helper 'collectDescendantIds' yang sudah ada
        $descendantIds = $this->collectDescendantIds([$task->id]);
        if (in_array($newParentId, $descendantIds)) {
            return response()->json(['error' => 'Tidak bisa memindahkan tugas ke dalam sub-tugasnya sendiri.'], 422);
        }
    }

    $task->parent_id = $newParentId;
    $task->save();

    return response()->json(['success' => 'Hierarki tugas berhasil diperbarui.']);
}

    /**
     * Mengambil semua status child berdasarkan head_status_id 
     * (bisa tetap digunakan untuk AJAX jika diperlukan)
     */
    public function getStatusChildrenByHead($headStatusId)
    {
        $statusChildren = StatusChild::where('head_status_id', $headStatusId)
            ->select('id', 'status_name', 'status_code', 'status_color')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $statusChildren,
            'message' => 'Status child berhasil diambil'
        ]);
    }

    /**
     * Mengambil semua karyawan untuk assign 
     */
    public function getEmployeesForAssign()
    {
        $employees = Employee::select('id', 'nama_karyawan', 'username_git')->get();

        return response()->json([
            'success' => true,
            'data' => $employees,
            'message' => 'Karyawan berhasil diambil'
        ]);
    }

    /**
     * Mengambil semua head_status yang tersedia
     */
    public function getHeadStatuses()
    {
        $headStatuses = HeadStatus::select('id', 'head_status_name')->get();

        return response()->json([
            'success' => true,
            'data' => $headStatuses,
            'message' => 'Head status berhasil diambil'
        ]);
    }

     public function destroy(Request $request, $id)
    {
        $task = Task::find($id);

         $redirectUrl = route('tasks.index') . $request->input('_redirect_params', '');

        if (!$task) {
            return redirect($redirectUrl)
            ->with('error', 'Tugas tidak ditemukan');
        }

        // Hapus menggunakan soft delete
        $task->delete();

        return redirect($redirectUrl)
            ->with('success', 'Tugas berhasil dihapus');
    }

    /**
     * Menampilkan tugas yang terhapus (trash)
     */
    public function trash()
    {
        $tasks = Task::onlyTrashed()
            ->with(['headStatus', 'currentStatus', 'assignments'])
            ->orderBy('deleted_at', 'DESC')
            ->paginate(10);

        return view('tasks.trash', compact('tasks'));
    }

    /**
     * Restore tugas dari trash
     */
    public function restore($id)
    {
        $task = Task::onlyTrashed()->find($id);

        if (!$task) {
            return redirect()->route('tasks.trash')
                ->with('error', 'Tugas tidak ditemukan di trash');
        }

        $task->restore();

        return redirect()->route('tasks.trash')
            ->with('success', 'Tugas berhasil dipulihkan');
    }

    /**
     * Hapus permanen tugas dari trash
     */
    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()->find($id);

        if (!$task) {
            return redirect()->route('tasks.trash')
                ->with('error', 'Tugas tidak ditemukan di trash');
        }

        // Hapus relasi sebelum force delete
        $task->files()->delete();
        $task->comments()->delete();
        $task->assignments()->detach();

        $task->forceDelete();

        return redirect()->route('tasks.trash')
            ->with('success', 'Tugas berhasil dihapus permanen');
    }

    public function getStatusTimeline(Task $task)
    {
        $statusLogs = $task->statusLogs()
            ->with(['changer', 'newStatus'])
            ->orderBy('created_at', 'asc')
            ->get();

        $timeline = $statusLogs->map(function ($log, $index) use ($statusLogs) {
            $currentTimestamp = $log->created_at;
            $durationText = '';

            // Cek apakah ada log setelah ini
            if (isset($statusLogs[$index + 1])) {
                // Jika ada, hitung durasi ke log berikutnya
                $nextTimestamp = $statusLogs[$index + 1]->created_at;
                $durationInMinutes = $nextTimestamp->diffInMinutes($currentTimestamp);
                $durationText = formatDuration($durationInMinutes);
            } else {
                // Jika ini adalah log terakhir, hitung durasi dari log ini sampai sekarang
                $durationInMinutes = Carbon::now()->diffInMinutes($currentTimestamp);
                $durationText = formatDuration($durationInMinutes) . ' (saat ini)';
            }

            return [
                'id' => $log->id,
                'changer' => $log->changer?->name,
                'status' => $log->newStatus?->status_name,
                'status_color' => $log->newStatus?->status_color,
                'created_at' => $log->created_at->format('d M Y H:i'),
                'duration' => $durationText, // <-- Tambahkan durasi di sini
            ];
        });

        return response()->json([
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
            ],
            'timeline' => $timeline,
        ]);
    }




        /**
     * Method untuk mendapatkan statistik status (opsional untuk dashboard)
     */
    public function getStatusStatistics()
    {
        $stats = \DB::table('tasks')
            ->join('child_statuses', 'tasks.current_status_id', '=', 'child_statuses.id')
            ->whereNull('tasks.deleted_at')
            ->select(
                'child_statuses.id',
                'child_statuses.status_name',
                'child_statuses.status_code',
                'child_statuses.status_color',
                \DB::raw('COUNT(tasks.id) as task_count')
            )
            ->groupBy(
                'child_statuses.id', 
                'child_statuses.status_name', 
                'child_statuses.status_code',
                'child_statuses.status_color'
            )
            ->orderBy('child_statuses.status_name')
            ->get();

        return response()->json($stats);
    }

    /**
     * Method untuk export filtered tasks (opsional)
     */
    public function exportFiltered(Request $request)
    {
        $filterClose = $request->get('filter_close', '0');
        $operator = $request->get('operator', '');
        $statusIds = $request->get('status_ids', []);
        $searchQuery = $request->get('q', '');

        // Gunakan logic yang sama seperti di index() untuk filtering
        if ($filterClose == '1') {
            $query = Task::with(['headStatus', 'currentStatus', 'assignments'])
                ->whereNull('parent_id');

            if (!empty($operator) && !empty($statusIds) && is_array($statusIds)) {
                if ($operator === '=') {
                    $query->whereIn('current_status_id', $statusIds);
                } elseif ($operator === '!=') {
                    $query->whereNotIn('current_status_id', $statusIds);
                }
            }

            if (!empty($searchQuery)) {
                $query->where('title', 'LIKE', '%' . $searchQuery . '%');
            }

            $tasks = $query->orderBy('planned_start', 'ASC')->get();
        } else {
            $query = Task::with(['headStatus', 'currentStatus', 'assignments'])
                ->whereHas('currentStatus', function ($q) {
                    $q->where('status_code', '<>', 'CLOSE');
                });

            if (!empty($operator) && !empty($statusIds) && is_array($statusIds)) {
                if ($operator === '=') {
                    $query->whereIn('current_status_id', $statusIds);
                } elseif ($operator === '!=') {
                    $query->whereNotIn('current_status_id', $statusIds);
                }
            }

            if (!empty($searchQuery)) {
                $query->where('title', 'LIKE', '%' . $searchQuery . '%');
            }

            $tasks = $query->orderBy('planned_start', 'ASC')->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil',
            'total_tasks' => $tasks->count(),
            'filters_applied' => [
                'close_filter' => $filterClose,
                'operator' => $operator,
                'status_ids' => $statusIds,
                'search' => $searchQuery
            ],
            'data' => $tasks
        ]);
    }

    /**
     * Method untuk mendapatkan children dengan filter AJAX
     */
    public function getChildrenAjax(Request $request, $parentId)
    {
        $showClosed = $request->get('show_closed', false);
        
        $query = Task::with(['headStatus', 'currentStatus', 'assignments', 'files'])
            ->where('parent_id', $parentId);

        if (!$showClosed) {
            $query->whereHas('currentStatus', function ($q) {
                $q->where('status_code', '<>', 'CLOSE');
            });
        }

        $children = $query->orderBy('planned_start', 'ASC')->get();

        return response()->json([
            'success' => true,
            'data' => $children,
            'count' => $children->count()
        ]);
    }

    /**
     * Method untuk mendapatkan task yang cocok dengan filter tertentu
     */
    public function searchTasks(Request $request)
    {
        $query = $request->get('q', '');
        $statusIds = $request->get('status_ids', []);
        $limit = $request->get('limit', 10);

        $tasksQuery = Task::with(['headStatus', 'currentStatus']);

        if (!empty($query)) {
            $tasksQuery->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%')
                ->orWhere('description', 'LIKE', '%' . $query . '%');
            });
        }

        if (!empty($statusIds) && is_array($statusIds)) {
            $tasksQuery->whereIn('current_status_id', $statusIds);
        }

        // Default: tidak tampilkan yang CLOSE
        $tasksQuery->whereHas('currentStatus', function ($q) {
            $q->where('status_code', '<>', 'CLOSE');
        });

        $tasks = $tasksQuery->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'total' => $tasks->count()
        ]);
    }

    public function getBreadcrumbsApi(Task $task)
{
    // Eager load semua parent secara rekursif
    $task->load('parent');

    $breadcrumbs = collect();
    $current = $task;
    while ($current) {
        // Prepend untuk urutan dari atas ke bawah
        $breadcrumbs->prepend([
            'id' => $current->id,
            'title' => $current->title
        ]);
        $current = $current->parent;
    }

    return response()->json($breadcrumbs);
}
}
