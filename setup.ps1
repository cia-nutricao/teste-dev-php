# Script de setup para Windows PowerShell
# Suppliers API Docker Setup

Write-Host "🐳 Configurando ambiente Docker para Suppliers API..." -ForegroundColor Cyan

# Verificar se Docker está rodando
try {
    docker --version | Out-Null
    Write-Host "✓ Docker encontrado" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker não encontrado ou não está rodando" -ForegroundColor Red
    exit 1
}

# Verificar se docker-compose está disponível
try {
    docker-compose --version | Out-Null
    Write-Host "✓ Docker Compose encontrado" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker Compose não encontrado" -ForegroundColor Red
    exit 1
}

# Copiar arquivo .env se não existir
if (-not (Test-Path ".env")) {
    Write-Host "📋 Copiando arquivo .env.docker para .env..." -ForegroundColor Yellow
    Copy-Item ".env.docker" ".env"
    Write-Host "✓ Arquivo .env criado" -ForegroundColor Green
} else {
    Write-Host "✓ Arquivo .env já existe" -ForegroundColor Green
}

# Subir containers
Write-Host "🚀 Iniciando containers..." -ForegroundColor Cyan
docker-compose up -d

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erro ao iniciar containers" -ForegroundColor Red
    exit 1
}

# Aguardar MySQL inicializar
Write-Host "⏳ Aguardando MySQL inicializar (30 segundos)..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Instalar dependências
Write-Host "📦 Instalando dependências..." -ForegroundColor Cyan
docker-compose exec app composer install

# Gerar chave da aplicação
Write-Host "🔑 Gerando chave da aplicação..." -ForegroundColor Cyan
docker-compose exec app php artisan key:generate

# Executar migrations
Write-Host "🗄️ Executando migrations..." -ForegroundColor Cyan
docker-compose exec app php artisan migrate

# Executar seeders
Write-Host "🌱 Populando banco com dados de exemplo..." -ForegroundColor Cyan
docker-compose exec app php artisan db:seed

# Limpar e pré-carregar cache
Write-Host "🔥 Pré-carregando cache..." -ForegroundColor Cyan
docker-compose exec app php artisan suppliers:cache-clear --warm-up

Write-Host ""
Write-Host "✅ Setup concluído com sucesso!" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Aplicação disponível em: http://localhost:8000" -ForegroundColor Cyan
Write-Host "🗄️ phpMyAdmin em: http://localhost:8080" -ForegroundColor Cyan
Write-Host "💾 Redis Commander em: http://localhost:8081" -ForegroundColor Cyan
Write-Host ""
Write-Host "📋 Comandos úteis:" -ForegroundColor Yellow
Write-Host "  docker-compose up -d          # Iniciar containers"
Write-Host "  docker-compose down           # Parar containers"
Write-Host "  docker-compose logs -f        # Ver logs"
Write-Host "  docker-compose exec app bash  # Acessar shell"