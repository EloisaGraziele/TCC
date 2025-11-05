<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Secretaria;
use Illuminate\Support\Facades\Hash;

class SecretariaController extends Controller
{
    private function checkAuth()
    {
        if (!Session::get('admin_logged_in')) {
            return redirect()->route('admin.login');
        }
        return null;
    }

    public function create()
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        
        return view('admin.secretaria-create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:secretarias',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        Secretaria::create($validated);

        return redirect()->route('admin.secretaria.create')
            ->with('success', 'Secretaria cadastrada com sucesso!');
    }
}