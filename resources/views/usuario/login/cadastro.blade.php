<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Presença - Cadastro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .bg-custom-green { background-color: #63FF63; }
        .text-custom-green { color: #63FF63; }
        .bg-custom-blue { background-color: #55B4F8; }
        .text-custom-blue-dark { color: #55B4F8; }
        
        body {
            background-color: #ecf2f7ff !important;
        }
    </style>
</head>
<body>
    
    <!-- Topo Verde -->
    <div class="position-fixed w-100 bg-custom-green p-3 d-flex justify-content-end align-items-center" style="height: 60px; z-index: 1000;">
        <a href="{{ route('login') }}" class="btn btn-primary text-white fw-bold px-3 py-2" style="background-color: #55B4F8; border-color: #55B4F8;">
            Login
        </a>
    </div>
    
    <!-- Título Sistema de Presença -->
    <div class="text-start" style="padding-top: 80px; margin-bottom: 30px; padding-left: 50px;">
        <h1 class="display-4 fw-bold mb-0">
            <span class="text-custom-blue-dark">Sistema de</span>
            <span class="text-custom-green">Presença</span>
        </h1>
    </div>
    
    <!-- Conteúdo Principal -->
    <div class="container d-flex justify-content-center align-items-center">
        <div class="bg-custom-blue shadow-lg rounded p-4" style="width: 550px;">
            <h2 class="h4 fw-bold text-center text-white mb-4">CADASTRO - ETAPA 1 DE 2</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.etapa1') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label text-white fw-bold">Nome Completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="form-control form-control-lg rounded-3" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label text-white fw-bold">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="form-control form-control-lg rounded-3" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cpf" class="form-label text-white fw-bold">CPF</label>
                        <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}"
                            class="form-control form-control-lg rounded-3" placeholder="000.000.000-00" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefone" class="form-label text-white fw-bold">Telefone</label>
                        <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}"
                            class="form-control form-control-lg rounded-3" placeholder="(11) 99999-9999" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label text-white fw-bold">Senha</label>
                    <input type="password" id="password" name="password"
                        class="form-control form-control-lg rounded-3" required>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label text-white fw-bold">Confirmar Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="form-control form-control-lg rounded-3" required>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="reset" class="btn fw-bold text-dark"
                            style="background-color: #ffc107; border: none; width: 48%; height: 50px; border-radius: 12px;">
                        Limpar
                    </button>
                    <button type="submit" class="btn fw-bold text-dark"
                            style="background-color: #63FF63; border: none; width: 48%; height: 50px; border-radius: 12px;">
                        Continuar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>