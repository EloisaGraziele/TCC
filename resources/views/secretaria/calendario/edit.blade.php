@extends('layouts.admin')

@section('content')
<!-- Título do Sistema -->
<div class="mb-3" style="margin-left: 20px; padding-left: 0;">
    <h2 class="fw-bold">
        <span style="color: #55B4F8;">Sistema de</span> 
        <span style="color: #63FF63;">Presença</span>
    </h2>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header bg-custom-blue text-white text-center py-3">
                    <h2 class="mb-0 fw-bold">📅 Calendário Escolar {{ $ano }}</h2>
                    <p class="mb-0">Editar eventos e feriados</p>
                </div>
                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success text-center rounded-3 mb-4 fw-bold">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <h6 class="fw-bold mb-2">ℹ️ Legenda:</h6>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <div style="width: 15px; height: 15px; background-color: #28a745; margin-right: 5px;"></div>
                                <span class="fw-bold">Letivo</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div style="width: 15px; height: 15px; background-color: #dc3545; margin-right: 5px; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px;">F</div>
                                <span class="fw-bold">Feriado</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div style="width: 15px; height: 15px; background-color: #ff6b35; margin-right: 5px; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px;">V</div>
                                <span class="fw-bold">Férias</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div style="width: 15px; height: 15px; background-color: #ffc107; margin-right: 5px; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px;">E</div>
                                <span class="fw-bold">Evento</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div style="width: 15px; height: 15px; background-color: #17a2b8; margin-right: 5px; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px;">P</div>
                                <span class="fw-bold">Facultativo</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div style="width: 15px; height: 15px; background-color: #007bff; margin-right: 5px; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px;">R</div>
                                <span class="fw-bold">Reunião</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div style="width: 15px; height: 15px; background-color: #2d5a2d; margin-right: 5px;"></div>
                                <span class="fw-bold">Sáb/Dom</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('secretaria.calendario.update', $ano) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            @php
                                $meses = [
                                    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
                                    '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
                                    '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
                                    '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                                ];
                            @endphp

                            @foreach($meses as $numeroMes => $nomeMes)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card" style="height: 280px;">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 fw-bold text-center">{{ $nomeMes }}</h6>
                                        </div>
                                        <div class="card-body p-3" style="height: 240px; overflow: hidden;">
                                            <!-- Cabeçalho dos dias da semana -->
                                            <div class="calendar-header">
                                                <div class="header-day">Dom</div>
                                                <div class="header-day">Seg</div>
                                                <div class="header-day">Ter</div>
                                                <div class="header-day">Qua</div>
                                                <div class="header-day">Qui</div>
                                                <div class="header-day">Sex</div>
                                                <div class="header-day">Sáb</div>
                                            </div>
                                            
                                            <!-- Grid do calendário -->
                                            <div class="calendar-grid" style="height: 180px;">
                                                @php
                                                    $primeiroDia = \Carbon\Carbon::createFromDate($ano, $numeroMes, 1);
                                                    $ultimoDia = $primeiroDia->copy()->endOfMonth();
                                                    $diaSemanaInicio = $primeiroDia->dayOfWeek; // 0=domingo, 6=sábado
                                                    
                                                    // Células vazias no início
                                                    for($i = 0; $i < $diaSemanaInicio; $i++) {
                                                        echo '<div class="calendar-day" style="background: #f8f9fa;"></div>';
                                                    }
                                                @endphp
                                                
                                                @if(isset($calendario[$numeroMes]))
                                                    @foreach($calendario[$numeroMes] as $dia)
                                                        <div class="calendar-day dia-{{ $dia->tipo_dia }} clickable-day" 
                                                             onclick="editarDia('{{ $dia->data }}', '{{ $dia->tipo_dia }}', '{{ $dia->descricao }}')">
                                                            <div class="day-number">
                                                                {{ \Carbon\Carbon::parse($dia->data)->format('d') }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Formulário de Adição Rápida -->
                        <div class="card border-2 mb-4" style="border-color: #63FF63;">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold" style="color: #63FF63;">➕ Adicionar Evento Rápido</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="date" name="eventos[0][data]" class="form-control form-control-sm" placeholder="Data">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="eventos[0][tipo_dia]" class="form-select form-select-sm">
                                            <option value="">— Selecione —</option>
                                            <option value="feriado">🎆 Feriado</option>
                                            <option value="ferias">🏖️ Férias</option>
                                            <option value="ponto_facultativo">📝 Ponto Facultativo</option>
                                            <option value="evento">🎉 Evento</option>
                                            <option value="reuniao">👥 Reunião</option>
                                            <option value="sabado_letivo">📚 Sábado Letivo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="eventos[0][descricao]" class="form-control form-control-sm" placeholder="Descrição">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('secretaria.calendario.index') }}" class="btn btn-secondary btn-lg">
                                    ← Voltar
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    💾 Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dia-letivo { background-color: #e8f5e8; }
    .dia-feriado { background-color: #dc3545; color: white; }
    .dia-ferias { background-color: #ff6b35; color: white; }
    .dia-evento { background-color: #ffc107; color: white; }
    .dia-ponto_facultativo { background-color: #17a2b8; color: white; }
    .dia-reuniao { background-color: #007bff; color: white; }
    .dia-sabado_letivo { background-color: #28a745; color: white; }
    .dia-sabado, .dia-domingo { background-color: #2d5a2d; color: white; }
    .clickable-day { cursor: pointer; transition: all 0.2s; }
    .clickable-day:hover { transform: scale(1.05); box-shadow: 0 2px 8px rgba(0,0,0,0.3); }
    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; }
    .calendar-day { 
        display: flex; 
        flex-direction: column; 
        justify-content: center; 
        align-items: center; 
        border: 1px solid #ddd; 
        border-radius: 4px; 
        font-size: 10px; 
        font-weight: bold; 
        height: 25px;
        position: relative;
    }
    .day-number { font-size: 11px; margin-bottom: 1px; }
    .day-type { font-size: 8px; padding: 1px 3px; border-radius: 2px; }
    .calendar-header { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; margin-bottom: 5px; }
    .header-day { text-align: center; font-weight: bold; padding: 3px; background: #f8f9fa; font-size: 10px; }
</style>

<script>
    // Teste se o JavaScript está carregando
    console.log('JavaScript carregado!');
    
    function editarDia(data, tipoAtual, descricaoAtual) {
        console.log('Função chamada com data:', data);
        
        // Converter a data para o formato YYYY-MM-DD
        let dataFormatada = data;
        if (data.includes(' ')) {
            dataFormatada = data.split(' ')[0]; // Pega apenas a parte da data
        }
        
        console.log('Data formatada:', dataFormatada);
        
        // Encontrar o campo de data
        const campoData = document.querySelector('input[name="eventos[0][data]"]');
        console.log('Campo encontrado:', campoData);
        
        if (campoData) {
            campoData.value = dataFormatada;
            console.log('Data definida como:', campoData.value);
        }
        
        // Preencher a descrição (apenas se existir o campo correto)
        const campoDescricao = document.querySelector('input[name="eventos[0][descricao]"]');
        if (campoDescricao && descricaoAtual) {
            campoDescricao.value = descricaoAtual;
        }
        
        // Rolar para o formulário
        const formulario = document.querySelector('.card.border-2');
        if (formulario) {
            formulario.scrollIntoView({ behavior: 'smooth' });
        }
        
        // Focar no select
        setTimeout(() => {
            const selectTipo = document.querySelector('select[name="eventos[0][tipo_dia]"]');
            if (selectTipo) {
                selectTipo.focus();
            }
        }, 500);
    }
    
    // Adicionar evento de clique em todos os dias do calendário
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM carregado, procurando dias do calendário...');
        
        const diasCalendario = document.querySelectorAll('.calendar-day.clickable-day');
        console.log('Dias encontrados:', diasCalendario.length);
        
        diasCalendario.forEach(function(dia) {
            dia.addEventListener('click', function() {
                console.log('Dia clicado!');
                // Tentar extrair a data do elemento
                const dataAtributo = dia.getAttribute('data-date') || dia.dataset.date;
                if (dataAtributo) {
                    editarDia(dataAtributo, '', '');
                }
            });
        });
    });
</script>
@endsection