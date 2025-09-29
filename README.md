# Mini ERP/CRM — Boilerplate (Apache + PHP 8 + MySQL + RabbitMQ)

### Subir o ambiente

```bash
# Na raiz do repo (pasta project/)
docker compose up --build -d
```

- App: http://localhost:8080
- RabbitMQ UI: http://localhost:15672 (guest/guest)

### Comandos úteis

```bash
docker compose ps
docker exec -it php_apache bash
# Rodar worker manualmente
docker exec -it php_apache php bin/worker.php
```

### Banco de dados

- MySQL exposto em 3307 (host) -> 3306 (container)
- DB: erp, user: erp_user, pass: erp_pass
- Schema inicial em `sql/init.sql` (admin: admin@example.com / senha: admin123)

### Estrutura

- `public/` DocumentRoot e roteador (`index.php`)
- `src/` Controllers, Models, Services, Queue
- `config/` `db.php` (PDO)
- `bin/` `worker.php` (consumidor RabbitMQ)

### Observações para entrevista

- Ambiente legado: Apache + mod_php (simulado em Docker)
- Mensageria: RabbitMQ com `php-amqplib`
- SQL: uso de PDO com prepared statements
