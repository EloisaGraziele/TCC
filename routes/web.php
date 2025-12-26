<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Usuario\AuthController;
use App\Http\Controllers\Secretaria\SecretariaAuthController;
use App\Http\Controllers\Secretaria\SecretariaDashboardController;
use App\Http\Controllers\Secretaria\SecretariaAlunoController;
use App\Http\Controllers\Secretaria\QrCodeController;
use App\Http\Controllers\Secretaria\GerenciamentoController;
use App\Http\Controllers\Secretaria\TurmaController;
use App\Http\Controllers\Secretaria\DispositivoController;
use App\Http\Controllers\Secretaria\FrequenciaController;
use App\Http\Controllers\Secretaria\CalendarioController;
use App\Http\Controllers\Usuario\ResponsavelController;
use App\Http\Controllers\PresencaController;





// API para receber dados de presença do ESP (sem CSRF)
Route::post('/api/presenca', [PresencaController::class, 'receberPresenca'])->withoutMiddleware(['csrf']);
Route::post('/esp/presenca', [PresencaController::class, 'receberPresencaEsp'])->withoutMiddleware(['csrf']);

// Rota de Início (Root)
Route::get('/', function () {
    return view('usuario.login.login');
})->name('root');

// Login usuário
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

// Cadastro de Responsável
Route::get('/register', function () {
    return view('usuario.login.cadastro');
})->name('register');
Route::get('/register/etapa1', [ResponsavelController::class, 'showEtapa1'])->name('register.etapa1.show');
Route::post('/register/etapa1', [ResponsavelController::class, 'etapa1'])->name('register.etapa1');
Route::post('/register/finalizar', [ResponsavelController::class, 'finalizar'])->name('register.finalizar');
Route::get('/register/voltar', [ResponsavelController::class, 'voltar'])->name('register.voltar');

