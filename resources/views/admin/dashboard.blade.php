<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sistema de Presença</title>
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
            <button type="submit" class="btn btn-danger">Sair</button>
        </form>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-header bg-custom-blue text-white text-center py-3">
                        <h2 class="mb-0 fw-bold">Painel Administrativo</h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-2" style="border-color: #55B4F8;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-custom-blue fw-bold">Usuários</h5>
                                        <p class="card-text">Visualizar usuários e seus alunos vinculados</p>
                                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-primary" style="background-color: #55B4F8;">
                                            Visualizar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-2" style="border-color: #63FF63;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title fw-bold" style="color: #63FF63;">Secretaria</h5>
                                        <p class="card-text">Cadastrar e gerenciar secretárias</p>
                                        <a href="{{ route('admin.secretaria.index') }}" class="btn btn-primary" style="background-color: #63FF63; color: #333;">
                                            Gerenciar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-2" style="border-color: #ffc107;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title fw-bold text-warning">Calendário</h5>
                                        <p class="card-text">Gerenciar calendário escolar</p>
                                        <a href="{{ route('admin.calendario.index') }}" class="btn btn-warning text-dark">
                                            Acessar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>