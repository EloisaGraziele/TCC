<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Aluno - Admin</title>
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
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-header bg-custom-blue text-white text-center py-3">
                        <h2 class="mb-0 fw-bold">Cadastrar Aluno</h2>
                        <p class="mb-0">Adicionar novo aluno ao sistema</p>
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

                        <form method="POST" action="{{ route('admin.aluno.store') }}">
                            @csrf

                            <!-- Nome -->
                            <div class="mb-4">
                                <label for="nome" class="form-label fw-bold">Nome Completo</label>
                                <input id="nome" type="text" name="nome"
                                       class="form-control form-control-lg rounded-3 @error('nome') is-invalid @enderror"
                                       value="{{ old('nome') }}" required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- CPF e Matrícula -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="cpf" class="form-label fw-bold">CPF</label>
                                    <input id="cpf" type="text" name="cpf" maxlength="14"
                                           class="form-control form-control-lg rounded-3 @error('cpf') is-invalid @enderror"
                                           value="{{ old('cpf') }}" placeholder="000.000.000-00" required>
                                    @error('cpf')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="matricula" class="form-label fw-bold">Matrícula</label>
                                    <input id="matricula" type="text" name="matricula"
                                           class="form-control form-control-lg rounded-3 @error('matricula') is-invalid @enderror"
                                           value="{{ old('matricula') }}" required>
                                    @error('matricula')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Data de Nascimento e Turma -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="data_nascimento" class="form-label fw-bold">Data de Nascimento</label>
                                    <input id="data_nascimento" type="date" name="data_nascimento"
                                           class="form-control form-control-lg rounded-3 @error('data_nascimento') is-invalid @enderror"
                                           value="{{ old('data_nascimento') }}" required>
                                    @error('data_nascimento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="turma_id" class="form-label fw-bold">Turma</label>
                                    <select id="turma_id" name="turma_id" 
                                            class="form-select form-control-lg rounded-3 @error('turma_id') is-invalid @enderror"
                                            required>
                                        <option value="" disabled selected>Selecione a turma</option>
                                        @foreach($turmas as $turma)
                                            <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                                                {{ $turma->turma }} ({{ $turma->ano }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('turma_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" 
                                        class="btn fw-bold text-dark"
                                        style="background-color: #63FF63; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Cadastrar Aluno
                                </button>

                                <button type="reset" 
                                        class="btn fw-bold text-dark"
                                        style="background-color: #ffc107; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Limpar Formulário
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