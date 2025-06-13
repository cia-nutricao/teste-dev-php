## Teste para Desenvolvedor PHP/Laravel

Bem-vindo ao teste de desenvolvimento para a posição de Desenvolvedor PHP/Laravel. 

O objetivo deste teste é desenvolver uma API Rest para o cadastro de fornecedores, permitindo a busca por CNPJ ou CPF, utilizando Laravel no backend.

## Descrição do Projeto

### Backend (API Laravel):

#### CRUD de Fornecedores:
- **Criar Fornecedor:**
  - Permita o cadastro de fornecedores usando CNPJ ou CPF, incluindo informações como nome/nome da empresa, contato, endereço, etc.
  - Valide a integridade e o formato dos dados, como o formato correto de CNPJ/CPF e a obrigatoriedade de campos.

- **Editar Fornecedor:**
  - Facilite a atualização das informações de fornecedores, mantendo a validação dos dados.

- **Excluir Fornecedor:**
  - Possibilite a remoção segura de fornecedores.

- **Listar Fornecedores:**
  - Apresente uma lista paginada de fornecedores, com filtragem e ordenação.

#### Migrations:
- Utilize migrations do Laravel para definir a estrutura do banco de dados, garantindo uma boa organização e facilidade de manutenção.

## Requisitos

### Backend:
- Implementar busca por CNPJ na [BrasilAPI](https://brasilapi.com.br/docs#tag/CNPJ/paths/~1cnpj~1v1~1{cnpj}/get) ou qualquer outro endpoint público.

## Tecnologias a serem utilizadas
- Framework Laravel (PHP) 9.x ou superior
- MySQL ou Postgres

## Critérios de Avaliação
- Adesão aos requisitos funcionais e técnicos.
- Qualidade do código, incluindo organização, padrões de desenvolvimento e segurança.
- Documentação do projeto, incluindo um README detalhado com instruções de instalação e operação.

## Bônus
- Implementação de Repository Pattern.
- Implementação de testes automatizados.
- Dockerização do ambiente de desenvolvimento.
- Implementação de cache para otimizar o desempenho.

## Entrega
- Para iniciar o teste, faça um fork deste repositório; Se você apenas clonar o repositório não vai conseguir fazer push.
- Crie uma branch com o nome que desejar;
- Altere o arquivo README.md com as informações necessárias para executar o seu teste (comandos, migrations, seeds, etc);
- Depois de finalizado, envie-nos o pull request;


