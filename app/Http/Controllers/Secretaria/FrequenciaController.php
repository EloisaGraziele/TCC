<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Frequencia;
use App\Models\Aluno;
use App\Models\Turma;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class FrequenciaController extends Controller
{
    public function index(Request $request)
    {

        
        // Só busca dados se turma E data foram fornecidas
        if ($request->filled('turma') && ($request->filled('data_inicio') || $request->filled('data_fim'))) {
            
            // Buscar turma
            $turma = DB::table('turmas')
                ->whereRaw('LOWER(turma) LIKE ?', ['%' . strtolower($request->turma) . '%'])
                ->first();
                
            if (!$turma) {
                $frequencias = new LengthAwarePaginator([], 0, 20);
                return view('secretaria.dashboard', compact('frequencias'));
            }
            
            // Buscar alunos da turma
            $alunos = DB::table('alunos')
                ->where('turma_id', $turma->id)
                ->get();
                
            $dataInicio = $request->data_inicio ?: $request->data_fim;
            $dataFim = $request->data_fim ?: $request->data_inicio;
            
            // Buscar dias não letivos do calendário
            $ano = $request->ano ?: date('Y');
            $diasNaoLetivos = DB::table('calendario_escolar')
                ->where('ano', $ano)
                ->where('ativo', true)
                ->whereIn('tipo_dia', ['feriado', 'ferias', 'recesso'])
                ->pluck('data')
                ->toArray();
            
            $resultados = [];
            $dataAtual = Carbon::parse($dataInicio);
            $dataFinal = Carbon::parse($dataFim);
            
            while ($dataAtual->lte($dataFinal)) {
                $dataStr = $dataAtual->format('Y-m-d');
                
                // Verificar se é dia letivo (segunda a sexta, não feriado)
                $isDiaLetivo = $dataAtual->dayOfWeek >= 1 && $dataAtual->dayOfWeek <= 5 && 
                              !in_array($dataStr, $diasNaoLetivos);
                
                foreach ($alunos as $aluno) {
                    if ($isDiaLetivo) {
                        // Buscar presença do aluno neste dia
                        $frequencia = DB::table('frequencias')
                            ->where('aluno_id', $aluno->id)
                            ->whereDate('created_at', $dataStr)
                            ->first();
                            

                        
                        if ($frequencia) {
                            $resultados[] = (object) [
                                'aluno' => (object) [
                                    'nome' => $aluno->nome,
                                    'turma' => (object) ['turma' => $turma->turma]
                                ],
                                'created_at' => $dataStr,
                                'frequencia' => $frequencia->frequencia,
                                'hora_entrada' => $frequencia->hora_entrada,
                                'horario_saida' => $frequencia->horario_saida ? Carbon::parse($frequencia->horario_saida)->format('H:i') : null
                            ];
                        } else {
                            $resultados[] = (object) [
                                'aluno' => (object) [
                                    'nome' => $aluno->nome,
                                    'turma' => (object) ['turma' => $turma->turma]
                                ],
                                'created_at' => $dataStr,
                                'frequencia' => 'falta',
                                'hora_entrada' => null,
                                'horario_saida' => null
                            ];
                        }
                    } else {
                        $resultados[] = (object) [
                            'aluno' => (object) [
                                'nome' => $aluno->nome,
                                'turma' => (object) ['turma' => $turma->turma]
                            ],
                            'created_at' => $dataStr,
                            'frequencia' => 'dia_nao_letivo',
                            'hora_entrada' => null,
                            'horario_saida' => null
                        ];
                    }
                }
                
                $dataAtual->addDay();
            }
            
            // Paginar resultados
            $page = $request->get('page', 1);
            $perPage = 20;
            $offset = ($page - 1) * $perPage;
            $items = array_slice($resultados, $offset, $perPage);
            
            $frequencias = new LengthAwarePaginator(
                $items,
                count($resultados),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            
        } else {
            $frequencias = new LengthAwarePaginator([], 0, 20);
        }

        // Buscar todas as turmas para o select
        $turmas = DB::table('turmas')->orderBy('turma')->get();
        
        return view('secretaria.dashboard', compact('frequencias', 'turmas'));
    }
}