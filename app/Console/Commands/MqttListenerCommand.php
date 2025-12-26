<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessarPresencaJob;
use App\Services\MqttServiceFactory;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Log;


class MqttListenerCommand extends Command
{
    protected $signature = 'mqtt:listen {--daemon : Executar como daemon} {--timeout=0 : Timeout em segundos (0 = infinito)}';
    protected $description = 'Escuta mensagens MQTT do ESP no tópico Presenca/saida';
    
    private $mqttService;
    private $messageCount = 0;
    private $errorCount = 0;
    private $startTime;

    public function handle()
    {
        $this->startTime = microtime(true);
        $this->mqttService = MqttServiceFactory::create();
        
        $this->info('🎧 Iniciando listener MQTT otimizado...');
        $this->info('📡 Conectando em broker.hivemq.com:1883');
        
        try {
            $client = new MqttClient('broker.hivemq.com', 1883, 'Laravel_Listener_' . time());
            
            $connectionSettings = (new ConnectionSettings())
                ->setKeepAliveInterval(30)
                ->setConnectTimeout(10)
                ->setUseTls(false);

            $client->connect($connectionSettings, true);
            $this->info('✅ Conectado ao broker MQTT');
            
            // Publicar status inicial de conexão
            $this->mqttService->publish('Presenca/confirma', json_encode([
                'status' => 'SISTEMA_ATIVO',
                'message' => 'Sistema de presença funcionando e pronto para receber dados',
                'timestamp' => now()->toISOString(),
                'listener_id' => 'Laravel_Listener_' . time()
            ]));
            
            $this->info('📡 Status enviado: Sistema ativo e funcionando');

            $client->subscribe('Presenca/saida', function (string $topic, string $message) {
                $this->processMessage($topic, $message);
            }, 0);
            $this->info('👂 Escutando tópico Presenca/saida...');
            $this->info('Pressione Ctrl+C para parar');

            $timeout = (int) $this->option('timeout');
            if ($timeout > 0) {
                $this->info("⏱️ Timeout configurado: {$timeout}s");
                $client->loop(true, $timeout);
            } else {
                $client->loop(true);
            }

        } catch (\Exception $e) {
            $this->error('❌ Erro crítico: ' . $e->getMessage());
            Log::error('MQTT Listener falhou', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        } finally {
            $this->showStats();
        }
    }
    
    public function processMessage(string $topic, string $message): void
    {
        $startTime = microtime(true);
        $this->messageCount++;
        
        try {
            // Validação rápida de tamanho
            if (strlen($message) > 2048) {
                $this->sendError('Mensagem muito grande (>2KB)');
                return;
            }
            
            // Apenas remover quebras de linha e espaços extras
            $message = trim($message);
            
            Log::info('MQTT: Mensagem recebida', ['message' => $message]);
            
            // Decodificação JSON otimizada
            $dados = json_decode($message, true, 3, JSON_THROW_ON_ERROR);
            
            // Validação de campos obrigatórios
            if (!$this->validateMessage($dados)) {
                return;
            }
            
            // Log da mensagem recebida
            $this->line("📨 Processando mensagem do ESP...");
            
            // Processar mensagem
            $dadosFormatados = [
                'mac_address' => $dados['mac'],
                'qr_code' => $dados['qrcode']
            ];
            
            ProcessarPresencaJob::dispatch($dadosFormatados);
            
            $processTime = round((microtime(true) - $startTime) * 1000, 2);
            $this->line("✅ [{$this->messageCount}] Processado em {$processTime}ms - MAC: {$dados['mac']}");
            
        } catch (\JsonException $e) {
            $this->sendError('JSON inválido: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->sendError('Erro interno: ' . $e->getMessage());
            Log::error('MQTT processamento falhou', ['message' => $message, 'error' => $e->getMessage()]);
        }
    }
    
    private function validateMessage(array $dados): bool
    {
        if (!isset($dados['mac']) || !isset($dados['qrcode'])) {
            $campos = implode(', ', array_keys($dados));
            $this->sendError("Campos obrigatórios ausentes. Recebido: [{$campos}]. Esperado: [mac, qrcode]");
            return false;
        }
        
        if (empty($dados['mac']) || empty($dados['qrcode'])) {
            $this->sendError('Campos mac ou qrcode estão vazios');
            return false;
        }
        
        // Validar formato MAC
        if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $dados['mac'])) {
            $this->sendError('Formato MAC inválido: ' . $dados['mac']);
            return false;
        }
        
        return true;
    }
    
    private function sendError(string $message): void
    {
        $this->errorCount++;
        $this->error("❌ [{$this->errorCount}] {$message}");
        
        $this->mqttService->publish('Presenca/confirma', json_encode([
            'status' => 'ERROR',
            'message' => $message,
            'timestamp' => now()->toISOString()
        ]));
    }
    
    private function showStats(): void
    {
        $runtime = round(microtime(true) - $this->startTime, 2);
        $this->info("\n📊 Estatísticas:");
        $this->info("⏱️ Tempo execução: {$runtime}s");
        $this->info("📨 Mensagens processadas: {$this->messageCount}");
        $this->info("❌ Erros: {$this->errorCount}");
        if ($this->messageCount > 0) {
            $avgTime = round($runtime / $this->messageCount * 1000, 2);
            $this->info("⚡ Tempo médio por mensagem: {$avgTime}ms");
        }
    }
}