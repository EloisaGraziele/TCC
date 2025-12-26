const mqtt = require('mqtt');
const axios = require('axios');

console.log('🌉 Iniciando MQTT WebSocket Bridge...');

// Conectar via WebSocket MQTT (mesma porta que ESP)
const client = mqtt.connect('ws://broker.hivemq.com:8000/mqtt', {
    clientId: 'Laravel_WebSocket_Bridge',
    keepalive: 60,
    clean: true
});

client.on('connect', () => {
    console.log('✅ Conectado ao broker via WebSocket (porta 8000)');
    console.log('👂 Escutando tópico: Presenca/saida');
    
    // Subscribe no tópico que ESP usa
    client.subscribe('Presenca/saida', (err) => {
        if (err) {
            console.error('❌ Erro ao subscribir:', err);
        } else {
            console.log('✅ Subscrito em Presenca/saida');
        }
    });
});

client.on('message', async (topic, message) => {
    const timestamp = new Date().toISOString();
    console.log(`[${timestamp}] 📨 Mensagem recebida no tópico ${topic}:`);
    console.log(message.toString());
    
    try {
        const data = JSON.parse(message.toString());
        
        if (data.mac && data.qrcode) {
            console.log('🔄 Enviando para Laravel...');
            
            // Enviar para Laravel via HTTP
            const response = await axios.post('http://localhost:8080/esp/presenca', data, {
                headers: { 'Content-Type': 'application/json' }
            });
            
            console.log('✅ Enviado para Laravel:', response.data);
        } else {
            console.log('❌ Formato inválido - esperado: mac e qrcode');
        }
    } catch (error) {
        console.error('❌ Erro ao processar:', error.message);
    }
});

client.on('error', (error) => {
    console.error('❌ Erro MQTT:', error);
});

console.log('🚀 Bridge ativo - ESP → WebSocket → Laravel');