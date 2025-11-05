<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Presença</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-custom-green { background-color: #63FF63; }
        .bg-custom-blue { background-color: #55B4F8; }
        .text-custom-blue { color: #55B4F8; }
        .text-custom-green { color: #63FF63; }
        body { background-color: #ecf2f7ff; }
    </style>
</head>
<body>
    <!-- Topo Verde -->
    <div class="bg-custom-green p-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold text-dark">Sistema de Presença</h4>
        <div>
            <span class="me-3 fw-bold">Olá, {{ $user->name }}!</span>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Sair</button>
            </form>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-header bg-custom-blue text-white text-center py-3">
                        <h2 class="mb-0 fw-bold">Meus Alunos</h2>
                    </div>
                    <div class="card-body p-4">
                        @if($alunos->count() > 0)
                            <div class="row">
                                @foreach($alunos as $aluno)
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100 border-2" style="border-color: #55B4F8;">
                                            <div class="card-body text-center">
                                                <h5 class="card-title text-custom-blue fw-bold">{{ $aluno->nome }}</h5>
                                                <p class="card-text">
                                                    <strong>Matrícula:</strong> {{ $aluno->matricula }}<br>
                                                    <strong>Turma:</strong> {{ $aluno->turma }} ({{ $aluno->ano }})<br>
                                                    <strong>Status:</strong> 
                                                    <span class="badge {{ $aluno->status === 'ativo' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ ucfirst($aluno->status) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <h4 class="text-muted">Nenhum aluno vinculado</h4>
                                <p class="text-muted">Você ainda não possui alunos vinculados ao seu cadastro.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>