<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Buscar alunos vinculados ao responsável
        $alunos = DB::table('responsavel_aluno')
            ->join('alunos', 'responsavel_aluno.aluno_id', '=', 'alunos.id')
            ->join('turmas', 'alunos.turma_id', '=', 'turmas.id')
            ->where('responsavel_aluno.user_id', $user->id)
            ->select('alunos.*', 'turmas.turma', 'turmas.ano')
            ->get();

        return view('usuario.dashboard', compact('user', 'alunos'));
    }
}