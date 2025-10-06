<?php

use App\Http\Controllers\HeadStatusController;
use App\Http\Controllers\ChildStatusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\MasterAppController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskPublicController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TimesheetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityLogController;

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
Route::get('/tasks/{task}/status-timeline', [TaskController::class, 'getStatusTimeline']);
Route::get('/pub/tasks', [TaskPublicController::class, 'index'])->name('public.tasks.index');
Route::get('/pub/tasks/view/{task}', [TaskPublicController::class, 'show'])->name('public.tasks.view');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/timesheets', [TimesheetController::class, 'index'])->name('timesheets.index');
    Route::get('/my-timesheet', [TimesheetController::class, 'myTimesheet'])->name('timesheets.mine');
    Route::get('/timesheets/create', [TimesheetController::class, 'create'])->name('timesheets.create');
    Route::post('/timesheets', [TimesheetController::class, 'store'])->name('timesheets.store');

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

    Route::get('/aplikasi', [MasterAppController::class, 'index'])->name('masterapps.index');
    Route::get('/aplikasi/create', [MasterAppController::class, 'create'])->name('masterapps.create');
    Route::post('/aplikasi/store', [MasterAppController::class, 'store'])->name('masterapps.store');
    Route::get('/aplikasi/show/{id}', [MasterAppController::class, 'show'])->name('masterapps.show');
    Route::get('/aplikasi/edit/{id}', [MasterAppController::class, 'edit'])->name('masterapps.edit');
    Route::put('/aplikasi/update/{id}', [MasterAppController::class, 'update'])->name('masterapps.update');
    Route::get('/aplikasi/destroy/{id}', [MasterAppController::class, 'destroy'])->name('masterapps.destroy');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::resource('head-statuses', HeadStatusController::class);
    Route::resource('child-statuses', ChildStatusController::class);

    Route::get('/tasks/files/{file}/download', [TaskController::class, 'downloadFile'])->name('tasks.files.download');
    Route::delete('/tasks/files/{file}', [TaskController::class, 'deleteFile'])->name('tasks.files.delete');

    Route::resource('tasks', TaskController::class);
    
    Route::post('tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder'); 
    Route::post('tasks/reparent', [TaskController::class, 'reparent'])->name('tasks.reparent');

    Route::patch('/tasks/{task}/set-parent', [TaskController::class, 'setParent'])->name('tasks.set-parent');
  
    Route::get('/tasks/view/{task}', [TaskController::class, 'show'])->name('tasks.view');

    Route::post('/tasks/bookmarks', [App\Http\Controllers\TaskFilterBookmarkController::class, 'store'])->name('tasks.bookmarks.store');
    Route::delete('/tasks/bookmarks/{bookmark}', [App\Http\Controllers\TaskFilterBookmarkController::class, 'destroy'])->name('tasks.bookmarks.destroy');

    // routes/web.php
    Route::get('/tasks/export/filtered', [TaskController::class, 'exportFiltered'])->name('tasks.export-filtered');
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');
    
    // Pastikan ini ada di dalam group middleware 'auth' atau yang sesuai
    Route::patch('/tasks/{task}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');

    Route::get('/api-child-statuses/{headStatusId}', function ($headStatusId) {
    $childStatuses = App\Models\ChildStatus::where('head_status_id', $headStatusId)->get();
        return response()->json($childStatuses);
    })->name('child-statuses.byHeadStatus');

   

});

require __DIR__.'/auth.php';
