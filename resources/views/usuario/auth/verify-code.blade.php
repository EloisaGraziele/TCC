<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código - Sistema de Presença</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-custom-green { background-color: #63FF63; }
        .bg-custom-blue { background-color: #55B4F8; }
        body { background-color: #ecf2f7ff; }
    </style>
</head>
<body>
    <div class="bg-custom-green p-3 text-center">
        <h4 class="mb-0 fw-bold text-dark">Sistema de Presença</h4>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-custom-blue text-white text-center py-3">
                        <h2 class="mb-0 fw-bold">Verificar Código</h2>
                        <p class="mb-0">Digite o código enviado para seu email</p>
                        <small class="text-dark">🕐 O código expira em 15 minutos</small>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success text-center mb-4 fw-bold">
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

                        <form method="POST" action="/verify-code" id="verifyForm">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email ?? session('email') }}">
                            
                            <div class="mb-4">
                                <label for="code" class="form-label fw-bold">Código de 6 dígitos</label>
                                <input id="code" type="text" name="code" class="form-control form-control-lg text-center" maxlength="6" style="font-size: 24px; letter-spacing: 10px;" required>
                                <small class="text-muted">Verifique sua caixa de entrada e spam</small>
                            </div>

                            <div class="text-start mb-3">
                                <p class="text-muted mb-2">Não recebeu o código?</p>
                                <a href="/forgot-password" class="btn btn-link text-primary fw-bold p-0">
                                    Reenviar código
                                </a>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="/forgot-password" class="btn fw-bold text-dark" style="background-color: #ffc107; border: none; width: 48%; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                    Voltar
                                </a>

                                <button type="submit" class="btn fw-bold text-dark" style="background-color: #63FF63; border: none; width: 48%; height: 60px; border-radius: 12px;" onclick="console.log('Botão clicado'); return true;">
                                    Verificar Código
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('verifyForm').addEventListener('submit', function(e) {
            console.log('Formulário enviado');
            const code = document.getElementById('code').value;
            console.log('Código digitado:', code);
            if (!code || code.length < 6) {
                alert('Digite um código de 6 dígitos');
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>