// Recuperar senha com código
Route::get('/forgot-password', [\App\Http\Controllers\Usuario\PasswordResetController::class, 'showRequestForm'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Usuario\PasswordResetController::class, 'sendCode'])->name('password.send.code');
Route::get('/send-reset-code', [\App\Http\Controllers\Usuario\PasswordResetController::class, 'sendCode']);
Route::post('/send-reset-code', [\App\Http\Controllers\Usuario\PasswordResetController::class, 'sendCode']);
Route::get('/verify-code', [\App\Http\Controllers\Usuario\PasswordResetController::class, 'showVerifyForm'])->name('password.verify.form');
Route::post('/verify-code', [\App\Http\Controllers\Usuario\PasswordResetController::class, 'verifyCode'])->name('password.verify.code');
Route::get('/reset-password', [\App\Http\Controllers\Usuario\PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [\App\Http\Controllers\Usuario\PasswordResetController::class, 'resetPassword'])->name('password.update');

// ROTAS DO ADMIN
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\AdminController::class, 'login'])->name('login.submit');
    Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('index');
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [\App\Http\Controllers\Admin\AdminController::class, 'logout'])->name('logout');
    
    // Rotas de Usuários
    Route::get('/usuarios', function() { $usuarios = DB::table('users')->get(); $html = '<!DOCTYPE html><html><head><title>Usuarios</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><style>.bg-custom-green{background-color:#63FF63;}.bg-custom-blue{background-color:#55B4F8;}body{background-color:#ecf2f7ff;}.table{font-size:18px;}</style></head><body><div class="bg-custom-green p-4 d-flex justify-content-between align-items-center"><h3 class="mb-0 fw-bold text-dark">Usuarios e Alunos Vinculados</h3><a href="/admin" class="btn btn-primary btn-lg fw-bold" style="background-color: #55B4F8;">Voltar ao Painel</a></div><div class="container py-4"><div class="card shadow-lg"><div class="card-header bg-custom-blue text-white text-center py-3"><h4 class="mb-0">Lista de Usuarios</h4></div><div class="card-body p-0"><table class="table table-striped table-hover mb-0 table-sm"><thead class="bg-dark text-white"><tr><th class="p-3 fw-semibold">Usuário</th><th class="p-3 fw-semibold">Email</th><th class="p-3 fw-semibold">Alunos Vinculados</th></tr></thead><tbody>'; foreach($usuarios as $usuario) { $alunosIds = DB::table('responsavel_aluno')->where('user_id', $usuario->id)->pluck('aluno_id'); $alunos = DB::table('alunos')->whereIn('id', $alunosIds)->get(); $html .= '<tr><td class="p-3"><strong class="text-primary fs-4">' . $usuario->name . '</strong></td><td class="p-3 text-muted">' . $usuario->email . '</td><td class="p-3">'; if($alunos->count() > 0) { foreach($alunos as $aluno) { $html .= '<span class="me-2 fs-5 fw-bold">' . $aluno->nome . '</span>'; } } else { $html .= '<span class="text-danger">Nenhum aluno vinculado</span>'; } $html .= '</td></tr>'; } $html .= '</tbody></table></div></div></div></body></html>'; return $html; })->name('usuarios.index');
    
    // Rotas de Secretaria
    Route::get('/secretaria', [\App\Http\Controllers\Admin\SecretariaController::class, 'index'])->name('secretaria.index');
    Route::get('/secretaria/create', [\App\Http\Controllers\Admin\SecretariaController::class, 'create'])->name('secretaria.create');
    Route::post('/secretaria', [\App\Http\Controllers\Admin\SecretariaController::class, 'store'])->name('secretaria.store');
    Route::put('/secretaria/{id}', [\App\Http\Controllers\Admin\SecretariaController::class, 'update'])->name('secretaria.update');
    Route::delete('/secretaria/{id}', [\App\Http\Controllers\Admin\SecretariaController::class, 'destroy'])->name('secretaria.destroy');
    
    // Rotas de Calendário
    Route::get('/calendario', [\App\Http\Controllers\Admin\CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('/calendario/create', [\App\Http\Controllers\Admin\CalendarioController::class, 'create'])->name('calendario.create');
    Route::post('/calendario', [\App\Http\Controllers\Admin\CalendarioController::class, 'store'])->name('calendario.store');
    Route::get('/calendario/{ano}', [\App\Http\Controllers\Admin\CalendarioController::class, 'show'])->name('calendario.show');
    Route::get('/calendario/{ano}/edit', [\App\Http\Controllers\Admin\CalendarioController::class, 'edit'])->name('calendario.edit');
    Route::put('/calendario/{ano}', [\App\Http\Controllers\Admin\CalendarioController::class, 'update'])->name('calendario.update');
    Route::post('/calendario/{ano}/status', [\App\Http\Controllers\Admin\CalendarioController::class, 'updateStatus'])->name('calendario.status');
    Route::delete('/calendario/{ano}', [\App\Http\Controllers\Admin\CalendarioController::class, 'destroy'])->name('calendario.destroy');
    

});

// ROTAS PROTEGIDAS PARA USUÁRIOS (RESPONSÁVEIS)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Usuario\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/aluno/{id}/frequencia', [\App\Http\Controllers\Usuario\DashboardController::class, 'getAlunoFrequencia'])->name('aluno.frequencia');
    Route::get('/aluno/{id}/semana', [\App\Http\Controllers\Usuario\DashboardController::class, 'getAlunoSemana'])->name('aluno.semana');
    Route::get('/aluno/{id}/pesquisar', [\App\Http\Controllers\Usuario\DashboardController::class, 'pesquisarPresenca'])->name('aluno.pesquisar');
    
    // Rotas de Notificações (Usuário)
    Route::get('/usuario/notificacoes/latest', [\App\Http\Controllers\Usuario\NotificacaoController::class, 'latest'])->name('usuario.notificacoes.latest');
    Route::post('/usuario/notificacoes/{id}/marcar-lida', [\App\Http\Controllers\Usuario\NotificacaoController::class, 'marcarLida'])->name('usuario.notificacoes.marcar-lida');
    Route::post('/usuario/notificacoes/marcar-todas-lidas', [\App\Http\Controllers\Usuario\NotificacaoController::class, 'marcarTodasLidas'])->name('usuario.notificacoes.marcar-todas-lidas');
    
    // Rotas de Perfil (Usuário)
    Route::get('/usuario/perfil', [\App\Http\Controllers\Usuario\PerfilController::class, 'show'])->name('usuario.perfil.show');
    Route::put('/usuario/perfil', [\App\Http\Controllers\Usuario\PerfilController::class, 'update'])->name('usuario.perfil.update');
});

