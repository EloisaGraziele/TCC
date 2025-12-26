@extends('layouts.admin') 

@section('content')
<!-- Título do Sistema -->
<div class="mb-3" style="margin-left: 20px; padding-left: 0;">
    <h2 class="fw-bold">
        <span style="color: #55B4F8;">Sistema de</span> 
        <span style="color: #63FF63;">Presença</span>
    </h2>
</div>

    <div class="container mt-4">
        <h1 class="h3 mb-4 fw-bold text-custom-blue">Frequência por Alunos</h1>

        <!-- Formulário de Pesquisa -->
        <form method="GET" class="mb-5" autocomplete="off">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="nome" class="form-label fw-medium">Nome do Aluno</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite o nome" 
                           class="form-control rounded-3 shadow-sm border" 
                           autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label for="cpf" class="form-label fw-medium">CPF</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" 
                           class="form-control rounded-3 shadow-sm border" 
                           autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label for="data_inicio" class="form-label fw-medium">Data Início</label>
                    <input type="date" id="data_inicio" name="data_inicio" 
                           class="form-control rounded-3 shadow-sm border" 
                           autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label for="data_fim" class="form-label fw-medium">Data Fim</label>
                    <input type="date" id="data_fim" name="data_fim" 
                           class="form-control rounded-3 shadow-sm border" 
                           autocomplete="off">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-custom-blue me-2">
                        <i class="fas fa-search me-1"></i>Pesquisar
                    </button>
                    <a href="{{ url()->current() }}" class="btn" style="background-color: #ffc107; color: black;">
                        <i class="fas fa-eraser me-1"></i>Limpar
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Tabela de Registros -->
        <div class="bg-white shadow-lg overflow-hidden">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr class="bg-custom-green text-dark fw-bold">
                        <th class="text-center" style="background-color: #63FF63 !important;">Aluno</th>
                        <th class="text-center" style="background-color: #63FF63 !important;">Turma</th>
                        <th class="text-center" style="background-color: #63FF63 !important;">Data</th>
                        <th class="text-center" style="background-color: #63FF63 !important;">Hora-Entrada</th>
                        <th class="text-center" style="background-color: #63FF63 !important;">Hora-Saída</th>
                        <th class="text-center" style="background-color: #63FF63 !important;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($frequencias as $frequencia)
                        <tr class="text-center">
                            <td class="text-start ps-4">
                                @if(isset($frequencia->aluno))
                                    {{ $frequencia->aluno->nome }}
                                @endif
                            </td>
                            <td>
                                @if(isset($frequencia->aluno->turma))
                                    {{ $frequencia->aluno->turma->turma }}
                                @endif
                            </td>
                            <td>
                                @if(isset($frequencia->created_at))
                                    {{ \Carbon\Carbon::parse($frequencia->created_at)->format('d/m/Y') }}
                                @endif
                            </td>
                            <td>
                                @if(isset($frequencia->hora_entrada) && $frequencia->hora_entrada)
                                    {{ \Carbon\Carbon::parse($frequencia->hora_entrada)->format('H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($frequencia->horario_saida) && $frequencia->horario_saida)
                                    {{ \Carbon\Carbon::parse($frequencia->horario_saida)->format('H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($frequencia->frequencia))
                                    @if($frequencia->frequencia == 'presente')
                                        <span class="badge bg-success">Presente</span>
                                    @else
                                        <span class="fw-bold" style="color: red;">Falta</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        @if(request()->hasAny(['nome', 'cpf', 'data_inicio', 'data_fim']))
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-clipboard-list fa-3x text-custom-blue mb-3"></i>
                                    <p class="text-custom-blue">Nenhum registro encontrado para os filtros selecionados.</p>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-clipboard-list fa-3x text-custom-blue mb-3"></i>
                                </td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection