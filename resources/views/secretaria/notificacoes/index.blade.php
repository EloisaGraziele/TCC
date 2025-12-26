@extends('layouts.admin')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<!-- Título do Sistema -->
<div class="mb-3" style="margin-left: 20px; padding-left: 0;">
    <h2 class="fw-bold">
        <span style="color: #55B4F8;">Sistema de</span> 
        <span style="color: #63FF63;">Presença</span>
    </h2>
</div>

<div class="container py-2">
    @if (session('success'))
        <div class="alert alert-success text-center rounded-3 mb-4 fw-bold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="card border-0 rounded-4 shadow-lg" style="background-color: #55B4F8;">
                <div class="card-header text-white fw-bold text-center py-3 rounded-top-4" 
                     style="background-color: #55B4F8; border-bottom: none;">
                    <h2 class="mb-0" style="font-size: 1.8rem;">Filtrar Notificações</h2>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('secretaria.notificacoes.index') }}">
                        <div class="row justify-content-center">
                            <div class="col-md-8 mb-3">
                                <label class="form-label text-dark fw-bold" style="font-size: 1.2rem;">Nome do Aluno</label>
                                <input type="text" name="aluno_nome" class="form-control form-control-lg rounded-3" 
                                       value="{{ request('aluno_nome') }}" placeholder="Digite o nome...">
                            </div>
                            <div class="col-md-4 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn fw-bold text-dark w-100" 
                                        style="background-color: #63FF63; border: none; height: 50px; border-radius: 12px;">
                                    Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações -->
    <div class="row justify-content-between mb-3">
        <div class="col-auto">
            <h1 class="fw-bold" style="color: #63FF63; font-size: 2rem;">Notificações da Secretaria</h1>
        </div>
        <div class="col-auto">
            <form method="POST" action="{{ route('secretaria.notificacoes.marcar-todas-lidas') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning fw-bold" style="padding: 10px 20px;">
                    Marcar Todas como Lidas
                </button>
            </form>
        </div>
    </div>

    <!-- Lista de Notificações -->
    @if($notificacoes->count() > 0)
    <div class="row">
        <div class="col-12">
            @foreach($notificacoes as $notificacao)
            <div class="card mb-3 shadow-sm {{ $notificacao->lida ? 'bg-light' : 'bg-white border-primary' }}" 
                 style="border-left: 5px solid {{ $notificacao->lida ? '#6c757d' : '#55B4F8' }};">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title mb-1 {{ $notificacao->lida ? 'text-muted' : 'text-primary' }}">
                                <strong>{{ $notificacao->aluno->nome }}</strong>
                                @if(!$notificacao->lida)
                                    <span class="badge bg-danger ms-2">Nova</span>
                                @endif
                            </h5>
                            <p class="card-text mb-1">{{ $notificacao->mensagem }}</p>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> {{ $notificacao->created_at->format('d/m/Y H:i') }}
                                @if($notificacao->lida)
                                    | <i class="fas fa-check"></i> Lida em {{ $notificacao->lida_em->format('d/m/Y H:i') }}
                                @endif
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge mb-2" style="background-color: #55B4F8; color: black; font-size: 0.9rem; padding: 8px 12px;">
                                @if($notificacao->alerta->tipo_alerta == 'faltas_consecutivas')
                                    Faltas Consecutivas
                                @elseif($notificacao->alerta->tipo_alerta == 'percentual_faltas')
                                    Percentual de Faltas
                                @else
                                    Dia Específico
                                @endif
                            </span>
                            @if(!$notificacao->lida)
                            <br>
                            <button onclick="marcarLida({{ $notificacao->id }})" 
                                    class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Marcar como Lida
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $notificacoes->withQueryString()->links() }}
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info text-center">
        <h4>Nenhuma notificação encontrada</h4>
        <p>Não há notificações para os filtros selecionados.</p>
    </div>
    @endif
</div>

<script>
function marcarLida(id) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    
    fetch(`{{ url('/secretaria/notificacoes') }}/${id}/marcar-lida`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        } else {
            console.error('Erro ao marcar como lida');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
    });
}
</script>
@endsection