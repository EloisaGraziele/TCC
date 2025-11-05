<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Secretaria - Admin</title>
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
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-header bg-custom-blue text-white text-center py-3">
                        <h2 class="mb-0 fw-bold">Cadastrar Secretaria</h2>
                        <p class="mb-0">Adicionar funcionário da secretaria</p>
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

                        <form method="POST" action="{{ route('admin.secretaria.store') }}">
                            @csrf

                            <!-- Nome -->
                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold">Nome Completo</label>
                                <input id="name" type="text" name="name"
                                       class="form-control form-control-lg rounded-3 @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input id="email" type="email" name="email"
                                       class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Senha -->
                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">Senha</label>
                                <input id="password" type="password" name="password"
                                       class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirmar Senha -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-bold">Confirmar Senha</label>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                       class="form-control form-control-lg rounded-3" required>
                            </div>

                            <!-- Botões -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="reset" 
                                        class="btn fw-bold text-dark"
                                        style="background-color: #ffc107; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Limpar Formulário
                                </button>

                                <button type="submit" 
                                        class="btn fw-bold text-dark"
                                        style="background-color: #63FF63; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Cadastrar Secretaria
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