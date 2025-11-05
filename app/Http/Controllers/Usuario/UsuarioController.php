<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('usuario.dashboard'); // Rende o arquivo dashboard.blade.php dentro da pasta usuario
    }
}