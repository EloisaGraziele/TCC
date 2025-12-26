#!/bin/bash

# Script para manter o Sistema de Presença sempre ativo
# Execute: ./manter-sistema-ativo.sh

echo "🚀 Iniciando Sistema de Presença em modo contínuo..."

while true; do
    echo "📅 $(date): Verificando sistema..."
    
    # Verificar se o listener está rodando
    if ! pgrep -f "mqtt:listen" > /dev/null; then
        echo "⚠️ Listener não encontrado. Reiniciando..."
        
        # Iniciar o sistema
        cd /home/ser/projetos/sistema-presenca
        ./vendor/bin/sail artisan sistema:iniciar &
        
        echo "✅ Sistema reiniciado"
    else
        echo "✅ Sistema funcionando normalmente"
    fi
    
    # Aguardar 30 segundos antes da próxima verificação
    sleep 30
done