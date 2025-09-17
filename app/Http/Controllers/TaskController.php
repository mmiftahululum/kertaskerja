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
    public function index()
    {
        $tasks = Task::with([
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
        ->orderBy('planned_start', 'ASC')
        ->paginate(10);

        $childStatuses = ChildStatus::get();
        $headStatuses = HeadStatus::select('id', 'head_status_name')->get();
        $employees = Karyawan::select('id', 'nama_karyawan')->get();

        return view('tasks.index', compact('employees','tasks','childStatuses','headStatuses'));
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
        $task = Task::find($id);

        if (!$task) {
            return redirect()->route('tasks.index')
                ->with('error', 'Tugas tidak ditemukan');
        }

        $headStatuses = HeadStatus::select('id', 'head_status_name')->get();
        $employees = Karyawan::select('id', 'nama_karyawan')->get();

        return view('tasks.edit', compact('task', 'headStatuses', 'employees'));
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $task->update($request->except('assignments'));

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

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil diperbarui');
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
     * Menghapus tugas dan redirect ke index dengan flash message
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return redirect()->route('tasks.index')
                ->with('error', 'Tugas tidak ditemukan');
        }

        $task->files()->delete();
        $task->comments()->delete();
        $task->assignments()->detach();

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil dihapus');
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
}
