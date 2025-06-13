#!/bin/bash

# Script de setup para ambiente Docker

echo "🐳 Configurando ambiente Docker para Suppliers API..."

# Copiar arquivo .env para Docker
if [ ! -f .env ]; then
    echo "📋 Copiando arquivo .env.docker para .env..."
    cp .env.docker .env
fi

# Subir containers
echo "🚀 Iniciando containers..."
docker-compose up -d

# Aguardar MySQL inicializar
echo "⏳ Aguardando MySQL inicializar..."
sleep 30

# Instalar dependências
echo "📦 Instalando dependências..."
docker-compose exec app composer install

# Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
docker-compose exec app php artisan key:generate

# Executar migrations
echo "🗄️ Executando migrations..."
docker-compose exec app php artisan migrate

# Executar seeders
echo "🌱 Populando banco com dados de exemplo..."
docker-compose exec app php artisan db:seed

# Limpar cache
echo "🧹 Limpando cache..."
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear

# Warm up cache
echo "🔥 Pré-carregando cache..."
docker-compose exec app php artisan suppliers:cache-clear --warm-up

echo "✅ Setup concluído!"
echo ""
echo "🌐 Aplicação disponível em: http://localhost:8000"
echo "🗄️ phpMyAdmin em: http://localhost:8080"
echo "💾 Redis Commander em: http://localhost:8081"
echo ""
echo "Para executar comandos no container:"
echo "docker-compose exec app php artisan [comando]"