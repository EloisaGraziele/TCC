<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecretariaAuthController extends Controller // <-- Classe correta
{
    /**
     * Exibe o formulário de login para secretarias.
     */
    public function showLoginForm()
    {
        return view('secretaria.login.login');
    }

    /**
     * Processa o login da secretaria.
     */
    public function login(Request $request)
    {
        // 1. Validação dos campos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. TENTATIVA DE LOGIN COM O GUARD 'secretaria'
        $credentials = $request->only('email', 'password');
        
        if (Auth::guard('secretaria')->attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redireciona para o dashboard da secretaria
            return redirect()->intended(route('secretaria.dashboard')); 
        }

        // Autenticação falhou
        return back()->with('error', 'Email ou senha incorretos. Tente novamente.');
    }

    /**
     * Efetua o logout da secretaria.
     */
    public function logout(Request $request)
    {
        Auth::guard('secretaria')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
