<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Usuario\AuthController;
use App\Http\Controllers\Secretaria\SecretariaAuthController;
use App\Http\Controllers\Secretaria\SecretariaController; // <-- Importado para cadastro
use App\Http\Controllers\Secretaria\SecretariaDashboardController;
use App\Http\Controllers\Secretaria\SecretariaAlunoController;
use App\Http\Controllers\Secretaria\SecretariaFrequenciaController;
use App\Http\Controllers\Secretaria\SecretariaAlertaController;
use App\Http\Controllers\Secretaria\QrCodeController; // Importação correta para o Controller de QR Code
use App\Http\Controllers\Secretaria\GerenciamentoController;
use App\Http\Controllers\Secretaria\TurmaController;
use App\Http\Controllers\Secretaria\DispositivoController;
use App\Http\Controllers\Secretaria\FrequenciaController;
use App\Http\Controllers\Usuario\ResponsavelController;

// ------------------------------------------
// 1. ROTAS DO USUÁRIO COMUM (RESPONSÁVEL) - LOGIN E UTILITÁRIOS
// ------------------------------------------

// Rota de Início (Root): Redireciona para o formulário de login ou exibe a view.
Route::get('/', function () {
    return view('usuario.login.login');
})->name('root');


// Exibe o formulário de login (GET /login)
Route::get('/login', [AuthController::class, 'create'])->name('login');

// Processa a submissão do formulário (POST /login)
Route::post('/login', [AuthController::class, 'store'])->name('login.submit');

// Logout (POST /logout)
Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');


// Cadastro de Responsável - Etapa 1
Route::get('/register', function () {
    return view('usuario.login.cadastro');
})->name('register');

// Processar Etapa 1 e ir para Etapa 2
Route::post('/register/etapa1', [ResponsavelController::class, 'etapa1'])->name('register.etapa1');

// Finalizar cadastro - Etapa 2
Route::post('/register/finalizar', [ResponsavelController::class, 'finalizar'])->name('register.finalizar');

// Voltar para Etapa 1
Route::get('/register/voltar', [ResponsavelController::class, 'voltar'])->name('register.voltar');

// Recuperar senha (GET /forgot-password)
Route::get('/forgot-password', function () {
    return view('usuario.login.forgot-password');
})->name('password.request');

// ROTAS DO ADMIN
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\AdminController::class, 'login'])->name('login.submit');
    Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('index');
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [\App\Http\Controllers\Admin\AdminController::class, 'logout'])->name('logout');
    
    // Rotas de Secretaria
    Route::get('/secretaria/create', [\App\Http\Controllers\Admin\SecretariaController::class, 'create'])->name('secretaria.create');
    Route::post('/secretaria', [\App\Http\Controllers\Admin\SecretariaController::class, 'store'])->name('secretaria.store');
    
    // Rotas de Calendário
    Route::get('/calendario', [\App\Http\Controllers\Admin\CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('/calendario/create', [\App\Http\Controllers\Admin\CalendarioController::class, 'create'])->name('calendario.create');
    Route::post('/calendario', [\App\Http\Controllers\Admin\CalendarioController::class, 'store'])->name('calendario.store');
    Route::get('/calendario/{ano}', [\App\Http\Controllers\Admin\CalendarioController::class, 'show'])->name('calendario.show');
    Route::get('/calendario/{ano}/edit', [\App\Http\Controllers\Admin\CalendarioController::class, 'edit'])->name('calendario.edit');
    Route::put('/calendario/{ano}', [\App\Http\Controllers\Admin\CalendarioController::class, 'update'])->name('calendario.update');
    Route::post('/calendario/{ano}/status', [\App\Http\Controllers\Admin\CalendarioController::class, 'updateStatus'])->name('calendario.status');
    Route::delete('/calendario/{ano}', [\App\Http\Controllers\Admin\CalendarioController::class, 'destroy'])->name('calendario.destroy');
    
    // Rotas MQTT
    Route::get('/mqtt/test', [\App\Http\Controllers\MqttController::class, 'testConnection'])->name('mqtt.test');
    Route::post('/mqtt/start', [\App\Http\Controllers\MqttController::class, 'startSubscriber'])->name('mqtt.start');
});

