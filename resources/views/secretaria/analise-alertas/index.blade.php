@extends('layouts.admin')

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

    <!-- Análise de Alertas -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 rounded-4 shadow-lg" style="background-color: #55B4F8;">
                <div class="card-header text-white fw-bold text-center py-4 rounded-top-4" 
                     style="background-color: #55B4F8; border-bottom: none;">
                    <h1 class="mb-0" style="font-size: 2rem;">Análise de Alertas</h1>
                </div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <p class="text-dark fw-bold" style="font-size: 1.2rem;">
                            Análise automática executada às 8:00h<br>
                            <small>(Apenas em dias letivos)</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notificações Geradas -->
    @if($notificacoes->count() > 0)
    <div class="row justify-content-center mt-4">
        <div class="col-12">
            <h1 class="text-start mb-4 fw-bold" style="color: #63FF63; font-size: 2rem;">Notificações Geradas</h1>
            
            <div class="table-responsive">
                <table class="table mb-0" style="background-color: white; border: 3px solid #55B4F8;">
                    <thead>
                        <tr style="border-bottom: 3px solid #55B4F8;">
                            <th class="fw-bold text-center py-3" style="font-size: 1.2rem; border-right: 2px solid #55B4F8;">Aluno</th>
                            <th class="fw-bold text-center py-3" style="font-size: 1.2rem; border-right: 2px solid #55B4F8;">Tipo Alerta</th>
                            <th class="fw-bold text-center py-3" style="font-size: 1.2rem; border-right: 2px solid #55B4F8;">Mensagem</th>
                            <th class="fw-bold text-center py-3" style="font-size: 1.2rem; border-right: 2px solid #55B4F8;">Destinatário</th>
                            <th class="fw-bold text-center py-3" style="font-size: 1.2rem;">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notificacoes as $notificacao)
                        <tr style="border-bottom: 2px solid #55B4F8;">
                            <td class="align-middle py-3" style="font-size: 1.1rem; border-right: 2px solid #55B4F8;">{{ $notificacao->aluno->nome }}</td>
                            <td class="text-center align-middle py-3" style="border-right: 2px solid #55B4F8;">
                                <span class="badge" style="background-color: #55B4F8; color: black; font-size: 1rem; padding: 10px 15px;">
                                    @if($notificacao->alerta->tipo_alerta == 'faltas_consecutivas')
                                        Faltas Consecutivas
                                    @elseif($notificacao->alerta->tipo_alerta == 'percentual_faltas')
                                        Percentual de Faltas
                                    @else
                                        Dia Específico
                                    @endif
                                </span>
                            </td>
                            <td class="align-middle py-3" style="font-size: 1.1rem; border-right: 2px solid #55B4F8;">{{ $notificacao->mensagem }}</td>
                            <td class="text-center align-middle py-3" style="font-size: 1.1rem; border-right: 2px solid #55B4F8;">
                                <span class="badge {{ $notificacao->destinatario_tipo == 'pais' ? 'bg-success' : 'bg-primary' }}">
                                    {{ ucfirst($notificacao->destinatario_tipo) }}
                                </span>
                            </td>
                            <td class="text-center align-middle py-3" style="font-size: 1.1rem;">
                                {{ $notificacao->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $notificacoes->links() }}
            </div>
        </div>
    </div>
    @else
    <div class="row justify-content-center mt-4">
        <div class="col-12">
            <div class="alert alert-info text-center">
                <strong>Nenhuma notificação gerada ainda.</strong><br>
                As notificações serão criadas automaticamente quando os critérios dos alertas forem atendidos.
            </div>
        </div>
    </div>
    @endif
</div>
@endsection