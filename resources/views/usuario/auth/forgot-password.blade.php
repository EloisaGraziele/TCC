<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Sistema de Presença</title>
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
                        <h2 class="mb-0 fw-bold">Recuperar Senha</h2>
                        <p class="mb-0">Digite seu email para receber o código</p>
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

                        <form method="POST" action="{{ route('password.send.code') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input id="email" type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" required>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('login') }}" class="btn fw-bold text-dark" style="background-color: #ffc107; border: none; width: 48%; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                    Voltar ao Login
                                </a>

                                <button type="submit" class="btn fw-bold text-dark" style="background-color: #63FF63; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Enviar Código
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>