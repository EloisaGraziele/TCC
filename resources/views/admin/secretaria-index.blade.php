<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Secretárias - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-custom-green { background-color: #63FF63; }
        .bg-custom-blue { background-color: #55B4F8; }
        body { background-color: #ecf2f7ff; }
    </style>
</head>
<body>
    <!-- Topo Verde -->
    <div class="bg-custom-green p-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold text-dark">Sistema de Presença - ADMIN</h4>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="background-color: #55B4F8;">
                Voltar ao Painel
            </a>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Formulário de Cadastro -->
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-custom-blue text-white text-center py-3">
                        <h2 class="mb-0 fw-bold">Cadastrar Secretária</h2>
                        <p class="mb-0">Adicionar funcionário da secretaria</p>
                    </div>
                    <div class="card-body p-4 bg-custom-blue">
                        @if (session('success'))
                            <div class="alert alert-success text-center mb-4 fw-bold">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->has('password_confirmation'))
                            <div class="alert alert-danger text-center mb-4 fw-bold">
                                {{ $errors->first('password_confirmation') }}
                            </div>
                        @endif

                        @if ($errors->any() && !session('editing_secretaria_id'))
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    @if($error !== 'A confirmação da senha não confere.')
                                        <div>{{ $error }}</div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.secretaria.store') }}" autocomplete="off">
                            @csrf

                            <!-- Nome -->
                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold text-white">Nome Completo</label>
                                <input id="name" type="text" name="name"
                                       class="form-control form-control-lg @error('name') is-invalid @enderror"
                                       autocomplete="off" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold text-white">Email</label>
                                <input id="email" type="email" name="email"
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       autocomplete="off" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Senha -->
                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold text-white">Senha</label>
                                <input id="password" type="password" name="password"
                                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                                       autocomplete="new-password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirmar Senha -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-bold text-white">Confirmar Senha</label>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                       class="form-control form-control-lg" autocomplete="new-password" required>
                            </div>

                            <!-- Botões -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="reset" 
                                        class="btn fw-bold text-dark"
                                        style="background-color: #ffc107; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Limpar Formulário
                                </button>

                                <button type="submit" 
                                        class="btn fw-bold text-dark"
                                        style="background-color: #63FF63; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Cadastrar Secretária
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de Secretárias Cadastradas -->
                <div class="card border-0 shadow-lg mt-4">
                    <div class="card-header bg-custom-blue text-white text-center py-3">
                        <h3 class="mb-0 fw-bold">Secretárias Cadastradas</h3>
                    </div>
                    <div class="card-body p-4">
                        @if($secretarias->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Data Cadastro</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($secretarias as $secretaria)
                                            <tr>
                                                <td class="fw-bold">{{ $secretaria->name }}</td>
                                                <td>{{ $secretaria->email }}</td>
                                                <td>{{ $secretaria->created_at->format('d/m/Y H:i') }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm me-2" style="background-color: #ffc107; color: black;" data-bs-toggle="modal" data-bs-target="#editModal{{ $secretaria->id }}">
                                                        Editar
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.secretaria.destroy', $secretaria->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm" style="background-color: #63FF63; color: black;" onclick="return confirm('Tem certeza que deseja excluir?')">
                                                            Excluir
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <h5 class="text-muted">Nenhuma secretária cadastrada</h5>
                                <p class="text-muted">Use o formulário acima para cadastrar a primeira secretária</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modais de Edição -->
    @foreach($secretarias as $secretaria)
        <div class="modal fade" id="editModal{{ $secretaria->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-custom-blue text-white">
                        <h5 class="modal-title">Editar Secretária</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.secretaria.update', $secretaria->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            @if ($errors->any() && session('editing_secretaria_id') == $secretaria->id)
                                <div class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="edit_name{{ $secretaria->id }}" class="form-label fw-bold">Nome</label>
                                <input type="text" name="name" id="edit_name{{ $secretaria->id }}" class="form-control" value="{{ old('name', $secretaria->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_email{{ $secretaria->id }}" class="form-label fw-bold">Email</label>
                                <input type="email" name="email" id="edit_email{{ $secretaria->id }}" class="form-control" value="{{ old('email', $secretaria->email) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_password{{ $secretaria->id }}" class="form-label fw-bold">Nova Senha (deixe em branco para manter)</label>
                                <input type="password" name="password" id="edit_password{{ $secretaria->id }}" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="edit_password_confirmation{{ $secretaria->id }}" class="form-label fw-bold">Confirmar Nova Senha</label>
                                <input type="password" name="password_confirmation" id="edit_password_confirmation{{ $secretaria->id }}" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn" style="background-color: #ffc107; color: black;" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn" style="background-color: #63FF63; color: black;">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @if(session('editing_secretaria_id'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('editModal{{ session('editing_secretaria_id') }}'));
                modal.show();
            });
        </script>
    @endif
</body>
</html>