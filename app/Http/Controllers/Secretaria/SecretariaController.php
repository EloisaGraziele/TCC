<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Secretaria; // Assumindo que este é o seu modelo
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SecretariaController extends Controller
{
    /**
     * Exibe o formulário de cadastro de uma nova secretária (acessível apenas por admin logado).
     * Rota: secretaria.register.create
     */
    public function create()
    {
        // Retorna a view onde o administrador logado cadastra novos usuários
        return view('secretaria.auth.register'); 
    }

    /**
     * Processa os dados e armazena a nova secretária no banco de dados.
     * Rota: secretaria.register.store
     */
    public function store(Request $request)
    {
        // 1. Validação dos dados
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // O email deve ser único na tabela 'secretarias'
            'email' => ['required', 'string', 'email', 'max:255', 'unique:secretarias'], 
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Este e-mail já está em uso por outro administrador.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        // 2. Criação do novo registro
        $secretaria = Secretaria::create([
            'name' => $request->name,
            'email' => $request->email,
            // A senha é armazenada com hash seguro
            'password' => Hash::make($request->password), 
        ]);
        
        // 3. Redireciona com uma mensagem de sucesso
        return redirect()->route('secretaria.dashboard')
                         ->with('status', 'Nova secretária ' . $secretaria->name . ' cadastrada com sucesso!');
    }

    // Você pode adicionar outras funções de gerenciamento aqui (index, edit, update, destroy)
}
