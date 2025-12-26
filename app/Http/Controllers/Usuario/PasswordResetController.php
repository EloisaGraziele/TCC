<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function showRequestForm()
    {
        return view('usuario.login.forgot-password');
    }

    public function sendCode(Request $request)
    {
        // Se for reenvio, usar email da sessão
        $email = $request->email ?: session('email');
        
        \Log::info('Email do request: ' . ($request->email ?: 'vazio'));
        \Log::info('Email da sessão: ' . (session('email') ?: 'vazio'));
        \Log::info('Email final: ' . ($email ?: 'vazio'));
        \Log::info('Iniciando processo de recuperação de senha para: ' . $email);
        
        if (!$email) {
            \Log::error('Nenhum email encontrado - request ou sessão');
            return back()->withErrors(['email' => 'Email não encontrado. Tente novamente.']);
        }
        
        // Só validar se não for reenvio
        if (!$request->has('resend')) {
            $request->validate(['email' => 'required|email'], [
                'email.required' => 'O campo email é obrigatório.',
                'email.email' => 'Digite um email válido.'
            ]);
        }

        // Verificar se o email existe
        $user = User::where('email', $email)->first();
        \Log::info('Usuário encontrado: ' . ($user ? 'SIM' : 'NÃO'));
        
        if (!$user) {
            \Log::warning('Email não encontrado: ' . $email);
            return back()->withErrors(['email' => 'E-mail não encontrado no sistema.']);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        \Log::info('Código gerado: ' . $code);
        
        DB::table('password_reset_codes')->where('email', $email)->delete();
        
        DB::table('password_reset_codes')->insert([
            'email' => $email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(15),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        \Log::info('Código salvo no banco de dados');

        try {
            Mail::raw("Seu código de recuperação de senha é: {$code}\n\nEste código expira em 15 minutos.", function ($message) use ($email) {
                $message->to($email)
                        ->subject('Código de Recuperação de Senha - Sistema de Presença');
            });
            
            $message = $request->has('resend') ? 'Novo código enviado para seu email!' : 'Código enviado para seu email!';
            return redirect('/verify-code')->with('email', $email)->with('success', $message . ' Expira em 15 minutos.');
        } catch (\Exception $e) {
            // Log do erro para debug
            \Log::error('Erro ao enviar email de recuperação: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Erro ao enviar email. Detalhes: ' . $e->getMessage()]);
        }
    }

    public function showVerifyForm()
    {
        $email = session('email');
        if (!$email) {
            return redirect('/forgot-password')->withErrors(['email' => 'Sessão expirada. Tente novamente.']);
        }
        return view('usuario.auth.verify-code', compact('email'));
    }

    public function verifyCode(Request $request)
    {
        \Log::info('VerifyCode chamado - Email: ' . ($request->email ?: 'vazio') . ', Code: ' . ($request->code ?: 'vazio') . ', Resend: ' . ($request->has('resend') ? 'sim' : 'não'));
        
        // Se for um reenvio, redirecionar para sendCode
        if ($request->has('resend') || !$request->filled('code')) {
            \Log::info('Redirecionando para sendCode');
            return $this->sendCode($request);
        }

        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ], [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'code.required' => 'O campo código é obrigatório.',
            'code.size' => 'O código deve ter exatamente 6 dígitos.'
        ]);

        $resetCode = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetCode) {
            return back()->withErrors(['code' => 'Código inválido ou expirado.']);
        }

        return redirect('/reset-password')->with('email', $request->email)->with('code', $request->code);
    }

    public function showResetForm()
    {
        return view('usuario.auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        // Pegar email e código da sessão se não estiverem no request
        $email = $request->email ?: session('email');
        $code = $request->code ?: session('code');
        
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ], [
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.'
        ]);
        
        if (!$email || !$code) {
            return redirect('/forgot-password')->withErrors(['email' => 'Sessão expirada. Digite seu email novamente.']);
        }

        $resetCode = DB::table('password_reset_codes')
            ->where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetCode) {
            return back()->withErrors(['code' => 'Código inválido ou expirado.']);
        }

        $user = User::where('email', $email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_codes')->where('email', $email)->delete();

        return redirect('http://localhost:8080/login')->with('success', 'Senha alterada com sucesso!');
    }
}