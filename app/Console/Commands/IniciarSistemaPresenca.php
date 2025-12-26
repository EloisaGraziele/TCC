<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MqttServiceFactory;

class IniciarSistemaPresenca extends Command
{
    protected $signature = 'sistema:iniciar';
    protected $description = 'Inicia o sistema completo de presença com listener MQTT ativo';

    public function handle()
    {
        $this->info('🚀 Iniciando Sistema de Presença...');
        
        // Enviar mensagem de sistema ativo
        try {
            $mqttService = MqttServiceFactory::create();
            $mqttService->publish('Presenca/confirma', json_encode([
                'status' => 'SISTEMA_INICIADO',
                'message' => 'Sistema de presença iniciado e funcionando',
                'timestamp' => now()->toISOString(),
                'version' => '1.0'
            ]));
            
            $this->info('📡 Status enviado: Sistema iniciado');
        } catch (\Exception $e) {
            $this->warn('⚠️ Não foi possível enviar status inicial: ' . $e->getMessage());
        }
        
        $this->info('🎧 Iniciando listener MQTT...');
        $this->call('mqtt:listen', ['--daemon' => true]);
        
        return 0;
    }
}