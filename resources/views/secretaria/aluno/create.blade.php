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
    <!-- Mensagem de sucesso -->
    @if (session('success'))
        <div class="alert alert-success text-center rounded-3 mb-4 fw-bold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Bloco principal -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 rounded-4 shadow-lg" 
                 style="background-color: #55B4F8; min-height: 80vh; display: flex; flex-direction: column; justify-content: center;">
                
                <div class="card-header text-white fw-bold text-center py-3 rounded-top-4" 
                     style="background-color: #55B4F8; border-bottom: none;">
                    <h2 class="mb-0">Cadastrar Aluno</h2>
                </div>

                <div class="card-body d-flex justify-content-center align-items-center">
                    <div class="w-100" style="max-width: 600px;">
                        <form method="POST" action="{{ route('secretaria.aluno.store') }}" id="formCadastroAluno">
                            @csrf

                            <!-- Nome -->
                            <div class="mb-4">
                                <label for="nome" class="form-label text-white fw-bold">Nome</label>
                                <input id="nome" type="text" name="nome"
                                       class="form-control form-control-lg rounded-3 @error('nome') is-invalid @enderror"
                                       value="{{ old('nome') }}" required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- CPF e Matrícula -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="cpf" class="form-label text-white fw-bold">CPF</label>
                                    <input id="cpf" type="text" name="cpf" maxlength="14"
                                           class="form-control form-control-lg rounded-3 @error('cpf') is-invalid @enderror"
                                           value="{{ old('cpf') }}" placeholder="000.000.000-00" required>
                                    @error('cpf')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="matricula" class="form-label text-white fw-bold">Matrícula</label>
                                    <input id="matricula" type="text" name="matricula"
                                           class="form-control form-control-lg rounded-3 @error('matricula') is-invalid @enderror"
                                           value="{{ old('matricula') }}" required>
                                    @error('matricula')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Turma -->
                            <div class="mb-4">
                                <label for="turma_id" class="form-label text-white fw-bold">Turma</label>
                                <select id="turma_id" name="turma_id" 
                                        class="form-select form-control-lg rounded-3 @error('turma_id') is-invalid @enderror"
                                        required>
                                    <option value="" disabled selected>Selecione a turma</option>
                                    @foreach($turmas as $turma)
                                        <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                                            {{ $turma->turma }} ({{ $turma->ano }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('turma_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($turmas->isEmpty())
                                    <div class="form-text text-white-50">
                                        Nenhuma turma cadastrada para {{ date('Y') }}. 
                                        <a href="{{ route('secretaria.gerenciamento.index') }}" class="text-white">Cadastre turmas primeiro</a>.
                                    </div>
                                @endif
                            </div>

                            <!-- Data de Nascimento -->
                            <div class="mb-4">
                                <label for="data_nascimento" class="form-label text-white fw-bold">Data de Nascimento</label>
                                <input id="data_nascimento" type="date" name="data_nascimento"
                                       class="form-control form-control-lg rounded-3 @error('data_nascimento') is-invalid @enderror"
                                       value="{{ old('data_nascimento') }}" required>
                                @error('data_nascimento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Botões -->
                            <div class="d-flex justify-content-between mt-4">
                                <!-- Limpar -->
                                <button type="button" 
                                        onclick="document.getElementById('formCadastroAluno').reset();" 
                                        class="btn fw-bold text-dark"
                                        style="background-color: #ffc107; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Limpar
                                </button>

                                <!-- Salvar -->
                                <button type="submit" 
                                        class="btn fw-bold text-dark"
                                        style="background-color: #63FF63; border: none; width: 48%; height: 60px; border-radius: 12px;">
                                    Salvar
                                </button>
                            </div>

                        </form>

                        {{-- Redirecionamento pós-cadastro (ativar futuramente)
                        @if(session('aluno_id'))
                            <script>
                                window.location.href = "{{ route('secretaria.aluno.show', session('aluno_id')) }}";
                            </script>
                        @endif
                        --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection