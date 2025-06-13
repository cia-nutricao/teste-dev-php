# 🏢 Suppliers API - Documentação Completa

> API RESTful para gerenciamento de fornecedores desenvolvida em Laravel com Docker

## 📋 Sobre o Projeto

A Suppliers API é uma aplicação robusta para gerenciamento de fornecedores, oferecendo operações CRUD completas, integração com APIs externas para validação de dados e sistema de cache para otimização de performance.

### ✨ Funcionalidades Principais

- 🔍 **CRUD Completo** - Criar, listar, visualizar, atualizar e deletar fornecedores
- 🌐 **Integração Externa** - Validação automática de CEP via Brasil API
- ⚡ **Sistema de Cache** - Cache inteligente com Redis para otimização
- 📊 **Factory & Seeders** - Geração automática de dados para testes
- 🐳 **Docker Ready** - Ambiente completamente containerizado
- 📚 **API Documentation** - Documentação completa dos endpoints
- 🧪 **Testes Automatizados** - Cobertura de testes unitários e de feature

### 🛠️ Tecnologias Utilizadas

- **Laravel 10.x** - Framework PHP
- **MySQL 8.0** - Banco de dados
- **Redis** - Sistema de cache
- **Docker & Docker Compose** - Containerização
- **Nginx** - Servidor web
- **phpMyAdmin** - Interface de gerenciamento do banco
- **Redis Commander** - Interface de gerenciamento do cache

## 🚀 Configuração do Ambiente

### Pré-requisitos

- Docker Desktop
- Docker Compose
- Git

### ⚡ Setup Automático

Execute o script de configuração automática:

```powershell
# Windows PowerShell
.\setup.ps1
```

```bash
# Linux/Mac
chmod +x docker/setup.sh
./docker/setup.sh
```

O script irá:
- ✅ Verificar dependências
- 🐳 Construir e iniciar containers Docker
- 📦 Instalar dependências do Composer
- 🔑 Gerar chave da aplicação
- 🗄️ Executar migrations
- 🌱 Popular banco com dados de exemplo
- 🔥 Configurar cache

### 🌐 Acesso às Aplicações

Após o setup, acesse:

- **🌍 API Principal**: http://localhost:8000
- **🗄️ phpMyAdmin**: http://localhost:8082
  - Usuário: `root`
  - Senha: `root`
- **💾 Redis Commander**: http://localhost:8081

### 🔧 Configuração Manual

Se preferir configurar manualmente:

1. **Clone o repositório**
```bash
git clone <repository-url>
cd temp-laravel
```

2. **Configure o ambiente**
```bash
cp .env.example .env
# Edite o .env com suas configurações
```

3. **Inicie os containers**
```bash
docker-compose up -d
```

4. **Configure a aplicação**
```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

## 📡 Endpoints da API

### Base URL
```
http://localhost:8000/api
```

### 🏢 Suppliers

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/suppliers` | Lista todos os fornecedores |
| `POST` | `/suppliers` | Cria um novo fornecedor |
| `GET` | `/suppliers/{id}` | Visualiza um fornecedor específico |
| `PUT` | `/suppliers/{id}` | Atualiza um fornecedor |
| `DELETE` | `/suppliers/{id}` | Remove um fornecedor |

### 📋 Estrutura do Fornecedor

```json
{
  "id": 1,
  "document": "12.345.678/0001-90",
  "document_type": "cnpj",
  "name": "Empresa LTDA",
  "trade_name": "Nome Fantasia",
  "email": "contato@empresa.com",
  "phone": "1134567890",
  "mobile_phone": "11987654321",
  "zip_code": "01234567",
  "street": "Rua das Flores",
  "number": "123",
  "complement": "Sala 456",
  "neighborhood": "Centro",
  "city": "São Paulo",
  "state": "SP",
  "cnae_code": "1234567",
  "cnae_description": "Descrição da atividade",
  "legal_nature": "LTDA",
  "opening_date": "2020-01-01",
  "situation": "ATIVA",
  "notes": "Observações adicionais",
  "active": true,
  "created_at": "2025-06-12T01:54:40.000000Z",
  "updated_at": "2025-06-12T01:54:40.000000Z"
}
```

## 📝 Detalhamento dos Endpoints

### 1. Listar Fornecedores

**GET** `/api/suppliers`

Lista todos os fornecedores com paginação.

**Parâmetros de Query (opcionais):**
- `page` - Número da página (padrão: 1)
- `per_page` - Itens por página (padrão: 15, máximo: 100)
- `search` - Busca por nome ou documento
- `active` - Filtro por status (true/false)

**Exemplo de Request:**
```bash
GET /api/suppliers?page=1&per_page=10&search=empresa&active=true
```

