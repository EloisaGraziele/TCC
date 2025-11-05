<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MqttSubscriber;
use App\Models\Frequencia;
use App\Models\Aluno;
use App\Models\Dispositivo;
use App\Models\CalendarioEscolar;
use App\Services\CriptografiaService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MqttController extends Controller
{
    public function startSubscriber()
    {
        try {
            $mqtt = new MqttSubscriber();
            
            if (!$mqtt->connect()) {
                return response()->json(['error' => 'Falha ao conectar ao broker MQTT'], 500);
            }

            // Subscribe ao tópico de presença
            $mqtt->subscribe('Presença/saida', function($topic, $message) {
                $this->processPresenceMessage($topic, $message);
            });

            return response()->json(['message' => 'MQTT Subscriber iniciado']);
            
        } catch (\Exception $e) {
            Log::error('MQTT Controller Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function processPresenceMessage($topic, $message)
    {
        try {
            $data = json_decode($message, true);
            
            if (!$data || !isset($data['nome'], $data['matricula'], $data['cpf'], $data['dispositivo'])) {
                Log::warning('Mensagem MQTT inválida: ' . $message);
                return;
            }

            // Verificar se é dia letivo
            $hoje = Carbon::today();
            $diaLetivo = CalendarioEscolar::where('data', $hoje)
                ->where('tipo_dia', 'LETIVO')
                ->where('ativo', true)
                ->exists();

            if (!$diaLetivo) {
                Log::info('Presença ignorada - não é dia letivo: ' . $hoje->format('Y-m-d'));
                return;
            }

            // Verificar dispositivo cadastrado
            $dispositivo = Dispositivo::where('mac_address', $data['dispositivo'])
                ->where('autorizado', true)
                ->first();

            if (!$dispositivo) {
                Log::warning('Dispositivo não autorizado: ' . $data['dispositivo']);
                return;
            }

            // Descriptografar dados
            $nome = CriptografiaService::decrypt($data['nome']);
            $matricula = CriptografiaService::decrypt($data['matricula']);
            $cpf = CriptografiaService::decrypt($data['cpf']);

            if (!$nome || !$matricula || !$cpf) {
                Log::warning('Falha na descriptografia dos dados');
                return;
            }

            // Buscar aluno
            $aluno = Aluno::where('matricula', $matricula)
                ->where('cpf', $cpf)
                ->first();
            
            if (!$aluno) {
                Log::warning('Aluno não encontrado - Matrícula: ' . $matricula);
                return;
            }

            // Buscar ou criar frequência
            $frequencia = Frequencia::where('aluno_id', $aluno->id)
                ->whereDate('data_presenca', $hoje)
                ->first();

            if ($frequencia) {
                if ($frequencia->status === 'presente') {
                    Log::info('Presença já registrada hoje para: ' . $aluno->nome);
                    return;
                }
                // Atualizar status
                $frequencia->update([
                    'status' => 'presente',
                    'dispositivo_origem' => $dispositivo->mac_address,
                    'observacoes' => 'Registrado via MQTT'
                ]);
            } else {
                // Criar nova frequência
                $frequencia = Frequencia::create([
                    'aluno_id' => $aluno->id,
                    'data_presenca' => now(),
                    'status' => 'presente',
                    'dispositivo_origem' => $dispositivo->mac_address,
                    'observacoes' => 'Registrado via MQTT'
                ]);
            }

            Log::info('Presença registrada via MQTT', [
                'aluno' => $aluno->nome,
                'matricula' => $aluno->matricula,
                'dispositivo' => $dispositivo->mac_address
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar mensagem MQTT: ' . $e->getMessage());
        }
    }

    public function testConnection()
    {
        try {
            $mqtt = new MqttSubscriber();
            $connected = $mqtt->connect();
            
            if ($connected) {
                $mqtt->disconnect();
                return response()->json(['message' => 'Conexão MQTT OK']);
            } else {
                return response()->json(['error' => 'Falha na conexão MQTT'], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}