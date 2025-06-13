# Makefile para Suppliers API Docker

.PHONY: help setup up down restart logs shell test cache-clear migrate seed

# Comando padrão
help:
	@echo "🐳 Suppliers API - Comandos Docker Disponíveis:"
	@echo ""
	@echo "  setup         - Configurar ambiente completo (primeira vez)"
	@echo "  up            - Iniciar containers"
	@echo "  down          - Parar containers"
	@echo "  restart       - Reiniciar containers"
	@echo "  logs          - Ver logs dos containers"
	@echo "  shell         - Acessar shell do container da aplicação"
	@echo "  test          - Executar testes"
	@echo "  cache-clear   - Limpar cache"
	@echo "  migrate       - Executar migrations"
	@echo "  seed          - Executar seeders"
	@echo "  fresh         - Reset completo do banco com seeders"
	@echo ""

# Setup inicial completo
setup:
	@echo "🐳 Configurando ambiente Docker..."
	@if not exist .env copy .env.docker .env
	docker-compose up -d
	@echo "⏳ Aguardando MySQL inicializar..."
	timeout /t 30 /nobreak > nul
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate
	docker-compose exec app php artisan db:seed
	docker-compose exec app php artisan suppliers:cache-clear --warm-up
	@echo "✅ Setup concluído!"
	@echo "🌐 Aplicação: http://localhost:8000"
	@echo "🗄️ phpMyAdmin: http://localhost:8080"
	@echo "💾 Redis Commander: http://localhost:8081"

# Iniciar containers
up:
	docker-compose up -d

# Parar containers
down:
	docker-compose down

# Reiniciar containers
restart:
	docker-compose restart

# Ver logs
logs:
	docker-compose logs -f

# Acessar shell do container
shell:
	docker-compose exec app bash

# Executar testes
test:
	docker-compose exec app php artisan test

# Limpar cache
cache-clear:
	docker-compose exec app php artisan suppliers:cache-clear --warm-up

# Executar migrations
migrate:
	docker-compose exec app php artisan migrate

# Executar seeders
seed:
	docker-compose exec app php artisan db:seed

# Reset completo do banco
fresh:
	docker-compose exec app php artisan migrate:fresh --seed
	docker-compose exec app php artisan suppliers:cache-clear --warm-up