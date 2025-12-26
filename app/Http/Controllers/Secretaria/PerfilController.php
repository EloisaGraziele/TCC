<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function show()
    {
        $secretaria = Auth::guard('secretaria')->user();
        if (!$secretaria) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }

        return response()->json([
            'id' => $secretaria->id,
            'name' => $secretaria->name,
            'email' => $secretaria->email,
        ]);
    }

    public function update(Request $request)
    {
        $secretaria = Auth::guard('secretaria')->user();
        if (!$secretaria) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }

        // Validação simples
        if (!$request->name || !$request->email) {
            return response()->json(['error' => 'Nome e email são obrigatórios'], 422);
        }

        if ($request->password && strlen($request->password) < 6) {
            return response()->json(['error' => 'Senha deve ter pelo menos 6 caracteres'], 422);
        }

        if ($request->password && !$request->password_confirmation) {
            return response()->json(['error' => 'Para alterar a senha, você deve confirmar a nova senha'], 422);
        }

        if ($request->password && $request->password !== $request->password_confirmation) {
            return response()->json(['error' => 'Confirmação de senha não confere'], 422);
        }

        // Verifica email duplicado
        if ($secretaria->email !== $request->email) {
            $exists = DB::table('secretarias')
                ->where('email', $request->email)
                ->where('id', '!=', $secretaria->id)
                ->exists();
            
            if ($exists) {
                return response()->json(['error' => 'E-mail já está em uso'], 422);
            }
        }

        // Atualiza dados
        $secretaria->name = $request->name;
        $secretaria->email = $request->email;

        if ($request->password) {
            $secretaria->password = Hash::make($request->password);
        }

        $secretaria->save();

        return response()->json(['success' => true, 'message' => 'Perfil atualizado com sucesso']);
    }
}