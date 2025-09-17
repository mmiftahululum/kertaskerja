<?php

namespace App\Http\Controllers;

use App\Models\MasterApp;
use Illuminate\Http\Request;

class MasterAppController extends Controller
{

     public function __invoke(Request $request)
    {
        
    }
    
    public function index(Request $request)
    {
        $search = $request->input('search');
        $apps = MasterApp::when($search, function($query, $search) {
            $query->where('nama_apps', 'like', "%{$search}%")
                  ->orWhere('gitaws', 'like', "%{$search}%");
        })->paginate(10);

        return view('masterdata.masterapps.index', compact('apps', 'search'));
    }

    public function create()
    {
        return view('masterdata.masterapps.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_apps' => 'required|string',
            'gitaws' => 'required|string',
            'domain_url_prod' => 'nullable|string',
            'domain_url_dev' => 'nullable|string',
            'username_login_dev' => 'nullable|string',
            'password_login_dev' => 'nullable|string',
            'db_IP_port_dev' => 'nullable|string',
            'db_name' => 'nullable|string',
            'db_username' => 'nullable|string',
            'db_password' => 'nullable|string',
        ]);

        MasterApp::create($request->all());

        return redirect()->route('masterapps.index')->with('success', 'Data aplikasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = MasterApp::findOrFail($id);
        return view('masterdata.masterapps.create', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_apps' => 'required|string',
            'gitaws' => 'required|string',
            'domain_url_prod' => 'nullable|string',
            'domain_url_dev' => 'nullable|string',
            'username_login_dev' => 'nullable|string',
            'password_login_dev' => 'nullable|string',
            'db_IP_port_dev' => 'nullable|string',
            'db_name' => 'nullable|string',
            'db_username' => 'nullable|string',
            'db_password' => 'nullable|string',
        ]);

        $app = MasterApp::findOrFail($id);
        $app->update($request->all());

        return redirect()->route('masterapps.index')->with('success', 'Data aplikasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $app = MasterApp::findOrFail($id);
        $app->delete();

        return redirect()->route('masterapps.index')->with('success', 'Data aplikasi berhasil dihapus.');
    }
}