Route::prefix('secretaria')->name('secretaria.')->group(function () {
    Route::get('/login', [SecretariaAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [SecretariaAuthController::class, 'login'])->name('login.submit');

    // ROTAS PROTEGIDAS
    Route::middleware('auth:secretaria')->group(function () {
        Route::post('/logout', [SecretariaAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', function () {
            return redirect()->route('secretaria.frequencias.index');
        })->name('dashboard');

        // Route::get('/cadastrar', [SecretariaController::class, 'create'])->name('register.create');
        // Route::post('/cadastrar', [SecretariaController::class, 'store'])->name('register.store');

        // Rotas de Aluno
        Route::get('/alunos/create', [SecretariaAlunoController::class, 'create'])->name('aluno.create');
        Route::post('/alunos', [SecretariaAlunoController::class, 'store'])->name('aluno.store');
        Route::get('/alunos/{aluno}/cracha', [SecretariaAlunoController::class, 'mostrarCracha'])->name('aluno.cracha');
        Route::get('/alunos/{aluno}/qrcode', [QrCodeController::class, 'gerarQrCode'])->name('aluno.qrcode');
        Route::get('/alunos/gerenciar', [SecretariaAlunoController::class, 'gerenciar'])->name('aluno.gerenciar');
        Route::put('/alunos/{aluno}', [SecretariaAlunoController::class, 'update'])->name('aluno.update');
        Route::patch('/alunos/{aluno}/status', [SecretariaAlunoController::class, 'updateStatus'])->name('aluno.status');
        Route::post('/alunos/{aluno}/regenerar-qr', [SecretariaAlunoController::class, 'regenerarQr'])->name('aluno.regenerar-qr');
        
        // Gerenciamento
        Route::prefix('gerenciamento')->name('gerenciamento.')->group(function () {
            Route::get('/', [GerenciamentoController::class, 'index'])->name('index');
            Route::resource('turmas', TurmaController::class);
            Route::resource('dispositivos', DispositivoController::class);
        });

        Route::get('/alunos', [SecretariaAlunoController::class, 'index'])->name('aluno.index');
        Route::get('/frequencias', [FrequenciaController::class, 'index'])->name('frequencias.index');
        Route::get('/alertas', [\App\Http\Controllers\Secretaria\AlertaController::class, 'index'])->name('alertas.index');
        Route::get('/alertas/create', [\App\Http\Controllers\Secretaria\AlertaController::class, 'create'])->name('alertas.create');
        Route::post('/alertas', [\App\Http\Controllers\Secretaria\AlertaController::class, 'store'])->name('alertas.store');
        Route::delete('/alertas/{id}', [\App\Http\Controllers\Secretaria\AlertaController::class, 'destroy'])->name('alertas.destroy');
        
        // Rotas de Calendário (apenas visualização e edição)
        Route::get('/calendario', [\App\Http\Controllers\Secretaria\CalendarioController::class, 'index'])->name('calendario.index');
        Route::get('/calendario/{ano}/edit', [\App\Http\Controllers\Secretaria\CalendarioController::class, 'edit'])->name('calendario.edit');
        Route::put('/calendario/{ano}', [\App\Http\Controllers\Secretaria\CalendarioController::class, 'update'])->name('calendario.update');

        // Rotas de Notificações (Secretaria)
        Route::get('/notificacoes', [\App\Http\Controllers\Secretaria\NotificacaoController::class, 'index'])->name('notificacoes.index');
        Route::post('/notificacoes/{id}/marcar-lida', [\App\Http\Controllers\Secretaria\NotificacaoController::class, 'marcarLida'])->name('notificacoes.marcar-lida');
        Route::post('/notificacoes/marcar-todas-lidas', [\App\Http\Controllers\Secretaria\NotificacaoController::class, 'marcarTodasLidas'])->name('notificacoes.marcar-todas-lidas');
        // API: últimas notificações (JSON) para painel lateral
        Route::get('/notificacoes/latest', [\App\Http\Controllers\Secretaria\NotificacaoController::class, 'latest'])->name('notificacoes.latest');
        
        // Rotas de Perfil
        Route::get('/perfil', [\App\Http\Controllers\Secretaria\PerfilController::class, 'show'])->name('perfil.show');
        Route::put('/perfil', [\App\Http\Controllers\Secretaria\PerfilController::class, 'update'])->name('perfil.update');
    });
});




