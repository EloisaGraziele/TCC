@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-custom-green text-dark">
                    <h4 class="mb-0">Editar Dispositivo</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('secretaria.gerenciamento.dispositivos.update', $dispositivo) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="mac_address" class="form-label">MAC Address</label>
                            <input type="text" name="mac_address" id="mac_address" class="form-control" value="{{ $dispositivo->mac_address }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="autorizado" class="form-label">Status</label>
                            <select name="autorizado" id="autorizado" class="form-select" required>
                                <option value="1" {{ $dispositivo->autorizado ? 'selected' : '' }}>Autorizado</option>
                                <option value="0" {{ !$dispositivo->autorizado ? 'selected' : '' }}>Não Autorizado</option>
                            </select>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">Salvar Alterações</button>
                            <a href="{{ route('secretaria.gerenciamento.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection