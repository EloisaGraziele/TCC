<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Sistema de Presença</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-custom-green { background-color: #63FF63 !important; }
        .bg-custom-blue { background-color: #55B4F8; }
        .text-custom-blue { color: #55B4F8; }
        .text-custom-green { color: #63FF63; }
        .btn-custom-blue { 
            background-color: #55B4F8; 
            border-color: #55B4F8; 
            color: white;
        }
        .btn-custom-blue:hover { 
            background-color: #40A4E5; 
            border-color: #40A4E5; 
            color: white;
        }
        .dropdown-custom {
            border: 2px solid white;
            border-radius: 50px;
            padding: 12px 50px 12px 20px;
            background-color: white;
            position: relative;
        }
        .dropdown-custom::after {
            font-size: 1.2em;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
        .aluno-card {
            background-color: #55B4F8;
            border-radius: 15px;
            padding: 60px;
            color: white;
            margin-top: 30px;
            min-height: 450px;
            font-size: 1.3em;
        }
        .quadrado-branco {
            width: 160px;
            height: 160px;
            background-color: white;
            margin: 0 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 30px;
            color: #333;
            font-weight: bold;
            text-align: center;
            font-size: 1.2em;
        }
        .quadrado-branco:first-child {
            margin-left: 0;
        }
        .quadrado-branco:last-child {
            margin-right: 0;
        }
        .status-presente {
            background-color: #B8FFB8 !important;
            color: #000 !important;
            padding: 6px 12px;
            border-radius: 0px;
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            min-width: 70px;
        }
        .tabela-custom {
            border: 3px solid #000 !important;
        }
        .tabela-custom td {
            border-left: none !important;
            border-right: none !important;
            border-top: 2px solid #000 !important;
            border-bottom: 2px solid #000 !important;
        }
        .tabela-custom th {
            border-left: none !important;
            border-right: none !important;
            border-top: 2px solid #000 !important;
            border-bottom: 2px solid #000 !important;
        }
        .status-falta {
            background-color: #FFB6C1 !important;
            color: #000 !important;
            padding: 6px 12px;
            border-radius: 0px;
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            min-width: 70px;
        }
        body { 
            background-color: #ecf2f7ff; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
    <!-- Topo Verde -->
    <div class="bg-custom-green p-3 d-flex justify-content-between align-items-center">
        <div></div>
        <div class="d-flex align-items-center">
            <span class="me-3 fw-bold">Olá, {{ $user->name }}!</span>
            @php
                $unreadCount = isset($notificacoes) ? $notificacoes->where('lida', false)->count() : 0;
            @endphp
            <button class="btn btn-custom-blue me-2 d-flex align-items-center px-2 py-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#notificacoesPanel" aria-controls="notificacoesPanel">
                <i class="fas fa-bell me-1"></i>
                Notificações (<span id="notificacoes-count">{{ $unreadCount }}</span>)
            </button>
            <button class="btn btn-custom-blue me-2 d-flex align-items-center px-2 py-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#perfilPanel" aria-controls="perfilPanel">
                <i class="fas fa-user-circle me-1"></i>
                Perfil
            </button>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger d-flex align-items-center px-2 py-1">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Sair
                </button>
            </form>
        </div>
    </div>

    <!-- Offcanvas notifications panel -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="notificacoesPanel" aria-labelledby="notificacoesPanelLabel" style="width: 400px;">
        <div class="offcanvas-header bg-custom-blue text-white">
            <h5 id="notificacoesPanelLabel" class="fw-bold"><i class="fas fa-bell me-2"></i>Notificações</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div id="notificacoes-list">
                <div class="text-center py-5" id="notificacoes-loading">
                    <div class="spinner-border text-custom-blue" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando notificações...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas perfil panel -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="perfilPanel" aria-labelledby="perfilPanelLabel" style="width: 400px;">
        <div class="offcanvas-header bg-custom-green text-dark">
            <h5 id="perfilPanelLabel" class="fw-bold"><i class="fas fa-user-circle me-2"></i>Meu Perfil</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div id="perfil-content">
                <div class="text-center py-5" id="perfil-loading">
                    <div class="spinner-border text-custom-blue" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-custom-blue">Carregando perfil...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sistema de Presença -->
    <div class="ps-4 py-4">
        <h2 class="h2 fw-bold">
            <span class="text-custom-blue">Sistema de</span>
            <span class="text-custom-green">Presença</span>
        </h2>
    </div>

    <!-- Título Frequências -->
    <div class="container py-4">
        <div class="text-center">
            <h1 class="display-3 fw-bold text-custom-green mb-5">Frequências</h1>
        </div>
        
        <!-- Barra de Pesquisa -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <div class="dropdown">
                    <button class="btn dropdown-toggle w-100 text-start dropdown-custom" type="button" id="alunoDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Nome do aluno
                    </button>
                    <ul class="dropdown-menu w-100" aria-labelledby="alunoDropdown">
                        @if(isset($alunos) && $alunos->count() > 0)
                            @foreach($alunos as $aluno)
                                <li><a class="dropdown-item" href="#" onclick="selecionarAluno('{{ $aluno->nome }}', '{{ $aluno->turma }} ({{ $aluno->ano }})', {{ $aluno->id }})">{{ $aluno->nome }}</a></li>
                            @endforeach
                        @else
                            <li><span class="dropdown-item text-muted">Nenhum aluno vinculado</span></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Card do Aluno -->
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="aluno-card" id="alunoCard">
                    <h4 id="nomeAluno" class="fw-bold mb-2">Nome do Aluno</h4>
                    <p id="turmaAluno" class="mb-3">Turma: </p>
                    <div class="d-flex justify-content-between">
                        <div class="quadrado-branco">
                            <div>Aulas</div>
                            <div class="fs-4 mt-2" id="totalAulas">-</div>
                        </div>
                        <div class="quadrado-branco">
                            <div>Presenças</div>
                            <div class="fs-4 mt-2" id="totalPresencas">-</div>
                        </div>
                        <div class="quadrado-branco">
                            <div>Faltas</div>
                            <div class="fs-4 mt-2" id="totalFaltas">-</div>
                        </div>
                        <div class="quadrado-branco">
                            <div>Frequência</div>
                            <div class="fs-4 mt-2" id="percentualFrequencia">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabela Presença da Semana -->
                            <tr>
                            </tr>
        <div class="row justify-content-center mt-4">
            <div class="col-md-10">
                <div class="table-responsive" id="tabelaSemana">
                    <table class="table text-center tabela-custom">
                        <thead>
                            <tr>
                                <th colspan="4" class="fw-bold text-start" style="background-color: #63FF63 !important;">Presença da Semana</th>
                            <tr>
                            </tr>
                            </tr>
                                                    <tr><th>Dia</th><th>Status</th><th>Entrada</th><th>Saída</th></tr>
                        </thead>
                            <tbody id="corpoTabelaSemana">
                                <!-- Dados serão preenchidos via JavaScript -->
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
        
        <!-- Quadrado de Pesquisa -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-10">
                <div class="bg-custom-blue rounded-0 shadow-sm p-4">
                    <div class="text-center mb-3">
                        <h3 class="fw-bold mb-0" style="color: #63FF63;">Pesquisar</h3>
                    </div>
                    <!-- Campos de Pesquisa -->
                    <div class="row g-3 justify-content-center">
                        <div class="col-md-5">
                            <select class="form-select rounded-pill" id="mesPesquisa">
                                <option value="">Mês</option>
                                <option value="01">Janeiro</option>
                                <option value="02">Fevereiro</option>
                                <option value="03">Março</option>
                                <option value="04">Abril</option>
                                <option value="05">Maio</option>
                                <option value="06">Junho</option>
                                <option value="07">Julho</option>
                                <option value="08">Agosto</option>
                                <option value="09">Setembro</option>
                                <option value="10">Outubro</option>
                                <option value="11">Novembro</option>
                                <option value="12">Dezembro</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="date" class="form-control rounded-pill" id="dataPesquisa" placeholder="Data">
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn fw-bold me-2 px-4 py-2" style="background-color: white; color: #63FF63; font-size: 1.15rem; border: 2px solid #63FF63;" onclick="pesquisarPresenca()">Pesquisar</button>
                        <button type="button" class="btn btn-outline-light px-4 py-2" style="font-size: 1.1rem;" onclick="limparPesquisa()">Limpar</button>
                    </div>
                    <!-- Resultado da Pesquisa -->
                    <div id="resultadoPesquisa" class="mt-4" style="display: none;">
                        <div class="bg-white rounded-3 p-3">
                            <div id="conteudoResultado"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Função para calcular e exibir datas da semana atual
        function carregarDiasSemana() {
            const hoje = new Date();
            const diaSemana = hoje.getDay(); // 0 = domingo, 1 = segunda, etc.
            const inicioSemana = new Date(hoje);
            
            // Calcular segunda-feira da semana atual
            const diasParaSegunda = diaSemana === 0 ? -6 : 1 - diaSemana;
            inicioSemana.setDate(hoje.getDate() + diasParaSegunda);
            
            const diasSemana = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
            const tbody = document.getElementById('corpoTabelaSemana');
            tbody.innerHTML = '';
            
            for (let i = 0; i < 7; i++) {
                const dataAtual = new Date(inicioSemana);
                dataAtual.setDate(inicioSemana.getDate() + i);
                const dia = dataAtual.getDate().toString().padStart(2, '0');
                
                const row = tbody.insertRow();
                row.insertCell(0).textContent = `${diasSemana[i]}. ${dia}`;
                row.insertCell(1).innerHTML = '<span class="text-muted">-</span>';
                row.insertCell(2).textContent = '-';
                row.insertCell(3).textContent = '-';
            }
        }
        
        // Carregar datas da semana quando a página carrega
        document.addEventListener('DOMContentLoaded', carregarDiasSemana);
        
        function selecionarAluno(nome, turma, alunoId) {
            console.log('Selecionando aluno:', nome, turma, alunoId);
            alunoSelecionadoId = alunoId; // Armazenar ID do aluno selecionado
            document.getElementById('alunoDropdown').textContent = nome;
            document.getElementById('nomeAluno').textContent = nome;
            document.getElementById('turmaAluno').textContent = 'Turma: ' + turma;
            
            // Exibir card
            var card = document.getElementById('alunoCard');
            if (card) {
                card.style.display = 'block';
            }
            
            // Buscar dados de frequência
            fetch(`/aluno/${alunoId}/frequencia`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalAulas').textContent = data.aulas;
                    document.getElementById('totalPresencas').textContent = data.presencas;
                    document.getElementById('totalFaltas').textContent = data.faltas;
                    document.getElementById('percentualFrequencia').textContent = data.frequencia;
                })
                .catch(error => {
                    console.error('Erro ao buscar dados:', error);
                    document.getElementById('totalAulas').textContent = '0';
                    document.getElementById('totalPresencas').textContent = '0';
                    document.getElementById('totalFaltas').textContent = '0';
                    document.getElementById('percentualFrequencia').textContent = '0%';
                });
                
            // Buscar dados da semana
            fetch(`/aluno/${alunoId}/semana`)
                .then(response => response.json())
                .then(data => {
                    preencherTabelaSemana(data);
                    document.getElementById('tabelaSemana').style.display = 'block';
                })
                .catch(error => {
                    console.error('Erro ao buscar dados da semana:', error);
                });
        }
        
        function preencherTabelaSemana(dados) {
            const diasSemana = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
            const tbody = document.getElementById('corpoTabelaSemana');
            tbody.innerHTML = '';
            
            dados.forEach((dia, index) => {
                const row = tbody.insertRow();
                
                // Dia da semana + data
                const cellDia = row.insertCell(0);
                cellDia.innerHTML = `${diasSemana[index]}. ${dia.data}`;
                
                // Status
                const cellStatus = row.insertCell(1);
                if (dia.status === 'presente') {
                    cellStatus.innerHTML = '<span class="status-presente">PRESENTE</span>';
                } else if (dia.status === 'falta') {
                    cellStatus.innerHTML = '<span class="status-falta">FALTA</span>';
                } else {
                    cellStatus.innerHTML = '<span class="text-muted">-</span>';
                }
                
                // Entrada
                const cellEntrada = row.insertCell(2);
                cellEntrada.textContent = dia.entrada || '-';
                
                // Saída
                const cellSaida = row.insertCell(3);
                cellSaida.textContent = dia.saida || '-';
            });
        }
        
        let alunoSelecionadoId = null;
        
        function pesquisarPresenca() {
            const mes = document.getElementById('mesPesquisa').value;
            const data = document.getElementById('dataPesquisa').value;
            
            if (!mes && !data) {
                alert('Selecione pelo menos um critério de pesquisa');
                return;
            }
            
            if (!alunoSelecionadoId) {
                alert('Selecione um aluno primeiro');
                return;
            }
            
            const resultado = document.getElementById('resultadoPesquisa');
            const conteudo = document.getElementById('conteudoResultado');
            
            // Mostrar loading
            conteudo.innerHTML = '<div class="text-center py-3"><div class="spinner-border" role="status"></div></div>';
            resultado.style.display = 'block';
            
            // Fazer requisição para o backend
            const params = new URLSearchParams();
            if (mes) params.append('mes', mes);
            if (data) params.append('data', data);
            
            fetch(`/aluno/${alunoSelecionadoId}/pesquisar?${params.toString()}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(dados => {
                    console.log('Dados recebidos:', dados);
                    if (dados.length === 0) {
                        conteudo.innerHTML = `
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Data</th>
                                            <th>Status</th>
                                            <th>Hora-Entrada</th>
                                            <th>Hora-Saída</th>
                                        </tr>
                                                                <tr><th>Dia</th><th>Status</th><th>Entrada</th><th>Saída</th></tr>
                        </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                <h6>Sem presença nesse período</h6>
                                                <p class="mb-0">Não foram encontrados registros de presença para os critérios selecionados.</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;
                    } else {
                        let linhas = '';
                        dados.forEach(item => {
                            const statusClass = item.status === 'PRESENTE' ? 'status-presente' : 'status-falta';
                            linhas += `
                                <tr>
                                    <td>${item.data}</td>
                                    <td><span class="${statusClass}">${item.status}</span></td>
                                    <td>${item.hora_entrada}</td>
                                    <td>${item.hora_saida}</td>
                                </tr>
                            `;
                        });
                        
                        conteudo.innerHTML = `
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Data</th>
                                            <th>Status</th>
                                            <th>Hora-Entrada</th>
                                            <th>Hora-Saída</th>
                                        </tr>
                                                                <tr><th>Dia</th><th>Status</th><th>Entrada</th><th>Saída</th></tr>
                        </thead>
                                    <tbody>
                                        ${linhas}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Erro ao pesquisar:', error);
                    conteudo.innerHTML = `<div class="text-center text-danger py-3">Erro ao buscar dados: ${error.message}<br>Verifique o console para mais detalhes.</div>`;
                });
        }
        
        function limparPesquisa() {
            document.getElementById('mesPesquisa').value = '';
            document.getElementById('dataPesquisa').value = '';
            document.getElementById('resultadoPesquisa').style.display = 'none';
        }
    </script>



    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2024 Sistema de Presença. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Notificações Panel Logic
    (function(){
        const panel = document.getElementById('notificacoesPanel');
        if (!panel) return;

        const loadNotifications = () => {
            const list = document.getElementById('notificacoes-list');
            const loading = document.getElementById('notificacoes-loading');
            fetch("/usuario/notificacoes/latest", {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            }).then(r => r.json()).then(data => {
                loading && (loading.style.display = 'none');
                if (!data || data.length === 0) {
                    list.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-custom-blue mb-3"></i>
                            <p class="text-custom-blue">Nenhuma notificação recente</p>
                        </div>`;
                    return;
                }
                list.innerHTML = '';
                data.forEach(n => {
                    const item = document.createElement('div');
                    item.className = `border-bottom p-3 ${!n.lida ? 'bg-light' : ''}`;
                    item.innerHTML = `
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="rounded-circle bg-custom-blue d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-info text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 fw-bold">Notificação</h6>
                                    ${!n.lida ? '<span class="badge bg-custom-green text-dark">Nova</span>' : ''}
                                </div>
                                <p class="mb-1 small">${n.mensagem}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-custom-blue">
                                        <i class="fas fa-clock me-1"></i>${new Date(n.created_at).toLocaleString()}
                                    </small>
                                    ${!n.lida ? '<button class="btn btn-sm btn-custom-blue marcar-lida-btn" data-id="'+n.id+'" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"><i class="fas fa-check me-1"></i>Marcar lida</button>' : ''}
                                </div>
                            </div>
                        </div>`;
                    list.appendChild(item);
                });

                document.querySelectorAll('.marcar-lida-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const id = e.currentTarget.getAttribute('data-id');
                        fetch(`/usuario/notificacoes/${id}/marcar-lida`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        }).then(res => {
                            if (res.ok) {
                                loadNotifications();
                                updateCount();
                            }
                        });
                    });
                });
            }).catch(err => {
                loading && (loading.innerText = 'Erro ao carregar notificações');
                console.error(err);
            });
        };

        const updateCount = () => {
            fetch("/usuario/notificacoes/latest", { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                .then(r => r.json()).then(data => {
                    const unread = data.filter(n => !n.lida).length;
                    const el = document.getElementById('notificacoes-count');
                    if (el) el.innerText = unread;
                }).catch(()=>{});
        };

        panel.addEventListener('show.bs.offcanvas', loadNotifications);
        setInterval(updateCount, 30000);
    })();

    // Perfil Panel Logic
    (function(){
        const perfilPanel = document.getElementById('perfilPanel');
        if (!perfilPanel) return;

        const loadPerfil = () => {
            const content = document.getElementById('perfil-content');
            const loading = document.getElementById('perfil-loading');
            
            fetch("/usuario/perfil", {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            }).then(r => r.json()).then(data => {
                loading && (loading.style.display = 'none');
                
                content.innerHTML = `
                    <div class="p-4">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-custom-blue d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-user fa-2x text-white"></i>
                            </div>
                            <h6 class="mt-3 mb-0 text-custom-blue fw-bold">${data.name || 'Usuário'}</h6>
                            <small class="text-custom-blue">${data.email || ''}</small>
                        </div>
                        
                        <form id="perfil-form">
                            <div class="mb-3">
                                <label for="name" class="form-label text-custom-blue fw-medium">
                                    <i class="fas fa-user me-2"></i>Nome
                                </label>
                                <input type="text" class="form-control border-2" id="name" name="name" value="${data.name || ''}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label text-custom-blue fw-medium">
                                    <i class="fas fa-envelope me-2"></i>E-mail
                                </label>
                                <input type="email" class="form-control border-2" id="email" name="email" value="${data.email || ''}" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label text-custom-blue fw-medium">
                                    <i class="fas fa-lock me-2"></i>Nova Senha (opcional)
                                </label>
                                <input type="password" class="form-control border-2" id="password" name="password" placeholder="Deixe em branco para manter a atual">
                            </div>
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label text-custom-blue fw-medium">
                                    <i class="fas fa-lock me-2"></i>Confirmar Nova Senha
                                </label>
                                <input type="password" class="form-control border-2" id="password_confirmation" name="password_confirmation" placeholder="Confirme a nova senha">
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-custom-blue py-2">
                                    <i class="fas fa-save me-2"></i>Atualizar Perfil
                                </button>
                            </div>
                        </form>
                        <div id="perfil-message" class="mt-3"></div>
                    </div>
                `;

                const form = document.getElementById('perfil-form');
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    const data = Object.fromEntries(formData);
                    
                    if (!data.password) {
                        delete data.password;
                        delete data.password_confirmation;
                    }
                    
                    fetch("/usuario/perfil", {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    }).then(r => {
                        return r.json().then(data => {
                            if (!r.ok) {
                                throw { status: r.status, data: data };
                            }
                            return data;
                        });
                    }).then(response => {
                        const messageDiv = document.getElementById('perfil-message');
                        if (response.success) {
                            messageDiv.innerHTML = '<div class="alert alert-success">' + response.message + '</div>';
                            document.getElementById('password').value = '';
                            document.getElementById('password_confirmation').value = '';
                        } else {
                            let errorMsg = response.error || 'Erro ao atualizar perfil';
                            if (response.errors) {
                                errorMsg += '<br>' + Object.values(response.errors).flat().join('<br>');
                            }
                            messageDiv.innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
                        }
                    }).catch(err => {
                        let errorMsg = 'Erro ao atualizar perfil';
                        if (err.data && err.data.error) {
                            errorMsg = err.data.error;
                        } else if (err.message) {
                            errorMsg = err.message;
                        }
                        document.getElementById('perfil-message').innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
                        console.error('Erro:', err);
                    });
                });
            }).catch(err => {
                loading && (loading.innerText = 'Erro ao carregar perfil');
                console.error(err);
            });
        };

        perfilPanel.addEventListener('show.bs.offcanvas', loadPerfil);
    })();
    </script>
</body>
</html>