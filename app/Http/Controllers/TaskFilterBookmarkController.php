<?php

// app/Http/Controllers/TaskFilterBookmarkController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskFilterBookmark;
use Illuminate\Support\Facades\Auth;

class TaskFilterBookmarkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'bookmark_name' => 'required|string|max:100',
            'filters' => 'required|array'
        ]);

        Auth::user()->taskFilterBookmarks()->create([
            'name' => $request->bookmark_name,
            'filters' => $request->filters,
        ]);

        return back()->with('success', 'Filter berhasil disimpan sebagai bookmark!');
    }

    public function destroy(TaskFilterBookmark $bookmark)
    {
        // Pastikan pengguna hanya bisa menghapus bookmark miliknya sendiri
        if ($bookmark->user_id !== Auth::id()) {
            abort(403);
        }

        $bookmark->delete();

        return back()->with('success', 'Bookmark berhasil dihapus.');
    }
}