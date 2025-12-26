<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificacaoController extends Controller
{
    public function latest()
    {
        $user = Auth::user();
        
        // Buscar IDs dos alunos vinculados ao usuário
        $alunosIds = DB::table('responsavel_aluno')
            ->where('user_id', $user->id)
            ->pluck('aluno_id');
            
        // Buscar notificações para esses alunos
        $notificacoes = DB::table('notificacoes')
            ->whereIn('aluno_id', $alunosIds)
            ->where('destinatario_tipo', 'pais')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
            
        return response()->json($notificacoes);
    }
    
    public function marcarLida($id)
    {
        DB::table('notificacoes')
            ->where('id', $id)
            ->update(['lida' => true, 'lida_em' => now()]);
            
        return response()->json(['success' => true]);
    }
}