<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - Administração')</title>
    
    <!-- Incluindo Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Google Font: Poppins (Semelhante à fonte da imagem) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Personalizado com suas cores e fonte -->
    <style>
        :root {
            /* Definindo suas variáveis de cor */
            --custom-blue: #55B4F8; /* Azul primário */
            --custom-green: #63FF63; /* Verde primário */
            --body-text: #343a40; /* Cor padrão para textos não-título */
        }

        /* Aplica a fonte Poppins ao corpo e garante que títulos usem a mesma fonte */
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Cores de Fundo */
        .bg-custom-green { background-color: var(--custom-green) !important; }
        .bg-custom-blue { background-color: var(--custom-blue) !important; }

        /* Cores de Texto */
        .text-custom-green { color: var(--custom-green) !important; }
        .text-custom-blue { color: var(--custom-blue) !important; }

        /* Estilo dos Botões de Ação Principal (Azul) */
        .btn-custom-blue { 
            background-color: var(--custom-blue); 
            border-color: var(--custom-blue); 
            color: white !important; /* Texto branco (solicitado pelo usuário) */
            font-weight: 600;
        }
        .btn-custom-blue:hover { 
            background-color: #40A4E5; /* Um azul um pouco mais escuro no hover */
            border-color: #40A4E5; 
            color: white !important;
        }
        
        /* Ajuste de Navbar */
        .nav-link.text-white:hover {
            color: #F8F8F8 !important; 
            text-decoration: underline;
        }
        
        /* Deixando títulos mais em negrito, como na imagem */
        .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
            font-weight: 700; 
        }
    </style>
</head>
<!-- Usando classes Bootstrap: bg-light, d-flex flex-column, min-vh-100 para layout vertical -->
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Acesso ao Usuário Logado (Usando o Guard 'secretaria') -->
    @php
        use Illuminate\Support\Facades\Auth;
        $secretaria = Auth::guard('secretaria')->user();
    @endphp

    <header class="w-100 shadow-sm sticky-top">
        <!-- 1. Faixa Verde (Top Bar) - Cor: #63FF63 -->
        <div class="bg-custom-green text-dark d-flex justify-content-between align-items-center p-3">
            <h1 class="h4 fw-bold mb-0 text-white">Administração</h1> <!-- Alterado para text-white -->
            
            <div class="d-flex align-items-center gap-3">
                @if ($secretaria)
                    <span class="text-dark me-2 d-none d-sm-block">Logado como: <strong class="fw-bolder">{{ $secretaria->name ?? 'Secretária' }}</strong></span>
                @endif
                
                <!-- Botões de Ação (Usando o Azul personalizado com texto branco) -->
                <button class="btn btn-custom-blue btn-sm">Notificações (0)</button>
                
                <!-- Botão Perfil Adicionado -->
                <button class="btn btn-custom-blue btn-sm">Perfil</button>

                <!-- Botão Sair/Logout (Alterado para custom-blue) -->
                <a href="{{ route('secretaria.logout') }}" class="btn btn-custom-blue btn-sm"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Sair
                </a>
                <form id="logout-form" action="{{ route('secretaria.logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>

       

        <!-- 3. Barra de Navegação Principal (Faixa Azul) - Cor: #55B4F8 -->
        <div class="bg-custom-blue p-3 shadow-lg">
             <div class="d-flex flex-column flex-md-row justify-content-end align-items-center">
                
                <!-- Links de Navegação -->
                <nav class="d-flex flex-wrap gap-4 fs-6 fw-medium">
                    <a href="{{ route('secretaria.frequencias.index') }}" class="nav-link text-white p-0">Frequência (Turmas)</a>
                    <a href="{{ route('secretaria.aluno.index') }}" class="nav-link text-white p-0">Frequência (Alunos)</a>
                    <a href="{{ route('secretaria.aluno.gerenciar') }}" class="nav-link text-white p-0">Gerenciar Aluno</a>
                    <a href="{{ route('secretaria.gerenciamento.index') }}" class="nav-link text-white p-0">Gerenciamento</a>
                    <a href="{{ route('secretaria.aluno.create') }}" class="nav-link text-white p-0">Cadastrar Aluno</a>
                    <a href="{{ route('secretaria.alertas.create') }}" class="nav-link text-white p-0">Criar Alertas</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Conteúdo da Página -->
    <main class="container-fluid p-4 flex-grow-1">
        @yield('content')

        <!-- Exibição de mensagens de status (Bootstrap Alert) -->
        @if (session('status'))
            <div class="alert alert-success border-start border-5 border-success p-4 mb-4" role="alert">
                <p class="fw-bold">Sucesso!</p>
                <p>{{ session('status') }}</p>
            </div>
        @endif
    </main>
    
    <!-- Footer Padrão -->
    <footer class="bg-dark text-white p-4 mt-auto">
        <div class="container text-center text-sm">
            <p>&copy; {{ date('Y') }} Sistema de Presença. Todos os direitos reservados. | Área da Secretaria.</p>
            <p class="mt-1 text-secondary">Desenvolvido para gerenciamento escolar.</p>
        </div>
    </footer>

    <!-- Incluindo Bootstrap 5 JS Bundle (necessário para alguns componentes como modais) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>