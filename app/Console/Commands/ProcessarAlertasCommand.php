<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aluno;
use App\Models\Frequencia;
use App\Models\Notificacao;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessarAlertasCommand extends Command
{
    protected $signature = 'alertas:processar';
    protected $description = 'Processa alertas automáticos para alunos com alta taxa de faltas';

    public function handle()
    {
        $alunos = Aluno::all();
        $diasLetivos = DB::table('calendario_escolar')
            ->where('tipo_dia', 'letivo')
            ->where('ativo', true)
            ->count();
        
        if ($diasLetivos == 0) {
            $this->info('Nenhum dia letivo encontrado no calendário.');
            return;
        }
        
        foreach ($alunos as $aluno) {
            // Conta apenas dias letivos a partir da data de cadastro do aluno
            $diasLetivosAluno = DB::table('calendario_escolar')
                ->where('tipo_dia', 'letivo')
                ->where('ativo', true)
                ->where('data', '>=', $aluno->created_at->format('Y-m-d'))
                ->count();
                
            if ($diasLetivosAluno > 0) {
                $presencas = Frequencia::where('aluno_id', $aluno->id)
                    ->where('frequencia', 'presente')
                    ->count();
                    
                $faltas = $diasLetivosAluno - $presencas;
                $percentualFaltas = ($faltas / $diasLetivosAluno) * 100;
            
                if ($percentualFaltas > 30) {
                    // Busca todos os responsáveis vinculados ao aluno
                    $responsaveis = DB::table('responsavel_aluno')
                        ->where('aluno_id', $aluno->id)
                        ->pluck('user_id');
                        
                    // Cria notificação para cada responsável
                    foreach ($responsaveis as $userId) {
                        Notificacao::create([
                            'alerta_id' => 1,
                            'aluno_id' => $aluno->id,
                            'destinatario_tipo' => 'pais',
                            'mensagem' => "Aluno {$aluno->nome} possui " . round($percentualFaltas, 1) . "% de faltas ({$faltas}/{$diasLetivosAluno} dias)",
                            'lida' => false
                        ]);
                    }
                    
                    // Cria notificação para secretaria
                    Notificacao::create([
                        'alerta_id' => 1,
                        'aluno_id' => $aluno->id,
                        'destinatario_tipo' => 'secretaria',
                        'mensagem' => "Aluno {$aluno->nome} possui " . round($percentualFaltas, 1) . "% de faltas ({$faltas}/{$diasLetivosAluno} dias)",
                        'lida' => false
                    ]);
                }
            }
        }
        
        $this->info('Alertas processados com sucesso!');
    }
}