<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Log;

class MqttSubscriber
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $clientId;
    private $socket;
    private $connected = false;

    public function __construct()
    {
        $this->host = env('MQTT_HOST', 'localhost');
        $this->port = env('MQTT_PORT', 1883);
        $this->username = env('MQTT_USERNAME', '');
        $this->password = env('MQTT_PASSWORD', '');
        $this->clientId = env('MQTT_CLIENT_ID', 'laravel_subscriber');
    }

    public function connect()
    {
        try {
            $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if (!$this->socket) {
                throw new Exception('Erro ao criar socket');
            }

            $result = socket_connect($this->socket, $this->host, $this->port);
            if (!$result) {
                throw new Exception('Erro ao conectar ao broker MQTT');
            }

            $this->sendConnectPacket();
            $this->connected = true;
            
            Log::info('MQTT: Conectado ao broker ' . $this->host . ':' . $this->port);
            return true;
        } catch (Exception $e) {
            Log::error('MQTT Connect Error: ' . $e->getMessage());
            return false;
        }
    }

    public function subscribe($topic, $callback = null)
    {
        if (!$this->connected) {
            throw new Exception('Não conectado ao broker MQTT');
        }

        try {
            $this->sendSubscribePacket($topic);
            Log::info('MQTT: Inscrito no tópico ' . $topic);

            while ($this->connected) {
                $data = socket_read($this->socket, 1024);
                if ($data) {
                    $this->processMessage($data, $callback);
                }
                usleep(100000);
            }
        } catch (Exception $e) {
            Log::error('MQTT Subscribe Error: ' . $e->getMessage());
        }
    }

    public function disconnect()
    {
        if ($this->socket) {
            socket_close($this->socket);
            $this->connected = false;
            Log::info('MQTT: Desconectado do broker');
        }
    }

    private function sendConnectPacket()
    {
        $packet = chr(0x10);
        
        $payload = '';
        $payload .= $this->encodeString('MQTT');
        $payload .= chr(0x04);
        $payload .= chr(0x02);
        $payload .= chr(0x00) . chr(0x3C);
        $payload .= $this->encodeString($this->clientId);

        $packet .= chr(strlen($payload)) . $payload;
        socket_write($this->socket, $packet);
    }

    private function sendSubscribePacket($topic)
    {
        $packet = chr(0x82);
        
        $payload = '';
        $payload .= chr(0x00) . chr(0x01);
        $payload .= $this->encodeString($topic);
        $payload .= chr(0x00);

        $packet .= chr(strlen($payload)) . $payload;
        socket_write($this->socket, $packet);
    }

    private function encodeString($string)
    {
        $length = strlen($string);
        return chr($length >> 8) . chr($length & 0xFF) . $string;
    }

    private function processMessage($data, $callback = null)
    {
        $messageType = ord($data[0]) >> 4;
        
        if ($messageType == 3) {
            $topic = $this->extractTopic($data);
            $message = $this->extractMessage($data);
            
            Log::info('MQTT Message Received', [
                'topic' => $topic,
                'message' => $message
            ]);

            if ($callback && is_callable($callback)) {
                call_user_func($callback, $topic, $message);
            }
        }
    }

    private function extractTopic($data)
    {
        $pos = 2;
        $topicLength = (ord($data[$pos]) << 8) + ord($data[$pos + 1]);
        return substr($data, $pos + 2, $topicLength);
    }

    private function extractMessage($data)
    {
        $pos = 2;
        $topicLength = (ord($data[$pos]) << 8) + ord($data[$pos + 1]);
        $messageStart = $pos + 2 + $topicLength;
        return substr($data, $messageStart);
    }
}