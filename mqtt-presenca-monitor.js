const mqtt = require('mqtt');

console.log('🔍 Monitor Presença - Escutando tópicos Presenca/*...');

const client = mqtt.connect('ws://broker.hivemq.com:8000/mqtt', {
    clientId: 'Monitor_Presenca',
    keepalive: 60,
    clean: true
});

client.on('connect', () => {
    console.log('✅ Monitor conectado via WebSocket');
    
    // Subscribe nos tópicos Presenca
    const topics = ['Presenca/entrada', 'Presenca/saida', 'Presenca/confirma'];
    
    topics.forEach(topic => {
        client.subscribe(topic, (err) => {
            if (err) {
                console.error(`❌ Erro ao subscribir ${topic}:`, err);
            } else {
                console.log(`👂 Monitorando: ${topic}`);
            }
        });
    });
    
    console.log('📡 Aguardando mensagens do ESP...');
});

client.on('message', (topic, message) => {
    const timestamp = new Date().toLocaleString();
    console.log(`\n[${timestamp}] 🎯 ESP DETECTADO!`);
    console.log(`📍 Tópico: ${topic}`);
    console.log(`📄 Mensagem: ${message.toString()}`);
    console.log('🔥 MENSAGEM DO ESP RECEBIDA!');
    console.log('─'.repeat(50));
});

client.on('error', (error) => {
    console.error('❌ Erro:', error);
});