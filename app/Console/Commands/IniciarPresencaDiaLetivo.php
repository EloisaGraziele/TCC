<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CalendarioEscolar;
use App\Models\Aluno;
use App\Models\Frequencia;
use Carbon\Carbon;

class IniciarPresencaDiaLetivo extends Command
{
    protected $signature = 'presenca:iniciar-dia';
    protected $description = 'Inicia presenças como não registradas em dias letivos';

    public function handle()
    {
        $hoje = Carbon::today();
        
        $diaLetivo = CalendarioEscolar::where('data', $hoje)
            ->where('tipo_dia', 'LETIVO')
            ->where('ativo', true)
            ->exists();

        if (!$diaLetivo) {
            $this->info('Hoje não é dia letivo');
            return;
        }

        $alunos = Aluno::where('ativo', true)->get();
        
        foreach ($alunos as $aluno) {
            $presencaExiste = Frequencia::where('aluno_id', $aluno->id)
                ->whereDate('data_presenca', $hoje)
                ->exists();

            if (!$presencaExiste) {
                Frequencia::create([
                    'aluno_id' => $aluno->id,
                    'data_presenca' => $hoje,
                    'status' => 'nao_registrada',
                    'dispositivo_origem' => 'sistema',
                    'observacoes' => 'Aguardando registro'
                ]);
            }
        }

        $this->info('Presenças iniciadas para dia letivo: ' . $hoje->format('d/m/Y'));
    }
}