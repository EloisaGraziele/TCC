<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\PresencaService;
use Illuminate\Support\Facades\Log;

class ProcessarPresencaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    private $dados;

    public function __construct(array $dados)
    {
        $this->dados = $dados;
    }

    public function handle(PresencaService $presencaService): void
    {
        try {
            Log::info('JOB: Processando presença', $this->dados);
            
            $resultado = $presencaService->processarPresenca($this->dados);
            
            Log::info('JOB: Presença processada', [
                'dados' => $this->dados,
                'resultado' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('JOB: Erro ao processar presença', [
                'dados' => $this->dados,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw para retry automático
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('JOB: Falha definitiva ao processar presença', [
            'dados' => $this->dados,
            'error' => $exception->getMessage()
        ]);
    }
}