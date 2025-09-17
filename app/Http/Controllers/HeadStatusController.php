<?php

namespace App\Http\Controllers;

use App\Models\HeadStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HeadStatusController extends Controller
{
    public function index()
    {
        $headStatuses = HeadStatus::with('childStatuses')->get();
        return view('masterdata.headstatus.index', compact('headStatuses'));
    }

    public function create()
    {
        return view('masterdata.headstatus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'head_status_name' => 'required|string|max:255'
        ]);

        HeadStatus::create($request->all());

        return redirect()->route('head-statuses.index')
            ->with('success', 'Head Status berhasil dibuat.');
    }

    public function show(HeadStatus $headStatus)
    {
        $headStatus->load('childStatuses');
        return view('masterdata.headstatus.show', compact('headStatus'));
    }

    public function edit(HeadStatus $headStatus)
    {
        return view('masterdata.headstatus.edit', compact('headStatus'));
    }

    public function update(Request $request, HeadStatus $headStatus)
    {
        $request->validate([
            'head_status_name' => 'required|string|max:255'
        ]);

        $headStatus->update($request->all());

        return redirect()->route('head-statuses.index')
            ->with('success', 'Head Status berhasil diupdate.');
    }

    public function destroy(HeadStatus $headStatus)
    {
        DB::transaction(function () use ($headStatus) {
            // hapus semua child terlebih dahulu
            $headStatus->childStatuses()->delete();
            // lalu hapus head
            $headStatus->delete();
        });

        return redirect()->route('head-statuses.index')
            ->with('success', 'Head Status dan child-childnya berhasil dihapus.');
    }
}