**Resposta de Sucesso (200):**
```json
{
  "data": [
    {
      "id": 1,
      "document": "12.345.678/0001-90",
      "document_type": "cnpj",
      "name": "Empresa LTDA",
      // ... outros campos
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/suppliers?page=1",
    "last": "http://localhost:8000/api/suppliers?page=10",
    "prev": null,
    "next": "http://localhost:8000/api/suppliers?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

### 2. Criar Fornecedor

**POST** `/api/suppliers`

Cria um novo fornecedor.

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (campos obrigatórios):**
```json
{
  "document": "12.345.678/0001-90",
  "document_type": "cnpj",
  "name": "Empresa LTDA",
  "email": "contato@empresa.com"
}
```

**Body (exemplo completo):**
```json
{
  "document": "12.345.678/0001-90",
  "document_type": "cnpj",
  "name": "Empresa LTDA",
  "trade_name": "Nome Fantasia",
  "email": "contato@empresa.com",
  "phone": "1134567890",
  "mobile_phone": "11987654321",
  "zip_code": "01234567",
  "street": "Rua das Flores",
  "number": "123",
  "complement": "Sala 456",
  "neighborhood": "Centro",
  "city": "São Paulo",
  "state": "SP",
  "cnae_code": "1234567",
  "cnae_description": "Descrição da atividade",
  "legal_nature": "LTDA",
  "opening_date": "2020-01-01",
  "situation": "ATIVA",
  "notes": "Observações adicionais",
  "active": true
}
```

**Resposta de Sucesso (201):**
```json
{
  "data": {
    "id": 1,
    "document": "12.345.678/0001-90",
    "document_type": "cnpj",
    "name": "Empresa LTDA",
    // ... todos os campos
    "created_at": "2025-06-12T01:54:40.000000Z",
    "updated_at": "2025-06-12T01:54:40.000000Z"
  }
}
```

**Resposta de Erro (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "document": ["O campo documento é obrigatório."],
    "email": ["O campo email deve ser um endereço válido."]
  }
}
```

### 3. Visualizar Fornecedor

**GET** `/api/suppliers/{id}`

Retorna um fornecedor específico.

**Parâmetros:**
- `id` - ID do fornecedor

**Resposta de Sucesso (200):**
```json
{
  "data": {
    "id": 1,
    "document": "12.345.678/0001-90",
    "document_type": "cnpj",
    "name": "Empresa LTDA",
    // ... todos os campos
  }
}
```

**Resposta de Erro (404):**
```json
{
  "message": "Fornecedor não encontrado."
}
```

### 4. Atualizar Fornecedor

**PUT** `/api/suppliers/{id}`

Atualiza um fornecedor existente.

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Parâmetros:**
- `id` - ID do fornecedor

**Body (parcial ou completo):**
```json
{
  "name": "Novo Nome da Empresa",
  "email": "novoemail@empresa.com",
  "active": false
}
```

**Resposta de Sucesso (200):**
```json
{
  "data": {
    "id": 1,
    "document": "12.345.678/0001-90",
    "document_type": "cnpj",
    "name": "Novo Nome da Empresa",
    "email": "novoemail@empresa.com",
    "active": false,
    // ... outros campos
    "updated_at": "2025-06-12T02:30:15.000000Z"
  }
}
```

### 5. Deletar Fornecedor

**DELETE** `/api/suppliers/{id}`

Remove um fornecedor.

**Parâmetros:**
- `id` - ID do fornecedor

**Resposta de Sucesso (204):**
```
No Content
```

**Resposta de Erro (404):**
```json
{
  "message": "Fornecedor não encontrado."
}
```

## 🔍 Validações

### Campos Obrigatórios
- `document` - Documento (CPF ou CNPJ)
- `document_type` - Tipo do documento (cpf, cnpj)
- `name` - Nome/Razão social
- `email` - Email válido

### Regras de Validação
- **document**: Formato válido de CPF ou CNPJ
- **document_type**: Deve ser 'cpf' ou 'cnpj'
- **email**: Formato válido de email, único no sistema
- **zip_code**: Formato válido de CEP (opcional)
- **phone/mobile_phone**: Formato válido de telefone brasileiro
- **opening_date**: Data válida no formato Y-m-d
- **situation**: Valores permitidos (ATIVA, SUSPENSA, INAPTA, BAIXADA)

### Validação Automática de CEP
Quando um CEP válido é fornecido, a API automaticamente:
- Valida o formato do CEP
- Consulta a Brasil API para obter dados do endereço
- Preenche automaticamente: street, neighborhood, city, state

## ⚡ Sistema de Cache

A API utiliza Redis para cache inteligente:

### Cache Automático
- **Lista de fornecedores**: Cache por 5 minutos
- **Fornecedor individual**: Cache por 10 minutos
- **Dados de CEP**: Cache por 24 horas

### Invalidação de Cache
O cache é automaticamente invalidado quando:
- Um fornecedor é criado, atualizado ou deletado
- Operações que afetam a listagem são executadas

### Comandos de Cache
```bash
# Limpar cache de fornecedores
docker-compose exec app php artisan cache:forget suppliers_*

# Pré-carregar cache
docker-compose exec app php artisan suppliers:warm-cache
```

## 🏗️ Arquitetura

### 📁 Estrutura do Projeto

