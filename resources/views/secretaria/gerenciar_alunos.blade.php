@extends('layouts.admin')

@section('content')
<!-- Título do Sistema -->
<div class="mb-3" style="margin-left: 20px; padding-left: 0;">
    <h2 class="fw-bold">
        <span style="color: #55B4F8;">Sistema de</span> 
        <span style="color: #63FF63;">Presença</span>
    </h2>
</div>

<div class="container py-5">
    @if (session('success'))
        <div class="alert alert-success text-center fw-bold rounded-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="h3 mb-4 fw-bold text-custom-blue">Gerenciar Alunos</h1>

    <!-- Formulário de Pesquisa -->
    <div class="card mb-4">
        <div class="card-header bg-custom-blue text-white">
            <h5 class="mb-0">Pesquisar Alunos</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('secretaria.aluno.gerenciar') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome do Aluno</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite o nome" 
                           class="form-control" value="{{ request('nome') }}">
                </div>
                <div class="col-md-6">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" 
                           class="form-control" value="{{ request('cpf') }}">
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-custom-blue me-2">
                        <i class="fas fa-search me-1"></i>Pesquisar
                    </button>
                    <a href="{{ route('secretaria.aluno.gerenciar') }}" class="btn" style="background-color: #ffc107; color: black;">
                        <i class="fas fa-eraser me-1"></i>Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Alunos -->
    <div class="card">
        <div class="card-header bg-custom-green text-dark">
            <h5 class="mb-0">Alunos Encontrados</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Matrícula</th>
                            <th>Turma</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alunos as $aluno)
                            <tr>
                                <td>{{ $aluno->nome }}</td>
                                <td>{{ $aluno->cpf }}</td>
                                <td>{{ $aluno->matricula }}</td>
                                <td>{{ $aluno->turma->turma ?? 'Sem turma' }} ({{ $aluno->turma->ano ?? '' }})</td>
                                <td>
                                    <span class="badge {{ $aluno->status == 'ativo' ? 'bg-success' : ($aluno->status == 'inativo' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ ucfirst($aluno->status) }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editModal" 
                                            onclick="editAluno({{ $aluno->id }}, '{{ $aluno->nome }}', '{{ $aluno->cpf }}', '{{ $aluno->matricula }}', '{{ $aluno->turma_id }}', '{{ $aluno->status }}', '{{ $aluno->data_nascimento }}')">
                                        Editar
                                    </button>
                                    <a href="{{ route('secretaria.aluno.cracha', $aluno) }}" class="btn btn-sm btn-info me-1" target="_blank">
                                        Crachá
                                    </a>
                                    <form method="POST" action="{{ route('secretaria.aluno.regenerar-qr', $aluno) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1" onclick="return confirm('Regenerar QR Code?')">
                                            Novo QR
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#statusModal" 
                                            onclick="changeStatus({{ $aluno->id }}, '{{ $aluno->status }}')">
                                        Status
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted p-4">
                                    @if(request()->hasAny(['nome', 'cpf']))
                                        Nenhum aluno encontrado para os filtros selecionados.
                                    @else
                                        Utilize os filtros acima para pesquisar alunos.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-custom-blue text-white">
                <h5 class="modal-title">Editar Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_nome" class="form-label">Nome</label>
                            <input type="text" name="nome" id="edit_nome" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_cpf" class="form-label">CPF</label>
                            <input type="text" name="cpf" id="edit_cpf" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_matricula" class="form-label">Matrícula</label>
                            <input type="text" name="matricula" id="edit_matricula" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_turma_id" class="form-label">Turma</label>
                            <select name="turma_id" id="edit_turma_id" class="form-select" required>
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}">{{ $turma->turma }} ({{ $turma->ano }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" name="data_nascimento" id="edit_data_nascimento" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Status -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Alterar Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status_select" class="form-label">Novo Status</label>
                        <select name="status" id="status_select" class="form-select" required>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Alterar Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAluno(id, nome, cpf, matricula, turmaId, status, dataNascimento) {
    document.getElementById('editForm').action = `/secretaria/alunos/${id}`;
    document.getElementById('edit_nome').value = nome;
    document.getElementById('edit_cpf').value = cpf;
    document.getElementById('edit_matricula').value = matricula;
    document.getElementById('edit_turma_id').value = turmaId;
    
    // Formatar data para o formato YYYY-MM-DD
    if (dataNascimento) {
        const data = new Date(dataNascimento);
        const dataFormatada = data.toISOString().split('T')[0];
        document.getElementById('edit_data_nascimento').value = dataFormatada;
    }
}

function changeStatus(id, currentStatus) {
    document.getElementById('statusForm').action = `/secretaria/alunos/${id}/status`;
    document.getElementById('status_select').value = currentStatus;
}
</script>

@endsection