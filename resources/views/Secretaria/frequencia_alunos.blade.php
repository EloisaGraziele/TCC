@extends('layouts.admin') 

@section('content')

    <div class="container mt-4">
        <h1 class="h3 mb-4 fw-bold text-custom-blue">Frequência por Alunos</h1>

        <!-- Formulário de Pesquisa -->
        <form method="GET" action="{{ route('secretaria.aluno.index') }}" class="mb-5">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="nome" class="form-label fw-medium">Nome do Aluno</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite o nome" 
                           class="form-control form-control-sm rounded-3 shadow-sm border" 
                           value="{{ request('nome') }}">
                </div>
                <div class="col-md-2">
                    <label for="cpf" class="form-label fw-medium">CPF</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" 
                           class="form-control form-control-sm rounded-3 shadow-sm border" 
                           value="{{ request('cpf') }}">
                </div>
                <div class="col-md-3">
                    <label for="data_inicio" class="form-label fw-medium">Data Início</label>
                    <input type="date" id="data_inicio" name="data_inicio" 
                           class="form-control form-control-sm rounded-3 shadow-sm border" 
                           value="{{ request('data_inicio') }}">
                </div>
                <div class="col-md-3">
                    <label for="data_fim" class="form-label fw-medium">Data Fim</label>
                    <input type="date" id="data_fim" name="data_fim" 
                           class="form-control form-control-sm rounded-3 shadow-sm border" 
                           value="{{ request('data_fim') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-custom-blue btn-sm me-2">Pesquisar</button>
                    <a href="{{ route('secretaria.aluno.index') }}" class="btn btn-secondary btn-sm">Limpar</a>
                </div>
            </div>
        </form>
        
        <!-- Tabela de Registros -->
        <div class="bg-white shadow-lg rounded-3 overflow-hidden">
            <!-- Cabeçalho da Tabela -->
            <div class="row text-center bg-custom-green text-dark fw-bold p-3">
                <div class="col-sm-3">Aluno</div>
                <div class="col-sm-2">Data</div>
                <div class="col-sm-2">Hora-Entrada</div>
                <div class="col-sm-2">Hora-Saída</div>
                <div class="col-sm-3">Status</div>
            </div>

            <!-- Dados da Tabela -->
            @forelse($frequencias as $frequencia)
                <div class="row text-center py-3 border-bottom align-items-center">
                    <div class="col-sm-3 text-start ps-4">
                        @if(isset($frequencia->aluno))
                            {{ $frequencia->aluno->nome }}
                            <br><small class="text-muted">CPF: {{ $frequencia->aluno->cpf }}</small>
                            <br><small class="text-muted">{{ $frequencia->aluno->turma->turma ?? '' }}</small>
                        @endif
                    </div>
                    <div class="col-sm-2">
                        @if(isset($frequencia->data_entrada))
                            {{ \Carbon\Carbon::parse($frequencia->data_entrada)->format('d/m/Y') }}
                        @endif
                    </div>
                    <div class="col-sm-2">
                        @if(isset($frequencia->data_entrada) && $frequencia->data_entrada)
                            {{ \Carbon\Carbon::parse($frequencia->data_entrada)->format('H:i') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                    <div class="col-sm-2">
                        @if(isset($frequencia->horario_saida) && $frequencia->horario_saida)
                            {{ \Carbon\Carbon::parse($frequencia->horario_saida)->format('H:i') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                    <div class="col-sm-3">
                        @if(isset($frequencia->frequencia))
                            <span class="badge {{ $frequencia->frequencia == 'presente' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($frequencia->frequencia) }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="row text-center py-4">
                    <div class="col-12 text-muted">
                        @if(request()->hasAny(['nome', 'cpf', 'data_inicio', 'data_fim']))
                            Nenhum registro encontrado para os filtros selecionados.
                        @else
                            Utilize os filtros acima para pesquisar frequências.
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>

@endsection