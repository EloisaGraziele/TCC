<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Calendário - Admin</title>
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
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-header bg-custom-blue text-white text-center py-3">
                        <h2 class="mb-0 fw-bold">📅 Criar Calendário Escolar</h2>
                        <p class="mb-0">Gerar calendário base para um ano</p>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success text-center rounded-3 mb-4 fw-bold">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <h6 class="fw-bold">ℹ️ Como funciona:</h6>
                            <ul class="mb-0 small">
                                <li>O sistema criará automaticamente todos os dias do ano selecionado</li>
                                <li>Todos os dias serão marcados como "letivos" inicialmente</li>
                                <li>Após criar, você poderá adicionar feriados e eventos especiais</li>
                            </ul>
                        </div>

                        <form method="POST" action="{{ route('admin.calendario.store') }}">
                            @csrf

                            <!-- Ano -->
                            <div class="mb-4">
                                <label for="ano" class="form-label fw-bold">Selecione o Ano</label>
                                <select id="ano" name="ano" class="form-select form-control-lg rounded-3 @error('ano') is-invalid @enderror" required>
                                    <option value="" disabled selected>Escolha o ano</option>
                                    @for($i = 2024; $i <= 2030; $i++)
                                        <option value="{{ $i }}" {{ old('ano') == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('ano')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <!-- Botões -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('admin.index') }}" 
                                   class="btn fw-bold text-dark"
                                   style="background-color: #ffc107; border: none; width: 48%; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                    Cancelar
                                </a>

                                <button type="submit" 
                                        class="btn fw-bold text-dark"
                                        style="background-color: #63FF63; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Gerar Calendário
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>