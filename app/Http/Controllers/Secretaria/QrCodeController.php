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
     * Gera QR Code com CPF puro
     */
    public function gerarQrCode($alunoId)
    {
        $aluno = Aluno::findOrFail($alunoId);
        
        $qrCode = QrCode::size(120)
            ->errorCorrection('M')
            ->format('svg')
            ->margin(1)
            ->generate($aluno->cpf);
        
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }

}