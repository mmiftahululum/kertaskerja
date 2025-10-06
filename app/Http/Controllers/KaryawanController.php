<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;


class KaryawanController extends Controller
{
     public function __invoke(Request $request)
    {
        
    }
    
    public function index()
    {
        $karyawans = Karyawan::orderBy('id', 'desc')->paginate(15);
        return view('masterdata.karyawans.index', compact('karyawans'));
    }

    public function create()
    {
        return view('masterdata.karyawans.create');
    }

    public function store(Request $request)
    {
       $data = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:karyawans,email',
            'phone_no' => 'nullable|string|max:20',
            'username_git' => 'required|string|max:255|unique:karyawans,username_git',
            'username_vpn' => 'nullable|string|max:255',
            'tanggal_berakhir_kontrak' => 'nullable|date',
            'sebagai' => 'nullable|string',
        ]);

        Karyawan::create($data);

        return redirect()->route('karyawans')->with('success', 'Karyawan berhasil dibuat.');
    }

    public function show()
    {
         $karyawan = Karyawan::findOrFail($id);
        return view('masterdata.karyawans.show', compact('karyawan'));
    }

    public function edit(Karyawan $karyawan)
    {
        return view('masterdata.karyawans.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
      $data = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:karyawans,email,' . $karyawan->id,
            'phone_no' => 'nullable|string|max:20',
            'username_git' => 'required|string|max:255|unique:karyawans,username_git,' . $karyawan->id,
            'username_vpn' => 'nullable|string|max:255',
            'tanggal_berakhir_kontrak' => 'nullable|date',
            'sebagai' => 'nullable|string',
        ]);

        $karyawan->update($data);

        return redirect()->route('karyawans')->with('success', 'Karyawan berhasil diupdate.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('karyawans')->with('success', 'Karyawan berhasil dihapus.');
    }
}
