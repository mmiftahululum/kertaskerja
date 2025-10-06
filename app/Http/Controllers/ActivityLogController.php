<?php

// app/Http/Controllers/ActivityLogController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Ambil semua log, urutkan dari yang terbaru, dan gunakan paginasi
        $activities = Activity::with('causer', 'subject') // Eager load relasi
            ->latest()
            ->paginate(20);

        return view('logs.index', compact('activities'));
    }
}