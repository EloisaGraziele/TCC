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
            
        // Buscar notificações para os alunos do responsável
        $alunosIds = $alunos->pluck('id');
        $notificacoes = DB::table('notificacoes')
            ->whereIn('aluno_id', $alunosIds)
            ->where('destinatario_tipo', 'pais')
            ->where('lida', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('usuario.dashboard', compact('user', 'alunos', 'notificacoes'));
    }
    
    public function getAlunoFrequencia($alunoId)
    {
        // Buscar dados do aluno
        $aluno = DB::table('alunos')
            ->join('turmas', 'alunos.turma_id', '=', 'turmas.id')
            ->where('alunos.id', $alunoId)
            ->select('alunos.*', 'turmas.turma', 'turmas.ano')
            ->first();
            
        if (!$aluno) {
            return response()->json(['error' => 'Aluno não encontrado'], 404);
        }
        
        // Calcular dias letivos desde o cadastro até hoje
        $dataCadastro = \Carbon\Carbon::parse($aluno->created_at);
        $hoje = \Carbon\Carbon::now();
        
        // Buscar dias não letivos do calendário escolar
        $diasNaoLetivos = DB::table('calendario_escolar')
            ->where('ano', $hoje->year)
            ->where('ativo', true)
            ->whereIn('tipo_dia', ['feriado', 'ferias', 'recesso'])
            ->pluck('data')
            ->toArray();
            
        // Contar dias letivos (excluindo fins de semana e feriados)
        $diasLetivos = 0;
        $dataAtual = $dataCadastro->copy();
        
        while ($dataAtual->lte($hoje)) {
            // Pular fins de semana (sábado = 6, domingo = 0)
            if ($dataAtual->dayOfWeek != 0 && $dataAtual->dayOfWeek != 6) {
                // Verificar se não é feriado/férias
                if (!in_array($dataAtual->format('Y-m-d'), $diasNaoLetivos)) {
                    $diasLetivos++;
                }
            }
            $dataAtual->addDay();
        }
        
        // Buscar presenças do aluno
        $presencas = DB::table('frequencias')
            ->where('aluno_id', $alunoId)
            ->where('frequencia', 'presente')
            ->count();
            
        // Calcular faltas (dias letivos - presenças)
        $faltas = $diasLetivos - $presencas;
        
        // Garantir que faltas não seja negativo
        $faltas = max(0, $faltas);
            
        // Calcular frequência percentual
        $frequenciaPercentual = $diasLetivos > 0 ? round(($presencas / $diasLetivos) * 100, 1) : 0;
        
        return response()->json([
            'aulas' => $diasLetivos,
            'presencas' => $presencas,
            'faltas' => $faltas,
            'frequencia' => $frequenciaPercentual . '%'
        ]);
    }
    
    public function getAlunoSemana($alunoId)
    {
        // Obter início e fim da semana atual (segunda a domingo)
        $hoje = \Carbon\Carbon::now();
        $inicioSemana = $hoje->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $fimSemana = $hoje->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
        
        $diasSemana = [];
        $dataAtual = $inicioSemana->copy();
        
        // Buscar dias não letivos
        $diasNaoLetivos = DB::table('calendario_escolar')
            ->where('ano', $hoje->year)
            ->where('ativo', true)
            ->whereIn('tipo_dia', ['feriado', 'ferias', 'recesso'])
            ->pluck('data')
            ->toArray();
        
        while ($dataAtual->lte($fimSemana)) {
            $dataFormatada = $dataAtual->format('d');
            $dataCompleta = $dataAtual->format('Y-m-d');
            
            // Verificar se é dia letivo
            $isDiaLetivo = $dataAtual->dayOfWeek != 0 && $dataAtual->dayOfWeek != 6 && 
                          !in_array($dataCompleta, $diasNaoLetivos);
            
            if ($isDiaLetivo && $dataAtual->lte($hoje)) {
                // Buscar presença do aluno neste dia
                $frequencia = DB::table('frequencias')
                    ->where('aluno_id', $alunoId)
                    ->whereDate('created_at', $dataCompleta)
                    ->first();
                
                if ($frequencia) {
                    $entrada = $frequencia->created_at ? 
                              \Carbon\Carbon::parse($frequencia->hora_entrada)->format('H:i') : null;
                    $saida = $frequencia->horario_saida ? substr($frequencia->horario_saida, 11, 5) : 
                    $saida = $frequencia->horario_saida ? substr($frequencia->horario_saida, 11, 5) : null;
                    
                    $diasSemana[] = [
                        'data' => $dataFormatada,
                        'status' => 'presente',
                        'entrada' => $entrada,
                        'saida' => $saida
                    ];
                } else {
                    $diasSemana[] = [
                        'data' => $dataFormatada,
                        'status' => 'falta',
                        'entrada' => null,
                        'saida' => null
                    ];
                }
            } else {
                $diasSemana[] = [
                    'data' => $dataFormatada,
                    'status' => null,
                    'entrada' => null,
                    'saida' => null
                ];
            }
            
            $dataAtual->addDay();
        }
        
        return response()->json($diasSemana);
    }
    
    public function pesquisarPresenca(Request $request, $alunoId)
    {
        try {
            $mes = $request->get('mes');
            $bimestre = $request->get('bimestre');
            $data = $request->get('data');
            
            // Definir período de busca
            $dataInicio = null;
            $dataFim = null;
            
            if ($data) {
                $dataInicio = $dataFim = $data;
            } elseif ($mes) {
                $ano = date('Y');
                $dataInicio = "$ano-$mes-01";
                $dataFim = date('Y-m-t', strtotime($dataInicio));
            } elseif ($bimestre) {
                $ano = date('Y');
                switch ($bimestre) {
                    case '1': $dataInicio = "$ano-02-01"; $dataFim = "$ano-04-30"; break;
                    case '2': $dataInicio = "$ano-05-01"; $dataFim = "$ano-07-31"; break;
                    case '3': $dataInicio = "$ano-08-01"; $dataFim = "$ano-10-31"; break;
                    case '4': $dataInicio = "$ano-11-01"; $dataFim = "$ano-12-31"; break;
                }
            }
            
            if (!$dataInicio || !$dataFim) {
                return response()->json([]);
            }
            
            // Buscar presenças registradas
            $presencas = DB::table('frequencias')
                ->where('aluno_id', $alunoId)
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->get()
                ->keyBy(function ($item) {
                    return date('Y-m-d', strtotime($item->created_at));
                });
            
            // Buscar dias não letivos
            $diasNaoLetivos = DB::table('calendario_escolar')
                ->where('ano', date('Y'))
                ->where('ativo', true)
                ->whereIn('tipo_dia', ['feriado', 'ferias', 'recesso'])
                ->pluck('data')
                ->toArray();
            
            $dados = [];
            $dataAtual = \Carbon\Carbon::parse($dataInicio);
            $dataFinal = \Carbon\Carbon::parse($dataFim);
            
            while ($dataAtual->lte($dataFinal)) {
                $dataStr = $dataAtual->format('Y-m-d');
                
                // Verificar se é dia letivo (segunda a sexta, não feriado)
                if ($dataAtual->dayOfWeek >= 1 && $dataAtual->dayOfWeek <= 5 && 
                    !in_array($dataStr, $diasNaoLetivos) && 
                    $dataAtual->lte(\Carbon\Carbon::now())) {
                    
                    if (isset($presencas[$dataStr])) {
                        // Tem registro de presença
                        $presenca = $presencas[$dataStr];
                        $dados[] = [
                            'data' => $dataAtual->format('d/m/Y'),
                            'status' => 'PRESENTE',
                            'hora_entrada' => date('H:i', strtotime($presenca->hora_entrada)),
                            'hora_saida' => $presenca->horario_saida ? date('H:i', strtotime($presenca->horario_saida)) : '-'
                        ];
                    } else {
                        // Não tem registro = falta
                        $dados[] = [
                            'data' => $dataAtual->format('d/m/Y'),
                            'status' => 'FALTA',
                            'hora_entrada' => '-',
                            'hora_saida' => '-'
                        ];
                    }
                }
                
                $dataAtual->addDay();
            }
            
            // Ordenar por data (mais recente primeiro)
            usort($dados, function ($a, $b) {
                return strtotime(str_replace('/', '-', $b['data'])) - strtotime(str_replace('/', '-', $a['data']));
            });
            
            return response()->json($dados);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}