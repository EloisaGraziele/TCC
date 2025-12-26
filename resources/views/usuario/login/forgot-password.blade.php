<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Sistema de Presença</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .bg-custom-green { background-color: #63FF63; }
        .text-custom-green { color: #63FF63; }
        .bg-custom-blue { background-color: #55B4F8; }
        .text-custom-blue-dark { color: #55B4F8; }
        
        .full-split {
            height: 100vh;
        }
        
        .admin-button {
            background-color: #55B4F8 !important;
            border-color: #55B4F8 !important;
        }
        body {
            background-color: #ecf2f7ff !important;
        }
    </style>
</head>
<body>

    <div class="position-absolute w-100 bg-custom-green p-3 d-flex justify-content-end align-items-center" style="height: 50px;">
        <a href="{{ route('login') }}" class="btn btn-primary admin-button text-white font-weight-bold px-3 py-1 me-2">
            Voltar ao Login
        </a>
        <a href="{{ route('secretaria.login') }}" class="btn btn-primary admin-button text-white font-weight-bold px-3 py-1 me-2">
            Secretaria
        </a>
        <a href="{{ route('admin.login') }}" class="btn btn-warning text-dark font-weight-bold px-3 py-1 me-4">
            Admin
        </a>
    </div>
    
    <div class="container-fluid full-split d-flex p-0">
        <div class="row w-100 m-0">
            
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center text-center">
                <h1 class="fw-bold mb-0" style="font-size: 6rem; line-height: 1.1;">
                    <span class="text-custom-blue-dark">Sistema</span><br>
                    <span class="text-custom-green">de Presença</span>
                </h1>
            </div>

            <div class="col-md-6 d-flex justify-content-center align-items-center">
                
                <div class="bg-custom-blue shadow-lg rounded p-5" style="width: 500px; max-width: 95%;">
                    <h2 class="h4 fw-bold text-center text-white mb-4">Recuperar Senha</h2>

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="alert alert-info text-dark">
                        <strong>Instruções:</strong><br>
                        Digite seu e-mail cadastrado e clique em "Enviar Código". 
                        Você receberá um código de 6 dígitos que expira em 15 minutos.
                    </div>

                    <form method="POST" action="/send-reset-code">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="email" class="form-label text-white">E-mail</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="form-control" placeholder="Digite seu e-mail cadastrado" required autofocus>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-lg fw-bold py-2" style="background-color: #63FF63; border-color: #63FF63; color: white;">
                                Enviar Código
                            </button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="text-white small fw-bold">
                                Voltar ao Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>