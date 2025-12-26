<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Secretaria;

class SecretariaController extends Controller
{
    public function index()
    {
        $secretarias = Secretaria::all();
        return view('admin.secretaria-index', compact('secretarias'));
    }

    public function create()
    {
        return view('admin.secretaria-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:secretarias',
            'password' => 'required|min:6|confirmed'
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.'
        ]);

        Secretaria::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return redirect()->route('admin.secretaria.index')->with('success', 'Secretária cadastrada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $secretaria = Secretaria::findOrFail($id);
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:secretarias,email,' . $id
        ];
        
        $messages = [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está cadastrado.',
        ];
        
        if ($request->filled('password')) {
            $rules['password'] = 'min:6|confirmed';
            $messages['password.min'] = 'A senha deve ter pelo menos 6 caracteres.';
            $messages['password.confirmed'] = 'A confirmação da senha não confere.';
        }
        
        $validator = \Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return redirect()->route('admin.secretaria.index')
                ->withErrors($validator)
                ->withInput()
                ->with('editing_secretaria_id', $id);
        }
        
        $secretaria->name = $request->name;
        $secretaria->email = $request->email;
        
        if ($request->filled('password')) {
            $secretaria->password = bcrypt($request->password);
        }
        
        $secretaria->save();
        
        return redirect()->route('admin.secretaria.index')->with('success', 'Secretária atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $secretaria = Secretaria::findOrFail($id);
        $secretaria->delete();
        
        return redirect()->route('admin.secretaria.index')->with('success', 'Secretária removida com sucesso!');
    }
}
