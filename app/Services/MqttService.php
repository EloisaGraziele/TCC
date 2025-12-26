<?php

namespace App\Services;

use App\Contracts\MqttPublisherInterface;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Log;
use Exception;

class MqttService implements MqttPublisherInterface
{
    private $client;
    private $config;
    private $isConnected = false;

    public function __construct()
    {
        $this->config = config('mqtt');
    }

    /**
     * Conecta ao broker MQTT
     */
    public function connect(): bool
    {
        try {
            // Se já conectado, desconectar primeiro
            if ($this->isConnected && $this->client) {
                try {
                    $this->client->disconnect();
                } catch (Exception $e) {
                    // Ignorar erro de desconexão
                }
            }
            
            $this->client = new MqttClient(
                $this->config['host'],
                $this->config['port'],
                $this->config['client_id'] . '_' . time() // ID único para evitar conflitos
            );

            $connectionSettings = (new ConnectionSettings())
                ->setKeepAliveInterval(30) // Reduzir keep alive
                ->setConnectTimeout(10) // Timeout menor
                ->setUseTls(false)
                ->setTlsSelfSignedAllowed(false);
                
            // Só definir username/password se não estiverem vazios
            if (!empty($this->config['username'])) {
                $connectionSettings->setUsername($this->config['username']);
            }
            if (!empty($this->config['password'])) {
                $connectionSettings->setPassword($this->config['password']);
            }

            $this->client->connect($connectionSettings, true); // Forçar clean session
            $this->isConnected = true;

            Log::info('MQTT: Conectado ao broker', [
                'host' => $this->config['host'],
                'port' => $this->config['port']
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('MQTT: Erro ao conectar', [
                'error' => $e->getMessage(),
                'host' => $this->config['host'],
                'port' => $this->config['port']
            ]);
            
            $this->isConnected = false;
            return false;
        }
    }

    /**
     * Desconecta do broker MQTT
     */
    public function disconnect(): void
    {
        if ($this->isConnected && $this->client) {
            try {
                $this->client->disconnect();
                $this->isConnected = false;
                Log::info('MQTT: Desconectado do broker');
            } catch (Exception $e) {
                Log::error('MQTT: Erro ao desconectar', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Publica uma mensagem no tópico
     */
    public function publish(string $topic, string $message, int $qos = 0): bool
    {
        $maxRetries = 3;
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            if (!$this->isConnected) {
                if (!$this->connect()) {
                    $retryCount++;
                    sleep(2); // Aguardar 2 segundos antes de tentar novamente
                    continue;
                }
            }

            try {
                $this->client->publish($topic, $message, $qos);
                
                Log::info('MQTT: Mensagem publicada', [
                    'topic' => $topic,
                    'message' => $message,
                    'qos' => $qos
                ]);

                return true;

            } catch (Exception $e) {
                Log::error('MQTT: Erro ao publicar mensagem', [
                    'topic' => $topic,
                    'error' => $e->getMessage(),
                    'retry' => $retryCount + 1
                ]);
                
                // Marcar como desconectado para forçar reconexão
                $this->isConnected = false;
                $retryCount++;
                
                if ($retryCount < $maxRetries) {
                    sleep(2); // Aguardar antes de tentar novamente
                }
            }
        }
        
        return false;
    }

    /**
     * Publica resposta de sucesso
     */
    public function publishSuccess(string $message): bool
    {
        $response = json_encode([
            'status' => 'success',
            'message' => $message,
            'timestamp' => now()->toISOString()
        ]);

        return $this->publish($this->config['topics']['saida'], $response);
    }

    /**
     * Publica resposta de erro
     */
    public function publishError(string $message): bool
    {
        $response = json_encode([
            'status' => 'error',
            'message' => $message,
            'timestamp' => now()->toISOString()
        ]);

        return $this->publish($this->config['topics']['saida'], $response);
    }

    /**
     * Testa a conexão com o broker
     */
    public function testConnection(): bool
    {
        $connected = $this->connect();
        if ($connected) {
            $this->disconnect();
        }
        return $connected;
    }

    /**
     * Verifica se está conectado
     */
    public function isConnected(): bool
    {
        return $this->isConnected;
    }
}