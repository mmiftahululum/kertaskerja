<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Karyawan;
use App\Models\ChildStatus;
use App\Models\HeadStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Menampilkan semua tugas dengan relasi ke view
     *
     * @return \Illuminate\View\View
     */
   public function index(Request $request)
    {
        $query = Task::with([
            'parent',
            'children',
            'headStatus',
            'currentStatus',
            'assignments',
            'files',
             // Muat relasi komentar dan urutkan berdasarkan waktu pembuatan terbaru
            'comments' => function ($query) {
                // Pastikan komentar diurutkan dari yang terbaru
                 $query->latest()->take(5); // Ambil 5 komentar terbaru
            },
            'comments.user'
        ])
        ->orderBy('planned_start', 'ASC');

        // Filter task utama yang currentStatus-nya BUKAN 'CLOSE'
        $query->whereHas('currentStatus', function ($q) {
            $q->where('status_code',"<>", 'CLOSE');
        });

        // Filter child tasks yang currentStatus-nya BUKAN 'CLOSE'
        // Ini akan memastikan child (dan grand-child, dst jika relasi children didefinisikan secara rekursif)
        // tidak memiliki status 'CLOSE'.
        $query->whereHas('children', function ($q) {
            $q->whereHas('currentStatus', function ($q2) {
                $q2->where('status_code', "<>",'CLOSE');
            });
        });
        
        // --- Bagian filter_close yang sudah ada, sesuaikan jika perlu ---
        if ($request->has('filter_close') && $request->filter_close == '1') {
            // Jika checkbox "Close" dicentang, kita mungkin ingin menampilkan task CLOSE.
            // Namun, karena permintaan Anda adalah "BUKAN CLOSE", bagian ini harus disesuaikan.
            // Saat ini, logika ini akan BENTROK dengan filter di atas.
            // Jika Anda ingin checkbox ini untuk filter task yang CLOSE saja,
            // maka hapus atau modifikasi filter whereDoesntHave di atas agar tidak selalu aktif.
            // Untuk skenario "tidak CLOSE" sebagai default, filter_close mungkin tidak diperlukan,
            // atau bisa diubah menjadi "tampilkan task CLOSE".
            // Saya akan mengasumsikan Anda ingin defaultnya TIDAK CLOSE.

            // Contoh: Jika filter_close=1 berarti tampilkan yang close,
            // maka kita harus menghapus whereDoesntHave di atas, dan pakai whereHas seperti ini:
            // $query->whereHas('currentStatus', function ($q) {
            //      $q->where('status_code', 'CLOSE');
            // });
            // Tapi karena permintaan Anda adalah "BUKAN CLOSE", maka saya akan biarkan filter whereDoesntHave di atas aktif.
        }
        // ----------------------------------------------------------------

        $tasks = $query->paginate(100);

        $childStatuses = ChildStatus::get();
        $headStatuses = HeadStatus::select('id', 'head_status_name')->get();
        $employees = Karyawan::select('id', 'nama_karyawan')->get();

        return view('tasks.index', compact('employees', 'tasks', 'childStatuses', 'headStatuses'))
           ->with('filter_close', $request->filter_close ?? null);
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
        $mTasks = Task::all(); // Semua task tersedia untuk dipilih sebagai parent
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
}
