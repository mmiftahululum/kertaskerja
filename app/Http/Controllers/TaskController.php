<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Karyawan;
use App\Models\ChildStatus;
use App\Models\TaskStatusLog;
use App\Models\HeadStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;          // <-- ditambah
use Illuminate\Support\Collection;   

class TaskController extends Controller
{
    /**
     * Menampilkan semua tugas dengan relasi ke view
     *
     * @return \Illuminate\View\View
     */


    public function view(Task $task)
{
    return view('tasks.view', compact('task'));
}
 
public function index(Request $request)
{
// Ambil parameter filter dari request
  $filterClose = $request->get('filter_close', '0');
  $filterMyTask  = $request->get('filter_mytask', '0');
    $operator = $request->get('operator', '');
    $statusIds = $request->get('status_ids', []);
    $searchQuery = $request->get('q', '');


    if ($request->has('filter_close') && $request->filter_close == '1') {
        // Tampilkan semua task (parent saja, children akan di-load di view)
        $query = Task::with([
            'parent',
            'headStatus',
            'currentStatus', 
            'assignments',
            'files',
            'comments' => function ($query) {
                $query->latest()->take(20);
            },
            'comments.user'
        ])
        ->whereNull('parent_id')
        ->orderBy('planned_start', 'ASC');

        if (!empty($operator) && !empty($statusIds) && is_array($statusIds)) {
            if ($operator === '=') {
                $query->whereIn('current_status_id', $statusIds);
            } elseif ($operator === '!=') {
                $query->whereNotIn('current_status_id', $statusIds);
            }
        }

        // Terapkan filter search jika ada
        if (!empty($searchQuery)) {
            $query->where('title', 'LIKE', '%' . $searchQuery . '%');
        }

        $tasks = $query->paginate(100);

    } else {
        // Ambil semua tasks yang valid (status bukan CLOSE) dalam satu query
        $validTasksQuery = Task::with([
            'parent',
            'headStatus',
            'currentStatus',
            'assignments', 
            'files',
            'comments' => function ($query) {
                $query->latest()->take(20);
            },
            'comments.user'
        ])
        ->whereHas('currentStatus', function ($q) {
            $q->where('status_code', '<>', 'CLOSE');
        });

        // Terapkan filter operator dan status jika ada
        if (!empty($operator) && !empty($statusIds) && is_array($statusIds)) {
            if ($operator === '=') {
                $validTasksQuery->whereIn('current_status_id', $statusIds);
            } elseif ($operator === '!=') {
                $validTasksQuery->whereNotIn('current_status_id', $statusIds);
            }
        }

        // Terapkan filter search jika ada
        if (!empty($searchQuery)) {
            $validTasksQuery->where('title', 'LIKE', '%' . $searchQuery . '%');
        }

        if($filterMyTask == "1"){
             // --------------------------------------------------------------
            // 4️⃣ **Filter tambahan untuk assignment user**
            // --------------------------------------------------------------                   
            $userId = Auth::id();                     // id karyawan yang login
            $userEmail = Auth::user()->email ?? '';   // (bila diperlukan)

            // a) Task yang langsung di‑assign ke user
            $assignedTaskIds = Task::whereHas('assignments', function ($q) use ($userEmail) {
                $q->where('email', $userEmail);
            })->pluck('id')->toArray();

            // b) Dapatkan semua ancestor (parent‑parent‑…) dari task yang di‑assign
            $ancestorIds = $this->collectAncestorIds($assignedTaskIds);

            // c) Dapatkan semua descendant (children‑children‑…) dari task yang di‑assign
            $descendantIds = $this->collectDescendantIds($assignedTaskIds);

            // d) Gabungkan semua ID yang relevan
            $relevantTaskIds = array_unique(array_merge(
                $assignedTaskIds,
                $ancestorIds,
                $descendantIds
            ));

            // e) Terapkan filter ke query utama (hanya ambil task yang termasuk dalam $relevantTaskIds)
            if (!empty($relevantTaskIds)) {
                $validTasksQuery->whereIn('id', $relevantTaskIds);
            } else {
                // Jika tidak ada task yang relevan, kembalikan collection kosong
                $validTasksQuery->whereRaw('0 = 1');
            }
        }
       

        $allValidTasks = $validTasksQuery->orderBy('planned_start', 'ASC')->get();

        // Buat tree structure dari flat collection
        $taskTree = $this->buildTaskTree($allValidTasks);
        
        // Convert ke paginated result (ambil parent tasks saja untuk pagination)
        $parentTasks = $taskTree->where('parent_id', null);
        
        // Manual pagination
        $page = $request->get('page', 1);
        $perPage = 100;
        $offset = ($page - 1) * $perPage;
        
        $paginatedParents = $parentTasks->slice($offset, $perPage)->values();
        
        $tasks = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedParents,
            $parentTasks->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    $childStatuses = ChildStatus::get();
    $headStatuses = HeadStatus::select('id', 'head_status_name')->get();
    $employees = Karyawan::select('id', 'nama_karyawan')->get();

    return view('tasks.index', compact('employees', 'tasks', 'childStatuses', 'headStatuses'))
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
    public function show($id)
    {
        $task = Task::with([
            'parent',
            'children',
            'headStatus',
            'currentStatus',
            'assignments.karyawan',
            'files',
            'comments.user',
        ])->find($id);

        if (!$task) {
            return redirect()->route('tasks.index')
                ->with('error', 'Tugas tidak ditemukan');
        }

        return view('tasks.show', compact('task'));
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
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $task = Task::create($request->except('assignments'));

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

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil dibuat');
    }

    /**
     * Menampilkan form edit tugas
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
     
          $task = Task::with(['assignments', 'links'])->find($id); // Tambahkan with('links')

       if (!$task) {
        return redirect()->route('tasks.index')
            ->with('error', 'Tugas tidak ditemukan');
    }

        $mTasks = Task::with(['currentStatus'])->whereDoesntHave('currentStatus', function ($query) {
            $query->where('status_code', 'CLOSE');
        })->get();
        $headStatuses = HeadStatus::select('id', 'head_status_name')->get();
        $employees = Karyawan::select('id', 'nama_karyawan')->get();

        return view('tasks.edit', compact('task', 'headStatuses', 'employees','mTasks'));
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
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $task->update($request->except(['assignments', 'link_names', 'link_urls']));

    // Handle assignments
    if ($request->has('assignments') && is_array($request->assignments)) {
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

    return redirect()->route('tasks.index')
        ->with('success', 'Tugas berhasil dibuat');
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

     public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return redirect()->route('tasks.index')
                ->with('error', 'Tugas tidak ditemukan');
        }

        // Hapus menggunakan soft delete
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil dihapus (dipindahkan ke trash)');
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
    $timeline = $task->statusLogs()
        ->with(['changer', 'newStatus']) // load newStatus dan child-nya
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(function ($log) {
            return [
                'id' => $log->id,
                'changer' => $log->changer?->name, // nama user yg ubah
                'status' => $log->newStatus?->status_name, // status utama
                'status_color' => $log->newStatus?->status_color, // status utama
                'created_at' => $log->created_at->format('d M Y H:i'),
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
}
