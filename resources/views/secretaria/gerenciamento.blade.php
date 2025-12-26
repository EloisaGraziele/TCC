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

    <h1 class="h3 mb-5 fw-bold text-custom-blue text-center">Gerenciamento do Sistema</h1>

    <!-- Seção de Turmas -->
    <div class="mb-5">
        <h4 class="mb-3">Gerenciamento de Turmas</h4>
        
        <!-- Formulário de Cadastro -->
        <div class="card mb-4">
            <div class="card-header bg-custom-blue text-white">
                <h5 class="mb-0">Cadastrar Nova Turma</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('secretaria.gerenciamento.turmas.store') }}" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">Ano</label>
                        <input type="text" name="ano" class="form-control" placeholder="Ex: 2024" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Turma</label>
                        <input type="text" name="turma" class="form-control" placeholder="Ex: 1º A" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-custom-blue">Cadastrar Turma</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Turmas -->
        <div class="card">
            <div class="card-header bg-custom-blue text-white">
                <h5 class="mb-0">Turmas Cadastradas</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="border: 1px solid #dee2e6;">
                        <thead style="background-color: #63FF63;">
                            <tr>
                                <th style="border: 1px solid #dee2e6;">Ano</th>
                                <th style="border: 1px solid #dee2e6;">Turma</th>
                                <th style="border: 1px solid #dee2e6;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($turmas as $turma)
                                <tr>
                                    <td style="border: 1px solid #dee2e6;">{{ $turma->ano }}</td>
                                    <td style="border: 1px solid #dee2e6;">{{ $turma->turma }}</td>
                                    <td style="border: 1px solid #dee2e6;">
                                        <button type="button" class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editTurmaModal" 
                                                onclick="editTurma({{ $turma->id }}, '{{ $turma->ano }}', '{{ $turma->turma }}')">Editar</button>
                                        <form method="POST" action="{{ route('secretaria.gerenciamento.turmas.destroy', $turma->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Excluir turma?')">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted p-4">Nenhuma turma cadastrada</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Dispositivos -->
    <div class="mb-5">
        <h4 class="mb-3">Gerenciamento de Dispositivos</h4>
        
        <!-- Formulário de Cadastro -->
        <div class="card mb-4">
            <div class="card-header bg-custom-green text-dark">
                <h5 class="mb-0">Cadastrar Novo Dispositivo</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('secretaria.gerenciamento.dispositivos.store') }}" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">MAC Address</label>
                        <input type="text" name="mac_address" class="form-control" placeholder="Ex: AA:BB:CC:DD:EE:FF" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="autorizado" class="form-select" required>
                            <option value="1">Autorizado</option>
                            <option value="0">Não Autorizado</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">Cadastrar Dispositivo</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Dispositivos -->
        <div class="card">
            <div class="card-header bg-custom-green text-dark">
                <h5 class="mb-0">Dispositivos Cadastrados</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="border: 1px solid #dee2e6;">
                        <thead style="background-color: #63FF63;">
                            <tr>
                                <th style="border: 1px solid #dee2e6;">MAC Address</th>
                                <th style="border: 1px solid #dee2e6;">Status</th>
                                <th style="border: 1px solid #dee2e6;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dispositivos as $dispositivo)
                                <tr>
                                    <td style="border: 1px solid #dee2e6;"><code>{{ $dispositivo->mac_address }}</code></td>
                                    <td style="border: 1px solid #dee2e6;">
                                        <span class="badge {{ $dispositivo->autorizado ? 'bg-success' : 'bg-danger' }}">
                                            {{ $dispositivo->autorizado ? 'Autorizado' : 'Não Autorizado' }}
                                        </span>
                                    </td>
                                    <td style="border: 1px solid #dee2e6;">
                                        <button type="button" class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editModal" 
                                                onclick="editDispositivo({{ $dispositivo->id }}, '{{ $dispositivo->mac_address }}', {{ $dispositivo->autorizado ? 'true' : 'false' }})">Editar</button>
                                        <form method="POST" action="{{ route('secretaria.gerenciamento.dispositivos.destroy', $dispositivo->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Excluir dispositivo?')">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted p-4">Nenhum dispositivo cadastrado</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição de Dispositivo -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-custom-green">
                <h5 class="modal-title">Editar Dispositivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_mac_address" class="form-label">MAC Address</label>
                        <input type="text" name="mac_address" id="edit_mac_address" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_autorizado" class="form-label">Status</label>
                        <select name="autorizado" id="edit_autorizado" class="form-select" required>
                            <option value="1">Autorizado</option>
                            <option value="0">Não Autorizado</option>
                        </select>
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

<!-- Modal de Edição de Turma -->
<div class="modal fade" id="editTurmaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-custom-blue text-white">
                <h5 class="modal-title">Editar Turma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTurmaForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_ano" class="form-label">Ano</label>
                        <input type="text" name="ano" id="edit_ano" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_turma" class="form-label">Turma</label>
                        <input type="text" name="turma" id="edit_turma" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-custom-blue">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editDispositivo(id, macAddress, autorizado) {
    document.getElementById('editForm').action = `/secretaria/gerenciamento/dispositivos/${id}`;
    document.getElementById('edit_mac_address').value = macAddress;
    document.getElementById('edit_autorizado').value = autorizado ? '1' : '0';
}

function editTurma(id, ano, turma) {
    document.getElementById('editTurmaForm').action = `/secretaria/gerenciamento/turmas/${id}`;
    document.getElementById('edit_ano').value = ano;
    document.getElementById('edit_turma').value = turma;
}
</script>

@endsection