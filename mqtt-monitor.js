const mqtt = require('mqtt');

console.log('🔍 Monitor MQTT Universal - Escutando TODOS os tópicos...');

// Conectar via WebSocket
const client = mqtt.connect('ws://broker.hivemq.com:8000/mqtt', {
    clientId: 'Monitor_Universal',
    keepalive: 60,
    clean: true
});

client.on('connect', () => {
    console.log('✅ Monitor conectado via WebSocket');
    
    // Subscribe em TODOS os tópicos
    client.subscribe('#', (err) => {
        if (err) {
            console.error('❌ Erro ao subscribir:', err);
        } else {
            console.log('👂 Monitorando TODOS os tópicos (#)');
            console.log('📡 Aguardando mensagens...');
        }
    });
});

client.on('message', (topic, message) => {
    const timestamp = new Date().toLocaleString();
    console.log(`\n[${timestamp}] 📨 MENSAGEM DETECTADA:`);
    console.log(`📍 Tópico: ${topic}`);
    console.log(`📄 Conteúdo: ${message.toString()}`);
    console.log('─'.repeat(50));
});

client.on('error', (error) => {
    console.error('❌ Erro:', error);
});