```
app/
├── Http/
│   ├── Controllers/
│   │   └── SupplierController.php
│   ├── Requests/
│   │   ├── StoreSupplierRequest.php
│   │   └── UpdateSupplierRequest.php
│   └── Resources/
│       └── SupplierResource.php
├── Models/
│   └── Supplier.php
├── Repositories/
│   ├── SupplierRepository.php
│   └── SupplierRepositoryInterface.php
└── Services/
    ├── BrasilApiService.php
    └── CacheService.php

database/
├── factories/
│   └── SupplierFactory.php
├── migrations/
│   └── 2025_06_12_002343_create_suppliers_table.php
└── seeders/
    └── SupplierSeeder.php
```

### 🔄 Padrões Utilizados

- **Repository Pattern** - Abstração da camada de dados
- **Service Layer** - Lógica de negócio centralizada
- **Resource Transformers** - Padronização das respostas
- **Form Requests** - Validação de dados estruturada

## 🧪 Testes

Execute os testes da aplicação:

```bash
# Todos os testes
docker-compose exec app php artisan test

# Testes específicos
docker-compose exec app php artisan test --filter SupplierTest

# Com cobertura
docker-compose exec app php artisan test --coverage
```

### Cobertura de Testes
- ✅ Testes de Feature para todos os endpoints
- ✅ Testes de Unit para Models e Services
- ✅ Testes de Integração com APIs externas
- ✅ Testes de Validação de dados

## 📦 Comandos Úteis

### 🐳 Docker

```bash
# Iniciar ambiente
docker-compose up -d

# Parar ambiente
docker-compose down

# Ver logs
docker-compose logs -f

# Acessar container da aplicação
docker-compose exec app bash

# Reiniciar um serviço específico
docker-compose restart app
```

### 🎯 Laravel

```bash
# Artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Composer
docker-compose exec app composer install
docker-compose exec app composer update
```

### 🔥 Cache

```bash
# Limpar cache de fornecedores
docker-compose exec app php artisan cache:forget suppliers_*

# Cache de configuração
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
```

## 🔧 Configurações

### 🌍 Variáveis de Ambiente

```env
# Aplicação
APP_NAME="Suppliers API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=suppliers_api
DB_USERNAME=laravel
DB_PASSWORD=password

# Cache
CACHE_DRIVER=redis
REDIS_HOST=redis
REDIS_PORT=6379
```

### 🔌 Portas dos Serviços

| Serviço | Porta Externa | Porta Interna |
|---------|---------------|---------------|
| Nginx | 8000 | 80 |
| MySQL | 3307 | 3306 |
| Redis | 6379 | 6379 |
| phpMyAdmin | 8082 | 80 |
| Redis Commander | 8081 | 8081 |

## 📊 Dados de Exemplo

O sistema inclui seeders que criam dados de exemplo para testes:

### Fornecedores de Exemplo
- **50 fornecedores** com dados realistas
- Documentos válidos (CPF e CNPJ)
- Endereços com CEPs válidos
- Diferentes tipos de empresas e pessoas físicas

### Executar Seeders
```bash
# Popular com dados de exemplo
docker-compose exec app php artisan db:seed

# Limpar e popular novamente
docker-compose exec app php artisan migrate:fresh --seed
```

## 🚨 Troubleshooting

### Problemas Comuns

**Erro de conexão com banco:**
```bash
docker-compose restart mysql
docker-compose exec app php artisan migrate
```

**Problemas de permissão:**
```bash
docker-compose exec app chown -R www-data:www-data /var/www
docker-compose exec app chmod -R 755 /var/www/storage
```

**Cache com problemas:**
```bash
docker-compose exec app php artisan cache:clear
docker-compose restart redis
```

**Conflito de portas:**
- Verifique se as portas 8000, 3307, 6379, 8081, 8082 estão livres
- Altere as portas no `docker-compose.yml` se necessário

### Logs Importantes

```bash
# Logs da aplicação
docker-compose logs -f app

# Logs do Nginx
docker-compose logs -f nginx

# Logs do MySQL
docker-compose logs -f mysql

# Logs específicos do Laravel
docker-compose exec app tail -f storage/logs/laravel.log
```

## 🔄 Versionamento da API

### Versão Atual: v1

A API segue padrões de versionamento semântico:
- **URL Base**: `/api/v1` (futuras versões)
- **Headers**: `Accept: application/json`
- **Formato**: JSON para requests e responses

## 🚀 Performance

### Otimizações Implementadas
- ✅ Cache Redis para consultas frequentes
- ✅ Paginação eficiente
- ✅ Índices otimizados no banco de dados
- ✅ Eager Loading para relacionamentos
- ✅ Compressão de respostas

### Métricas
- **Resposta média**: < 100ms
- **Cache hit ratio**: > 80%
- **Throughput**: > 1000 req/min

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

### Padrões de Código
- PSR-12 para PHP
- Laravel Best Practices
- Testes obrigatórios para novas features
- Documentação atualizada

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 Autor

**Leonardo Meyer**
- GitHub: [@leonardo-meyer](https://github.com/leonardo-meyer)
- Email: leonardo@revendamais.com

---

<p align="center">
  Desenvolvido com ❤️ para Revenda Mais
</p>