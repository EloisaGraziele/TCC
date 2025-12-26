<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessarPresencaJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PresencaController extends Controller
{
    public function receberPresenca(Request $request)
    {
        $dados = $request->validate([
            'mac_address' => 'required|string',
            'qr_code' => 'required|string'
        ]);

        Log::info('Dados de presença recebidos', $dados);

        ProcessarPresencaJob::dispatch($dados);

        return response()->json(['status' => 'success', 'message' => 'Presença em processamento']);
    }

    public function receberPresencaEsp(Request $request)
    {
        $dados = $request->validate([
            'mac' => 'required|string',
            'qrcode' => 'required|string'
        ]);

        // Mapear para formato interno
        $dadosFormatados = [
            'mac_address' => $dados['mac'],
            'qr_code' => $dados['qrcode']
        ];

        Log::info('Dados ESP recebidos via HTTP', $dadosFormatados);

        ProcessarPresencaJob::dispatch($dadosFormatados);

        return response()->json(['status' => 'success', 'message' => 'Presença processada']);
    }
}
