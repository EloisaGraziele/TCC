<?php

namespace App\Services;

use App\Contracts\MqttPublisherInterface;
use Illuminate\Support\Facades\Log;
use Exception;

class WebSocketMqttService implements MqttPublisherInterface
{
    private $config;

    public function __construct()
    {
        $this->config = config('mqtt');
    }

    /**
     * Publica mensagem via HTTP (simulando WebSocket MQTT)
     */
    public function publish(string $topic, string $message, int $qos = 0): bool
    {
        try {
            // Como não podemos usar WebSocket diretamente do PHP,
            // vamos usar uma API REST que publique no MQTT
            // Ou usar um cliente TCP normal na porta 1883
            
            Log::info('WebSocket MQTT: Tentando publicar', [
                'topic' => $topic,
                'message' => $message
            ]);

            // Fallback: usar TCP na porta 1883
            $client = new \PhpMqtt\Client\MqttClient(
                'broker.hivemq.com',
                1883,
                'Laravel_WebSocket_Client'
            );

            $connectionSettings = (new \PhpMqtt\Client\ConnectionSettings())
                ->setKeepAliveInterval(10)
                ->setConnectTimeout(60)
                ->setUseTls(false);

            $client->connect($connectionSettings, true);
            $client->publish($topic, $message, $qos);
            $client->disconnect();

            Log::info('WebSocket MQTT: Mensagem publicada via TCP fallback');
            return true;

        } catch (Exception $e) {
            Log::error('WebSocket MQTT: Erro ao publicar', [
                'error' => $e->getMessage(),
                'topic' => $topic
            ]);
            return false;
        }
    }

    public function publishSuccess(string $message): bool
    {
        $response = json_encode([
            'status' => 'success',
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'protocol' => 'websocket_fallback'
        ]);

        return $this->publish($this->config['topics']['saida'], $response);
    }

    public function publishError(string $message): bool
    {
        $response = json_encode([
            'status' => 'error',
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'protocol' => 'websocket_fallback'
        ]);

        return $this->publish($this->config['topics']['saida'], $response);
    }
}