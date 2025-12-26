<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Presença - Login</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Cores personalizadas com !important */
        .bg-custom-green { background-color: #63FF63 !important; } /* Verde-Limão Forte */
        .text-custom-green { color: #63FF63 !important; }
        .bg-custom-blue { background-color: #55B4F8 !important; } /* Azul do Card */
        .text-custom-blue { color: #55B4F8 !important; } /* Azul do Título */
        
        /* Ajuste para cobrir a tela inteira, garantindo centralização */
        .full-split {
            min-height: 100vh; 
            padding-top: 50px; /* Adiciona espaço para não cobrir o cabeçalho */
        }
        
        /* CORREÇÃO DO BOTÃO: Fundo Azul Customizado e Texto Branco */
        .admin-button {
            background-color: #55B4F8 !important;  /* Azul Customizado */
            border-color: #55B4F8 !important;     /* Borda Azul */
            color: white !important;               /* Texto Branco */
            font-weight: bold;
        }

        /* Botão de login com fundo verde */
        .login-button {
            background-color: #63FF63 !important;
            border-color: #63FF63 !important;
            color: white !important;
            font-weight: bold;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
</head>
<body class="bg-light"> 

    <!-- CABEÇALHO TOP BAR (Verde Limão) -->
    <div class="position-absolute w-100 bg-custom-green p-3 d-flex justify-content-end align-items-center" style="height: 50px; z-index: 100;">
        <!-- BOTÃO LOGIN USUÁRIO -->
        <a href="{{ route('login') }}" class="btn admin-button px-3 py-1 me-2">
            Login Usuário
        </a>
        <!-- BOTÃO ADMIN -->
        <a href="{{ route('admin.login') }}" class="btn btn-warning text-dark px-3 py-1 me-4">
            Admin
        </a>
    </div>
    
    <!-- CORPO PRINCIPAL: CONTEÚDO CENTRALIZADO -->
    <div class="container-fluid full-split d-flex flex-column justify-content-center align-items-center p-4">
        
        <!-- TÍTULO: Sistema de Presença -->
        <div class="text-center mb-5 mt-5">
            <h1 class="display-1 fw-bold mb-0">
                <span class="text-custom-blue">Sistema de</span>  <!-- Texto azul com a cor customizada -->
                <span class="text-custom-green">Presença</span>  <!-- Texto verde -->
            </h1>
        </div>

        <!-- CARD DE LOGIN (Azul) -->
        <div class="bg-custom-blue shadow-lg rounded p-5" style="width: 400px; max-width: 90%;">
            <h2 class="h2 fw-bold text-center text-white mb-4">Secretaria</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('secretaria.login.submit') }}">
                @csrf
                
                <!-- Identificação (Email) -->
                <div class="mb-3">
                    <label for="email" class="form-label text-white">Identificação</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="form-control" placeholder="Digite sua Identificação" required autofocus>
                </div>

                <!-- Senha -->
                <div class="mb-3">
                    <label for="password" class="form-label text-white">Senha</label>
                    <input type="password" id="password" name="password"
                        class="form-control" placeholder="Digite sua senha" required>
                </div>

                <!-- Botão Login -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-lg login-button py-2">
                        Acessar
                    </button>
                </div>
                

            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
