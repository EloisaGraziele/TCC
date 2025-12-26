<?php

namespace App\Http\Controllers\Secretaria; // CORRIGIDO: Deve incluir a subpasta 'Secretaria'

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Adicionado para checar o usuário logado

class SecretariaDashboardController extends Controller
{
    /**
     * Exibe o painel principal da secretaria.
     * Rota: secretaria.dashboard
     */
    public function index()
    {
        // Garante que o usuário está logado no guard 'secretaria' antes de exibir
        if (!Auth::guard('secretaria')->check()) {
            // Se não estiver logado, redireciona para o login
            return redirect()->route('secretaria.login.login');
        }
        
        // Retorna a view que foi criada: resources/views/secretaria/dashboard/index.blade.php
        return view('secretaria.dashboard'); 
    }
}