// ROTAS PROTEGIDAS PARA USUÁRIOS (RESPONSÁVEIS)
Route::middleware('auth')->group(function () {
    // Dashboard do usuário
    Route::get('/dashboard', [\App\Http\Controllers\Usuario\DashboardController::class, 'index'])->name('dashboard');
});



Route::prefix('secretaria')->name('secretaria.')->group(function () {

   
    Route::get('/login', [SecretariaAuthController::class, 'showLoginForm'])->name('login'); // Nome completo: secretaria.login
    Route::post('/login', [SecretariaAuthController::class, 'login'])->name('login.submit'); // Nome completo: secretaria.login.submit

    // ROTAS PROTEGIDAS
    Route::middleware('auth:secretaria')->group(function () {


        // Logout
        Route::post('/logout', [SecretariaAuthController::class, 'logout'])->name('logout'); // Nome completo: secretaria.logout

        // Dashboard - Redireciona para frequências
        Route::get('/dashboard', function () {
            return redirect()->route('secretaria.frequencias.index');
        })->name('dashboard'); // Nome completo: secretaria.dashboard

        // Cadastro de NOVAS Secretarias (Gerenciamento de Usuários Admin)
        Route::get('/cadastrar', [SecretariaController::class, 'create'])->name('register.create'); // Nome completo: secretaria.register.create
        Route::post('/cadastrar', [SecretariaController::class, 'store'])->name('register.store'); // Nome completo: secretaria.register.store

        // Rotas de Aluno
        Route::get('/alunos/create', [SecretariaAlunoController::class, 'create'])->name('aluno.create'); // Formulário de criação
        Route::post('/alunos', [SecretariaAlunoController::class, 'store'])->name('aluno.store'); // Nome completo: secretaria.aluno.store (Cadastro principal)
        Route::get('/alunos/{aluno}/cracha', [SecretariaAlunoController::class, 'mostrarCracha'])->name('aluno.cracha'); // Crachá com QR Code
        Route::get('/alunos/gerenciar', [SecretariaAlunoController::class, 'gerenciar'])->name('aluno.gerenciar'); // Gerenciar Aluno
        Route::put('/alunos/{aluno}', [SecretariaAlunoController::class, 'update'])->name('aluno.update'); // Atualizar Aluno
        Route::patch('/alunos/{aluno}/status', [SecretariaAlunoController::class, 'updateStatus'])->name('aluno.status'); // Alterar Status
        Route::post('/alunos/{aluno}/regenerar-qr', [SecretariaAlunoController::class, 'regenerarQr'])->name('aluno.regenerar-qr'); // Regenerar QR
        
        // Gerenciamento
        Route::prefix('gerenciamento')->name('gerenciamento.')->group(function () {
            
            // Rota 1: O Dashboard/Página Inicial (Onde estão os links)
            Route::get('/', [GerenciamentoController::class, 'index'])->name('index');

            // Rota 2: Gerenciamento de Turmas (CRUD)
            Route::resource('turmas', TurmaController::class);

            // Rota 3: Gerenciamento de Dispositivos (CRUD)
            Route::resource('dispositivos', DispositivoController::class);

        });

        // Rota para visualizar alunos (index) - Frequência por Alunos
        Route::get('/alunos', [SecretariaAlunoController::class, 'index'])->name('aluno.index'); // Nome completo: secretaria.aluno.index

        // ----------------------------------------------------

        // Rotas de Frequência e Outros
        Route::get('/frequencias', [FrequenciaController::class, 'index'])->name('frequencias.index'); // Nome completo: secretaria.frequencias.index

        // Rota para criar alertas (se o link estiver no menu)
        Route::get('/alertas/create', function () { 
            return "Página de criação de alertas";
        })->name('alertas.create'); // Nome completo: secretaria.alertas.create

    });
});
