#!/bin/bash

# Habilita o modo "strict" para parar o script se algum comando falhar
set -e

echo "🚀 Iniciando entrypoint..."

# Aguarda o banco de dados ficar disponível
echo "⏳ Aguardando o banco de dados estar disponível..."
until nc -z -v -w30 db 5432; do
    echo "⚠️  Aguardando conexão com o banco de dados..."
    sleep 1
done
echo "✅ Banco de dados disponível!"

# Executa as migrations
echo "📜 Executando migrações do Laravel..."
php artisan migrate --no-interaction --force

# Se precisar rodar seeds automaticamente, descomente a linha abaixo:
php artisan db:seed --no-interaction --force

# Inicia o Apache
echo "🚀 Iniciando o Apache..."
exec apache2-foreground
