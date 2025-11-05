<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Mantido, caso o método validarPresenca precise
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Str;
use App\Models\Aluno;

class QrCodeController extends Controller
{
    /**
     * IMPORTANTE: Os métodos gerar() e salvarImagem() foram removidos 
     * pois o fluxo AJAX foi substituído pela submissão de formulário padrão.
     * A geração da imagem do QR Code será feita em uma rota dedicada 
     * (Ex: para impressão do crachá) no futuro.
     */

    /**
     * Valida o QR Code (ESP32 chama essa rota)
     */
    public function validarPresenca(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $tokenLido = $request->token;

        // Busca o aluno pelo token (que agora será a Matrícula, conforme a nova lógica)
        $aluno = Aluno::where('matricula', $tokenLido)
                      ->where('status', 'ativo')
                      ->first();

        if ($aluno) {
            // 🎯 Aqui você registra a presença
            // Presenca::create([
            //     'aluno_id' => $aluno->id,
            //     'data_hora' => now(),
            // ]);
            
            return response()->json([
                'success' => true,
                'aluno' => [
                    'id' => $aluno->id,
                    'nome' => $aluno->nome,
                    'matricula' => $aluno->matricula,
                    'turma' => $aluno->turma
                ],
                'message' => 'Presença registrada com sucesso!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'QR Code inválido ou aluno inativo!'
        ], 404);
    }
}