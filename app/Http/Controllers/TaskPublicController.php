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

class TaskPublicController extends Controller
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

    return view('public.tasks.index', compact(
            'employees', 'tasks', 'childStatuses', 'headStatuses', 'currentKaryawanId',
            'searchableTasks',
        ))
        ->with([
            'filter_close' => $request->filter_close ?? null,
            'filter_mytask' => $request->filter_mytask ?? null,
            'selected_operator' => $operator,
            'selected_status_ids' => $statusIds,
            'search_query' => $searchQuery
        ]);
}


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



}