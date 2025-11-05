<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Sistema de Presença</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-custom-green { background-color: #63FF63; }
        .bg-custom-blue { background-color: #55B4F8; }
        .text-custom-blue { color: #55B4F8; }
        body { background-color: #ecf2f7ff; }
    </style>
</head>
<body>
    <!-- Topo Verde -->
    <div class="bg-custom-green p-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold text-dark">Sistema de Presença - ADMIN</h4>
        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">Sair</button>
        </form>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-header bg-custom-blue text-white text-center py-3">
                        <h2 class="mb-0 fw-bold">Painel Administrativo</h2>
                        <p class="mb-0">Controle total do sistema</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-3 mb-4">
                                <div class="card h-100 border-2" style="border-color: #55B4F8;">
                                    <div class="card-body text-center">
                                        <i class="fas fa-school fa-3x text-custom-blue mb-3"></i>
                                        <h5 class="card-title text-custom-blue fw-bold">Secretaria</h5>
                                        <p class="card-text">Acesso completo ao sistema da secretaria escolar</p>
                                        <a href="{{ route('secretaria.login') }}" class="btn btn-primary" style="background-color: #55B4F8;">
                                            Acessar Secretaria
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-4">
                                <div class="card h-100 border-2" style="border-color: #55B4F8;">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-tie fa-3x text-custom-blue mb-3"></i>
                                        <h5 class="card-title text-custom-blue fw-bold">Secretarias</h5>
                                        <p class="card-text">Cadastrar e gerenciar funcionários da secretaria</p>
                                        <a href="{{ route('admin.secretaria.create') }}" class="btn btn-primary" style="background-color: #55B4F8;">
                                            Cadastrar Secretaria
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-4">
                                <div class="card h-100 border-2" style="border-color: #55B4F8;">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-plus fa-3x text-custom-blue mb-3"></i>
                                        <h5 class="card-title text-custom-blue fw-bold">Criar Calendário</h5>
                                        <p class="card-text">Gerar calendário base para um novo ano</p>
                                        <a href="{{ route('admin.calendario.create') }}" class="btn btn-primary" style="background-color: #55B4F8;">
                                            Criar Calendário
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-4">
                                <div class="card h-100 border-2" style="border-color: #63FF63;">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-edit fa-3x mb-3" style="color: #63FF63;"></i>
                                        <h5 class="card-title fw-bold" style="color: #63FF63;">Gerenciar Calendários</h5>
                                        <p class="card-text">Adicionar feriados, eventos e reuniões</p>
                                        <a href="{{ route('admin.calendario.index') }}" class="btn btn-success" style="background-color: #63FF63; color: #000;">
                                            Gerenciar Calendários
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <div class="card border-2" style="border-color: #63FF63;">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold">Gerenciamento de Logins</h6>
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">
                                                📱 Login de Responsáveis
                                            </a>
                                            <a href="{{ route('secretaria.login') }}" class="btn btn-outline-primary btn-sm">
                                                🏢 Login da Secretaria
                                            </a>
                                            <a href="{{ route('admin.login') }}" class="btn btn-outline-warning btn-sm">
                                                ⚙️ Login do Admin
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card border-2" style="border-color: #63FF63;">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold">Ações Rápidas</h6>
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('admin.secretaria.create') }}" class="btn btn-outline-success btn-sm">
                                                ➕ Nova Secretaria
                                            </a>
                                            <a href="{{ route('admin.calendario.create') }}" class="btn btn-outline-primary btn-sm">
                                                📅 Criar Calendário
                                            </a>
                                            <a href="{{ route('admin.calendario.index') }}" class="btn btn-outline-success btn-sm">
                                                🎆 Gerenciar Calendários
                                            </a>
                                            <button class="btn btn-outline-info btn-sm" disabled>
                                                📈 Relatórios (Em breve)
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                                ⚙️ Configurações (Em breve)
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção Calendário Escolar -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-2" style="border-color: #55B4F8;">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold text-custom-blue">
                                            📅 Gerenciamento do Calendário Escolar
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 mb-2">
                                                <button class="btn btn-outline-primary btn-sm w-100" disabled>
                                                    📅 Criar Evento
                                                </button>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <button class="btn btn-outline-success btn-sm w-100" disabled>
                                                    🎆 Feriados
                                                </button>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <button class="btn btn-outline-warning btn-sm w-100" disabled>
                                                    📚 Períodos Letivos
                                                </button>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <button class="btn btn-outline-info btn-sm w-100" disabled>
                                                    📈 Visualizar Calendário
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                ⚠️ Funcionalidades do calendário escolar estarão disponíveis em breve.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>