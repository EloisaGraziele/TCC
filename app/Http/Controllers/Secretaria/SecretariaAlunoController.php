<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Frequencia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class SecretariaAlunoController extends Controller
{
    /**
     * Exibe frequência por alunos.
     */
    public function index(Request $request)
    {
        $frequencias = collect();
        
        // Se houver filtros, gerar dados
        if ($request->hasAny(['nome', 'cpf', 'data_inicio', 'data_fim'])) {
            // Buscar alunos pelos filtros
            $alunosQuery = Aluno::with('turma');
            
            if ($request->filled('nome')) {
                $alunosQuery->where('nome', 'like', '%' . $request->nome . '%');
            }
            
            if ($request->filled('cpf')) {
                $alunosQuery->where('cpf', 'like', '%' . $request->cpf . '%');
            }
            
            $alunos = $alunosQuery->get();
            
            // Se houver intervalo de datas
            if ($request->filled('data_inicio') && $request->filled('data_fim')) {
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
                            ->whereDate('created_at', $dataAtual)
                            ->first();
                        
                        if ($frequenciaExistente) {
                            $frequenciasData[] = $frequenciaExistente;
                        } else {
                            // Criar registro vazio para exibição
                            $frequenciasData[] = (object) [
                                'aluno' => $aluno,
                                'created_at' => $dataAtual,
                                'horario_saida' => null,
                                'frequencia' => 'ausente',
                                'existe' => false
                            ];
                        }
                    }
                    
                    $dataInicio->addDay();
                }
                
                $frequencias = collect($frequenciasData)->sortByDesc('created_at');
            } else {
                // Sem intervalo de datas, buscar frequências existentes dos alunos
                $alunoIds = $alunos->pluck('id');
                $frequenciasExistentes = Frequencia::whereIn('aluno_id', $alunoIds)
                    ->with('aluno.turma')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $frequencias = $frequenciasExistentes;
            }
        }

        return view('secretaria.frequencia_alunos', compact('frequencias'));
    }

    /**
     * Exibe página de gerenciar alunos.
     */
    public function gerenciar(Request $request)
    {
        $alunos = collect(); // Coleção vazia por padrão
        
        // Só busca alunos se houver filtros
        if ($request->hasAny(['nome', 'cpf'])) {
            $alunosQuery = Aluno::with('turma');
            
            if ($request->filled('nome')) {
                $alunosQuery->where('nome', 'like', '%' . $request->nome . '%');
            }
            
            if ($request->filled('cpf')) {
                $alunosQuery->where('cpf', 'like', '%' . $request->cpf . '%');
            }
            
            $alunos = $alunosQuery->get();
        }
        
        $turmas = Turma::all();
        
        return view('secretaria.gerenciar_alunos', compact('alunos', 'turmas'));
    }

    /**
     * Atualiza dados do aluno.
     */
    public function update(Request $request, Aluno $aluno)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:alunos,cpf,' . $aluno->id,
            'matricula' => 'required|string|max:50|unique:alunos,matricula,' . $aluno->id,
            'turma_id' => 'required|exists:turmas,id',
            'data_nascimento' => 'required|date',
            'status' => 'required|in:ativo,inativo,transferido',
        ]);

        $aluno->update($validated);
        
        return redirect()->route('secretaria.aluno.gerenciar')
            ->with('success', 'Aluno atualizado com sucesso!');
    }

    /**
     * Altera status do aluno.
     */
    public function updateStatus(Request $request, Aluno $aluno)
    {
        $validated = $request->validate([
            'status' => 'required|in:ativo,inativo,transferido',
        ]);

        $aluno->update(['status' => $validated['status']]);
        
        return redirect()->route('secretaria.aluno.gerenciar')
            ->with('success', 'Status do aluno alterado com sucesso!');
    }

    /**
     * Regenera QR Code do aluno.
     */
    public function regenerarQr(Aluno $aluno)
    {
        // Gerar novo token
        $aluno->qr_code_token = \Illuminate\Support\Str::uuid() . '-' . $aluno->matricula;
        $aluno->save();
        
        return redirect()->route('secretaria.aluno.gerenciar')
            ->with('success', 'QR Code regenerado com sucesso!');
    }
    /**
     * Exibe o formulário de cadastro de aluno.
     * Rota: secretaria.aluno.create
     */
    public function create()
    {
        $turmas = Turma::where('ano', date('Y'))->get();
        return view('secretaria.aluno.create', compact('turmas'));
    }

    /**
     * Armazena o novo aluno no banco de dados.
     * Rota: secretaria.aluno.store
     */
    public function store(Request $request)
    {
        // 1️⃣ Validação dos campos necessários
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:alunos',
            'matricula' => 'required|string|max:50|unique:alunos',
            'turma_id' => 'required|exists:turmas,id',
            'data_nascimento' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // 2️⃣ Cria o aluno com status automaticamente como 'ativo'
            $aluno = Aluno::create([
                'nome' => $validated['nome'],
                'cpf' => $validated['cpf'],
                'matricula' => $validated['matricula'],
                'turma_id' => $validated['turma_id'],
                'data_nascimento' => $validated['data_nascimento'],
                'status' => 'ativo', // status fixo como ativo
            ]);

            DB::commit();

            // 3️⃣ Geração do QR Code com CPF puro
            $qrCodeSvg = QrCode::size(120)
                ->errorCorrection('M')
                ->format('svg')
                ->margin(1)
                ->generate($aluno->cpf); // Gerando o QR Code com CPF puro

            // 4️⃣ Redirecionamento para a página do crachá com QR Code
            return redirect()->route('secretaria.aluno.cracha', $aluno->id)
                ->with('success', 'Aluno ' . $aluno->nome . ' cadastrado com sucesso! O crachá com o QR Code foi gerado.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erro no cadastro de aluno: " . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['cadastro' => 'Ocorreu um erro ao salvar o aluno.']);
        }
    }

    /**
     * Exibe o crachá com o QR Code.
     * Rota: secretaria.aluno.cracha
     */
    public function mostrarCracha(Aluno $aluno)
    {
        // 1️⃣ Gerando o QR Code com CPF puro
        $qrCodeSvg = QrCode::size(120)
            ->errorCorrection('M')
            ->format('svg')
            ->margin(1)
            ->generate($aluno->cpf); // Gerando o QR Code com CPF puro

        // 2️⃣ Retornando para a view do crachá com o QR Code gerado
        return view('secretaria.aluno.cracha', [
            'aluno' => $aluno,
            'qrCodeSvg' => $qrCodeSvg
        ]);
    }
}