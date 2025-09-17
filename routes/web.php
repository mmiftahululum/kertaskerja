<?php

use App\Http\Controllers\HeadStatusController;
use App\Http\Controllers\ChildStatusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\MasterAppController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/karyawans', [KaryawanController::class, 'index'])->name('karyawans');
    Route::get('/karyawans/create', [KaryawanController::class, 'create'])->name('karyawans.create');
    Route::post('/karyawans/store', [KaryawanController::class, 'store'])->name('karyawans.store');
    Route::get('/karyawans/show/{id}', [KaryawanController::class, 'show'])->name('karyawans.show');
    Route::get('/karyawans/edit/{karyawan}', [KaryawanController::class, 'edit'])->name('karyawans.edit');
    Route::post('/karyawans/update/{karyawan}', [KaryawanController::class, 'update'])->name('karyawans.update');
    Route::post('/karyawans/destroy/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawans.destroy');

    Route::get('/apps', [MasterAppController::class, 'index'])->name('masterapps.index');
    Route::get('/apps/create', [MasterAppController::class, 'create'])->name('masterapps.create');
    Route::post('/apps/store', [MasterAppController::class, 'store'])->name('masterapps.store');
    Route::get('/apps/show/{id}', [MasterAppController::class, 'show'])->name('masterapps.show');
    Route::get('/apps/edit/{id}', [MasterAppController::class, 'edit'])->name('masterapps.edit');
    Route::put('/apps/update/{id}', [MasterAppController::class, 'update'])->name('masterapps.update');
    Route::get('/apps/destroy/{id}', [MasterAppController::class, 'destroy'])->name('masterapps.destroy');

    Route::resource('head-statuses', HeadStatusController::class);
    Route::resource('child-statuses', ChildStatusController::class);
    Route::resource('tasks', TaskController::class);

    // routes/web.php
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');

    // Pastikan ini ada di dalam group middleware 'auth' atau yang sesuai
    Route::patch('/tasks/{task}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');

    Route::get('/api-child-statuses/{headStatusId}', function ($headStatusId) {
    $childStatuses = App\Models\ChildStatus::where('head_status_id', $headStatusId)->get();
        return response()->json($childStatuses);
    })->name('child-statuses.byHeadStatus');

    Route::get('tasks/{task}/comments', function (App\Models\Task $task) {
    $comments = $task->comments()
                    ->with('user:id,name')          // eager‑load nama user
                    ->orderByDesc('created_at')
                    ->get()
                    ->map(function ($c) {
                        return [
                            'id'        => $c->id,
                            'comment'   => $c->comment,
                            'user_name' => $c->user->name ?? 'Anon',
                            'created_at'=> $c->created_at->toDateTimeString(),
                        ];
                    });
    return response()->json($comments);
});

});

require __DIR__.'/auth.php';
