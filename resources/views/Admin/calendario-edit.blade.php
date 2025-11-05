<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Calendário {{ $ano }} - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-custom-green { background-color: #63FF63; }
        .bg-custom-blue { background-color: #55B4F8; }
        body { background-color: #ecf2f7ff; }
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
</head>
<body>
    <!-- Topo Verde -->
    <div class="bg-custom-green p-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold text-dark">Sistema de Presença - ADMIN</h4>
        <div>
            <a href="{{ route('admin.index') }}" class="btn btn-primary btn-sm me-2" style="background-color: #55B4F8;">
                Voltar ao Painel
            </a>
            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Sair</button>
            </form>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-header bg-custom-blue text-white text-center py-3">
                        <h2 class="mb-0 fw-bold">📅 Calendário Escolar {{ $ano }}</h2>
                        <p class="mb-0">Adicionar eventos e feriados</p>
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

                        <form method="POST" action="{{ route('admin.calendario.update', $ano) }}">
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
                                    <a href="{{ route('admin.calendario.index') }}" class="btn btn-secondary btn-lg">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarDia(data, tipoAtual, descricaoAtual) {
            // Preencher o formulário com os dados do dia clicado
            document.querySelector('input[name="eventos[0][data]"]').value = data;
            document.querySelector('input[name="eventos[0][descricao]"]').value = descricaoAtual || '';
            
            // Focar no select para o usuário escolher
            const selectTipo = document.querySelector('select[name="eventos[0][tipo_dia]"]');
            selectTipo.focus();
            
            alert('Use o formulário "Adicionar Evento Rápido" para alterar este dia e clique em "Salvar Alterações".');
        }
    </script>
</body>
</html>