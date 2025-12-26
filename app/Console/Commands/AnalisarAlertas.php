<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alerta;
use App\Models\Aluno;
use App\Models\Frequencia;
use App\Models\Notificacao;
use App\Models\CalendarioEscolar;
use Carbon\Carbon;

class AnalisarAlertas extends Command
{
    protected $signature = 'alertas:analisar';
    protected $description = 'Analisa alertas diariamente e cria notificações';

    public function handle()
    {
        // Verifica se hoje é dia letivo
        if (!$this->isDiaLetivo()) {
            $this->info('Hoje não é dia letivo. Análise cancelada.');
            return 0;
        }

        $this->info('Iniciando análise diária de alertas...');
        
        $alertas = Alerta::all();
        $notificacoes_criadas = 0;

        foreach ($alertas as $alerta) {
            $alunos_afetados = $this->verificarPadraoAlerta($alerta);
            
            foreach ($alunos_afetados as $aluno) {
                $notificacoes_criadas += $this->criarNotificacoes($alerta, $aluno);
            }
        }

        $this->info("Análise concluída! {$notificacoes_criadas} notificações criadas.");
        return 0;
    }

    private function isDiaLetivo()
    {
        $hoje = Carbon::today();
        
        // Verifica se é fim de semana
        if ($hoje->isWeekend()) {
            return false;
        }
        
        // Verifica se existe evento no calendário escolar para hoje que não seja 'letivo'
        // A tabela usa a coluna `tipo_dia` (ex: 'letivo', 'feriado', 'evento', etc.)
        $evento = CalendarioEscolar::whereDate('data', $hoje)
            ->where('tipo_dia', '!=', 'letivo')
            ->first();

        // Se existir um registro diferente de 'letivo', então não é dia letivo
        return $evento ? false : true;
    }

    private function verificarPadraoAlerta($alerta)
    {
        switch ($alerta->tipo_alerta) {
            case 'faltas_consecutivas':
                return $this->verificarFaltasConsecutivas($alerta);
            case 'percentual_faltas':
                return $this->verificarPercentualFaltas($alerta);
            case 'dia_especifico':
                return $this->verificarDiaEspecifico($alerta);
            default:
                return [];
        }
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
                ->orderBy('created_at', 'desc')
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
            $query->whereDate('created_at', $data_especifica)
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

        // Mensagem mais descritiva contendo tipo do alerta e nome do aluno
        $mensagem_pais = "Tipo: {$alerta->tipo_alerta} - Aluno: {$aluno->nome} - {$alerta->descricao}";
        $mensagem_secretaria = "Tipo: {$alerta->tipo_alerta} - Aluno: {$aluno->nome} - {$alerta->descricao}";

        if (in_array($destinatarios, ['responsaveis', 'ambos'])) {
            // Busca todos os responsáveis vinculados ao aluno
            $responsaveis = \DB::table('responsavel_aluno')
                ->where('aluno_id', $aluno->id)
                ->pluck('user_id');
                
            // Cria notificação para cada responsável
            foreach ($responsaveis as $userId) {
                Notificacao::create([
                    'alerta_id' => $alerta->id,
                    'aluno_id' => $aluno->id,
                    'destinatario_tipo' => 'pais',
                    'mensagem' => $mensagem_pais
                ]);
                $notificacoes_criadas++;
            }
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