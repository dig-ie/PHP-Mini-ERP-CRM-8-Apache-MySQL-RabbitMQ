# Mini ERP/CRM em PHP 8 + Apache + MySQL + RabbitMQ + Asaas 🚀

Sistema exemplo de Mini ERP/CRM com autenticação por sessão, cadastro de clientes, pedidos e criação de cobranças via Asaas. Usa MySQL para persistência e RabbitMQ para processamento assíncrono (publicação/consumo de eventos).

## Visão Geral

- **Login e Sessão**: autenticação simples baseada em sessão (PHP nativo).
- **Mini CRM/ERP**: gestão de clientes, pedidos e cobranças com dashboard.
- **Integração Asaas**: criação de clientes e cobranças (PIX, Boleto, Cartão) via API.
- **Persistência MySQL**: tabelas `users`, `clients`, `orders`, `payments`.
- **RabbitMQ**: publicação do evento `order.created` ao criar pedido e worker consumidor (em desenvolvimento) para orquestrar integrações/filas relacionadas ao gateway de pagamento.
- **Docker Compose**: orquestra PHP+Apache, MySQL, RabbitMQ e um worker PHP.

## Tecnologias

- PHP 8.2 (Apache) • Docker/Docker Compose
- MySQL 8
- RabbitMQ 3 (com `management` UI)
- Asaas API (sandbox/prod)
- Bibliotecas: `php-amqplib/php-amqplib`, `monolog/monolog`

## Arquitetura

Estrutura em camadas (sem framework), estilo MVC enxuto:

- `public/index.php`: roteamento básico por caminho/método HTTP e bootstrap de sessão/env.
- `src/Controllers`: lógica de interface/fluxos (ex.: `AuthController`, `ClientController`, `OrderController`, `PaymentController`).
- `src/Models`: acesso a dados via PDO (ex.: `User`, `Client`, `Order`, `Payment`).
- `src/Services`: integrações externas (ex.: `AsaasService`).
- `src/Queue`: publicação de mensagens (`RabbitMQPublisher`).
- `bin/worker.php`: consumidor RabbitMQ (processa eventos).
- `src/Config/Database.php`: criação de conexão PDO usando env.
- `views/*`: templates PHP simples (login, dashboard, formulários).
- `sql/init.sql`: schema e seed do usuário admin.

### Roteamento

O roteador manual em `public/index.php` expõe rotas como:

- Autenticação: `GET /login`, `POST /login`, `GET /logout`
- Clientes: `GET /clients/create`, `POST /clients/store`, `GET /clients/edit`, `POST /clients/update`, `GET /clients/delete`
- Pedidos: `GET /orders/create`, `POST /orders/store`, `GET /orders/edit`, `POST /orders/update`, `GET /orders/delete`
- Pagamentos: `GET /payments/create`, `POST /payments/store`, `GET /payments/client`
- Dashboard: `GET /` ou `GET /dashboard`

## Autenticação (Login)

- Página `views/login.php` e controlador `AuthController`.
- Verificação por `User::findByEmail` e `password_verify`.
- Sessões armazena `user_id` e `user_name`.
- Seed de usuário admin (em `sql/init.sql`):
  - Email: `admin@example.com`
  - Senha: `admin123`

## CRM/ERP (Clientes, Pedidos, Cobranças)

- **Clientes** (`Client`): CRUD básico; opção de criar cliente correspondente na Asaas ao cadastrar/editar.
- **Pedidos** (`Order`): criação/edição/exclusão; ao criar, publica evento no RabbitMQ.
- **Cobranças** (`Payment` + Asaas): cria cobrança na Asaas e persiste informações locais (valor, vencimento, status, URLs).
- **Dashboard** (`views/dashboard.php`): visão de clientes e pedidos; atalho para criar cobrança e listar cobranças do cliente.

## Integração com Asaas 💳

O serviço `App\Services\AsaasService` encapsula chamadas à API:

- `createCustomer` (POST `/customers`)
- `listCustomers` (GET `/customers`)
- `getCustomer` (GET `/customers/{id}`)
- `createPayment` (POST `/payments`)

Uso na aplicação:

- `ClientController`: cria cliente na Asaas (opcional) durante cadastro/edição local (exige `name` e `cpfCnpj`).
- `PaymentController`: garante existência do `customer` na Asaas e cria cobrança; salva na tabela `payments` dados retornados (ex.: `invoiceUrl`, `bankSlipUrl`, `pixQrCode`).

Variáveis de ambiente relevantes (ver `.env`/`env.example`):

```env
ASAAS_API_URL=https://api-sandbox.asaas.com/v3
ASAAS_ACCESS_TOKEN=seu_token_aqui
```

## RabbitMQ (Filas e Eventos) 🐰

