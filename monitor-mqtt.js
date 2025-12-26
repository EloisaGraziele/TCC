const mqtt = require('mqtt');

console.log('🔍 Monitor MQTT - Detectando todas as mensagens...');

const client = mqtt.connect('mqtt://broker.hivemq.com:1883', {
    clientId: 'Monitor_' + Math.random().toString(16).substr(2, 8)
});

client.on('connect', () => {
    console.log('✅ Conectado ao broker MQTT');
    
    // Subscrever a todos os tópicos Presenca
    client.subscribe('Presenca/+', (err) => {
        if (err) {
            console.error('❌ Erro ao subscrever:', err);
        } else {
            console.log('👂 Monitorando tópicos Presenca/*');
        }
    });
});

client.on('message', (topic, message) => {
    const timestamp = new Date().toISOString();
    console.log(`\n📨 [${timestamp}] Tópico: ${topic}`);
    console.log(`📄 Mensagem: ${message.toString()}`);
    console.log('─'.repeat(50));
});

client.on('error', (err) => {
    console.error('❌ Erro MQTT:', err);
});

// Manter rodando por 30 segundos
setTimeout(() => {
    console.log('\n⏰ Tempo esgotado - Finalizando monitor');
    client.end();
    process.exit(0);
}, 30000);