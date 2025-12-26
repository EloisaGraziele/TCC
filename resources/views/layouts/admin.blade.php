<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Administração')</title>
    
    <!-- Incluindo Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Google Font: Poppins (Semelhante à fonte da imagem) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome para ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

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
            <h1 class="h4 fw-bold mb-0 text-white">Secretaria</h1> <!-- Alterado para text-white -->
            
            <div class="d-flex align-items-center gap-3">
                @if ($secretaria)
                    <span class="text-dark me-2 d-none d-sm-block">Logado como: <strong class="fw-bolder">{{ $secretaria->name ?? 'Secretária' }}</strong></span>
                @endif
                
                <!-- Botões de Ação (Usando o Azul personalizado com texto branco) -->
                @php
                    use App\Models\Notificacao;
                    $unreadCount = 0;
                    try {
                        $unreadCount = Notificacao::where('destinatario_tipo', 'secretaria')->where('lida', false)->count();
                    } catch (\Exception $e) {
                        $unreadCount = 0;
                    }
                @endphp
                                <button class="btn btn-custom-blue me-2 d-flex align-items-center px-2 py-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#notificacoesPanel" aria-controls="notificacoesPanel" id="notificacoesBtn">
                                        <i class="fas fa-bell me-1"></i>Notificações (<span id="notificacoes-count">{{ $unreadCount }}</span>)
                                </button>

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
                                        <div class="p-3 border-top bg-light">
                                            <a href="{{ route('secretaria.notificacoes.index') }}" class="btn btn-custom-blue btn-sm w-100">
                                                <i class="fas fa-list me-1"></i>Ver todas as notificações
                                            </a>
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
                
                <!-- Botão Perfil Adicionado -->
                <button class="btn btn-custom-blue me-2 d-flex align-items-center px-2 py-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#perfilPanel" aria-controls="perfilPanel">
                    <i class="fas fa-user-circle me-1"></i>Perfil
                </button>

                <!-- Botão Sair/Logout (Alterado para custom-blue) -->
                <a href="{{ route('secretaria.logout') }}" class="btn btn-danger d-flex align-items-center px-2 py-1"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-1"></i>Sair
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
                    <a href="{{ route('secretaria.calendario.index') }}" class="nav-link text-white p-0">Calendário</a>
                    <a href="{{ route('secretaria.alertas.index') }}" class="nav-link text-white p-0">Configuração de Alertas</a>
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
    <script>
    (function(){
        const panel = document.getElementById('notificacoesPanel');
        if (!panel) return;

        const loadNotifications = () => {
            const list = document.getElementById('notificacoes-list');
            const loading = document.getElementById('notificacoes-loading');
            fetch("{{ route('secretaria.notificacoes.latest') }}", {
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
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 fw-bold">${n.aluno_nome || 'Aluno'}</h6>
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

                // Attach events to mark-as-read
                document.querySelectorAll('.marcar-lida-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const id = e.currentTarget.getAttribute('data-id');
                        fetch(`/secretaria/notificacoes/${id}/marcar-lida`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        }).then(res => {
                            if (res.ok) {
                                // reload panel
                                loadNotifications();
                                // update unread count
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
            fetch("{{ route('secretaria.notificacoes.latest') }}", { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                .then(r => r.json()).then(data => {
                    const unread = data.filter(n => !n.lida).length;
                    const el = document.getElementById('notificacoes-count');
                    if (el) el.innerText = unread;
                }).catch(()=>{});
        };

        // Load when panel shown
        panel.addEventListener('show.bs.offcanvas', loadNotifications);
        // Update count periodically
        setInterval(updateCount, 30000);
    })();

    // Perfil Panel Logic
    (function(){
        const perfilPanel = document.getElementById('perfilPanel');
        if (!perfilPanel) return;

        const loadPerfil = () => {
            const content = document.getElementById('perfil-content');
            const loading = document.getElementById('perfil-loading');
            
            fetch("{{ route('secretaria.perfil.show') }}", {
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

                // Handle form submission
                const form = document.getElementById('perfil-form');
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    const data = Object.fromEntries(formData);
                    
                    // Remove empty password fields
                    if (!data.password) {
                        delete data.password;
                        delete data.password_confirmation;
                    }
                    
                    fetch("{{ route('secretaria.perfil.update') }}", {
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
                            // Update name in header if changed
                            const nameSpan = document.querySelector('span.text-dark strong');
                            if (nameSpan && data.name) {
                                nameSpan.textContent = data.name;
                            }
                            // Clear password fields
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

        // Load when panel shown
        perfilPanel.addEventListener('show.bs.offcanvas', loadPerfil);
    })();
    </script>
</body>
</html>