<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Frequencia;
use App\Models\Aluno;
use App\Models\Turma;
use Carbon\Carbon;

class FrequenciaController extends Controller
{
    public function index(Request $request)
    {
        $frequencias = collect();
        
        // Se houver filtros, gerar dados
        if ($request->filled(['ano', 'turma', 'data_inicio', 'data_fim'])) {
            // Buscar alunos da turma
            $alunos = Aluno::whereHas('turma', function ($q) use ($request) {
                $q->where('ano', $request->ano)
                  ->where('turma', 'like', '%' . $request->turma . '%');
            })->with('turma')->get();
            
            // Gerar intervalo de datas
            $dataInicio = Carbon::parse($request->data_inicio);
            $dataFim = Carbon::parse($request->data_fim);
            
            $frequenciasData = [];
            
            // Para cada data no intervalo
            while ($dataInicio->lte($dataFim)) {
                $dataAtual = $dataInicio->format('Y-m-d');
                
                // Para cada aluno
                foreach ($alunos as $aluno) {
                    // Buscar frequência existente
                    $frequenciaExistente = Frequencia::where('aluno_id', $aluno->id)
                        ->whereDate('data_entrada', $dataAtual)
                        ->first();
                    
                    if ($frequenciaExistente) {
                        $frequenciasData[] = $frequenciaExistente;
                    } else {
                        // Criar registro vazio para exibição
                        $frequenciasData[] = (object) [
                            'aluno' => $aluno,
                            'data_entrada' => $dataAtual,
                            'horario_saida' => null,
                            'frequencia' => 'ausente',
                            'existe' => false
                        ];
                    }
                }
                
                $dataInicio->addDay();
            }
            
            $frequencias = collect($frequenciasData)->sortByDesc('data_entrada');
        }

        return view('secretaria.dashboard', compact('frequencias'));
    }
}