<?php

// app/Http/Controllers/TimesheetController.php
namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Task;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
    // Menampilkan daftar semua timesheet (untuk admin/manajer)
    public function index()
    {
        $timesheets = Timesheet::with(['karyawan', 'task'])
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('timesheets.index', compact('timesheets'));
    }

    // Menampilkan timesheet milik karyawan yang sedang login
    public function myTimesheet()
    {
        $userEmail = Auth::user()->email;
        $karyawan = Karyawan::where('email', $userEmail)->first();

        if (!$karyawan) {
            // Jika tidak ditemukan karyawan dengan email yang sama, kembalikan halaman kosong atau beri pesan error
            return redirect()->route('dashboard')->with('error', 'Data karyawan tidak ditemukan untuk email Anda.');
        }

        $timesheets = Timesheet::with('task')
            ->where('karyawan_id', $karyawan->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('timesheets.mine', compact('timesheets', 'karyawan'));
    }

    // Menampilkan form untuk membuat timesheet baru
   // Menampilkan form untuk membuat timesheet baru
public function create(Request $request) // Tambahkan Request $request
{
    $userEmail = Auth::user()->email;
    $karyawan = Karyawan::where('email', $userEmail)->first();

    if (!$karyawan) {
         return redirect()->route('dashboard')->with('error', 'Hanya karyawan yang bisa mengisi timesheet.');
    }

    $tasks = Task::whereHas('assignments', function ($query) use ($karyawan) {
        $query->where('karyawan_id', $karyawan->id);
    })
    ->whereDoesntHave('currentStatus', function ($q) {
        $q->where('status_code', 'CLOSE');
    })
    ->get();

    // (BARU) Ambil task_id dari request jika ada
    $selectedTaskId = $request->input('task_id');

    return view('timesheets.create', compact('tasks', 'selectedTaskId'));
}

    // Menyimpan data timesheet baru
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string',
        ]);

        $userEmail = Auth::user()->email;
        $karyawan = Karyawan::where('email', $userEmail)->first();

        if (!$karyawan) {
            return back()->with('error', 'Data karyawan Anda tidak ditemukan.');
        }

        Timesheet::create([
            'karyawan_id' => $karyawan->id,
            'task_id' => $request->task_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('timesheets.mine')->with('success', 'Timesheet berhasil disimpan.');
    }
}