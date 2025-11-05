<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Exibe o formulário de login.
     */
    public function create()
    {
        return view('usuario.login.login');
    }

    /**
     * Processa o login do usuário.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            // Aqui você pode redirecionar para diferentes páginas com base no tipo de usuário
            return redirect()->intended(route('dashboard'));  // Redireciona para a página inicial do usuário
        }

        return back()->withErrors([
            'email' => 'As credenciais não são válidas.',
        ]);
    }

    /**
     * Desfaz a autenticação do usuário.
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        return redirect('/');  // Redireciona para a página inicial após o logout
    }
}