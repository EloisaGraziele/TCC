<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MQTT Broker Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações do broker MQTT para o sistema de presença
    |
    */

    'host' => env('MQTT_HOST', 'hivemq'),
    'port' => env('MQTT_PORT', 1883),
    'protocol' => env('MQTT_PROTOCOL', 'tcp'), // tcp ou websocket
    'username' => env('MQTT_USERNAME', ''),
    'password' => env('MQTT_PASSWORD', ''),
    'client_id' => env('MQTT_CLIENT_ID', 'Presenca_Client'),
    
    /*
    |--------------------------------------------------------------------------
    | MQTT Topics
    |--------------------------------------------------------------------------
    |
    | Tópicos utilizados para comunicação MQTT
    |
    */
    
    'topics' => [
        'entrada' => env('MQTT_TOPIC_ENTRADA', 'Presenca/entrada'),
        'saida' => env('MQTT_TOPIC_SAIDA', 'Presenca/confirma'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    |
    | Configurações de conexão e timeout
    |
    */
    
    'connection_timeout' => 60,
    'keep_alive' => 10,
    'clean_session' => true,
];