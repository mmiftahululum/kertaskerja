<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserTodo;

class UserTodoController extends Controller
{
    /**
     * Menampilkan halaman To-do List
     */
    public function index()
    {
        $userId = auth()->id();

        // 1. Ambil SEMUA to-do yang BELUM selesai
        // Diurutkan berdasarkan yang terbaru dibuat
        $incompleteTodos = UserTodo::where('user_id', $userId)
                                 ->whereNull('completed_at')
                                 ->with(['task.parentRecursive', 'task.masterApp']) // (Relasi dari customisasi Anda sebelumnya)
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        // 2. Ambil 20 to-do TERBARU yang SUDAH selesai
        // Diurutkan berdasarkan kapan diselesaikan
        $completedTodos = UserTodo::where('user_id', $userId)
                                ->whereNotNull('completed_at')
                                ->with(['task.parentRecursive', 'task.masterApp']) // (Relasi dari customisasi Anda sebelumnya)
                                ->orderBy('completed_at', 'desc') // <-- Ambil yang paling baru selesai
                                ->take(20) // <-- Batasi hanya 20
                                ->get();

        // 3. Gabungkan keduanya
        // To-do yang belum selesai akan ada di bagian atas,
        // diikuti oleh 20 to-do yang baru saja selesai.
        $todos = $incompleteTodos->merge($completedTodos);

        return view('todos.index', compact('todos'));
    }
    /**
     * Menandai to-do sebagai selesai
     */
    public function complete(Request $request, UserTodo $todo)
    {
        // Pastikan hanya pemilik yang bisa menyelesaikan
        if ($todo->user_id !== auth()->id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $todo->update(['completed_at' => now()]);

        return back()->with('success', 'To-do ditandai selesai!');
    }
}
