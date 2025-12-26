<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Aluno;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ResponsavelController extends Controller
{
    /**
     * Exibe a página da etapa 1 (vincular aluno)
     */
    public function showEtapa1()
    {
        return view('usuario.login.vincular-aluno');
    }

    /**
     * Processa dados da etapa 1 e vai para etapa 2
     */
    public function etapa1(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => 'required|string|max:14|unique:users',
            'telefone' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Armazenar dados na sessão
        session(['responsavel_dados' => $validated]);

        return view('usuario.login.vincular-aluno');
    }

    /**
     * Processa cadastro completo - Etapa 2
     */
    public function finalizar(Request $request)
    {
        $request->validate([
            'alunos_data' => 'required|string',
        ]);

        $dadosResponsavel = session('responsavel_dados');
        if (!$dadosResponsavel) {
            return redirect()->route('register')
                ->withErrors(['error' => 'Sessão expirada. Refaça o cadastro.']);
        }

        // Decodificar dados dos alunos
        $alunosData = json_decode($request->alunos_data, true);
        if (empty($alunosData)) {
            return redirect()->back()
                ->withErrors(['alunos' => 'Adicione pelo menos um aluno.']);
        }

        // Verificar se todos os alunos existem no banco de dados
        $alunosValidos = [];
        $alunosNaoEncontrados = [];
        
        foreach ($alunosData as $alunoData) {
            $aluno = Aluno::where('nome', $alunoData['nome'])
                         ->where('cpf', $alunoData['cpf'])
                         ->first();
            
            if (!$aluno) {
                $alunosNaoEncontrados[] = "{$alunoData['nome']} (CPF: {$alunoData['cpf']})";
            } else {
                $alunosValidos[] = $aluno->id;
            }
        }
        
        // Se houver alunos não encontrados, retornar erro
        if (!empty($alunosNaoEncontrados)) {
            $mensagem = count($alunosNaoEncontrados) === 1 
                ? "O aluno " . $alunosNaoEncontrados[0] . " não foi encontrado no sistema."
                : "Os seguintes alunos não foram encontrados no sistema: " . implode(', ', $alunosNaoEncontrados);
                
            return redirect()->back()
                ->withInput()
                ->withErrors(['alunos' => $mensagem]);
        }

        try {
            DB::beginTransaction();

            // Criar usuário
            $user = User::create([
                'name' => $dadosResponsavel['name'],
                'email' => $dadosResponsavel['email'],
                'cpf' => $dadosResponsavel['cpf'],
                'telefone' => $dadosResponsavel['telefone'],
                'password' => Hash::make($dadosResponsavel['password']),
            ]);

            // Vincular alunos na tabela pivot
            foreach ($alunosValidos as $alunoId) {
                DB::table('responsavel_aluno')->insert([
                    'user_id' => $user->id,
                    'aluno_id' => $alunoId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            // Limpar sessão
            session()->forget('responsavel_dados');

            return redirect()->route('login')
                ->with('success', 'Cadastro realizado com sucesso! Faça login para continuar.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao realizar cadastro. Tente novamente.']);
        }
    }

    /**
     * Volta para etapa 1
     */
    public function voltar()
    {
        $dados = session('responsavel_dados', []);
        return view('usuario.login.cadastro', compact('dados'));
    }
}