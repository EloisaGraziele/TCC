const mqtt = require('mqtt');

console.log('🧪 Simulando ESP via WebSocket...');

// Conectar via WebSocket (mesma configuração do ESP)
const client = mqtt.connect('ws://broker.hivemq.com:8000/mqtt', {
    clientId: 'ESP_Simulator',
    keepalive: 60,
    clean: true
});

client.on('connect', () => {
    console.log('✅ ESP Simulator conectado via WebSocket');
    
    // Simular dados do ESP
    const espData = {
        mac: '84:F3:EB:B4:71:EA',
        qrcode: 'eyJpdiI6InhTUjdYb1kvc1U5bEZQR0V3SlY5bGc9PSIsInZhbHVlIjoiUElvbUg0eWtLQmdoWTBST29KbHRuREh1MVdFd25RcFlibDV3b3JLRlZpTT0iLCJtYWMiOiJUa0dqRDFteXp6RHo0VzVHalRoanlEeVczajRHM0d6bWgybGpqRDBEMmlpR25qaW49In0='
    };
    
    console.log('📤 Enviando dados para Presenca/saida...');
    console.log('Dados:', JSON.stringify(espData));
    
    client.publish('Presenca/saida', JSON.stringify(espData), (err) => {
        if (err) {
            console.error('❌ Erro ao publicar:', err);
        } else {
            console.log('✅ Mensagem enviada com sucesso!');
        }
        
        setTimeout(() => {
            client.end();
            process.exit(0);
        }, 2000);
    });
});

client.on('error', (error) => {
    console.error('❌ Erro:', error);
});