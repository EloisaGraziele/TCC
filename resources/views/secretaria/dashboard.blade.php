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
        <h1 class="h3 mb-4 fw-bold text-custom-blue">Registro de Frequência</h1>
        

        
        @if(session('debug_info'))
            <div class="alert alert-warning mb-3">
                {{ session('debug_info') }}
            </div>
        @endif

        <!-- Formulário de Pesquisa -->
        <form method="GET" class="mb-5" autocomplete="off">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="ano" class="form-label fw-medium">Ano</label>
                    <input type="number" id="ano" name="ano" placeholder="Ex: 2025" 
                           class="form-control rounded-3 shadow-sm border" 
                           min="2020" max="2030" autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label for="turma" class="form-label fw-medium">Turma</label>
                    <select id="turma" name="turma" class="form-control rounded-3 shadow-sm border" autocomplete="off">
                        <option value="">Selecione uma turma ▼</option>
                        @foreach($turmas as $turma)
                            <option value="{{ $turma->turma }}">{{ $turma->turma }}</option>
                        @endforeach
                    </select>
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
                    <a href="{{ route('secretaria.frequencias.index') }}" class="btn" style="background-color: #ffc107; color: black;">
                        <i class="fas fa-eraser me-1"></i>Limpar
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Tabela de Registros -->
        <div class="bg-white shadow-lg overflow-hidden">
            @if(request('turma'))
                <div class="bg-custom-blue text-white text-center py-2">
                    <h5 class="mb-0 fw-bold">{{ request('turma') }}</h5>
                </div>
            @endif
            <table class="table table-bordered mb-0">
                <thead>
                    <tr class="bg-custom-green text-dark fw-bold">
                        <th class="text-center" style="background-color: #63FF63 !important;">Aluno</th>
                        <th class="text-center" style="background-color: #63FF63 !important;">Data</th>
                        <th class="text-center" style="background-color: #63FF63 !important;">Presença</th>
                        <th class="text-center" style="background-color: #63FF63 !important;">Hora-Entrada</th>
                        <th class="text-center" style="background-color: #63FF63 !important;">Hora-Saída</th>
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
                                {{ \Carbon\Carbon::parse($frequencia->created_at)->format('d/m/Y') }}
                            </td>
                            <td>
                                @if(isset($frequencia->frequencia))
                                    @if($frequencia->frequencia == 'presente')
                                        <span class="badge bg-success">Presente</span>
                                    @elseif($frequencia->frequencia == 'falta')
                                        <span class="fw-bold" style="color: red;">Falta</span>
                                    @elseif($frequencia->frequencia == 'dia_nao_letivo')
                                        <span class="badge bg-secondary">Dia Não Letivo</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($frequencia->hora_entrada)
                                    {{ $frequencia->hora_entrada }}
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
                        </tr>
                    @empty
                        @if(request()->hasAny(['turma', 'data_inicio', 'data_fim']))
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-clipboard-list fa-3x text-custom-blue mb-3"></i>
                                    <p class="text-custom-blue">Nenhuma frequência encontrada com os filtros aplicados.</p>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-clipboard-list fa-3x text-custom-blue mb-3"></i>
                                </td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Botão Imprimir -->
        @if($frequencias->count() > 0)
            <div class="text-center mt-3">
                <button onclick="imprimirTabela()" class="btn" style="background-color: #55B4F8; color: white;">
                    <i class="fas fa-print me-1"></i>Imprimir
                </button>
            </div>
        @endif
        
        <!-- Paginação -->
        @if($frequencias->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $frequencias->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

<script>
function limparPesquisa() {
    document.getElementById('ano').value = '';
    document.getElementById('turma').value = '';
    document.getElementById('data_inicio').value = '';
    document.getElementById('data_fim').value = '';
    window.location.href = window.location.pathname;
}

function imprimirTabela() {
    var conteudo = document.querySelector('.bg-white.shadow-lg').outerHTML;
    var janela = window.open('', '_blank');
    janela.document.write('<html><head><title>Frequência - ' + '{{ request("turma") }}' + '</title>');
    janela.document.write('<style>table{border-collapse:collapse;width:100%;}th,td{border:1px solid #000;padding:8px;text-align:center;}th{background-color:#63FF63;}</style>');
    janela.document.write('</head><body>');
    janela.document.write(conteudo);
    janela.document.write('</body></html>');
    janela.document.close();
    janela.print();
}


</script>

@endsection