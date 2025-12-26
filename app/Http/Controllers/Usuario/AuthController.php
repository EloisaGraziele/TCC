<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
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
            return redirect('/dashboard');
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
        return redirect('/');
    }
}