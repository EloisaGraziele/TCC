<?php

namespace App\Services;

use App\Models\{Aluno, Dispositivo, Frequencia, CalendarioEscolar};
use App\Contracts\MqttPublisherInterface;
use App\Services\MqttServiceFactory;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class PresencaService
{
    private MqttPublisherInterface $mqttService;

    public function __construct()
    {
        $this->mqttService = MqttServiceFactory::create();
    }

    /**
     * Processa dados de presença recebidos do ESP via MQTT
     */
    public function processarPresenca(array $dados): array
    {
        $startTime = microtime(true);
        
        try {
            Log::info('PRESENÇA: Iniciando processamento', $dados);

            // 1. Verificar se é dia letivo
            $diaLetivoStart = microtime(true);
            if (!$this->isDiaLetivo()) {
                $erro = 'Hoje não é dia letivo - Verifique o calendário escolar';
                Log::warning('PRESENÇA: ' . $erro, ['data' => Carbon::now()->format('Y-m-d')]);
                $this->mqttService->publishError($erro);
                return ['success' => false, 'message' => $erro];
            }
            Log::debug('PERFORMANCE: Verificação dia letivo', ['tempo_ms' => round((microtime(true) - $diaLetivoStart) * 1000, 2)]);

            // 2. Verificar dispositivo autorizado
            $dispositivoStart = microtime(true);
            $macAddress = $dados['mac_address'] ?? $dados['mac'] ?? '';
            if (!$this->isDispositivoAutorizado($macAddress)) {
                $erro = 'Dispositivo não autorizado - MAC não cadastrado ou inativo';
                Log::warning('PRESENÇA: ' . $erro, ['mac' => $macAddress]);
                $this->mqttService->publishError($erro);
                return ['success' => false, 'message' => $erro];
            }
            Log::debug('PERFORMANCE: Verificação dispositivo', ['tempo_ms' => round((microtime(true) - $dispositivoStart) * 1000, 2)]);

            // 3. Descriptografar dados do aluno
            $qrStart = microtime(true);
            $qrCode = $dados['qr_code'] ?? $dados['qrcode'] ?? '';
            $alunoId = $this->descriptografiarAluno($qrCode);
            if (!$alunoId) {
                $erro = 'QR Code inválido - Código corrompido ou formato incorreto';
                Log::warning('PRESENÇA: ' . $erro, ['qr_length' => strlen($qrCode)]);
                $this->mqttService->publishError($erro);
                return ['success' => false, 'message' => $erro];
            }
            Log::debug('PERFORMANCE: Descriptografia QR', ['tempo_ms' => round((microtime(true) - $qrStart) * 1000, 2)]);

            // 4. Verificar se aluno existe
            $alunoStart = microtime(true);
            $aluno = Aluno::find($alunoId);
            if (!$aluno) {
                $erro = "Aluno não encontrado - ID {$alunoId} não existe no sistema";
                Log::warning('PRESENÇA: ' . $erro, ['aluno_id' => $alunoId]);
                $this->mqttService->publishError($erro);
                return ['success' => false, 'message' => $erro];
            }
            Log::debug('PERFORMANCE: Busca aluno', ['tempo_ms' => round((microtime(true) - $alunoStart) * 1000, 2)]);

            // 5. Registrar presença
            $frequenciaStart = microtime(true);
            $resultado = $this->registrarFrequencia($aluno);
            Log::debug('PERFORMANCE: Registro frequência', ['tempo_ms' => round((microtime(true) - $frequenciaStart) * 1000, 2)]);
            
            // 6. Publicar resultado no MQTT
            if ($resultado['success']) {
                $this->mqttService->publishSuccess($resultado['message']);
            } else {
                $this->mqttService->publishError($resultado['message']);
            }

            $totalTime = round((microtime(true) - $startTime) * 1000, 2);
            Log::info('PERFORMANCE: Processamento completo', ['tempo_total_ms' => $totalTime]);

            return $resultado;

        } catch (Exception $e) {
            $erro = 'Erro interno: ' . $e->getMessage();
            Log::error('PRESENÇA: Erro fatal', [
                'error' => $e->getMessage(),
                'dados' => $dados,
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->mqttService->publishError($erro);
            return ['success' => false, 'message' => $erro];
        }
    }

    /**
     * Verifica se hoje é dia letivo
     */
    private function isDiaLetivo(): bool
    {
        $hoje = Carbon::now();
        
        // Por enquanto, considera qualquer dia como letivo para testes
        // Verificar se é fim de semana
        if ($hoje->isWeekend()) {
            return false;
        }

        // Se não é fim de semana, considera dia letivo
        return true;
    }

    /**
     * Verifica se o dispositivo está autorizado
     */
    private function isDispositivoAutorizado(string $macAddress): bool
    {
        if (empty($macAddress)) {
            return false;
        }

        try {
            $dispositivo = Dispositivo::where('mac_address', $macAddress)
                ->where('autorizado', true)
                ->first();

            return $dispositivo !== null;
        } catch (Exception $e) {
            Log::warning('Erro ao verificar dispositivo, permitindo acesso', ['error' => $e->getMessage()]);
            return true; // Permitir acesso se houver erro de conexão
        }
    }

    /**
     * Descriptografa o QR code para obter o aluno pelo CPF
     */
    private function descriptografiarAluno(string $qrCode): ?int
    {
        if (empty($qrCode)) {
            return null;
        }

        // Debug: mostrar QR code completo
        Log::info('PRESENÇA: QR Code recebido', [
            'qr_code_completo' => $qrCode,
            'tamanho' => strlen($qrCode),
            'primeiros_50' => substr($qrCode, 0, 50),
            'ultimos_50' => substr($qrCode, -50)
        ]);
        
        // Usar CPF direto (TEMPORÁRIO)
        try {
            $aluno = Aluno::where('cpf', $qrCode)->first();
            
            if ($aluno) {
                Log::info('PRESENÇA: Aluno encontrado via CPF direto', ['aluno_id' => $aluno->id, 'nome' => $aluno->nome]);
                return $aluno->id;
            }
            
            // Tentar extrair CPF do formato UUID-CPF
            if (strpos($qrCode, '-') !== false) {
                $parts = explode('-', $qrCode);
                $cpfPossivel = end($parts); // Última parte
                
                $aluno = Aluno::where('cpf', $cpfPossivel)->first();
                
                if ($aluno) {
                    Log::info('PRESENÇA: Aluno encontrado via CPF extraído', ['aluno_id' => $aluno->id, 'nome' => $aluno->nome, 'cpf' => $cpfPossivel]);
                    return $aluno->id;
                }
            }
        } catch (Exception $e) {
            Log::error('Erro ao buscar aluno no banco', ['error' => $e->getMessage()]);
        }
        
        Log::error('PRESENÇA: Nenhum aluno encontrado', ['qr_code' => $qrCode]);
        return null;
    }

    /**
     * Registra a frequência do aluno
     */
    private function registrarFrequencia($aluno): array
    {
        $agora = Carbon::now("America/Sao_Paulo");
        $hoje = $agora->format('Y-m-d');

        // Buscar registro de hoje - usar created_at
        $frequenciaHoje = Frequencia::where('aluno_id', $aluno->id)
            ->whereDate('created_at', $hoje)
            ->first();
            
        // Se não encontrou registro de hoje, tratar como primeira batida
        if (!$frequenciaHoje) {
            // Primeira batida = ENTRADA
            Frequencia::create([
                'aluno_id' => $aluno->id,
                'frequencia' => 'presente',
                'hora_entrada' => $agora->format('H:i:s')
            ]);

            return [
                'success' => true,
                'message' => "Entrada registrada para {$aluno->nome} às {$agora->format('H:i')}"
            ];
        }

        // Já tem entrada, verificar se pode registrar saída
        if (empty($frequenciaHoje->horario_saida)) {
            // Sempre permitir saída se já tem entrada (remover restrição de 10 min por enquanto)
            $frequenciaHoje->update([
                'horario_saida' => $agora->format('H:i:s')
            ]);

            return [
                'success' => true,
                'message' => "Saída registrada para {$aluno->nome} às {$agora->format('H:i')}"
            ];
        }

        // Já tem entrada e saída
        return [
            'success' => false,
            'message' => "Frequência já completa para {$aluno->nome} hoje"
        ];
    }
}