<?php

namespace App\Http\Controllers;

use App\Models\ChildStatus;
use App\Models\HeadStatus;
use Illuminate\Http\Request;

class ChildStatusController extends Controller
{
    public function index()
    {
        $childStatuses = ChildStatus::with('headStatus')->get();
        return view('masterdata.childstatus.index', compact('childStatuses'));
    }

    public function create()
    {
        $headStatuses = HeadStatus::all();
        return view('masterdata.childstatus.create', compact('headStatuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'head_status_id' => 'required|exists:head_statuses,id',
            'status_name' => 'required|string|max:255',
            'status_code' => 'required|string|max:50',
            'status_color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        ChildStatus::create($request->all());

        // return redirect()->route('child-statuses.index')
        //     ->with('success', 'Child Status berhasil dibuat.');

              return redirect()->route('head-statuses.index')
            ->with('success', 'Child Status berhasil diupdate.');
    }

    public function show(ChildStatus $childStatus)
    {
        $childStatus->load('headStatus');
        return view('masterdata.childstatus.show', compact('childStatus'));
    }

    public function edit(ChildStatus $childStatus)
    {
        $headStatuses = HeadStatus::all();
        return view('masterdata.childstatus.edit', compact('childStatus', 'headStatuses'));
    }

    public function update(Request $request, ChildStatus $childStatus)
    {
        $request->validate([
            'head_status_id' => 'required|exists:head_statuses,id',
            'status_name' => 'required|string|max:255',
            'status_code' => 'required|string|max:50',
            'status_color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        $childStatus->update($request->all());

          return redirect()->route('head-statuses.index')
            ->with('success', 'Child Status berhasil diupdate.');
    }

    public function destroy(ChildStatus $childStatus)
    {
        $childStatus->delete();

           return redirect()->route('head-statuses.index')
            ->with('success', 'Child Status berhasil dihapus.');

        // return redirect()->route('child-statuses.index')
        //     ->with('success', 'Child Status berhasil dihapus.');
    }
}
