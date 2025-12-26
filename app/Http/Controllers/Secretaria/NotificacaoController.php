<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use App\Models\Notificacao;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificacaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Notificacao::with(['alerta', 'aluno'])
            ->where('destinatario_tipo', 'secretaria')
            ->orderBy('created_at', 'desc');

        // Filtro por aluno
        if ($request->filled('aluno_nome')) {
            $query->whereHas('aluno', function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->aluno_nome . '%');
            });
        }

        // Filtro por data
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $notificacoes = $query->paginate(15);
        $alunos = Aluno::orderBy('nome')->get();

        return view('secretaria.notificacoes.index', compact('notificacoes', 'alunos'));
    }

    /**
     * Return latest notifications as JSON for the secretaria panel
     */
    public function latest(Request $request)
    {
        $notificacoes = Notificacao::with(['alerta', 'aluno'])
            ->where('destinatario_tipo', 'secretaria')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Return minimal data for client
        return response()->json($notificacoes->map(function($n) {
            return [
                'id' => $n->id,
                'aluno_nome' => $n->aluno->nome ?? null,
                'mensagem' => $n->mensagem,
                'lida' => (bool) $n->lida,
                'created_at' => $n->created_at->toDateTimeString(),
                'alerta_tipo' => $n->alerta->tipo_alerta ?? null,
            ];
        }));
    }

    public function marcarLida($id)
    {
        $notificacao = Notificacao::findOrFail($id);
        $notificacao->update([
            'lida' => true,
            'lida_em' => Carbon::now()
        ]);

        return response()->json(['success' => true]);
    }

    public function marcarTodasLidas()
    {
        Notificacao::where('destinatario_tipo', 'secretaria')
            ->where('lida', false)
            ->update([
                'lida' => true,
                'lida_em' => Carbon::now()
            ]);

        return redirect()->back()->with('success', 'Todas as notificações foram marcadas como lidas!');
    }
}