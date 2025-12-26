<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vincular Alunos - Etapa 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card { border-radius: 20px; }
        .btn-custom-blue { background-color: #55B4F8; border-color: #55B4F8; color: white; }
        .btn-custom-green { background-color: #63FF63; border-color: #63FF63; color: black; }
        .btn-custom-yellow { background-color: #FFD700; border-color: #FFD700; color: black; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-11">
                <div class="card shadow-lg border-0">
                    <div class="card-header text-center py-4" style="background-color: #55B4F8; color: white; border-radius: 20px 20px 0 0;">
                        <h2 class="mb-0 fw-bold">Vincular Alunos</h2>
                        <p class="mb-0">Etapa 2 de 2 - Informar Alunos</p>
                    </div>
                    <div class="card-body p-5">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Formulário para adicionar aluno -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label fw-bold">Nome do Aluno</label>
                                        <input type="text" id="nome_aluno" class="form-control" placeholder="Digite o nome">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">CPF do Aluno</label>
                                        <input type="text" id="cpf_aluno" class="form-control" placeholder="000.000.000-00">
                                    </div>
                                    <div class="col-md-3 mb-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-custom-green w-100" onclick="confirmarAluno()">
                                            Confirmar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de alunos confirmados -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0 fw-bold">Alunos Vinculados</h6>
                            </div>
                            <div class="card-body p-0">
                                <div id="lista-alunos" class="list-group list-group-flush">
                                    <div class="list-group-item text-center text-muted" id="sem-alunos">
                                        Nenhum aluno adicionado ainda
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('register.finalizar') }}" id="formVincular">
                            @csrf
                            <input type="hidden" id="alunos_data" name="alunos_data" value="[]">

                            <!-- Botões -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('register.voltar') }}" class="btn btn-custom-yellow fw-bold px-4 py-2 rounded-3">
                                    Voltar
                                </a>
                                <button type="submit" class="btn btn-custom-green fw-bold px-4 py-2 rounded-3" id="btnCadastrar" disabled>
                                    Cadastrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let alunosConfirmados = [];

        function confirmarAluno() {
            const nome = document.getElementById('nome_aluno').value.trim();
            const cpf = document.getElementById('cpf_aluno').value.trim();

            if (!nome || !cpf) {
                alert('Por favor, preencha nome e CPF do aluno.');
                return;
            }

            // Verificar se já foi adicionado
            if (alunosConfirmados.some(aluno => aluno.cpf === cpf)) {
                alert('Este aluno já foi adicionado.');
                return;
            }

            // Adicionar à lista
            alunosConfirmados.push({ nome, cpf });
            atualizarLista();
            
            // Limpar campos
            document.getElementById('nome_aluno').value = '';
            document.getElementById('cpf_aluno').value = '';
        }

        function atualizarLista() {
            const lista = document.getElementById('lista-alunos');
            const semAlunos = document.getElementById('sem-alunos');
            const btnCadastrar = document.getElementById('btnCadastrar');

            if (alunosConfirmados.length === 0) {
                semAlunos.style.display = 'block';
                btnCadastrar.disabled = true;
            } else {
                semAlunos.style.display = 'none';
                btnCadastrar.disabled = false;
                
                // Limpar lista atual
                lista.innerHTML = '';
                
                // Adicionar alunos
                alunosConfirmados.forEach((aluno, index) => {
                    const item = document.createElement('div');
                    item.className = 'list-group-item d-flex justify-content-between align-items-center';
                    item.innerHTML = `
                        <div>
                            <strong>${aluno.nome}</strong><br>
                            <small class="text-muted">CPF: ${aluno.cpf}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removerAluno(${index})">
                            Remover
                        </button>
                    `;
                    lista.appendChild(item);
                });
            }

            // Atualizar campo hidden
            document.getElementById('alunos_data').value = JSON.stringify(alunosConfirmados);
        }

        function removerAluno(index) {
            alunosConfirmados.splice(index, 1);
            atualizarLista();
        }

        function limparTudo() {
            alunosConfirmados = [];
            document.getElementById('nome_aluno').value = '';
            document.getElementById('cpf_aluno').value = '';
            atualizarLista();
        }
    </script>
</body>
</html>