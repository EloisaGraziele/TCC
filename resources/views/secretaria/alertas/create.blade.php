@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <h2>Configurar Alerta</h2>
    
    <form method="POST" action="{{ route('secretaria.alertas.store') }}">
        @csrf
        
        <div class="mb-3">
            <label class="form-label">Tipo de Alerta</label>
            <select name="tipo_alerta" class="form-select" required>
                <option value="">Selecione o tipo</option>
                <option value="alerta_consecutivo">Faltas Consecutivas</option>
                <option value="percentual_falta">Percentual de Faltas</option>
                <option value="dia_importante">Falta em Dia Importante</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Descricao</label>
            <textarea name="descricao" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input type="checkbox" name="notificar_pais" class="form-check-input" checked>
                <label class="form-check-label">Notificar Pais</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="notificar_secretaria" class="form-check-input" checked>
                <label class="form-check-label">Notificar Secretaria</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alerta</button>
        <a href="{{ route('secretaria.alertas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
