@extends('layouts.admin')

@section('content')
<!-- Título do Sistema -->
<div class="mb-3" style="margin-left: 20px; padding-left: 0;">
    <h2 class="fw-bold">
        <span style="color: #55B4F8;">Sistema de</span> 
        <span style="color: #63FF63;">Presença</span>
    </h2>
</div>

<div class="container py-2">
    <!-- Mensagem de sucesso -->
    @if (session('success'))
        <div class="alert alert-success text-center rounded-3 mb-4 fw-bold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Bloco principal -->
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card border-0 rounded-4 shadow-lg" 
                 style="background-color: #55B4F8; min-height: 70vh; display: flex; flex-direction: column; justify-content: center;">
                
                <div class="card-header text-white fw-bold text-center py-4 rounded-top-4" 
                     style="background-color: #55B4F8; border-bottom: none;">
                    <h1 class="mb-0" style="font-size: 2rem;">Configurar Alertas</h1>
                </div>

                <div class="card-body d-flex justify-content-center align-items-center">
                    <div class="w-100" style="max-width: 600px;">
                        <form method="POST" action="{{ route('secretaria.alertas.store') }}" id="formAlerta">
                            @csrf

                            <!-- Tipo de Alerta -->
                            <div class="mb-4">
                                <label for="tipo_alerta" class="form-label text-dark fw-bold" style="font-size: 1.4rem;">Tipo de Alerta</label>
                                <select id="tipo_alerta" name="tipo_alerta" class="form-select form-control-lg rounded-3" required onchange="mostrarCriterios()">
                                    <option value="" disabled selected>Selecione o tipo</option>
                                    <option value="faltas_consecutivas">Faltas Consecutivas</option>
                                    <option value="percentual_faltas">Percentual de Faltas</option>
                                    <option value="dia_especifico">Falta em Dia Específico</option>
                                </select>
                            </div>

                            <!-- Critérios para Faltas Consecutivas -->
                            <div id="criterio_consecutivas" class="mb-4" style="display: none;">
                                <label for="dias_consecutivos" class="form-label text-dark fw-bold" style="font-size: 1.4rem;">Número de Dias Consecutivos</label>
                                <input type="number" id="dias_consecutivos" name="dias_consecutivos" min="1" max="30" 
                                       class="form-control form-control-lg rounded-3" placeholder="Ex: 3">
                            </div>

                            <!-- Critérios para Percentual -->
                            <div id="criterio_percentual" class="mb-4" style="display: none;">
                                <label for="percentual_limite" class="form-label text-dark fw-bold" style="font-size: 1.4rem;">Percentual Limite (%)</label>
                                <input type="number" id="percentual_limite" name="percentual_limite" min="1" max="100" 
                                       class="form-control form-control-lg rounded-3" placeholder="Ex: 25">
                            </div>

                            <!-- Critérios para Dia Específico -->
                            <div id="criterio_dia_especifico" class="mb-4" style="display: none;">
                                <label for="data_especifica" class="form-label text-dark fw-bold" style="font-size: 1.4rem;">Data Específica</label>
                                <input type="date" id="data_especifica" name="data_especifica" 
                                       class="form-control form-control-lg rounded-3">
                            </div>

                            <!-- Descrição -->
                            <div class="mb-4">
                                <label for="descricao" class="form-label text-dark fw-bold" style="font-size: 1.4rem;">Mensagem</label>
                                <textarea id="descricao" name="descricao" rows="3" class="form-control form-control-lg rounded-3" required></textarea>
                            </div>

                            <!-- Destinatários -->
                            <div class="mb-4">
                                <label class="form-label text-dark fw-bold" style="font-size: 1.4rem;">Destinatários</label>
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="notificar_pais" class="form-check-input" style="transform: scale(1.5); margin-right: 10px;" checked>
                                    <label class="form-check-label text-dark fw-bold" style="font-size: 1.2rem;">Responsáveis/Pais</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="notificar_secretaria" class="form-check-input" style="transform: scale(1.5); margin-right: 10px;" checked>
                                    <label class="form-check-label text-dark fw-bold" style="font-size: 1.2rem;">Secretaria</label>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="d-flex justify-content-between mt-4">
                                <!-- Limpar -->
                                <button type="button" 
                                        onclick="document.getElementById('formAlerta').reset(); ocultarCriterios();" 
                                        class="btn fw-bold text-dark"
                                        style="background-color: #ffc107; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Limpar
                                </button>

                                <!-- Salvar -->
                                <button type="submit" 
                                        class="btn fw-bold text-dark"
                                        style="background-color: #63FF63; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Salvar
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Alertas -->
    @if($alertas->count() > 0)
    <div class="row justify-content-center mt-5">
        <div class="col-12">
            <h1 class="text-start mb-4 fw-bold" style="color: #63FF63; font-size: 2rem;">Lista de Alertas</h1>
            
            <div class="table-responsive">
                <table class="table mb-0" style="background-color: white; border: 3px solid #55B4F8;">
                    <thead>
                        <tr style="border-bottom: 3px solid #55B4F8;">
                            <th class="fw-bold text-center py-3" style="font-size: 1.2rem; border-right: 2px solid #55B4F8;">Tipo</th>
                            <th class="fw-bold text-center py-3" style="font-size: 1.2rem; border-right: 2px solid #55B4F8;">Descrição</th>
                            <th class="fw-bold text-center py-3" style="font-size: 1.2rem; border-right: 2px solid #55B4F8;">Critério</th>
                            <th class="fw-bold text-center py-3" style="font-size: 1.2rem; border-right: 2px solid #55B4F8;">Data Criação</th>
                            <th class="fw-bold text-center py-3" style="font-size: 1.2rem; border-right: 2px solid #55B4F8;">Destinatários</th>
                            <th class="fw-bold text-center py-3" style="font-size: 1.2rem;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alertas as $alerta)
                        <tr style="border-bottom: 2px solid #55B4F8;">
                            <td class="text-center align-middle py-3" style="border-right: 2px solid #55B4F8;">
                                <span class="badge" style="background-color: #55B4F8; color: black; font-size: 1rem; padding: 10px 15px;">
                                    @if($alerta->tipo_alerta == 'faltas_consecutivas')
                                        Faltas Consecutivas
                                    @elseif($alerta->tipo_alerta == 'percentual_faltas')
                                        Percentual de Faltas
                                    @else
                                        Dia Específico
                                    @endif
                                </span>
                            </td>
                            <td class="align-middle py-3" style="font-size: 1.1rem; border-right: 2px solid #55B4F8;">{{ $alerta->descricao }}</td>
                            <td class="text-center align-middle py-3" style="font-size: 1.1rem; border-right: 2px solid #55B4F8;">
                                @if($alerta->tipo_alerta == 'faltas_consecutivas')
                                    <strong>{{ $alerta->parametros['dias_consecutivos'] ?? 'N/A' }}</strong> dias
                                @elseif($alerta->tipo_alerta == 'percentual_faltas')
                                    <strong>{{ $alerta->parametros['percentual_limite'] ?? 'N/A' }}%</strong>
                                @else
                                    <strong>{{ $alerta->parametros['data_especifica'] ?? 'N/A' }}</strong>
                                @endif
                            </td>
                            <td class="text-center align-middle py-3" style="font-size: 1.1rem; border-right: 2px solid #55B4F8;">
                                {{ $alerta->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-center align-middle py-3" style="border-right: 2px solid #55B4F8;">
                                <span class="badge bg-info text-dark px-3 py-2">
                                    {{ ucfirst($alerta->destinatarios) }}
                                </span>
                            </td>
                            <td class="text-center align-middle py-3">
                                <form method="POST" action="{{ route('secretaria.alertas.destroy', $alerta->id) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 10px 20px; font-size: 1rem;" onclick="return confirm('Tem certeza que deseja excluir?')">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function mostrarCriterios() {
    const tipo = document.getElementById('tipo_alerta').value;
    ocultarCriterios();
    
    if (tipo === 'faltas_consecutivas') {
        document.getElementById('criterio_consecutivas').style.display = 'block';
    } else if (tipo === 'percentual_faltas') {
        document.getElementById('criterio_percentual').style.display = 'block';
    } else if (tipo === 'dia_especifico') {
        document.getElementById('criterio_dia_especifico').style.display = 'block';
    }
}

function ocultarCriterios() {
    document.getElementById('criterio_consecutivas').style.display = 'none';
    document.getElementById('criterio_percentual').style.display = 'none';
    document.getElementById('criterio_dia_especifico').style.display = 'none';
}
</script>
@endsection