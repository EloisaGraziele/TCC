<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Sistema de Presença</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-custom-green { background-color: #63FF63; }
        .bg-custom-blue { background-color: #55B4F8; }
        body { background-color: #ecf2f7ff; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="bg-custom-blue shadow-lg rounded p-4">
                    <h2 class="h4 fw-bold text-center text-white mb-4">ADMIN LOGIN</h2>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.submit') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label text-white fw-bold">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="form-control form-control-lg rounded-3" required>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label text-white fw-bold">Senha</label>
                            <input type="password" id="password" name="password"
                                class="form-control form-control-lg rounded-3" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn fw-bold text-dark"
                                    style="background-color: #63FF63; border: none; height: 50px; border-radius: 12px;">
                                Entrar
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-white small">Voltar ao Login Normal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>