- Publicação via `App\Queue\RabbitMQPublisher` (AMQP topic):
  - Exchange: `orders`
  - Routing key: `orders.created`
  - Payload: `{ type: 'order.created', order_id, client_id, total_amount, meta, timestamp }`
- Consumo via `bin/worker.php`:
  - Declara `exchange=orders`, `queue=orders.created.q`, binding `orders.created`.
  - Processa mensagens `order.created` (simula envio de e-mail, notificação e atualização de estoque) e dá `ack`.

Status atual do RabbitMQ: implementação funcional de publicação/consumo para eventos de pedido. Em desenvolvimento está a orquestração de integrações voltadas ao gateway de pagamento (ex.: consumo de eventos de pagamento/assinaturas, atualização assíncrona de status de cobranças, processamento de webhooks da Asaas para filas internas e reconciliação no banco).

## Banco de Dados (MySQL)

Conexão via `App\Config\Database::pdo()` (PDO). Schema inicial em `sql/init.sql`:

- `users (id, email, password_hash, name, created_at)` — seed admin.
- `clients (id, name, email, phone, cpf_cnpj, asaas_customer_id, asaas_synced_at, created_at)`.
- `orders (id, client_id, total_amount, status, created_at)`.
- `payments (id, client_id, asaas_payment_id, billing_type, value, due_date, status, description, invoice_url, bank_slip_url, pix_qr_code, created_at)`.

## Como Executar (Docker)

Pré-requisitos: Docker Desktop.

1. Copie as variáveis de ambiente:

   ```bash
   cp env.example .env
   ```

   - Edite `.env` e informe `ASAAS_ACCESS_TOKEN` (sandbox ou produção).

2. Suba os serviços:

   - Windows: `start.bat`
   - Linux/macOS: `./start.sh`
   - Ou manualmente: `docker-compose up -d`

3. Acesse:

   - Aplicação: `http://localhost:8080`
   - RabbitMQ Management: `http://localhost:15672` (guest/guest)
   - MySQL: `localhost:3307` (DB: `erp`, user: `erp_user`, pass: `erp_pass`)

4. Login inicial:

   - `admin@example.com` / `admin123`

5. Logs do worker (consumidor RabbitMQ):
   ```bash
   docker logs -f worker
   ```

## Variáveis de Ambiente

Veja `env.example` para a lista completa. Principais chaves:

```env
# App
APP_ENV=local
APP_DEBUG=1

# Database
DB_HOST=mysql
DB_PORT=3306
DB_NAME=erp
DB_USER=erp_user
DB_PASS=erp_pass

# RabbitMQ
RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASS=guest

# Asaas
ASAAS_API_URL=https://api-sandbox.asaas.com/v3
ASAAS_ACCESS_TOKEN=token_conta_asaas
```

## Rotas Principais

| Método | Rota             | Descrição                               |
| ------ | ---------------- | --------------------------------------- |
| GET    | /login           | Formulário de login                     |
| POST   | /login           | Autentica e cria sessão                 |
| GET    | /logout          | Encerra a sessão                        |
| GET    | /dashboard       | Dashboard (requer login)                |
| GET    | /clients/create  | Form novo cliente                       |
| POST   | /clients/store   | Cria cliente (e opcionalmente na Asaas) |
| GET    | /clients/edit    | Edita cliente                           |
| POST   | /clients/update  | Atualiza cliente                        |
| GET    | /clients/delete  | Exclui cliente                          |
| GET    | /orders/create   | Form novo pedido                        |
| POST   | /orders/store    | Cria pedido e publica evento RabbitMQ   |
| GET    | /orders/edit     | Edita pedido                            |
| POST   | /orders/update   | Atualiza pedido                         |
| GET    | /orders/delete   | Exclui pedido                           |
| GET    | /payments/create | Form nova cobrança                      |
| POST   | /payments/store  | Cria cobrança na Asaas e persiste local |
| GET    | /payments/client | Lista cobranças de um cliente           |

## Exemplo (CLI) — Criar cliente na Asaas

Há um exemplo em `examples/create_customer.php` que carrega env e demonstra a criação de cliente pela `AsaasController`. Você pode executá-lo dentro do container:

```bash
docker exec -it php_apache php examples/create_customer.php
```

## Roadmap (próximos passos)

- Webhooks da Asaas -> filas internas -> atualização de status em `payments`.
- Filas dedicadas para retentativas, dead-letter e auditoria de eventos.
- Regras de permissão/roles na autenticação e melhoria de segurança.
- Validações e mensagens de erro mais ricas nos formulários.

---

Este repositório tem fins educacionais/demonstração para padrões de integração e orquestração com filas e um gateway de pagamento (Asaas).
