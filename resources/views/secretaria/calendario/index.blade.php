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
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header bg-custom-blue text-white text-center py-3">
                    <h2 class="mb-0 fw-bold">📅 Calendários Escolares</h2>
                    <p class="mb-0">Visualizar e editar calendários ativos</p>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success text-center rounded-3 mb-4 fw-bold">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger text-center rounded-3 mb-4 fw-bold">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($calendarios->count() > 0)
                        <div class="row">
                            @foreach($calendarios as $calendario)
                                @php $ano = $calendario->ano; @endphp
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card border-2" style="border-color: #55B4F8;">
                                        <div class="card-header bg-light text-center">
                                            <h5 class="mb-0 fw-bold" style="color: #55B4F8;">📅 {{ $ano }}</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <p class="card-text mb-3">Calendário Escolar {{ $ano }}</p>
                                            <span class="badge bg-success mb-3">✅ Ativo</span>
                                            
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('secretaria.calendario.edit', $ano) }}" 
                                                   class="btn btn-primary" 
                                                   style="background-color: #55B4F8;">
                                                    ✏️ Editar Calendário
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center">
                            <div class="alert alert-info">
                                <h5 class="fw-bold">📅 Nenhum calendário ativo encontrado</h5>
                                <p class="mb-0">Não há calendários ativos disponíveis para edição.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection