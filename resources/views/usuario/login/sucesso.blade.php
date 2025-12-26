<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Realizado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card { border-radius: 20px; }
        .btn-custom-blue { background-color: #55B4F8; border-color: #55B4F8; color: white; }
        .success-icon { font-size: 4rem; color: #28a745; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header text-center py-4" style="background-color: #28a745; color: white; border-radius: 20px 20px 0 0;">
                        <h2 class="mb-0 fw-bold">Cadastro Realizado!</h2>
                    </div>
                    <div class="card-body p-5 text-center">
                        <div class="success-icon mb-4">
                            ✓
                        </div>
                        
                        <h4 class="mb-4 fw-bold text-success">Parabéns!</h4>
                        
                        <p class="mb-4 text-muted">
                            Seu cadastro foi realizado com sucesso! Agora você pode fazer login no sistema 
                            para acompanhar a frequência dos seus alunos.
                        </p>

                        @if(session('alunos_vinculados'))
                            <div class="alert alert-info">
                                <strong>Alunos vinculados:</strong>
                                <ul class="list-unstyled mb-0 mt-2">
                                    @foreach(session('alunos_vinculados') as $aluno)
                                        <li>• {{ $aluno }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('login') }}" class="btn btn-custom-blue fw-bold px-5 py-3 rounded-3">
                                Fazer Login
                            </a>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                Você será redirecionado para a página de login onde poderá acessar o sistema.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto redirect após 10 segundos -->
    <script>
        setTimeout(function() {
            window.location.href = "{{ route('login') }}";
        }, 10000);
    </script>
</body>
</html>