<?php

namespace App\Services;

use App\Contracts\MqttPublisherInterface;

class MqttServiceFactory
{
    public static function create(): MqttPublisherInterface
    {
        $protocol = config('mqtt.protocol', 'tcp');
        
        return match($protocol) {
            'websocket' => new WebSocketMqttService(),
            'tcp' => new MqttService(),
            default => new MqttService()
        };
    }
}