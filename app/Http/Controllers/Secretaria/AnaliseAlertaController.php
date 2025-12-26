<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Aluno;
use App\Models\Frequencia;
use App\Models\Notificacao;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnaliseAlertaController extends Controller
{
    public function index()
    {
        $notificacoes = Notificacao::with(['alerta', 'aluno'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('secretaria.analise-alertas.index', compact('notificacoes'));
    }

    public function analisar()
    {
        $alertas = Alerta::all();
        $notificacoes_criadas = 0;

        foreach ($alertas as $alerta) {
            $alunos_afetados = $this->verificarPadraoAlerta($alerta);
            
            foreach ($alunos_afetados as $aluno) {
                $notificacoes_criadas += $this->criarNotificacoes($alerta, $aluno);
            }
        }

        return redirect()->route('secretaria.analise-alertas.index')
            ->with('success', "Análise concluída! {$notificacoes_criadas} notificações criadas.");
    }

    private function verificarPadraoAlerta($alerta)
    {
        $alunos_afetados = [];
        
        switch ($alerta->tipo_alerta) {
            case 'faltas_consecutivas':
                $alunos_afetados = $this->verificarFaltasConsecutivas($alerta);
                break;
            case 'percentual_faltas':
                $alunos_afetados = $this->verificarPercentualFaltas($alerta);
                break;
            case 'dia_especifico':
                $alunos_afetados = $this->verificarDiaEspecifico($alerta);
                break;
        }

        return $alunos_afetados;
    }

    private function verificarFaltasConsecutivas($alerta)
    {
        $params = is_array($alerta->parametros) ? $alerta->parametros : (json_decode($alerta->parametros, true) ?: []);
        $dias_limite = $params['dias_consecutivos'] ?? 0;
        $alunos_afetados = [];
        
        $alunos = Aluno::all();
        
        foreach ($alunos as $aluno) {
            $frequencias = Frequencia::where('aluno_id', $aluno->id)
                ->where('frequencia', 'ausente')
                ->orderBy('data_entrada', 'desc')
                ->take($dias_limite)
                ->get();
                
            if ($frequencias->count() >= $dias_limite) {
                $alunos_afetados[] = $aluno;
            }
        }
        
        return $alunos_afetados;
    }

    private function verificarPercentualFaltas($alerta)
    {
        $params = is_array($alerta->parametros) ? $alerta->parametros : (json_decode($alerta->parametros, true) ?: []);
        $percentual_limite = $params['percentual_limite'] ?? 0;
        $alunos_afetados = [];
        
        $alunos = Aluno::all();
        
        foreach ($alunos as $aluno) {
            $total_dias = Frequencia::where('aluno_id', $aluno->id)->count();
            $faltas = Frequencia::where('aluno_id', $aluno->id)
                ->where('frequencia', 'ausente')
                ->count();
                
            if ($total_dias > 0) {
                $percentual_faltas = ($faltas / $total_dias) * 100;
                if ($percentual_faltas >= $percentual_limite) {
                    $alunos_afetados[] = $aluno;
                }
            }
        }
        
        return $alunos_afetados;
    }

    private function verificarDiaEspecifico($alerta)
    {
        $params = is_array($alerta->parametros) ? $alerta->parametros : (json_decode($alerta->parametros, true) ?: []);
        $data_especifica = $params['data_especifica'] ?? null;

        return Aluno::whereHas('frequencias', function($query) use ($data_especifica) {
            $query->whereDate('data_entrada', $data_especifica)
                  ->where('frequencia', 'ausente');
        })->get()->toArray();
    }

    private function criarNotificacoes($alerta, $aluno)
    {
        $notificacoes_criadas = 0;
        
        // Verifica se já existe notificação para este aluno e alerta hoje
        $ja_notificado = Notificacao::where('alerta_id', $alerta->id)
            ->where('aluno_id', $aluno->id)
            ->whereDate('created_at', Carbon::today())
            ->exists();
            
        if ($ja_notificado) {
            return 0;
        }

        $destinatarios = $alerta->destinatarios ?? null;

        $mensagem_pais = "Tipo: {$alerta->tipo_alerta} - Aluno: {$aluno->nome} - {$alerta->descricao}";
        $mensagem_secretaria = "Tipo: {$alerta->tipo_alerta} - Aluno: {$aluno->nome} - {$alerta->descricao}";

        if (in_array($destinatarios, ['responsaveis', 'ambos'])) {
            Notificacao::create([
                'alerta_id' => $alerta->id,
                'aluno_id' => $aluno->id,
                'destinatario_tipo' => 'pais',
                'mensagem' => $mensagem_pais
            ]);
            $notificacoes_criadas++;
        }

        if (in_array($destinatarios, ['secretaria', 'ambos'])) {
            Notificacao::create([
                'alerta_id' => $alerta->id,
                'aluno_id' => $aluno->id,
                'destinatario_tipo' => 'secretaria',
                'mensagem' => $mensagem_secretaria
            ]);
            $notificacoes_criadas++;
        }

        return $notificacoes_criadas;
    }
}