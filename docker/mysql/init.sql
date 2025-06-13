-- Criar banco de dados para a aplicação
CREATE DATABASE IF NOT EXISTS suppliers_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar banco de dados para testes
CREATE DATABASE IF NOT EXISTS suppliers_api_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Garantir privilégios para o usuário laravel
GRANT ALL PRIVILEGES ON suppliers_api.* TO 'laravel'@'%';
GRANT ALL PRIVILEGES ON suppliers_api_test.* TO 'laravel'@'%';
FLUSH PRIVILEGES;