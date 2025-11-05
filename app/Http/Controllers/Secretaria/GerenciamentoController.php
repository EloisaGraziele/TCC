<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Turma;
use App\Models\Dispositivo;

class GerenciamentoController extends Controller
{
    /**
     * Exibe a página principal de gerenciamento.
     */
    public function index()
    {
        try {
            $turmas = Turma::all();
            $dispositivos = Dispositivo::all();
        } catch (\Exception $e) {
            $turmas = collect();
            $dispositivos = collect();
        }
        
        return view('secretaria.gerenciamento', compact('turmas', 'dispositivos'));
    }
}