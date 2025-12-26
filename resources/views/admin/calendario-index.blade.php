<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendários - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-custom-green { background-color: #63FF63; }
        .bg-custom-blue { background-color: #55B4F8; }
        body { background-color: #ecf2f7ff; }
    </style>
</head>
<body>
    <!-- Topo Verde -->
    <div class="bg-custom-green p-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold text-dark">Sistema de Presença - ADMIN</h4>
        <div>
            <a href="{{ route('admin.index') }}" class="btn btn-primary" style="background-color: #55B4F8;">
                Voltar ao Painel
            </a>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-header bg-custom-blue text-white text-center py-3">
                        <h2 class="mb-0 fw-bold">📅 Gerenciar Calendários</h2>
                        <p class="mb-0">Visualizar e editar calendários escolares</p>
                    </div>
                    <div class="card-body p-4">
                        @if($calendarios->count() > 0)
                            <div class="row">
                                @foreach($calendarios as $calendario)
                                    @php $ano = $calendario->ano; @endphp
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card border-2" style="border-color: #55B4F8;">
                                            <div class="card-header bg-light text-center">
                                                <h5 class="mb-0 fw-bold" style="color: #55B4F8;">📅 {{ $ano }}</h5>
                                            </div>
                                            <div class="card-body text-center">
                                                <p class="card-text mb-3">Calendário Escolar {{ $ano }}</p>
                                                
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('admin.calendario.show', $ano) }}" 
                                                       class="btn btn-success" 
                                                       style="background-color: #28a745;">
                                                        👁️ Ver Calendário
                                                    </a>
                                                    <a href="{{ route('admin.calendario.edit', $ano) }}" 
                                                       class="btn btn-primary" 
                                                       style="background-color: #55B4F8;">
                                                        ✏️ Editar Calendário
                                                    </a>
                                                    
                                                    <!-- Botão Status -->
                                                    <div class="dropdown">
                                                        <button class="btn {{ $calendario->ativo ? 'btn-success' : 'btn-secondary' }} dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                                            {{ $calendario->ativo ? '✅ Ativo' : '❌ Inativo' }}
                                                        </button>
                                                        <ul class="dropdown-menu w-100">
                                                            @if(!$calendario->ativo)
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.calendario.status', $ano) }}" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="status" value="1">
                                                                        <button type="submit" class="dropdown-item">
                                                                            ✅ Ativar
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.calendario.status', $ano) }}" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="status" value="0">
                                                                        <button type="submit" class="dropdown-item">
                                                                            ❌ Desativar
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                    
                                                    <form method="POST" action="{{ route('admin.calendario.destroy', $ano) }}" 
                                                          onsubmit="return confirm('Tem certeza que deseja deletar o calendário de {{ $ano }}? Esta ação não pode ser desfeita!')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger w-100">
                                                            🗑️ Deletar Calendário
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center">
                                <div class="alert alert-info">
                                    <h5 class="fw-bold">📅 Nenhum calendário encontrado</h5>
                                    <p class="mb-3">Você ainda não criou nenhum calendário escolar.</p>
                                    <a href="{{ route('admin.calendario.create') }}" class="btn btn-success">
                                        Criar Primeiro Calendário
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="text-center mt-4">
                            <a href="{{ route('admin.calendario.create') }}" class="btn btn-success btn-lg">
                                ➕ Criar Novo Calendário
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>