<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Task;
use App\Models\TaskComment;

class TaskCommentController extends Controller
{


     /**
     * Menyimpan tugas baru dan redirect ke index dengan flash message
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Task $task)
    {
        // 1. Validasi input
        $request->validate([
            'comment' => 'required|string|max:1000', // Komentar wajib, string, maksimal 1000 karakter
        ]);

        // 2. Buat komentar baru
        $comment = new TaskComment();
        $comment->task_id = $task->id; // ID tugas dari parameter rute
        $comment->user_id = Auth::id(); // ID user yang sedang login
        $comment->comment = $request->comment;
        // Jika ada metadata tambahan, bisa ditambahkan di sini, misal:
        // $comment->metadata = ['some_key' => 'some_value']; 
        $comment->save();

        // Kode BARU
        $redirectUrl = route('tasks.index') . $request->input('_redirect_params', '');

        return redirect($redirectUrl)
            ->with('success', 'Komentar berhasil ditambahkan.');
    }

}