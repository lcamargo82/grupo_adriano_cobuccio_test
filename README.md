# Projeto Test Grupo Adriano Cobuccio

Este projeto contém uma API desenvolvida em Laravel versão 10 e um frontend desenvolvido em Vuej v3. A seguir, estão as instruções para configurar e executar o projeto em um ambiente Docker local.

## Pré-requisitos

- Docker
- Docker Compose

## Instruções

### 1. Clone o repositório do GitHub

Clone o repositório para sua máquina local:

```bash
git clone git@github.com:lcamargo82/grupo_adriano_cobuccio_test.git
```

### 2. Acesse a pasta do projeto api

Acesse a pasta do projeto para configurar:

```bash
cd financial_system/api
```

### 3. Configure o .env

Copie o arquivo env.exemple para .env:

```bash
cp financial_system/api/.env.example financial_system/api/.env
```

### 4. Acesse a pasta do projeto frontend

Acesse a pasta do projeto para configurar:

```bash
cd financial_system/frontend/app
```

### 5. Configure o .env

Copie o arquivo env.exemple para .env:

```bash
cp financial_system/api/.env.example financial_system/api/.env
```

### 4. Fazer o build e up do container

Faça o buid do container e subir a aplicação:

```bash
docker compose up --build -d
```

As aplicações estarão disponíveis nos endereços:

API: http://localhost:8000/

Documentação: http://localhost:8000/api/documentation

Frontend: http://localhost:5173/

### 5. Migrations com seeds

Fazendo as migrations com seeds:

```bash
docker exec -it api_app php artisan migrate:fresh --seed
```

### 6. Rodando os testes

Rodando os testes:

```bash
docker exec -it api_app php artisan test
```

## Observações
- Para testes de criação de usuário e login via postman é necessário a criptografia da senha antes do envio com o comando e dentro do pasta secret:
- 
```bash
echo -n "sua_senha_a_ser_enviada" | openssl rsautl -encrypt -pubin -inkey public.pem | base64
```

- Certifique-se de que suas portas no Docker não estejam em conflito com outras aplicações.
- Configure as variáveis de ambiente no arquivo .env conforme necessário para sua aplicação.
